<?php

namespace Modules\Subscription\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Notifications\NewSessionBookedNotification;
use Modules\Subscription\Notifications\LowBenefitsBalanceNotification;
use Modules\Subscription\Models\Booking;
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
                    'price' => $sessionPrice,
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
}