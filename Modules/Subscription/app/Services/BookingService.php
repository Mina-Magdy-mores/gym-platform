<?php

namespace Modules\Subscription\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Notifications\NewSessionBookedNotification;
use Modules\Subscription\Notifications\LowBenefitsBalanceNotification;
use Modules\Subscription\Notifications\SessionCancelledNotification;
use Modules\Payment\Models\Payment;
use Modules\Payment\Services\PaymentService;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\UserSubscription;
use Modules\Wallet\Services\WalletService;

class BookingService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get all active certified trainers via service layer.
     */
    public function getAllTrainers(): Collection
    {
        return User::role('trainer')->get();
    }

    /**
     * Get bookings for a specific user (member or trainer) with Eager Loading.
     */
    public function getUserBookings(User $user): Collection
    {
        return Booking::with(['user', 'trainer', 'walletTransaction', 'payment'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('trainer_id', $user->id);
            })
            ->latest()
            ->get();
    }

    /**
     * Book a private trainer session with concurrency pessimistic locking, benefit deduction & auto-credit trainer wallet.
     */
    public function bookTrainerSession(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            if (!$user->is_active) {
                throw new \Exception('Your account is currently frozen/inactive. Please contact gym administration to activate your account.');
            }

            $trainerId = $data['trainer_id'];
            $bookingDate = $data['booking_date'];
            $startTime = $data['start_time'];
            $endTime = $data['end_time'];

            // 1. Pessimistic Concurrency Lock: Prevents double-booking during overlapping intervals
            $overlappingBookingExists = Booking::where('trainer_id', $trainerId)
                ->where('booking_date', $bookingDate)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                })
                ->lockForUpdate()
                ->exists();

            if ($overlappingBookingExists) {
                throw new \Exception('The selected trainer is already booked for this specific time slot.');
            }

            $trainer = User::findOrFail($trainerId);
            $sessionPrice = (float) ($trainer->session_rate ?? $data['price'] ?? 200.00);
            $activeSub = $user->activeSubscription;

            // CASE 1: Member has active subscription with remaining PT sessions -> Deduct 1 session & Confirm Immediately
            if ($activeSub && $activeSub->remaining_pt_sessions > 0) {
                $activeSub->decrement('remaining_pt_sessions');

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'trainer_id' => $trainerId,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'confirmed',
                    'price' => 0.00,
                    'notes' => $data['notes'] ?? 'Booked from active subscription PT balance.',
                ]);

                // Auto-credit Trainer Wallet with 85% payout and 15% platform commission
                $this->walletService->creditTrainerForSession($booking);

                // Dispatch real-time notification to trainer
                if ($trainer) {
                    Notification::send($trainer, new NewSessionBookedNotification($booking));
                }

                // Dispatch low benefits warning notification to member if 1 PT session remains
                $freshSub = $activeSub->fresh();
                if ($freshSub && $freshSub->remaining_pt_sessions === 1) {
                    Notification::send($user, new LowBenefitsBalanceNotification('PT Sessions', 1));
                }

                return $booking;
            }

            // CASE 2: Member has 0 PT sessions -> Fallback throw exception or return response
            throw new \Exception('OUT_OF_POCKET_PAYMENT_REQUIRED');
        });
    }

    /**
     * Confirm and record PT session booking after successful out-of-pocket payment gateway execution.
     */
    public function confirmPTSessionAfterPayment(User $user, User $trainer, array $bookingData): Booking
    {
        return DB::transaction(function () use ($user, $trainer, $bookingData) {
            $sessionPrice = (float) ($trainer->session_rate ?? $bookingData['price'] ?? 200.00);

            $booking = Booking::create([
                'user_id' => $user->id,
                'trainer_id' => $trainer->id,
                'booking_date' => $bookingData['booking_date'],
                'start_time' => $bookingData['start_time'],
                'end_time' => $bookingData['end_time'],
                'status' => 'confirmed',
                'price' => $sessionPrice,
                'notes' => $bookingData['notes'] ?? 'Paid out-of-pocket via payment gateway.',
            ]);

            // Credit 85% to Trainer Wallet and 15% platform commission
            $this->walletService->creditTrainerForSession($booking);

            // Dispatch real-time notification to trainer
            if ($trainer) {
                Notification::send($trainer, new NewSessionBookedNotification($booking));
            }

            return $booking;
        });
    }
    /**
     * Cancel a booking session and execute refund / session recovery logic.
     */
    public function cancelBooking(Booking $booking, ?string $refundMethod = null): array
    {
        if ($booking->status === 'cancelled') {
            return ['status' => false, 'message' => 'Booking is already cancelled.'];
        }

        $sessionDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->start_time);
        $isEligibleForFullRefund = now()->diffInHours($sessionDateTime, false) >= 24;

        DB::transaction(function () use ($booking, $isEligibleForFullRefund, $refundMethod) {
            // 1. Mark booking as cancelled
            $booking->status = 'cancelled';

            if ($isEligibleForFullRefund) {
                // Scenario A: Free subscription session -> Increment remaining sessions
                if ($booking->price == 0) {
                    $activeSub = UserSubscription::where('user_id', $booking->user_id)
                        ->where('status', 'active')
                        ->first();

                    if ($activeSub) {
                        $activeSub->increment('remaining_pt_sessions', 1);
                    }
                    $booking->refund_status = 'refunded';
                    $booking->refund_method = 'session_restored';
                    $booking->refunded_amount = 0;
                    $booking->refunded_at = now();
                } else {
                    // Scenario B: Paid booking -> Mark as refund_pending or execute refund
                    $booking->refund_status = 'pending';
                    $booking->refund_method = $ref0undMethod ?? 'instapay';
                    $booking->refunded_amount = $booking->price;
                }

                // Rollback trainer wallet transaction & balance if existed
                if ($booking->walletTransaction) {
                    $txn = $booking->walletTransaction;
                    if ($txn->status === 'completed') {
                        $wallet = $txn->wallet;
                        if ($wallet) {
                            $wallet->decrement('balance', $txn->net_amount);
                            $wallet->decrement('total_earned', $txn->net_amount);
                        }
                        $txn->update(['status' => 'cancelled']);
                    }
                }
            } else {
                // Cancelled within 24h -> No refund granted to protect trainer time
                $booking->refund_status = 'none';
            }

            $booking->save();

            // Dispatch SessionCancelledNotification to member and trainer
            if ($booking->user) {
                Notification::send($booking->user, new SessionCancelledNotification($booking));
            }
            if ($booking->trainer) {
                Notification::send($booking->trainer, new SessionCancelledNotification($booking));
            }
        });

        return [
            'status' => true,
            'is_eligible' => $isEligibleForFullRefund,
            'message' => $isEligibleForFullRefund 
                ? 'Booking cancelled successfully and refund/benefit recovery processed.' 
                : 'Booking cancelled, but session is non-refundable as it was cancelled within 24 hours of session time.'
        ];
    }

    /**
     * Mark a booking session as completed (Trainer/Admin action).
     */
    public function completeBooking(Booking $booking): array
    {
        if ($booking->status === 'completed') {
            return ['status' => false, 'message' => 'Booking is already marked as completed.'];
        }

        if ($booking->status === 'cancelled') {
            return ['status' => false, 'message' => 'Cannot complete a cancelled session.'];
        }

        $booking->update(['status' => 'completed']);

        return [
            'status' => true,
            'message' => 'Session marked as completed successfully.'
        ];
    }

    /**
     * Get all platform session bookings with eager loading for Master Admin Control Panel.
     */
    public function getAllBookingsForAdmin(): Collection
    {
        return Booking::with(['user.activeSubscription.plan', 'trainer', 'walletTransaction', 'payment'])
            ->latest()
            ->get();
    }

    /**
     * Process and resolve refund for a booking session (Admin action).
     * Automatically resolves gateway adapter (Paymob vs Stripe) or manual methods (InstaPay/Vodafone/Cash).
     */
    public function processBookingRefund(Booking $booking, string $refundMethod = 'auto_gateway', ?string $adminNotes = null): array
    {
        if ($booking->refund_status === 'refunded') {
            return ['status' => false, 'message' => 'Booking refund has already been resolved and processed.'];
        }

        $payment = $booking->payment;
        $gatewayName = strtolower($payment->gateway ?? 'paymob');
        $resolvedGateway = $refundMethod;

        if ($refundMethod === 'auto_gateway') {
            $resolvedGateway = 'auto_gateway (' . strtoupper($gatewayName) . ')';
            $txnId = $payment->transaction_id ?? null;
            $refundAmount = $booking->price > 0 ? $booking->price : ($payment->amount ?? 0);

            if ($txnId && $refundAmount > 0) {
                $adapter = app(PaymentService::class)->getGatewayAdapter($gatewayName);

                Log::info("Executing Live Gateway API Refund via [{$gatewayName}] for TXN #{$txnId}, Amount: EGP {$refundAmount}");
                $gatewayResponse = $adapter->refund($txnId, $refundAmount);

                if (! $gatewayResponse->isSuccessful && app()->environment('production')) {
                    throw new \Exception("Gateway Auto Refund API Failed: " . $gatewayResponse->message);
                }
            }
        }

        DB::transaction(function () use ($booking, $payment, $resolvedGateway, $adminNotes) {
            $refundAmount = $booking->price > 0 ? $booking->price : ($payment->amount ?? 0);

            $booking->update([
                'refund_status' => 'refunded',
                'refund_method' => $resolvedGateway,
                'refunded_amount' => $refundAmount,
                'refunded_at' => now(),
            ]);

            // Create negative ledger refund record in payments table for 100% accounting precision
            if ($refundAmount > 0) {
                Payment::create([
                    'user_id' => $booking->user_id,
                    'subscription_plan_id' => null,
                    'booking_id' => $booking->id,
                    'transaction_id' => 'REFUND-BK-' . $booking->id . '-' . time(),
                    'gateway' => $resolvedGateway,
                    'amount' => -abs($refundAmount),
                    'currency' => $payment->currency ?? 'EGP',
                    'status' => 'completed',
                    'payload' => [
                        'type' => 'refund',
                        'refund_method' => $resolvedGateway,
                        'notes' => $adminNotes ?? 'Admin resolved booking refund',
                        'processed_at' => now()->toIso8601String(),
                    ],
                ]);
            }
        });

        return [
            'status' => true,
            'message' => "Refund resolved and recorded successfully via [{$resolvedGateway}]."
        ];
    }
}