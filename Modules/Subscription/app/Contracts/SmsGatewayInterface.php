<?php

namespace Modules\Subscription\Contracts;

interface SmsGatewayInterface
{
    /**
     * Send SMS notification message to target phone number.
     */
    public function sendSms(string $phoneNumber, string $message): bool;
}
