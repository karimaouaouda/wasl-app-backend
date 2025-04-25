<?php

namespace App\Services;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderService
{
    use ResponseTrait;

    /**
     * @throws AuthorizationException
     */
    public function pickup(Order $order): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('pickup-order', $order);

        $order->pivot->status = DeliveryStatus::PICKED->value;
        $order->pivot->save();

        return $this->success([
            'success' => 'order picked successfully'
        ]);

    }

    /**
     * @throws AuthorizationException
     * @throws \Throwable
     */
    public function complete(Order $order): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('complete-order', $order);

        DB::transaction(function() use ($order){
            $order->pivot->status = DeliveryStatus::COMPLETED->value;
            $order->pivot->save();


            $order->status = OrderStatus::COMPLETED->value;
            $order->save();
        });

        return $this->success('order completed successfully');
    }

    /**
     * @throws AuthorizationException
     */
    public function reject(Order $order): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('reject-order', $order);

        $user = Auth::user();
        $user->orders()->attach([$order->id], [
            'updated_at' => now(),
            'status' => DeliveryStatus::REJECTED->value
        ]);

        return $this->success('Order rejected successfully.');
    }

    public function cancel(Order $order): \Illuminate\Http\JsonResponse
    {

    }

    /**
     * @throws \Throwable
     * @throws AuthorizationException
     */
    public function finished(): \Illuminate\Http\Resources\Json\ResourceCollection
    {
       Gate::authorize('fetch-finished-orders');

       $user = Auth::user();

        return $user->orders()
            ->wherePivot('status', DeliveryStatus::COMPLETED->value)
            ->get()
            ->toResourceCollection();
    }

    public function today(): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('fetch-today-orders');

        return $user->orders()
            ->wherePivotIn('status', ['accepted', 'picked'])
            ->get()
            ->toResourceCollection();
    }

    public function active(): \Illuminate\Http\JsonResponse
    {

    }
}
