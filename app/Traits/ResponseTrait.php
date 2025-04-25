<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ResponseTrait
{
    public function success($data): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, 200);
    }

    public function failure($data): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, 400);
    }

    public function error($data): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, 400);
    }

    public function notAuthorized($data): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $user?->currentAccessToken()->delete();
        return response()->json($data, 401);
    }

    public function notFound($data): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, 404);
    }
}
