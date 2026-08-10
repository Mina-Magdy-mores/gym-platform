<?php

namespace Modules\Payment\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Payment\Models\Payment;
use Modules\Payment\Notifications\WeeklyDigestGeneratedNotification;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Models\UserSubscription;

class SendWeeklyAdminDigestCommand extends Command
{
    /**
     * اسم الأمر في التيرمينال
     */
    protected $signature = 'admin:send-weekly-digest';

    /**
     * وصف الأمر للتيرمينال
     */
    protected $description = 'Generate and dispatch weekly financial & operational executive digest for gym admins.';

    /**
     * الدالة التنفيذية لحساب الأرقام والتقرير الأسبوعي
     */
    public function handle(): int
    {
        $this->info('Calculating weekly financial & operational stats for admin digest...');

        $startDate = now()->subDays(7)->startOfDay();
        $endDate = now()->endOfDay();

        // 1. حساب إجمالي الإيرادات المالية لآخر 7 أيام
        $weeklyRevenue = Payment::whereIn('status', ['completed', 'paid'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // 2. حساب عدد الاشتراكات المباعة خلال الأسبوع
        $newSubscriptionsCount = UserSubscription::whereBetween('created_at', [$startDate, $endDate])->count();

        // 3. حساب عدد جلسات التدريب الشخصي المحجوزة
        $ptSessionsCount = Booking::whereBetween('created_at', [$startDate, $endDate])->count();

        $summaryText = "FIT CLUB Weekly Executive Summary [{$startDate->toDateString()} to {$endDate->toDateString()}]: "
            . "Gross Revenue: EGP " . number_format($weeklyRevenue, 2) . " | "
            . "New Subscriptions: {$newSubscriptionsCount} | "
            . "PT Sessions Booked: {$ptSessionsCount}";

        // Dispatch real-time notification to all platform Admins
        $admins = User::role('Admin')->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new WeeklyDigestGeneratedNotification($weeklyRevenue, $newSubscriptionsCount, $ptSessionsCount));
        }

        // توثيق التقرير بسجلات الـ Logs المعتمدة
        Log::info($summaryText);

        $this->info('--------------------------------------------------');
        $this->info('📊 FIT CLUB WEEKLY ADMIN DIGEST REPORT');
        $this->info('--------------------------------------------------');
        $this->info("💵 Weekly Gross Revenue: EGP " . number_format($weeklyRevenue, 2));
        $this->info("🎟️  New Subscriptions: {$newSubscriptionsCount}");
        $this->info("🏋️  PT Sessions Booked: {$ptSessionsCount}");
        $this->info('--------------------------------------------------');
        $this->info('Weekly digest generated and logged successfully.');

        return Command::SUCCESS;
    }
}