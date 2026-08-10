<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\Booking;

class SessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;
    public string $timeWindow;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(Booking $booking, string $timeWindow = '24h')
    {
        $this->booking = $booking;
        $this->timeWindow = $timeWindow;

        $member = $booking->user;
        $trainer = $booking->trainer;
        Log::info("Notification Dispatched [SessionReminderNotification] -> Window: {$timeWindow} | Member: {$member?->email} | Trainer: {$trainer?->email} | Slot: {$booking->booking_date} {$booking->start_time}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة (داتابيز وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للتذكير
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isTrainer = ($notifiable->id === $this->booking->trainer_id);
        $otherPartyName = $isTrainer ? $this->booking->user?->name : $this->booking->trainer?->name;

        return (new MailMessage)
            ->subject("⏰ PT Session Reminder ({$this->timeWindow}) - FIT CLUB")
            ->greeting("Hello {$notifiable->name},")
            ->line("This is an automated reminder that your private PT session with {$otherPartyName} is coming up soon.")
            ->line("Session Date: {$this->booking->booking_date}")
            ->line("Session Time: {$this->booking->start_time} - {$this->booking->end_time}")
            ->action('View Booking Details', url('/bookings'))
            ->line('Thank you for choosing FIT CLUB Gym Platform!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس
     */
    public function toArray(object $notifiable): array
    {
        $isTrainer = ($notifiable->id === $this->booking->trainer_id);
        $otherPartyName = $isTrainer ? $this->booking->user?->name : $this->booking->trainer?->name;

        return [
            'title' => "⏰ PT Session Reminder ({$this->timeWindow})",
            'message' => "Reminder: Upcoming session with {$otherPartyName} on {$this->booking->booking_date} at {$this->booking->start_time}",
            'booking_id' => $this->booking->id,
            'booking_date' => $this->booking->booking_date,
            'start_time' => $this->booking->start_time,
            'time_window' => $this->timeWindow,
            'type' => 'session_reminder',
        ];
    }
}