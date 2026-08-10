<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\UserSubscription;

class SubscriptionExpiringSoonNotification extends Notification implements ShouldQueue
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
        Log::info("Notification Dispatched [SubscriptionExpiringSoonNotification] -> Member: {$user?->email} | Plan: {$plan?->name} | Expiry Date: {$subscription->ends_at?->toDateString()}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للمشترك (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للتذكير بالتجديد
     */
    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject('⚠️ Your Subscription Expires in 3 Days - FIT CLUB')
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a friendly reminder that your current subscription for {$plan?->name} will expire on {$this->subscription->ends_at?->format('Y-m-d')}.")
            ->line("Don't lose your gym benefits, trainer sessions, and workout routine continuity!")
            ->action('Renew or Queue Next Subscription', url('/plans'))
            ->line('Keep pushing your limits at FIT CLUB Gym Platform!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس
     */
    public function toArray(object $notifiable): array
    {
        $plan = $this->subscription->plan;

        return [
            'title' => '⚠️ Subscription Expiring Soon',
            'message' => "Your subscription for {$plan?->name} expires in 3 days on {$this->subscription->ends_at?->toDateString()}. Renew now!",
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan?->name,
            'ends_at' => $this->subscription->ends_at?->toDateString(),
            'type' => 'subscription_expiring_soon',
        ];
    }
}