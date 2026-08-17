<?php

namespace Modules\Workout\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class WorkoutRoutine extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['trainer_id', 'user_id', 'title', 'goal', 'status', 'notes'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('workout_routines');
    }

    protected $fillable = [
        'trainer_id',
        'user_id',
        'title',
        'goal',
        'status',
        'notes',
    ];

    /**
     * Get trainer assigned to this routine.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Get member assigned to this routine.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get exercises included in this routine.
     */
    public function exercises(): HasMany
    {
        return $this->hasMany(RoutineExercise::class, 'workout_routine_id');
    }
}
