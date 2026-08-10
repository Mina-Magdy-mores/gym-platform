<?php

namespace Modules\Subscription\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Notifications\SessionReminderNotification;

class SendBookingRemindersCommand extends Command
{
    /**
     * اسم الأمر في التيرمينال
     */
    protected $signature = 'subscription:send-booking-reminders';

    /**
     * وصف الأمر
     */
    protected $description = 'Send automated booking reminders to members and trainers 24h and 2h before session.';

    /**
     * الدالة التنفيذية للأمر
     */
    public function handle(): int
    {
        $this->info('Scanning upcoming confirmed bookings for reminder notifications...');

        $now = now();
        $tomorrow = $now->copy()->addDay()->toDateString();
        $today = $now->toDateString();

        // 1. جلب الحجوزات القادمة خلال الغد واليوم
        $upcomingBookings = Booking::with(['user', 'trainer'])
            ->where('status', 'confirmed')
            ->whereIn('booking_date', [$today, $tomorrow])
            ->get();

        if ($upcomingBookings->isEmpty()) {
            $this->info('No upcoming confirmed bookings require reminders right now.');
            return Command::SUCCESS;
        }

        $remindersCount = 0;

        foreach ($upcomingBookings as $booking) {
            $member = $booking->user;
            $trainer = $booking->trainer;

            // Dispatch real-time reminder notification to both Member and Trainer
            if ($member) {
                Notification::send($member, new SessionReminderNotification($booking, '24h'));
            }
            if ($trainer) {
                Notification::send($trainer, new SessionReminderNotification($booking, '24h'));
            }

            $remindersCount++;
        }

        $this->info("Processed {$remindersCount} booking reminder notification(s) successfully.");

        return Command::SUCCESS;
    }
}