<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
        'token' => $request->bearerToken(),
    ]);
})->middleware('auth:sanctum');

Route::get('/users/{user}', function(\App\Models\User $user){
    return $user
            ->toResource();
});


Route::get('/test', function() {
    return response()->json(['message' => 'Hello, World!']);
});

//authentication
Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')
            ->middleware('auth:sanctum');
    });





Route::controller(OrderController::class)
    ->prefix('orders')
    ->group(function () {
        Route::middleware('auth:sanctum')
            ->group(function() {
                Route::get('/active', 'active');

                Route::post('/confirm', 'confirm');

                Route::post('/cancel', 'cancel');

                Route::post('/reject', 'reject');

                Route::post('/complete', 'complete');

                Route::get('/{user}/today', 'today');

                Route::get('/{user}/finished', 'finished');

            });
    });

Route::resource('orders', OrderController::class)
    ->middleware('auth:sanctum')
    ->except(['destroy']);

Route::get('/test-api', function(){
    return response()->json([
        'message' => 'every thing work fine'
    ], 200);
});
