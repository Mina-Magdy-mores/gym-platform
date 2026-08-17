<?php

namespace Modules\Workout\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Workout\Models\WorkoutRoutine;
use Modules\Workout\Services\WorkoutService;

class ApiWorkoutController extends Controller
{
    protected WorkoutService $workoutService;

    public function __construct(WorkoutService $workoutService)
    {
        $this->workoutService = $workoutService;
    }

    /**
     * Get workout routines for the authenticated user (member or coach).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $routines = WorkoutRoutine::with(['exercises', 'trainer:id,name,email', 'user:id,name,email'])
            ->where(function ($query) use ($user) {
                if ($user->hasRole('member')) {
                    $query->where('user_id', $user->id);
                } elseif ($user->hasRole('trainer')) {
                    $query->where('trainer_id', $user->id);
                }
            })
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $routines,
        ]);
    }

    /**
     * Display a specific workout routine with its exercises.
     */
    public function show(Request $request, WorkoutRoutine $routine): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('member') && $routine->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to routine.'], 403);
        }

        if ($user->hasRole('trainer') && $routine->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to routine.'], 403);
        }

        $routine->load(['exercises', 'trainer:id,name,email', 'user:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => $routine,
        ]);
    }

    /**
     * Coach creates a new workout routine with exercises for an athlete.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole(['trainer', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Only certified coaches or admins can assign workout routines.'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,archived',
            'notes' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.day_name' => 'required|string|max:255',
            'exercises.*.exercise_name' => 'required|string|max:255',
            'exercises.*.target_muscle' => 'nullable|string|max:255',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|string|max:50',
            'exercises.*.rest_seconds' => 'nullable|integer|min:0',
            'exercises.*.notes' => 'nullable|string',
        ]);

        $validated['trainer_id'] = $user->id;
        $validated['status'] = $validated['status'] ?? 'active';

        $routine = $this->workoutService->createRoutine($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workout routine assigned to athlete successfully.',
            'data' => $routine->load('exercises'),
        ], 201);
    }

    /**
     * Coach updates an existing workout routine.
     */
    public function update(Request $request, WorkoutRoutine $routine): JsonResponse
    {
        $user = $request->user();

        if ($routine->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to modify this routine.'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'goal' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,archived',
            'notes' => 'nullable|string',
        ]);

        $routine->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workout routine updated successfully.',
            'data' => $routine->fresh(['exercises', 'trainer:id,name', 'user:id,name']),
        ]);
    }

    /**
     * Delete or archive a workout routine.
     */
    public function destroy(Request $request, WorkoutRoutine $routine): JsonResponse
    {
        $user = $request->user();

        if ($routine->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to delete this routine.'], 403);
        }

        $routine->exercises()->delete();
        $routine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workout routine deleted successfully.',
        ]);
    }

    /**
     * Coach retrieves athletes / members roster.
     */
    public function athletes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole(['trainer', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $athletes = $this->workoutService->getTrainerMembers($user->id);

        return response()->json([
            'success' => true,
            'data' => $athletes,
        ]);
    }
}
