<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\PaymentResponse;

interface PaymentGatewayInterface
{
    /**
     * Get unique name identifier of the payment gateway adapter.
     */
    public function getName(): string;

    /**
     * Initiate payment transaction.
     */
    public function pay(float $amount, string $currency = 'EGP', array $metadata = []): PaymentResponse;

    /**
     * Handle incoming webhook notifications.
     */
    public function handleWebhook(array $payload): PaymentResponse;

    /**
     * Refund a paid transaction.
     */
    public function refund(string $transactionId, float $amount): PaymentResponse;
}