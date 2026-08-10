<?php

namespace Modules\Subscription\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Subscription\Models\UserSubscription;
use Modules\Subscription\Notifications\QueuedSubscriptionActivatedNotification;

class ActivateQueuedSubscriptionsCommand extends Command
{
    /**
     * اسم الأمر الذي يُكتب بالتيرمينال
     */
    protected $signature = 'subscription:activate-queued';

    /**
     * وصف التيرمينال للأمر
     */
    protected $description = 'Automatically activate queued subscriptions whose start date has arrived.';

    /**
     * الدالة التنفيذية الرئيسية عند تشغيل الأمر
     */
    public function handle(): int
    {
        $this->info('Scanning database for queued subscriptions ready for activation...');

        $now = now()->toDateString();

        // 1. جلب الاشتراكات المؤجلة التي حان تاريخ بدايتها
        $queuedSubscriptions = UserSubscription::with(['user', 'plan'])
            ->where('status', 'queued')
            ->whereDate('starts_at', '<=', $now)
            ->get();

        if ($queuedSubscriptions->isEmpty()) {
            $this->info('No queued subscriptions require activation today.');
            return Command::SUCCESS;
        }

        $activatedCount = 0;

        foreach ($queuedSubscriptions as $subscription) {
            try {
                DB::transaction(function () use ($subscription, &$activatedCount) {
                    $user = $subscription->user;

                    // 2. تحويل أي اشتراك نشط قديم لنفس العضو إلى منتهي (expired)
                    UserSubscription::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->where('id', '!=', $subscription->id)
                        ->update(['status' => 'expired']);

                    // 3. تحويل حالة الاشتراك المؤجل الحالي إلى نشط (active)
                    $subscription->update([
                        'status' => 'active',
                    ]);

                    $activatedCount++;
                });

                // Dispatch real-time notification to member
                if ($subscription->user) {
                    Notification::send($subscription->user, new QueuedSubscriptionActivatedNotification($subscription));
                }

                Log::info("Queued Subscription ID {$subscription->id} activated successfully for User ID {$subscription->user_id}.");
            } catch (\Exception $e) {
                Log::error("Failed to activate queued subscription ID {$subscription->id}: " . $e->getMessage());
                $this->error("Error activating subscription ID {$subscription->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully activated {$activatedCount} queued subscription(s).");

        return Command::SUCCESS;
    }
}