<?php

namespace Modules\Workout\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Workout\Services\WorkoutService;

class TrainerWorkoutController extends Controller
{
    protected WorkoutService $workoutService;

    public function __construct(WorkoutService $workoutService)
    {
        $this->workoutService = $workoutService;
    }

    /**
     * Display members list assigned to trainer.
     */
    public function members(): View
    {
        $members = $this->workoutService->getTrainerMembers(Auth::id());

        return view('workout::trainer.members.index', compact('members'));
    }

    /**
     * Show form to create a workout routine for a member.
     */
    public function create(User $user): View
    {
        return view('workout::trainer.workout.create', compact('user'));
    }

    /**
     * Store a new workout routine for a member.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.day_name' => 'required|string|max:255',
            'exercises.*.exercise_name' => 'required|string|max:255',
            'exercises.*.target_muscle' => 'nullable|string|max:255',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string|max:50',
            'exercises.*.rest_seconds' => 'required|integer|min:0',
            'exercises.*.notes' => 'nullable|string',
        ]);

        $validated['trainer_id'] = Auth::id();
        $validated['user_id'] = $user->id;

        $this->workoutService->createRoutine($validated);

        return redirect()->route('trainer.members.index')->with('success', 'Workout routine assigned to member successfully!');
    }
}