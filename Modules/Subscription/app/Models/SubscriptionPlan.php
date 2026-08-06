<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Payment\Models\Payment;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration_months',
        'price',
        'currency',
        'free_days',
        'freeze_days',
        'invitations_count',
        'inbody_scans',
        'pt_sessions',
        'kickboxing_classes',
        'nutrition_plans',
        'spa_access',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
        'free_days' => 'integer',
        'freeze_days' => 'integer',
        'invitations_count' => 'integer',
        'inbody_scans' => 'integer',
        'pt_sessions' => 'integer',
        'kickboxing_classes' => 'integer',
        'nutrition_plans' => 'integer',
        'spa_access' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}