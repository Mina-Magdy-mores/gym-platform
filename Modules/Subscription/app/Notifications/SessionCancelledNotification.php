<?php

namespace Modules\Subscription\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Subscription\Models\Booking;
use Modules\Subscription\Channels\SmsChannel;

class SessionCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sessionDate = $this->booking->booking_date;
        $startTime = $this->booking->start_time;
        $refundStatus = $this->booking->refund_status;

        $refundText = $refundStatus === 'refunded'
            ? 'Your PT session pass has been automatically restored back to your subscription balance.'
            : ($refundStatus === 'pending' 
                ? 'Your paid session refund of EGP ' . number_format($this->booking->refunded_amount, 2) . ' is being processed.'
                : 'Session cancelled within 24 hours of start time.');

        return (new MailMessage)
            ->subject('Session Cancelled - Fit Club')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The personal training session scheduled for ' . $sessionDate . ' at ' . $startTime . ' has been cancelled.')
            ->line($refundText)
            ->action('View My Bookings', route('bookings.index'))
            ->line('Thank you for using Fit Club Platform!');
    }

    /**
     * Get the SMS text representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $refundInfo = $this->booking->refund_status === 'refunded'
            ? 'Session pass restored.'
            : ($this->booking->refund_status === 'pending'
                ? 'Refund of EGP ' . number_format($this->booking->refunded_amount, 0) . ' pending.'
                : 'Cancelled within 24h.');

        return "Fit Club: Your PT session on {$this->booking->booking_date} at {$this->booking->start_time} has been cancelled. {$refundInfo}";
    }

    /**
     * Get the array representation of the notification for database & UI.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'session_cancelled',
            'booking_id' => $this->booking->id,
            'title' => 'Session Cancelled',
            'message' => 'Your PT session on ' . $this->booking->booking_date . ' at ' . $this->booking->start_time . ' has been cancelled.',
            'refund_status' => $this->booking->refund_status,
            'refunded_amount' => $this->booking->refunded_amount,
        ];
    }
}
