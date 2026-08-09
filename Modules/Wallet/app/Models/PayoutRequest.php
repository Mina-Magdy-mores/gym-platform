<?php

namespace Modules\Wallet\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutRequest extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'account_details',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: The trainer who submitted this payout request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}