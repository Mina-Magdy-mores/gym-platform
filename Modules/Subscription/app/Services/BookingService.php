<?php

namespace Modules\Subscription\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
        return Booking::with(['user', 'trainer', 'walletTransaction'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('trainer_id', $user->id);
            })
            ->latest()
            ->get();
    }

    /**
     * Book a private trainer session with concurrency pessimistic locking & auto-credit trainer wallet.
     */
    public function bookTrainerSession(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $trainerId = $data['trainer_id'];
            $bookingDate = $data['booking_date'];
            $startTime = $data['start_time'];
            $endTime = $data['end_time'];

            // Pessimistic Concurrency Lock: Prevents double-booking during overlapping intervals
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

            $booking = Booking::create([
                'user_id' => $user->id,
                'trainer_id' => $trainerId,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed',
                'price' => $data['price'] ?? 0.00,
                'notes' => $data['notes'] ?? null,
            ]);

            // Auto-credit Trainer Wallet with 15% platform commission deduction
            if ($booking->price > 0) {
                $this->walletService->creditTrainerForSession($booking);
            }

            return $booking;
        });
    }
}