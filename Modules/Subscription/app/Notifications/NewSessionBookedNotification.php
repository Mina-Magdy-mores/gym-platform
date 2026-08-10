<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Models\Booking;

class NewSessionBookedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        $member = $booking->user;
        $trainer = $booking->trainer;
        Log::info("Notification Dispatched [NewSessionBookedNotification] -> Trainer: {$trainer?->email} | Member: {$member?->email} | Slot: {$booking->booking_date} {$booking->start_time}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للمدرب (داتابيز للـ Bell Icon وإيميل)
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * قوالب رسالة البريد الإلكتروني للمدرب
     */
    public function toMail(object $notifiable): MailMessage
    {
        $member = $this->booking->user;

        return (new MailMessage)
            ->subject('🏋️ New Session Booked - Captain ' . $notifiable->name)
            ->greeting("Hello Coach {$notifiable->name},")
            ->line("Member {$member?->name} has booked a private PT session with you.")
            ->line("Date: {$this->booking->booking_date}")
            ->line("Time: {$this->booking->start_time} - {$this->booking->end_time}")
            ->line("Notes: " . ($this->booking->notes ?? 'None provided'))
            ->action('View My Schedule Bookings', url('/bookings'))
            ->line('Please be ready on time at the gym floor!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس بـ لوحة الكابتن
     */
    public function toArray(object $notifiable): array
    {
        $member = $this->booking->user;

        return [
            'title' => '🏋️ New PT Session Booked',
            'message' => "{$member?->name} booked a session with you on {$this->booking->booking_date} at {$this->booking->start_time}",
            'booking_id' => $this->booking->id,
            'member_id' => $member?->id,
            'booking_date' => $this->booking->booking_date,
            'start_time' => $this->booking->start_time,
            'type' => 'new_session_booked',
        ];
    }
}