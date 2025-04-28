<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/**
 * @mixin User
 */
class AuthController extends Controller
{
    use ResponseTrait;
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'success' => 'Login successful',
                'token' => $token,
                'user' => $user->toResource(),
            ]);
        }

        return $this->error(['message' => 'Invalid credentials']);
    }

    /**
     * @throws \Throwable
     */
    public function register(CreateUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create($request->validated());

        $user->save();

        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'success' => 'Login successful',
            'token' => $token,
            'user' => $user->toResource(),
        ]);
    }

    public function logout(Request $request){
        $user = $request->user()->currentAccessToken()->delete();
        return $this->success([
            'success' => 'Logout successful',
        ]);
    }
}
