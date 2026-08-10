<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\UserSubscription;

class QueuedSubscriptionActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public UserSubscription $subscription;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;

        $user = $subscription->user;
        $plan = $subscription->plan;
        Log::info("Notification Dispatched [QueuedSubscriptionActivatedNotification] -> Member: {$user?->email} | Activated Plan: {$plan?->name}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للمشترك
     */
    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject('🚀 Your Subscription is Now Active! - FIT CLUB')
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your queued subscription for {$plan?->name} is now officially ACTIVE!")
            ->line("Your plan benefits and sessions have been credited to your account balance.")
            ->action('View My Dashboard Benefits', url('/dashboard'))
            ->line('Enjoy your workout journey at FIT CLUB Gym!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس
     */
    public function toArray(object $notifiable): array
    {
        $plan = $this->subscription->plan;

        return [
            'title' => '🚀 Subscription Activated!',
            'message' => "Your subscription for {$plan?->name} is now ACTIVE! Enjoy your gym benefits.",
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan?->name,
            'type' => 'queued_subscription_activated',
        ];
    }
}