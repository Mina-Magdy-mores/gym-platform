<?php

namespace Modules\Payment\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Subscription\Models\SubscriptionPlan;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'transaction_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}