<?php

namespace Modules\Wallet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Models\PayoutRequest;

class PayoutApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public PayoutRequest $payoutRequest;

    /**
     * Create a new notification instance and log execution.
     */
    public function __construct(PayoutRequest $payoutRequest)
    {
        $this->payoutRequest = $payoutRequest;

        $trainer = $payoutRequest->trainer;
        Log::info("Notification Dispatched [PayoutApprovedNotification] -> Trainer: {$trainer?->email} | Approved Amount: EGP {$payoutRequest->amount}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للمدرب (داتابيز وإيميل)
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
        return (new MailMessage)
            ->subject('🎉 Wallet Payout Approved & Transferred - FIT CLUB')
            ->greeting("Hello Captain {$notifiable->name},")
            ->line("Great news! Your wallet payout request has been APPROVED and transferred by admin.")
            ->line("Amount Transferred: EGP " . number_format($this->payoutRequest->amount, 2))
            ->line("Payment Method: " . strtoupper($this->payoutRequest->payment_method))
            ->action('Download Settlement Voucher PDF', url("/payouts/{$this->payoutRequest->id}/download"))
            ->line('Thank you for your dedication as a certified trainer at FIT CLUB!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس بـ لوحة المدرب
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '🎉 Wallet Payout Approved!',
            'message' => "Your payout request of EGP " . number_format($this->payoutRequest->amount, 2) . " has been approved and transferred successfully.",
            'payout_request_id' => $this->payoutRequest->id,
            'amount' => $this->payoutRequest->amount,
            'type' => 'payout_approved',
        ];
    }
}