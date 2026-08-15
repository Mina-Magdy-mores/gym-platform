<?php

namespace Modules\Workout\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Booking;
use Modules\Workout\Models\WorkoutRoutine;

class WorkoutService
{
    /**
     * Get members assigned to a trainer via bookings or all members for administration.
     */
    public function getTrainerMembers(int $trainerId): Collection
    {
        $user = User::find($trainerId);

        // 1. Master Admin sees all platform members (role 'member' only)
        if ($user && $user->hasRole('admin')) {
            return User::role('member')
                ->with(['activeSubscription.plan', 'activeWorkoutRoutine.exercises', 'activeDietPlan.meals'])
                ->latest()
                ->get();
        }

        // 2. Fetch all Member IDs who have booked sessions with this trainer
        $memberIds = Booking::where('trainer_id', $trainerId)
            ->pluck('user_id')
            ->unique();

        if ($memberIds->isNotEmpty()) {
            return User::role('member')
                ->whereIn('id', $memberIds)
                ->with(['activeSubscription.plan', 'activeWorkoutRoutine.exercises', 'activeDietPlan.meals'])
                ->get();
        }

        // 3. Fallback: If no specific bookings found -> show registered members only
        return User::role('member')
            ->with(['activeSubscription.plan', 'activeWorkoutRoutine.exercises', 'activeDietPlan.meals'])
            ->latest()
            ->get();
    }

    /**
     * Create or update a workout routine with exercises for a member.
     */
    public function createRoutine(array $data): WorkoutRoutine
    {
        return DB::transaction(function () use ($data) {
            $status = $data['status'] ?? 'active';

            // Check if member already has an existing active routine
            $routine = WorkoutRoutine::where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->first();

            if ($routine) {
                // In-place update existing routine details
                $routine->update([
                    'trainer_id' => $data['trainer_id'],
                    'title' => $data['title'],
                    'goal' => $data['goal'] ?? null,
                    'status' => $status,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Clear old exercises to replace with updated ones
                $routine->exercises()->delete();
            } else {
                // Create brand new routine
                $routine = WorkoutRoutine::create([
                    'trainer_id' => $data['trainer_id'],
                    'user_id' => $data['user_id'],
                    'title' => $data['title'],
                    'goal' => $data['goal'] ?? null,
                    'status' => $status,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

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