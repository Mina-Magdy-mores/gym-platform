<?php

namespace Modules\Workout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietMeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'diet_plan_id',
        'meal_name',
        'meal_time',
        'food_items',
        'calories',
    ];

    /**
     * Get the parent diet plan.
     */
    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class, 'diet_plan_id');
    }
}
