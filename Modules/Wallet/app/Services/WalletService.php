<?php

namespace Modules\Wallet\Services;

use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Booking;
use Modules\Wallet\Models\TrainerWallet;
use Modules\Wallet\Models\WalletTransaction;

class WalletService
{
    /**
     * Platform commission percentage (15%).
     */
    protected float $commissionRate = 0.15;

    /**
     * Auto-credit trainer wallet for a booked session with 15% platform commission deduction.
     */
    public function creditTrainerForSession(Booking $booking): WalletTransaction
    {
        return DB::transaction(function () use ($booking) {
            $grossAmount = (float) $booking->price;
            $commissionAmount = round($grossAmount * $this->commissionRate, 2);
            $netAmount = round($grossAmount - $commissionAmount, 2);

            // 1. Find or Create Trainer Wallet
            $wallet = TrainerWallet::firstOrCreate(
                ['user_id' => $booking->trainer_id],
                ['balance' => 0.00, 'total_earned' => 0.00]
            );

            // 2. Increment Wallet Balance & Total Earned
            $wallet->increment('balance', $netAmount);
            $wallet->increment('total_earned', $netAmount);

            // 3. Create Ledger Transaction Record
            return WalletTransaction::create([
                'trainer_wallet_id' => $wallet->id,
                'booking_id' => $booking->id,
                'amount' => $grossAmount,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'type' => 'session_credit',
                'status' => 'completed',
                'notes' => 'Session credit after 15% gym commission deduction for booking #' . $booking->id,
            ]);
        });
    }

    /**
     * Get trainer wallet and recent ledger transactions history.
     */
    public function getTrainerWalletData(int $trainerUserId): array
    {
        $wallet = TrainerWallet::with(['transactions' => function ($query) {
            $query->latest()->take(20);
        }])->firstOrCreate(
            ['user_id' => $trainerUserId],
            ['balance' => 0.00, 'total_earned' => 0.00]
        );

        return [
            'wallet' => $wallet,
            'transactions' => $wallet->transactions,
        ];
    }
}