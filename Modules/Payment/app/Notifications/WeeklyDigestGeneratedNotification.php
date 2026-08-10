<?php

namespace Modules\Payment\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WeeklyDigestGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public float $weeklyRevenue;
    public int $newSubscriptionsCount;
    public int $ptSessionsCount;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(float $weeklyRevenue, int $newSubscriptionsCount, int $ptSessionsCount)
    {
        $this->weeklyRevenue = $weeklyRevenue;
        $this->newSubscriptionsCount = $newSubscriptionsCount;
        $this->ptSessionsCount = $ptSessionsCount;

        Log::info("Notification Dispatched [WeeklyDigestGeneratedNotification] -> Gross Revenue: EGP {$weeklyRevenue} | New Subs: {$newSubscriptionsCount} | PT Sessions: {$ptSessionsCount}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للأدمن (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني التنفيذي للأدمن
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📊 FIT CLUB - Weekly Executive Financial Digest')
            ->greeting("Hello Admin {$notifiable->name},")
            ->line('Here is your automated weekly financial & operational summary report:')
            ->line("💵 Weekly Gross Revenue: EGP " . number_format($this->weeklyRevenue, 2))
            ->line("🎟️ New Subscriptions Sold: {$this->newSubscriptionsCount}")
            ->line("🏋️ PT Sessions Booked: {$this->ptSessionsCount}")
            ->action('View Full Financial Ledger', url('/payments'))
            ->line('Keep scaling FIT CLUB Gym Platform to new heights!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس بـ لوحة الأدمن
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '📊 Weekly Executive Financial Report',
            'message' => "Gross Revenue: EGP " . number_format($this->weeklyRevenue, 2) . " | New Subs: {$this->newSubscriptionsCount} | PT Sessions: {$this->ptSessionsCount}",
            'weekly_revenue' => $this->weeklyRevenue,
            'new_subscriptions_count' => $this->newSubscriptionsCount,
            'pt_sessions_count' => $this->ptSessionsCount,
            'type' => 'weekly_admin_digest',
        ];
    }
}