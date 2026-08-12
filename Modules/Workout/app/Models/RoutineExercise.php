<?php

namespace Modules\Workout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutineExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_routine_id',
        'day_name',
        'exercise_name',
        'target_muscle',
        'sets',
        'reps',
        'rest_seconds',
        'notes',
    ];

    /**
     * Get the parent workout routine.
     */
    public function routine(): BelongsTo
    {
        return $this->belongsTo(WorkoutRoutine::class, 'workout_routine_id');
    }
}
