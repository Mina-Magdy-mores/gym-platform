<?php

namespace Modules\Workout\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Workout\Models\WorkoutRoutine;

class WorkoutService
{
    /**
     * Get all members assigned to a trainer via active bookings or subscriptions.
     */
    public function getTrainerMembers(int $trainerId): Collection
    {
        return User::role('member')
            ->whereHas('bookings', function ($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->with(['activeSubscription.plan', 'activeWorkoutRoutine.exercises', 'activeDietPlan.meals'])
            ->get();
    }

    /**
     * Create a new workout routine with exercises for a member.
     */
    public function createRoutine(array $data): WorkoutRoutine
    {
        return DB::transaction(function () use ($data) {
            // Archive previous active routines for this user
            WorkoutRoutine::where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->update(['status' => 'archived']);

            $routine = WorkoutRoutine::create([
                'trainer_id' => $data['trainer_id'],
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'goal' => $data['goal'] ?? null,
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
            ]);

            if (!empty($data['exercises']) && is_array($data['exercises'])) {
                foreach ($data['exercises'] as $ex) {
                    if (!empty($ex['exercise_name'])) {
                        $routine->exercises()->create([
                            'day_name' => $ex['day_name'] ?? 'Day 1',
                            'exercise_name' => $ex['exercise_name'],
                            'target_muscle' => $ex['target_muscle'] ?? null,
                            'sets' => $ex['sets'] ?? 3,
                            'reps' => $ex['reps'] ?? '10-12',
                            'rest_seconds' => $ex['rest_seconds'] ?? 60,
                            'notes' => $ex['notes'] ?? null,
                        ]);
                    }
                }
            }

            return $routine->load('exercises');
        });
    }

    /**
     * Get active workout routine for a member.
     */
    public function getMemberActiveRoutine(int $userId): ?WorkoutRoutine
    {
        return WorkoutRoutine::with('exercises', 'trainer')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();
    }
}