<?php

namespace App\Providers;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('pickup-order', function (User $user, Order $order) {

            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            $query = $user->orders()
                ->wherePivot('order_id', $order->id)
                ->wherePivot('status', DeliveryStatus::ACCEPTED->value);

            if( !$query->exists() ){
                return Response::denyAsNotFound('no order to pickup, maybe is canceled');
            }

            return Response::allow();
        });

        Gate::define('accept-order', function (User $user, Order $order) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            if(!in_array($order->status, [OrderStatus::PREPARING->value, OrderStatus::READY->value])){
                return Response::denyWithStatus(400, 'order status not allowed');
            }

            return Response::allow();
        });

        Gate::define('reject-order', function (User $user, Order $order) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            if (!in_array($order->status, [OrderStatus::READY->value, OrderStatus::PREPARING->value])){
                return Response::denyWithStatus(400, 'order status not allowed');
            }

            return Response::allow();
        });

        Gate::define('deliver-order', function (User $user, Order $order) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            $query = $user->orders()
                ->wherePivot('order_id', $order->id)
                ->wherePivot('status', DeliveryStatus::ACCEPTED->value);

            if( !$query->exists() ){
                return Response::denyAsNotFound('no order to deliver, maybe is canceled');
            }

            return Response::allow();
        });

        Gate::define('complete-order', function (User $user, Order $order) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            $query = $user->orders()
                ->wherePivot('order_id', $order->id)
                ->wherePivot('status', DeliveryStatus::PICKED->value);

            if(! $query->exists() ){
                return Response::denyAsNotFound('no order to complete, maybe you forget to pickup');
            }

            return Response::allow();
        });

        Gate::define('fetch-finished-orders', function (User $user) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            return Response::allow();
        });

        Gate::define('fetch-today-orders', function (User $user) {
            if( $user->isAdmin() ){
                $user->currentAccessToken()->delete();
                return Response::denyWithStatus(
                    401,
                    'admins not allowed to dealing with orders'
                );
            }

            return Response::allow();
        });
    }
}
