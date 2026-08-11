<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\Admin\ApiAdminGymScheduleController;
use Modules\Subscription\Http\Controllers\Api\Admin\ApiAdminSubscriptionPlanController;
use Modules\Subscription\Http\Controllers\Api\ApiBookingController;
use Modules\Subscription\Http\Controllers\Api\ApiNotificationController;
use Modules\Subscription\Http\Controllers\Api\ApiSubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes for Subscription Module (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Routes (Plans, Gym Operating Schedules & Regulations)
    Route::get('plans', [ApiSubscriptionController::class, 'plans']);
    Route::get('schedules', [ApiSubscriptionController::class, 'schedules']);
    Route::get('gym-rules', [ApiSubscriptionController::class, 'gymRules']);

    // Protected Routes (Requires Sanctum Token)
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Member Dashboard Real-time Benefits & Usage API
        Route::get('member/dashboard', [ApiSubscriptionController::class, 'memberDashboard']);

        // Notifications API (Mobile Symmetry)
        Route::get('notifications', [ApiNotificationController::class, 'index']);
        Route::patch('notifications/{id}/read', [ApiNotificationController::class, 'markAsRead']);
        Route::post('notifications/mark-all-read', [ApiNotificationController::class, 'markAllAsRead']);

        // Subscription Management
        Route::post('subscriptions', [ApiSubscriptionController::class, 'subscribe']);

        // Trainer Bookings
        Route::get('bookings', [ApiBookingController::class, 'index']);
        Route::post('bookings', [ApiBookingController::class, 'store']);

        // Admin Only API Management Endpoints (Plans & Schedules CRUD)
        Route::middleware(['role:admin'])->prefix('admin')->group(function () {
            // Plans Management API
            Route::apiResource('plans', ApiAdminSubscriptionPlanController::class);
            Route::patch('plans/{id}/toggle', [ApiAdminSubscriptionPlanController::class, 'toggleActive']);

            // Schedules Management API
            Route::apiResource('schedules', ApiAdminGymScheduleController::class);
        });

    });

});