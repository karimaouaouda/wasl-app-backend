<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Events\OrderEvents\OrderAccepted;
use App\Events\OrderEvents\OrderCanceled;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nette\NotImplementedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    use ResponseTrait;
    public function __construct(protected Logger $logger, protected OrderService $orderService){}

    /**
     * Display a listing of the resource.
     * @throws \Throwable
     */
    public function index(): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        $user = Auth::user();

        $rejected_ids = $user->orders()
            ->wherePivot('status', '=', DeliveryStatus::REJECTED->value)
            ->get(['id'])
            ->pluck('id')
            ->toArray();

        // return only who has many items at least one
        $orders = Order::has('items')
            ->whereIn('status', ['preparing', 'ready'])
            ->whereNotIn('id', array_values($rejected_ids))
            ->with('items')
            ->get();


        return $orders
            ->toResourceCollection(); // convert to HTTP Resource
    }

    /**
     * @throws \Throwable
     */
    public function active()
    {
        $user = Auth::user();

        $rejected_ids = $user->orders()
            ->wherePivot('status', '=', DeliveryStatus::REJECTED->value)
            ->get(['order_id'])
            ->pluck('order_id')
            ->toArray();

        $orders = Order::query()
            ->whereIn('status', ['preparing', 'ready'])
            ->whereNotIn('id', array_values($rejected_ids))
            ->get();

        return $orders->toResourceCollection();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        // this endpoint is under the web middleware not API
        return view('order.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // for security, you can modify the authorize method in StoreOrderRequest

        $order_data = $request->only(['source_app', 'restaurant_data']);
        $order = new Order($order_data);

        DB::transaction(function () use ($order, $request) {
            $order->save();

            $items = $request->get('items');

            foreach ($items as $item) {
                $order->items()->create($item);
            }
        }, 2);

        return response()->json([
            'success' => 'Order created successfully.',
            'order_id' => $order->getAttribute('id')
        ]);
    }

    /**
     * Display the specified resource.
     * @throws \Throwable
     */
    public function show(Order $order)
    {
        // show a specific order, because we use work as API, we will return the order resource
        return $this->orderService->show($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        throw new NotFoundHttpException("this page un available");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        throw new NotImplementedException("this page un available");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        throw new NotImplementedException("this page un available");
    }

    // custom functions

    /**
     * @throws \Throwable
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $order_id = $request->get('order_id');

        $order = Order::query()
                        ->findOrFail($order_id);

        if ( !in_array($order->getAttribute('status'), ['preparing', 'ready'])){
            return response()->json([
                'message' => 'Order already finished.',
            ], 400);
        }


        $user = Auth::user();

        DB::transaction(function() use ($order, $user, $order_id){

            $order->update([
                'status' => OrderStatus::ACCEPTED->value
            ]);

            $user->orders()->attach($order_id, [
                'updated_at' => now(),
            ]);
        });

        OrderAccepted::dispatch($order);

        return $this->success([
            'success' => 'Order accepted successfully.',
            'order' => $this->show($order)
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function today(User $user, Request $request): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        return $this->orderService->today();
    }

    /**
     * @throws \Throwable
     */
    public function finished(User $user): \Illuminate\Http\Resources\Json\ResourceCollection|\Illuminate\Http\JsonResponse
    {
        return $this->orderService->finished();
    }

    public function reject(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $order = Order::findOrFail($request->input('order_id'));

        return $this->orderService->reject($order);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id']
        ]);

        $order_id = $request->input('order_id');

        // TODO implement authorization for this action

        $order = Order::query()
            ->findOrFail($order_id);
        try{
            $order->update([
                'status' => OrderStatus::CANCELLED->value
            ]);

            OrderCanceled::dispatch($order);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'some error occured'
            ], 400);
        }

        return response()->json([
            'message' => 'order canceled successfully'
        ]);
    }

    /**
     * @throws \Throwable
     * @throws AuthorizationException
     */
    public function complete(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id']
        ]);

        $order_id = $request->input('order_id');

        $order = Order::query()->findOrFail($order_id);

        return $this->orderService->complete($order);
    }

    /**
     * @throws AuthorizationException
     */
    public function pickup(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id']
        ]);

        $order = Order::query()->findOrFail($request->input('order_id'));

        return $this->orderService->pickup($order);
    }
}
