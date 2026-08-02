<?php

namespace Modules\User\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Operation successful.', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message = 'An error occurred.', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}