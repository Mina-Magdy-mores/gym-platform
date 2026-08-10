<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class LowBenefitsBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $benefitType;
    public int $remainingCount;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(string $benefitType = 'PT Sessions', int $remainingCount = 1)
    {
        $this->benefitType = $benefitType;
        $this->remainingCount = $remainingCount;

        Log::info("Notification Dispatched [LowBenefitsBalanceNotification] -> Benefit: {$benefitType} | Remaining: {$remainingCount}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للمشترك (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني لتنبيه رصيد المزايا
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("⚡ Low Benefits Alert: 1 {$this->benefitType} Remaining - FIT CLUB")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have only {$this->remainingCount} {$this->benefitType} remaining in your active subscription balance.")
            ->line("Make sure to book your session with your trainer before your balance runs out!")
            ->action('Book Session Now', url('/bookings'))
            ->line('Keep building your physique at FIT CLUB Gym Platform!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس بـ لوحة المشترك
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "⚡ Low Benefits Alert ({$this->benefitType})",
            'message' => "Only {$this->remainingCount} {$this->benefitType} remaining in your plan balance. Book now!",
            'benefit_type' => $this->benefitType,
            'remaining_count' => $this->remainingCount,
            'type' => 'low_benefits_balance',
        ];
    }
}