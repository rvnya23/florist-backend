<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Support\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->loginAs($request, 'admin');
    }

    public function customerLogin(LoginRequest $request): JsonResponse
    {
        return $this->loginAs($request, 'customer');
    }

    private function loginAs(LoginRequest $request, string $role): JsonResponse
    {
        $credentials = [...$request->safe()->only(['email', 'password']), 'role' => $role];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return APIResponse::failed(
                'Email atau password salah.',
                ['email' => ['Email atau password salah.']],
                422,
            );
        }

        $request->session()->regenerate();

        return APIResponse::success(
            (new UserResource($request->user()->load('member')))->resolve($request),
            'Login berhasil.',
        );
    }

    public function user(Request $request): JsonResponse
    {
        return APIResponse::success(
            (new UserResource($request->user()->load('member')))->resolve($request),
        );
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::forgetGuards();

        return APIResponse::success(message: 'Logout berhasil.');
    }
}
