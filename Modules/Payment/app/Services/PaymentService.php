<?php

namespace Modules\Payment\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\Models\Payment;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Services\SubscriptionService;

class PaymentService
{
    protected PaymentGatewayInterface $paymentGateway;
    protected SubscriptionService $subscriptionService;

    public function __construct(PaymentGatewayInterface $paymentGateway, SubscriptionService $subscriptionService)
    {
        $this->paymentGateway = $paymentGateway;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Process subscription plan checkout payment dynamically.
     */
    public function processSubscriptionPayment(User $user, SubscriptionPlan $plan): PaymentResponse
    {
        $prep = $this->subscriptionService->prepareSubscriptionAction($user, $plan);

        $response = $this->paymentGateway->pay(
            amount: $prep['amount_to_pay'],
            currency: $plan->currency ?? 'EGP',
            metadata: [
                'first_name' => $user->name ?? 'Gym',
                'last_name' => 'Member',
                'email' => $user->email,
                'phone_number' => $user->phone ?? '+201000000000',
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'action_type' => $prep['action'],
            ]
        );

        if (! $response->isSuccessful) {
            throw new \Exception($response->message);
        }

        if ($response->redirectUrl) {
            return $response;
        }

        DB::transaction(function () use ($user, $plan, $prep, $response) {
            Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'transaction_id' => $response->transactionId,
                'gateway' => $this->paymentGateway->getName(),
                'amount' => $prep['amount_to_pay'],
                'currency' => $plan->currency ?? 'EGP',
                'status' => 'completed',
                'payload' => $response->rawPayload,
            ]);

            $this->subscriptionService->executeSubscriptionAction($user, $plan, $prep['action']);
        });

        return $response;
    }

    /**
     * Verify gateway webhook payload, resolve exact User & Plan dynamically, record transaction & activate subscription.
     */
    public function handleGatewayWebhook(array $payload): PaymentResponse
    {
        Log::info('Incoming Paymob Webhook Payload:', $payload);

        $response = $this->paymentGateway->handleWebhook($payload);

        if ($response->isSuccessful) {
            DB::transaction(function () use ($response) {
                $obj = $response->rawPayload['obj'] ?? $response->rawPayload;
                $merchantOrderId = $obj['order']['merchant_order_id'] ?? '';

                // Parse structured order string: FITCLUB-U{userId}-P{planId}-A{action}-T{timestamp}
                preg_match('/FITCLUB-U(\d+)-P(\d+)-A(\w+)/', $merchantOrderId, $matches);

                $userId = $matches[1] ?? null;
                $planId = $matches[2] ?? null;
                $action = $matches[3] ?? 'new';

                // Safe resolution with fallback for test pings
                $user = $userId ? User::find($userId) : User::first();
                $plan = $planId ? SubscriptionPlan::find($planId) : SubscriptionPlan::first();

                if ($user && $plan) {
                    Payment::create([
                        'user_id' => $user->id,
                        'subscription_plan_id' => $plan->id,
                        'transaction_id' => (string) ($response->transactionId ?? $obj['id'] ?? ('TXN-' . time())),
                        'gateway' => $this->paymentGateway->getName(),
                        'amount' => isset($obj['amount_cents']) ? ($obj['amount_cents'] / 100) : 0,
                        'currency' => $obj['currency'] ?? 'EGP',
                        'status' => 'completed',
                        'payload' => $response->rawPayload,
                    ]);

                    $this->subscriptionService->executeSubscriptionAction($user, $plan, $action);
                }
            });
        }

        return $response;
    }
}