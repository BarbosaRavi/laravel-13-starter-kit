<?php

namespace App\Services\Auth;

use App\Exceptions\ApiException;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService 
{
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if ($user && Auth::attempt(['email' => strtolower($data['email']), 'password' => $data['password']])) {

            if ($user && $user->email_verified_at == null) {
                throw new ApiException('É necessário confirmar o email antes', 403);
            }

            $refreshTtlInSeconds = Config::get('jwt.refresh_ttl') * 60;
            $token = JWTAuth::fromUser($user);

            $user->forceFill(['last_login' => now()])->save();

            return [
                'user' => new UserResource($user->load(['roles.permissions'])), 
                'token' => $token, 
                'refresh_expires_in' => $refreshTtlInSeconds
            ];
        }

        throw new ApiException('Email e/ou senha inválido', 401);
    }

    public function me(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user->load('roles.permissions'));
    }

    public function refreshToken(): array
    {
        $refreshTtlInSeconds = Config::get('jwt.refresh_ttl') * 60;
        $token = auth('api')->refresh();

        return [
            'token' => $token,
            'refresh_expires_in' => $refreshTtlInSeconds,
        ];
    }

    public function logout()
    {
        Auth::logout();
    }
}