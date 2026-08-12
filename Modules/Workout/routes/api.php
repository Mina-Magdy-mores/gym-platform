<?php

use Illuminate\Support\Facades\Route;
use Modules\Workout\Http\Controllers\WorkoutController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('workouts', WorkoutController::class)->names('workout');
});
