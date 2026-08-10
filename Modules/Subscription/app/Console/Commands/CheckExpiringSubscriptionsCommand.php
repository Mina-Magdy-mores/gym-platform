<?php

namespace Modules\Subscription\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\UserSubscription;
use Modules\Subscription\Notifications\SubscriptionExpiringSoonNotification;

class CheckExpiringSubscriptionsCommand extends Command
{
    /**
     * اسم الأمر في التيرمينال
     */
    protected $signature = 'subscription:check-expiring';

    /**
     * وصف الأمر للتيرمينال
     */
    protected $description = 'Scan database for subscriptions expiring in 3 days and dispatch warning notifications.';

    /**
     * الدالة التنفيذية لفحص الاشتراكات القريبة من الانتهاء
     */
    public function handle(): int
    {
        $this->info('Scanning database for subscriptions expiring in 3 days...');

        $targetExpiryDate = now()->addDays(3)->toDateString();

        // 1. جلب الاشتراكات النشطة التي تنتهي بعد 3 أيام من اليوم
        $expiringSubscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'active')
            ->whereDate('ends_at', $targetExpiryDate)
            ->get();

        if ($expiringSubscriptions->isEmpty()) {
            $this->info('No active subscriptions expiring in 3 days found today.');
            return Command::SUCCESS;
        }

        $dispatchedCount = 0;

        foreach ($expiringSubscriptions as $subscription) {
            if ($subscription->user) {
                // إرسال الإشعار اللحظي والإيميل للمشترك
                Notification::send($subscription->user, new SubscriptionExpiringSoonNotification($subscription));
                $dispatchedCount++;
            }
        }

        $this->info("Successfully dispatched {$dispatchedCount} expiring subscription warning notification(s).");

        return Command::SUCCESS;
    }
}
