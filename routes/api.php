<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
        'token' => $request->bearerToken(),
    ]);
})->middleware('auth:sanctum');


Route::get('/test', function() {
    return response()->json(['message' => 'Hello, World!']);
});

//authentication
Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
    });

Route::controller(OrderController::class)
    ->group(function () {
        Route::resource('orders', OrderController::class)
            ->except(['destroy']);
    });
