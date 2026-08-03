<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Admin\AdminGymScheduleController;
use Modules\Subscription\Http\Controllers\Admin\AdminSubscriptionPlanController;
use Modules\Subscription\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes for Subscription Module
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Member Subscription Plans & Schedules Page
    Route::get('plans', [SubscriptionController::class, 'plans'])->name('plans.index');
    Route::post('subscriptions', [SubscriptionController::class, 'subscribe'])->name('subscriptions.store');

    // Private Trainer Bookings Page
    Route::get('bookings', [SubscriptionController::class, 'bookings'])->name('bookings.index');
    Route::post('bookings', [SubscriptionController::class, 'bookSession'])->name('bookings.store');

    // Admin Only Subscription Plans & Gym Schedules Management Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Plans Management
        Route::get('subscription-plans', [AdminSubscriptionPlanController::class, 'index'])->name('admin.plans.index');
        Route::get('subscription-plans/create', [AdminSubscriptionPlanController::class, 'create'])->name('admin.plans.create');
        Route::post('subscription-plans', [AdminSubscriptionPlanController::class, 'store'])->name('admin.plans.store');
        Route::get('subscription-plans/{id}/edit', [AdminSubscriptionPlanController::class, 'edit'])->name('admin.plans.edit');
        Route::put('subscription-plans/{id}', [AdminSubscriptionPlanController::class, 'update'])->name('admin.plans.update');
        Route::patch('subscription-plans/{id}/toggle', [AdminSubscriptionPlanController::class, 'toggleActive'])->name('admin.plans.toggle');
        Route::delete('subscription-plans/{id}', [AdminSubscriptionPlanController::class, 'destroy'])->name('admin.plans.destroy');

        // Gym Schedules Management
        Route::get('gym-schedules', [AdminGymScheduleController::class, 'index'])->name('admin.schedules.index');
        Route::get('gym-schedules/create', [AdminGymScheduleController::class, 'create'])->name('admin.schedules.create');
        Route::post('gym-schedules', [AdminGymScheduleController::class, 'store'])->name('admin.schedules.store');
        Route::get('gym-schedules/{id}/edit', [AdminGymScheduleController::class, 'edit'])->name('admin.schedules.edit');
        Route::put('gym-schedules/{id}', [AdminGymScheduleController::class, 'update'])->name('admin.schedules.update');
        Route::delete('gym-schedules/{id}', [AdminGymScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
    });
});
