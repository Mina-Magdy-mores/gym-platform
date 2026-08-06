<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Subscription\Models\Booking;

class WalletTransaction extends Model
{
    protected $fillable = [
        'trainer_wallet_id',
        'booking_id',
        'amount',
        'commission_amount',
        'net_amount',
        'type',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(TrainerWallet::class, 'trainer_wallet_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}