<?php

namespace App\Http\Controllers\Auth;

use App\Builder\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected AuthService $service){}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->service->login($request->validated());
        return ApiResponse::success($data, "Usuário com sucesso!", 200);
    }

    public function me(): JsonResponse
    {
        $data = $this->service->me();
        return ApiResponse::success($data, "Dados do usuário", 200);
    }

    public function logout(): JsonResponse
    {
        $this->service->logout();
        return ApiResponse::success(null, "Desconectado com sucesso", 200);
    }

    public function refreshToken(): JsonResponse
    {
        $data = $this->service->refreshToken();
        return ApiResponse::success($data, "Token atualizado com sucesso!", 200);
    }
}