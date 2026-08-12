<?php

use Illuminate\Support\Facades\Route;
use Modules\Workout\Http\Controllers\TrainerWorkoutController;
use Modules\Workout\Http\Controllers\TrainerDietController;
use Modules\Workout\Http\Controllers\WorkoutController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('workouts', WorkoutController::class)->names('workout');
});

Route::middleware(['auth', 'role:trainer|admin'])->prefix('trainer')->group(function () {
    Route::get('members', [TrainerWorkoutController::class, 'members'])->name('trainer.members.index');
    
    // Workout Routine Assignment
    Route::get('members/{user}/workout/create', [TrainerWorkoutController::class, 'create'])->name('trainer.workout.create');
    Route::post('members/{user}/workout', [TrainerWorkoutController::class, 'store'])->name('trainer.workout.store');

    // Diet Plan Assignment
    Route::get('members/{user}/diet/create', [TrainerDietController::class, 'create'])->name('trainer.diet.create');
    Route::post('members/{user}/diet', [TrainerDietController::class, 'store'])->name('trainer.diet.store');
});
