<?php

namespace Modules\Payment\DTOs;

class PaymentResponse
{
    public bool $isSuccessful;
    public ?string $transactionId;
    public ?string $redirectUrl;
    public string $message;
    public array $rawPayload;

    public function __construct(
        bool $isSuccessful,
        ?string $transactionId = null,
        ?string $redirectUrl = null,
        string $message = '',
        array $rawPayload = []
    ) {
        $this->isSuccessful = $isSuccessful;
        $this->transactionId = $transactionId;
        $this->redirectUrl = $redirectUrl;
        $this->message = $message;
        $this->rawPayload = $rawPayload;
    }

    /**
     * Named constructor for successful payment response.
     */
    public static function success(string $transactionId, ?string $redirectUrl = null, string $message = 'Payment initiated successfully', array $rawPayload = []): self
    {
        return new self(true, $transactionId, $redirectUrl, $message, $rawPayload);
    }

    /**
     * Named constructor for failed payment response.
     */
    public static function failure(string $message = 'Payment processing failed', array $rawPayload = []): self
    {
        return new self(false, null, null, $message, $rawPayload);
    }
}