<?php

namespace Modules\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Payment\Models\Payment;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class UserSubscription extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id', 'subscription_plan_id', 'status', 'price_paid',
                'remaining_freeze_days', 'remaining_invitations', 'remaining_inbody_scans',
                'remaining_pt_sessions', 'remaining_kickboxing_classes', 'remaining_nutrition_plans'
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('subscriptions');
    }
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'status',
        'price_paid',
        'remaining_freeze_days',
        'remaining_invitations',
        'remaining_inbody_scans',
        'remaining_pt_sessions',
        'remaining_kickboxing_classes',
        'remaining_nutrition_plans',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('ends_at', '>=', now());
    }

    /**
     * Relationship: The user who owns this subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: The subscription plan chosen.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Relationship: The payment record associated with this subscription.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'user_subscription_id');
    }
}