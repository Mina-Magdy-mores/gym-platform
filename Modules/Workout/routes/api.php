<?php

use Illuminate\Support\Facades\Route;
use Modules\Workout\Http\Controllers\Api\ApiDietController;
use Modules\Workout\Http\Controllers\Api\ApiWorkoutController;

/*
|--------------------------------------------------------------------------
| API Routes for Workout & Nutrition Module (v1)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('v1/workouts')->group(function () {

    // Workout Routines API
    Route::get('routines', [ApiWorkoutController::class, 'index']);
    Route::get('routines/{routine}', [ApiWorkoutController::class, 'show']);
    Route::post('routines', [ApiWorkoutController::class, 'store']);
    Route::put('routines/{routine}', [ApiWorkoutController::class, 'update']);
    Route::delete('routines/{routine}', [ApiWorkoutController::class, 'destroy']);

    // Diet & Nutrition Plans API
    Route::get('diets', [ApiDietController::class, 'index']);
    Route::get('diets/{dietPlan}', [ApiDietController::class, 'show']);
    Route::post('diets', [ApiDietController::class, 'store']);
    Route::put('diets/{dietPlan}', [ApiDietController::class, 'update']);
    Route::delete('diets/{dietPlan}', [ApiDietController::class, 'destroy']);

    // Coach Athletes Roster
    Route::get('athletes', [ApiWorkoutController::class, 'athletes']);

});
