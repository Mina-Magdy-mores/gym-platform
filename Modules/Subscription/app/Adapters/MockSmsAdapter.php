<?php

namespace Modules\Subscription\Adapters;

use Illuminate\Support\Facades\Log;
use Modules\Subscription\Contracts\SmsGatewayInterface;

class MockSmsAdapter implements SmsGatewayInterface
{
    /**
     * Log SMS message in development environment.
     */
    public function sendSms(string $phoneNumber, string $message): bool
    {
        Log::info("📲 SMS DISPATCHED [MockSmsAdapter] -> To: {$phoneNumber} | Message: \"{$message}\"");
        return true;
    }
}
