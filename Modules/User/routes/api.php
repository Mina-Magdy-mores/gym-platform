<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\ApiAdminUserController;
use Modules\User\Http\Controllers\Api\ApiAuthController;
use Modules\User\Http\Controllers\Api\ApiProfileController;
use Modules\User\Http\Controllers\Api\ApiTrainerController;

/*
|--------------------------------------------------------------------------
| API Routes for User Module (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Authentication & Coaches Showcase Routes
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login'])->middleware('throttle:login');
    Route::get('trainers', [ApiTrainerController::class, 'publicIndex']);

    // Protected Routes (Requires Sanctum Token)
    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('logout', [ApiAuthController::class, 'logout']);

        // Profile & Media Management
        Route::get('profile', [ApiProfileController::class, 'show']);
        Route::post('profile', [ApiProfileController::class, 'update']);
        Route::delete('profile/media/{mediaId}', [ApiProfileController::class, 'destroyMedia']);

        // Admin Moderation & Trainer Recruitment Endpoints
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            // User Management API
            Route::get('users', [ApiAdminUserController::class, 'index']);
            Route::post('users', [ApiAdminUserController::class, 'store']);
            Route::get('users/{user}', [ApiAdminUserController::class, 'show']);
            Route::put('users/{user}', [ApiAdminUserController::class, 'update']);
            Route::patch('users/{user}/toggle-active', [ApiAdminUserController::class, 'toggleActive']);
            Route::post('users/{user}/block', [ApiAdminUserController::class, 'block']);
            Route::post('users/{user}/unblock', [ApiAdminUserController::class, 'unblock']);
            Route::delete('users/{user}', [ApiAdminUserController::class, 'destroy']);

            // Certified Trainers Recruitment API
            Route::get('trainers', [ApiTrainerController::class, 'index']);
            Route::post('trainers', [ApiTrainerController::class, 'store']);
            Route::post('trainers/promote', [ApiTrainerController::class, 'promote']);
            Route::post('trainers/{trainer}/certificates', [ApiTrainerController::class, 'uploadCertificates']);
            Route::delete('trainers/certificates/{mediaId}', [ApiTrainerController::class, 'deleteCertificate']);
            Route::delete('trainers/{trainer}', [ApiTrainerController::class, 'destroy']);
        });

    });

});