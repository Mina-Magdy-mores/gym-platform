<?php

namespace Modules\Wallet\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\Booking;
use Modules\Wallet\Models\PayoutRequest;
use Modules\Wallet\Models\TrainerWallet;
use Modules\Wallet\Models\WalletTransaction;
use Modules\Wallet\Notifications\PayoutApprovedNotification;
use Modules\Wallet\Notifications\TrainerPayoutRequestedNotification;

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
            $trainer = User::find($booking->trainer_id);
            $sessionRate = (float) ($trainer->session_rate ?? 200.00);
            $grossAmount = (float) ($booking->price > 0 ? $booking->price : $sessionRate);
            $commissionAmount = round($grossAmount * $this->commissionRate, 2);
            $netAmount = round($grossAmount - $commissionAmount, 2);

            // 1. Find or Create Trainer Wallet
            $wallet = TrainerWallet::firstOrCreate(
                ['user_id' => $booking->trainer_id],
                ['balance' => 0.00, 'total_earned' => 0.00, 'pending_payout' => 0.00]
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
            ['balance' => 0.00, 'total_earned' => 0.00, 'pending_payout' => 0.00]
        );

        $payoutRequests = PayoutRequest::where('user_id', $trainerUserId)->latest()->get();

        return [
            'wallet' => $wallet,
            'transactions' => $wallet->transactions,
            'payout_requests' => $payoutRequests,
        ];
    }

    /**
     * Submit trainer payout request with pessimistic lock & freeze requested balance into pending_payout.
     */
    public function requestTrainerPayout(User $trainer, float $amount, string $paymentMethod, string $accountDetails): PayoutRequest
    {
        if ($amount <= 0) {
            throw new \Exception('Payout amount must be greater than zero.');
        }

        $payoutRequest = DB::transaction(function () use ($trainer, $amount, $paymentMethod, $accountDetails) {
            $wallet = TrainerWallet::where('user_id', $trainer->id)
                ->lockForUpdate()
                ->first();

            if (! $wallet || (float) $wallet->balance < $amount) {
                throw new \Exception('Insufficient wallet balance to request this payout.');
            }

            // Freeze requested balance from available balance to pending_payout
            $wallet->decrement('balance', $amount);
            $wallet->increment('pending_payout', $amount);

            return PayoutRequest::create([
                'user_id' => $trainer->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'account_details' => $accountDetails,
                'status' => 'pending',
                'notes' => 'Payout requested via ' . strtoupper($paymentMethod),
            ]);
        });

        // Dispatch real-time notification to all platform Admins
        $admins = User::role('admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new TrainerPayoutRequestedNotification($payoutRequest));
        }

        return $payoutRequest;
    }

    /**
     * Admin Approve Payout Request: Settle pending_payout balance & record ledger payout transaction with pessimistic lock.
     */
    public function approvePayoutRequest(PayoutRequest $payoutRequest): PayoutRequest
    {
        return DB::transaction(function () use ($payoutRequest) {
            $lockedRequest = PayoutRequest::where('id', $payoutRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== 'pending') {
                throw new \Exception('This payout request is already processed.');
            }

            $wallet = TrainerWallet::where('user_id', $lockedRequest->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Settle frozen pending payout
            $wallet->decrement('pending_payout', $lockedRequest->amount);

            // Record Payout Ledger Transaction
            WalletTransaction::create([
                'trainer_wallet_id' => $wallet->id,
                'booking_id' => null,
                'amount' => $lockedRequest->amount,
                'commission_amount' => 0.00,
                'net_amount' => $lockedRequest->amount,
                'type' => 'payout',
                'status' => 'completed',
                'notes' => 'Payout disbursed via ' . strtoupper($lockedRequest->payment_method) . ' to account: ' . $lockedRequest->account_details,
            ]);

            $lockedRequest->update(['status' => 'approved']);

            // Dispatch real-time notification to trainer
            $trainer = User::find($lockedRequest->user_id);
            if ($trainer) {
                Notification::send($trainer, new PayoutApprovedNotification($lockedRequest));
            }

            return $lockedRequest;
        });
    }

    /**
     * Admin Reject Payout Request: Unfreeze pending_payout & restore balance back to trainer wallet.
     */
    public function rejectPayoutRequest(PayoutRequest $payoutRequest, ?string $reason = null): PayoutRequest
    {
        if ($payoutRequest->status !== 'pending') {
            throw new \Exception('This payout request is already processed.');
        }

        $wallet = TrainerWallet::where('user_id', $payoutRequest->user_id)->firstOrFail();

        return DB::transaction(function () use ($payoutRequest, $wallet, $reason) {
            // Unfreeze pending payout & restore back to available balance
            $wallet->decrement('pending_payout', $payoutRequest->amount);
            $wallet->increment('balance', $payoutRequest->amount);

            $payoutRequest->update([
                'status' => 'rejected',
                'notes' => $reason ?? 'Payout request rejected by admin.',
            ]);

            return $payoutRequest;
        });
    }

    /**
     * Get all payout requests for Admin panel listing.
     */
    public function getAllPayoutRequests(): Collection
    {
        return PayoutRequest::with('user')->latest()->get();
    }
}