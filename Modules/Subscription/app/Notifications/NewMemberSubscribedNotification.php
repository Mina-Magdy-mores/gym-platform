<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\UserSubscription;

class NewMemberSubscribedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public UserSubscription $subscription;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;

        // تسجيل الـ Log فور إنشاء الإشعار
        $user = $subscription->user;
        $plan = $subscription->plan;
        Log::info("Notification Dispatched [NewMemberSubscribedNotification] -> Member: {$user?->email} | Plan: {$plan?->name} | Amount: EGP {$subscription->price_paid}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة (داتابيز للـ Bell Icon وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للأدمن
     */
    public function toMail(object $notifiable): MailMessage
    {
        $user = $this->subscription->user;
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject('🎉 New Member Subscription - ' . $plan?->name)
            ->greeting('Hello Admin,')
            ->line("Member {$user?->name} ({$user?->email}) has successfully subscribed to {$plan?->name}.")
            ->line("Amount Paid: EGP " . number_format($this->subscription->price_paid, 2))
            ->line("Start Date: " . $this->subscription->starts_at?->format('Y-m-d'))
            ->action('View Payments Ledger', url('/payments'))
            ->line('Thank you for managing FIT CLUB Gym Platform!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس
     */
    public function toArray(object $notifiable): array
    {
        $user = $this->subscription->user;
        $plan = $this->subscription->plan;

        return [
            'title' => '🎉 New Member Subscription',
            'message' => "{$user?->name} subscribed to {$plan?->name} for EGP " . number_format($this->subscription->price_paid, 2),
            'subscription_id' => $this->subscription->id,
            'user_id' => $user?->id,
            'plan_name' => $plan?->name,
            'amount' => $this->subscription->price_paid,
            'type' => 'new_subscription',
        ];
    }
}