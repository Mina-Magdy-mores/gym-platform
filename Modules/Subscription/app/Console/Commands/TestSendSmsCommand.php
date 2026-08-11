<?php

namespace Modules\Subscription\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Channels\SmsChannel;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Notifications\SessionReminderNotification;

class TestSendSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:send-sms';

    /**
     * The console command description.
     */
    protected $description = 'Send live test SMS via SmsMisr adapter';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::first();

        if (! $user || ! $user->phone) {
            $this->error('No user with phone number found.');
            return Command::FAILURE;
        }

        $booking = Booking::first() ?? new Booking([
            'user_id' => $user->id,
            'trainer_id' => $user->id,
            'booking_date' => date('Y-m-d'),
            'start_time' => '17:00:00',
        ]);

        $this->info("Sending live test notification via SmsMisr to phone: {$user->phone}...");

        Notification::send($user, new SessionReminderNotification($booking, '24h'));

        $this->info('Notification pushed to queue & SmsChannel executed! Check storage/logs/laravel.log.');
        return Command::SUCCESS;
    }
}
