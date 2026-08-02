<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\ApiAuthController;
use Modules\User\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes for User Module (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Authentication Routes
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);

    // Protected Routes (Requires Sanctum Token)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        Route::post('logout', [ApiAuthController::class, 'logout']);
        
        // Admin Only Routes
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('users', UserController::class)->names('user');
        });

    });
    
});