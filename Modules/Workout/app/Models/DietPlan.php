<?php

namespace Modules\Workout\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DietPlan extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['trainer_id', 'user_id', 'title', 'daily_calories', 'protein_grams', 'carbs_grams', 'fats_grams', 'status', 'notes'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('diet_plans');
    }

    protected $fillable = [
        'trainer_id',
        'user_id',
        'title',
        'daily_calories',
        'protein_grams',
        'carbs_grams',
        'fats_grams',
        'status',
        'notes',
    ];

    /**
     * Get trainer assigned to this diet plan.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Get member assigned to this diet plan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get meals included in this diet plan.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(DietMeal::class, 'diet_plan_id');
    }
}
