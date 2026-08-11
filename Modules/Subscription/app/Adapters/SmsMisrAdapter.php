<?php

namespace Modules\Subscription\Adapters;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\Contracts\SmsGatewayInterface;

class SmsMisrAdapter implements SmsGatewayInterface
{
    protected string $environment;
    protected string $username;
    protected string $password;
    protected string $senderToken;

    public function __construct()
    {
        $this->environment = config('services.smsmisr.environment', env('SMSMISR_ENV', '1'));
        $this->username = config('services.smsmisr.username', env('SMSMISR_USERNAME', ''));
        $this->password = config('services.smsmisr.password', env('SMSMISR_PASSWORD', ''));
        $this->senderToken = config('services.smsmisr.sender_token', env('SMSMISR_SENDER_TOKEN', ''));
    }

    /**
     * Send SMS via SmsMisr API v2 endpoint.
     */
    public function sendSms(string $phoneNumber, string $message): bool
    {
        if (empty($this->username) || empty($this->password)) {
            Log::warning("📲 SmsMisr credentials missing. Falling back to log.");
            return false;
        }

        $url = "https://smsmisr.com/api/SMS/";

        $response = Http::post($url, [
            'environment' => $this->environment,
            'username' => $this->username,
            'password' => $this->password,
            'sender' => $this->senderToken,
            'mobile' => $phoneNumber,
            'message' => $message,
            'language' => '1',
        ]);

        if ($response->successful() && ($response->json('code') == '4901' || $response->json('code') == '1901')) {
            Log::info("📲 SMS sent via SmsMisr to: {$phoneNumber} | Code: " . $response->json('code'));
            return true;
        }

        Log::error("📲 SmsMisr SMS failed: " . $response->body());
        return false;
    }
}
