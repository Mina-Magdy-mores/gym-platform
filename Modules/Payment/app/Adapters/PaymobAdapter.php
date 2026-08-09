<?php

namespace Modules\Payment\Adapters;

use Illuminate\Support\Facades\Http;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentResponse;

class PaymobAdapter implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $integrationId;
    protected string $iframeId;
    protected string $hmacSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key', env('PAYMOB_API_KEY'));
        $this->integrationId = config('services.paymob.integration_id', env('PAYMOB_INTEGRATION_ID'));
        $this->iframeId = config('services.paymob.iframe_id', env('PAYMOB_IFRAME_ID'));
        $this->hmacSecret = config('services.paymob.hmac_secret', env('PAYMOB_HMAC_SECRET'));
        $this->baseUrl = 'https://accept.paymob.com/api';
    }

    /**
     * Return gateway identifier name.
     */
    public function getName(): string
    {
        return 'paymob';
    }

    /**
     * Initiate Paymob checkout flow via Intention API 3-step authentication protocol.
     */
    public function pay(float $amount, string $currency = 'EGP', array $metadata = []): PaymentResponse
    {
        $amountCents = (int) round($amount * 100);
        $userId = $metadata['user_id'] ?? 1;
        $type = $metadata['type'] ?? 'subscription';

        // Structured Universal Merchant Order Identifier (Distinguishes PT Sessions from Subscriptions)
        if ($type === 'pt_session') {
            $trainerId = $metadata['trainer_id'] ?? 1;
            $bookingDate = $metadata['booking_date'] ?? now()->toDateString();
            $startTime = $metadata['start_time'] ?? '17:00';
            $endTime = $metadata['end_time'] ?? '18:00';

            $merchantOrderId = "FITCLUB-PT-U{$userId}-T{$trainerId}-D{$bookingDate}-S{$startTime}-E{$endTime}-T" . time();
        } else {
            $planId = $metadata['subscription_plan_id'] ?? 1;
            $action = $metadata['action_type'] ?? 'new';
            $merchantOrderId = "FITCLUB-U{$userId}-P{$planId}-A{$action}-T" . time();
        }

        // 1. Step 1: Request Authentication Token
        $authResponse = Http::post($this->baseUrl . '/auth/tokens', [
            'api_key' => $this->apiKey,
        ]);

        if ($authResponse->failed()) {
            return PaymentResponse::failure('Paymob Auth Failed: ' . $authResponse->body(), $authResponse->json() ?? []);
        }

        $authToken = $authResponse->json('token');

        // 2. Step 2: Register Order with Universal Merchant Order ID
        $orderResponse = Http::post($this->baseUrl . '/ecommerce/orders', [
            'auth_token' => $authToken,
            'delivery_needed' => 'false',
            'amount_cents' => (string) $amountCents,
            'currency' => $currency,
            'merchant_order_id' => $merchantOrderId,
            'items' => [],
        ]);

        if ($orderResponse->failed()) {
            return PaymentResponse::failure('Paymob Order Registration Failed: ' . $orderResponse->body(), $orderResponse->json() ?? []);
        }

        $orderId = $orderResponse->json('id');

        $firstName = (string) ($metadata['first_name'] ?? 'Gym');
        $lastName = (string) ($metadata['last_name'] ?? 'Member');

        if (strlen($firstName) < 2) {
            $firstName = $firstName . ' User';
        }
        if (strlen($lastName) < 2) {
            $lastName = $lastName . ' Member';
        }

        $street = $metadata['booking_date'] ?? 'NA';
        $floor = $metadata['start_time'] ?? 'NA';
        $building = $metadata['end_time'] ?? 'NA';

        // 3. Step 3: Request Payment Key with Customer Billing Data
        $keyResponse = Http::post($this->baseUrl . '/acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => (string) $amountCents,
            'expiration' => 3600,
            'order_id' => (string) $orderId,
            'billing_data' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $metadata['email'] ?? 'user@example.com',
                'phone_number' => $metadata['phone_number'] ?? '+201000000000',
                'floor' => $floor,
                'building' => $building,
                'street' => $street,
                'apartment' => 'NA',
                'city' => 'Cairo',
                'country' => 'EG',
                'state' => 'NA',
            ],
            'currency' => $currency,
            'integration_id' => $this->integrationId,
        ]);

        if ($keyResponse->failed()) {
            return PaymentResponse::failure('Paymob Payment Key Failed: ' . $keyResponse->body(), $keyResponse->json() ?? []);
        }

        $paymentToken = $keyResponse->json('token');
        $redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";

        return PaymentResponse::success(
            transactionId: (string) $orderId,
            redirectUrl: $redirectUrl,
            message: 'Paymob checkout session created successfully.',
            rawPayload: $keyResponse->json() ?? []
        );
    }

    /**
     * Handle incoming gateway webhook notification with HMAC Verification.
     */
    public function handleWebhook(array $payload): PaymentResponse
    {
        $obj = $payload['obj'] ?? $payload;

        // HMAC Signature Verification Protocol
        $receivedHmac = request()->query('hmac') ?? ($payload['hmac'] ?? null);

        if ($this->hmacSecret && $receivedHmac) {
            $concatenatedString = 
                ($obj['amount_cents'] ?? '') .
                ($obj['created_at'] ?? '') .
                ($obj['currency'] ?? '') .
                (filter_var($obj['error_occured'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['has_parent_transaction'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                ($obj['id'] ?? '') .
                ($obj['integration_id'] ?? '') .
                (filter_var($obj['is_3d_secure'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['is_auth'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['is_capture'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['is_refunded'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['is_standalone_payment'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                (filter_var($obj['is_voided'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                ($obj['order']['id'] ?? '') .
                ($obj['owner'] ?? '') .
                (filter_var($obj['pending'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false') .
                ($obj['source_data']['pan'] ?? '') .
                ($obj['source_data']['sub_type'] ?? '') .
                ($obj['source_data']['type'] ?? '') .
                (filter_var($obj['success'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false');

            $calculatedHmac = hash_hmac('sha512', $concatenatedString, $this->hmacSecret);

            if (! hash_equals($calculatedHmac, $receivedHmac)) {
                return PaymentResponse::failure('Invalid Paymob HMAC Signature.', $payload);
            }
        }

        $isSuccess = filter_var($obj['success'] ?? false, FILTER_VALIDATE_BOOLEAN) === true 
            && filter_var($obj['error_occured'] ?? false, FILTER_VALIDATE_BOOLEAN) === false;

        if ($isSuccess) {
            return PaymentResponse::success(
                transactionId: (string) ($obj['id'] ?? $obj['order']['id'] ?? 'unknown'),
                redirectUrl: null,
                message: 'Paymob payment verified successfully via Webhook.',
                rawPayload: $payload
            );
        }

        return PaymentResponse::failure('Paymob transaction failed or declined.', $payload);
    }

    /**
     * Refund a paid transaction via Paymob API.
     */
    public function refund(string $transactionId, float $amount): PaymentResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
        ])->post($this->baseUrl . '/acceptance/void/refund', [
            'transaction_id' => $transactionId,
            'amount_cents' => (int) round($amount * 100),
        ]);

        if ($response->successful()) {
            return PaymentResponse::success(
                transactionId: $transactionId,
                redirectUrl: null,
                message: 'Paymob refund processed successfully.',
                rawPayload: $response->json() ?? []
            );
        }

        return PaymentResponse::failure('Paymob refund failed: ' . $response->body(), $response->json() ?? []);
    }
}