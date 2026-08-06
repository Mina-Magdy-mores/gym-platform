<?php

namespace Modules\Payment\Adapters;

use Illuminate\Support\Str;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentResponse;

class MockPaymentAdapter implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'mock';
    }

    public function pay(float $amount, string $currency = 'EGP', array $metadata = []): PaymentResponse
    {
        $transactionId = 'MOCK-TXN-' . strtoupper(Str::random(10));
        
        return PaymentResponse::success(
            transactionId: $transactionId,
            redirectUrl: null,
            message: 'Mock payment processed successfully for test amount: ' . $amount . ' ' . $currency,
            rawPayload: [
                'gateway' => 'mock',
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => $metadata,
                'timestamp' => now()->toIso8601String(),
            ]
        );
    }

    public function handleWebhook(array $payload): PaymentResponse
    {
        return PaymentResponse::success(
            transactionId: $payload['transaction_id'] ?? 'MOCK-WEBHOOK-' . time(),
            redirectUrl: null,
            message: 'Mock webhook verified successfully.',
            rawPayload: $payload
        );
    }

    public function refund(string $transactionId, float $amount): PaymentResponse
    {
        return PaymentResponse::success(
            transactionId: $transactionId,
            redirectUrl: null,
            message: 'Mock refund of ' . $amount . ' processed successfully.',
            rawPayload: ['refund_id' => 'MOCK-REFUND-' . time()]
        );
    }
}