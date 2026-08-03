<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\ApiAuthController;
use Modules\User\Http\Controllers\Api\ApiProfileController;
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
        
        // Profile & Media Management
        Route::get('profile', [ApiProfileController::class, 'show']);
        Route::post('profile', [ApiProfileController::class, 'update']);
        Route::delete('profile/media/{mediaId}', [ApiProfileController::class, 'destroyMedia']);        
        // Admin Only Routes
        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('users', UserController::class)->names('user');
        });

    });
    
});