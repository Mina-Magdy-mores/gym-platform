<?php

namespace Modules\Subscription\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Models\UserSubscription;
use Modules\Subscription\Notifications\LowBenefitsBalanceNotification;
use Modules\Subscription\Notifications\NewMemberSubscribedNotification;
use Modules\Subscription\Notifications\PlanUpgradedNotification;
use Modules\Subscription\Notifications\SessionReminderNotification;
use Modules\Subscription\Notifications\SubscriptionExpiringSoonNotification;

class TestCreateNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:create-notifications';

    /**
     * The console command description.
     */
    protected $description = 'Generate rich suite of test notifications for local UI inspection';

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

        $plan = SubscriptionPlan::first() ?? new SubscriptionPlan(['name' => '15 Month VIP Gold', 'price' => 4000]);
        $userSub = UserSubscription::first() ?? new UserSubscription(['user_id' => $user->id, 'subscription_plan_id' => 1, 'ends_at' => now()->addDays(3)]);
        $booking = Booking::first() ?? new Booking(['user_id' => $user->id, 'trainer_id' => $user->id, 'booking_date' => date('Y-m-d'), 'start_time' => '17:00:00']);

        $this->info("Creating rich test notifications suite for user: {$user->name} ({$user->email})...");

        Notification::send($user, new SessionReminderNotification($booking, '24h'));
        Notification::send($user, new SubscriptionExpiringSoonNotification($userSub, 3));
        Notification::send($user, new LowBenefitsBalanceNotification('PT Sessions', 1));
        Notification::send($user, new PlanUpgradedNotification($userSub, $plan, 500.00));
        Notification::send($user, new NewMemberSubscribedNotification($userSub));

        $this->info('5 test notifications pushed to database & queue successfully!');
        return Command::SUCCESS;
    }
}
