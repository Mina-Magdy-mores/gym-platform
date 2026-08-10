<?php

namespace Modules\Subscription\Channels;

use Illuminate\Notifications\Notification;
use Modules\Subscription\Contracts\SmsGatewayInterface;

class SmsChannel
{
    protected SmsGatewayInterface $smsGateway;

    /**
     * Dependency Injection via SmsGatewayInterface
     */
    public function __construct(SmsGatewayInterface $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    /**
     * Send the given notification via SMS channel.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phoneNumber = $notifiable->phone ?? $notifiable->phone_number ?? '+201000000000';

        if ($message && $phoneNumber) {
            $this->smsGateway->sendSms($phoneNumber, $message);
        }
    }
}
