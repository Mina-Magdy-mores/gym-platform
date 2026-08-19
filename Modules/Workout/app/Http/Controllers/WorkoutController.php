<?php

namespace Modules\Workout\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Workout\Models\DietPlan;
use Modules\Workout\Models\WorkoutRoutine;

class WorkoutController extends Controller
{
    /**
     * Display member's personalized workout routine.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $routines = WorkoutRoutine::with(['trainer', 'exercises'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeRoutine = $routines->firstWhere('status', 'active') ?? $routines->first();

        return view('workout::index', compact('routines', 'activeRoutine'));
    }

    /**
     * Display member's personalized nutrition and diet plan.
     */
    public function diet(Request $request): View
    {
        $user = $request->user();

        $diets = DietPlan::with(['trainer', 'meals'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $activeDiet = $diets->firstWhere('status', 'active') ?? $diets->first();

        return view('workout::diet', compact('diets', 'activeDiet'));
    }

    /**
     * Show specific workout routine.
     */
    public function show(WorkoutRoutine $workout): View
    {
        $workout->load(['trainer', 'exercises']);

        return view('workout::show', compact('workout'));
    }
}

