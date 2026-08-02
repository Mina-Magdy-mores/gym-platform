<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\User\Http\Requests\Api\ApiRegisterRequest;
use Modules\User\Http\Requests\Api\ApiLoginRequest;
use Modules\User\Services\AuthService;
use Modules\User\Traits\ApiResponseTrait;
use Modules\User\Transformers\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiAuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    /**
     * Inject AuthService dependency.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle registration of a new user.
     */
    public function register(ApiRegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        $data = [
            'user' => new UserResource($result['user']),
            'access_token' => $result['access_token'],
            'token_type' => 'Bearer',
        ];

        return $this->successResponse($data, 'User registered successfully.', 201);
    }

    /**
     * Handle login and issue token.
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->errorResponse('Invalid login credentials.', 401);
        }

        $data = [
            'user' => new UserResource($result['user']),
            'access_token' => $result['access_token'],
            'token_type' => 'Bearer',
        ];

        return $this->successResponse($data, 'User logged in successfully.');
    }

    /**
     * Handle logout and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(null, 'Token revoked and logged out successfully.');
    }
}