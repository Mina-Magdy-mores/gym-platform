<?php

namespace Modules\Wallet\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainerWalletResource extends JsonResource
{
    /**
     * Transform the trainer wallet resource into an array.
     */
    public function toArray(Request $request): array
    {
        $wallet = $this['wallet'];
        $transactions = $this['transactions'];

        return [
            'wallet' => [
                'id' => $wallet->id,
                'trainer_id' => $wallet->user_id,
                'balance' => (float) $wallet->balance,
                'total_earned' => (float) $wallet->total_earned,
                'currency' => 'EGP',
            ],
            'recent_transactions' => $transactions->map(function ($txn) {
                return [
                    'id' => $txn->id,
                    'booking_id' => $txn->booking_id,
                    'gross_amount' => (float) $txn->amount,
                    'gym_commission_15pct' => (float) $txn->commission_amount,
                    'net_trainer_credit' => (float) $txn->net_amount,
                    'type' => $txn->type,
                    'status' => $txn->status,
                    'notes' => $txn->notes,
                    'created_at' => $txn->created_at?->toIso8601String(),
                ];
            }),
        ];
    }
}
