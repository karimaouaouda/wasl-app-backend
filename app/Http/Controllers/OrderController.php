<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\DB;
use Nette\NotImplementedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws \Throwable
     */
    public function index(): \Illuminate\Http\Resources\Json\ResourceCollection
    {
        // return only who has many items at least one
        $orders = Order::has('items')->with('items')->get();


        return $orders
            ->toResourceCollection(); // convert to HTTP Resource
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
        return $order
            ->toResource();
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
}
