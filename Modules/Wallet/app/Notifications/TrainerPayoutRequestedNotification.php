<?php

namespace Modules\Wallet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Models\PayoutRequest;

class TrainerPayoutRequestedNotification extends Notification implements ShouldQueue
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
        Log::info("Notification Dispatched [TrainerPayoutRequestedNotification] -> Trainer: {$trainer?->email} | Amount: EGP {$payoutRequest->amount} | Method: {$payoutRequest->payment_method}");
    }

    /**
     * تحديد قنوات الإرسال المزدوجة للأدمن (داتابيز وإيميل)
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
        $trainer = $this->payoutRequest->trainer;

        return (new MailMessage)
            ->subject('💸 New Trainer Payout Withdrawal Request - Captain ' . $trainer?->name)
            ->greeting("Hello Admin {$notifiable->name},")
            ->line("Captain {$trainer?->name} has requested a wallet payout withdrawal.")
            ->line("Amount Requested: EGP " . number_format($this->payoutRequest->amount, 2))
            ->line("Payment Method: " . strtoupper($this->payoutRequest->payment_method))
            ->line("Account / Phone: {$this->payoutRequest->account_details}")
            ->action('Review & Approve Payout', url('/admin/payouts'))
            ->line('Thank you for managing FIT CLUB Gym Platform!');
    }

    /**
     * بيانات الإشعار اللحظي للتخزين بالداتابيز لعرضها بأيقونة الجرس بـ لوحة الأدمن
     */
    public function toArray(object $notifiable): array
    {
        $trainer = $this->payoutRequest->trainer;

        return [
            'title' => '💸 Trainer Payout Requested',
            'message' => "Captain {$trainer?->name} requested payout of EGP " . number_format($this->payoutRequest->amount, 2) . " via {$this->payoutRequest->payment_method}",
            'payout_request_id' => $this->payoutRequest->id,
            'trainer_id' => $trainer?->id,
            'amount' => $this->payoutRequest->amount,
            'payment_method' => $this->payoutRequest->payment_method,
            'type' => 'trainer_payout_requested',
        ];
    }
}