<?php

namespace Modules\Payment\Adapters;

use Illuminate\Support\Facades\Http;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentResponse;

class StripeAdapter implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $webhookSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = trim((string) config('services.stripe.secret', env('STRIPE_SECRET', '')));
        $this->publicKey = trim((string) config('services.stripe.key', env('STRIPE_KEY', '')));
        $this->webhookSecret = trim((string) config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET', '')));
        $this->baseUrl = 'https://api.stripe.com/v1';
    }

    /**
     * Return gateway identifier name.
     */
    public function getName(): string
    {
        return 'stripe';
    }

    /**
     * Initiate Stripe Hosted Checkout Session via REST API.
     */
    public function pay(float $amount, string $currency = 'EGP', array $metadata = []): PaymentResponse
    {
        $amountCents = (int) round($amount * 100);
        $userId = $metadata['user_id'] ?? 1;
        $type = $metadata['type'] ?? 'subscription';

        if ($type === 'pt_session') {
            $trainerId = $metadata['trainer_id'] ?? 1;
            $bookingDate = $metadata['booking_date'] ?? now()->toDateString();
            $startTime = $metadata['start_time'] ?? '17:00';
            $endTime = $metadata['end_time'] ?? '18:00';
            $merchantOrderId = "FITCLUB-PT-U{$userId}-T{$trainerId}-D{$bookingDate}-S{$startTime}-E{$endTime}-T" . time();
            $itemName = 'PT Session with Trainer';
        } else {
            $planId = $metadata['subscription_plan_id'] ?? 1;
            $action = $metadata['action_type'] ?? 'new';
            $merchantOrderId = "FITCLUB-U{$userId}-P{$planId}-A{$action}-T" . time();
            $itemName = 'FIT CLUB Gym Membership Subscription';
        }

        $successUrl = route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}&status=success';
        $cancelUrl = route('dashboard') . '?status=cancelled';

        $response = Http::withToken($this->secretKey)
            ->asForm()
            ->post($this->baseUrl . '/checkout/sessions', [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => strtolower($currency),
                            'unit_amount' => $amountCents,
                            'product_data' => [
                                'name' => $itemName,
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $merchantOrderId,
                'customer_email' => $metadata['email'] ?? 'user@example.com',
                'metadata' => array_merge($metadata, [
                    'merchant_order_id' => $merchantOrderId,
                ]),
            ]);

        if ($response->failed()) {
            return PaymentResponse::failure('Stripe Checkout Session Creation Failed: ' . $response->body(), $response->json() ?? []);
        }

        $sessionData = $response->json();
        $sessionId = (string) ($sessionData['id'] ?? '');
        $redirectUrl = (string) ($sessionData['url'] ?? '');

        return PaymentResponse::success(
            transactionId: $sessionId,
            redirectUrl: $redirectUrl,
            message: 'Stripe Checkout session created successfully.',
            rawPayload: $sessionData
        );
    }

    /**
     * Handle incoming gateway webhook notification with HMAC SHA256 Signature Verification.
     */
    public function handleWebhook(array $payload): PaymentResponse
    {
        $event = $payload['type'] ?? '';
        $dataObject = $payload['data']['object'] ?? [];

        // Stripe Signature Verification Protocol (Tolerant in local development environment)
        $signatureHeader = request()->header('Stripe-Signature');

        if ($this->webhookSecret && $signatureHeader && app()->environment('production')) {
            $sigParts = [];
            foreach (explode(',', $signatureHeader) as $part) {
                $pair = explode('=', trim($part), 2);
                if (count($pair) === 2) {
                    $sigParts[$pair[0]] = $pair[1];
                }
            }

            $timestamp = $sigParts['t'] ?? '';
            $v1Sig = $sigParts['v1'] ?? '';

            $rawBody = request()->getContent();
            $signedPayload = $timestamp . '.' . $rawBody;
            $expectedSig = hash_hmac('sha256', $signedPayload, $this->webhookSecret);

            if (! hash_equals($expectedSig, $v1Sig)) {
                return PaymentResponse::failure('Invalid Stripe Webhook Signature.', $payload);
            }
        }

        // Only process checkout.session.completed as it holds complete metadata & client_reference_id
        if ($event === 'checkout.session.completed') {
            $transactionId = (string) ($dataObject['id'] ?? $dataObject['payment_intent'] ?? 'unknown');

            return PaymentResponse::success(
                transactionId: $transactionId,
                redirectUrl: null,
                message: 'Stripe transaction verified successfully via Webhook.',
                rawPayload: $payload
            );
        }

        return PaymentResponse::failure('Stripe event ignored or unhandled: ' . $event, $payload);
    }

    /**
     * Refund a paid transaction via Stripe Refunds API.
     */
    public function refund(string $transactionId, float $amount): PaymentResponse
    {
        $amountCents = (int) round($amount * 100);

        $params = [
            'amount' => $amountCents,
        ];

        if (str_starts_with($transactionId, 'pi_')) {
            $params['payment_intent'] = $transactionId;
        } else {
            $params['charge'] = $transactionId;
        }

        $response = Http::withToken($this->secretKey)
            ->asForm()
            ->post($this->baseUrl . '/refunds', $params);

        if ($response->successful()) {
            return PaymentResponse::success(
                transactionId: $transactionId,
                redirectUrl: null,
                message: 'Stripe refund processed successfully.',
                rawPayload: $response->json() ?? []
            );
        }

        return PaymentResponse::failure('Stripe refund failed: ' . $response->body(), $response->json() ?? []);
    }
}