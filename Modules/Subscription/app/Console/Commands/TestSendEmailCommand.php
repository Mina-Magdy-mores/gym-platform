<?php

namespace Modules\Subscription\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Notifications\SessionReminderNotification;

class TestSendEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:send-email';

    /**
     * The console command description.
     */
    protected $description = 'Send test SessionReminderNotification to Mailtrap sandbox';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = User::first();

        if (! $user) {
            $this->error('No users found in database.');
            return Command::FAILURE;
        }

        $booking = Booking::first() ?? new Booking([
            'user_id' => $user->id,
            'trainer_id' => $user->id,
            'booking_date' => date('Y-m-d'),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
        ]);

        $this->info("Dispatching test SessionReminderNotification for user: {$user->email}...");
        Notification::send($user, new SessionReminderNotification($booking));

        $this->info('Notification pushed into jobs queue successfully!');
        return Command::SUCCESS;
    }
}
