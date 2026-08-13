<?php

namespace Modules\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Payment\Models\Payment;
use Modules\Wallet\Models\WalletTransaction;

class Booking extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'trainer_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'price',
        'notes',
        'refund_status',
        'refund_method',
        'refunded_amount',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Relationship: The member who booked the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship Alias: Athlete who booked the session.
     */
    public function athlete(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Relationship: The trainer being booked for the session.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * Relationship: The financial ledger transaction for this trainer session booking.
     */
    public function walletTransaction(): HasOne
    {
        return $this->hasOne(WalletTransaction::class);
    }

    /**
     * Relationship: The payment gateway receipt for this booking session.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}