<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\UserSubscription;

class PlanUpgradedNotification extends Notification implements ShouldQueue
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
        Log::info("Notification Dispatched [PlanUpgradedNotification] -> Member: {$user?->email} | Upgraded Plan: {$plan?->name}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للترقية
     */
    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject('💎 Subscription Plan Upgraded! - FIT CLUB')
            ->greeting("Hello {$notifiable->name},")
            ->line("Congratulations! Your FIT CLUB subscription has been successfully UPGRADED to {$plan?->name}.")
            ->line("Your new upgraded benefits and features are now fully unlocked on your account.")
            ->action('Explore Upgraded Benefits', url('/dashboard'))
            ->line('Thank you for elevating your fitness journey at FIT CLUB!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس
     */
    public function toArray(object $notifiable): array
    {
        $plan = $this->subscription->plan;

        return [
            'title' => '💎 Plan Upgraded Successfully!',
            'message' => "Your subscription plan has been upgraded to {$plan?->name}. Enjoy your premium benefits!",
            'subscription_id' => $this->subscription->id,
            'plan_name' => $plan?->name,
            'type' => 'plan_upgraded',
        ];
    }
}