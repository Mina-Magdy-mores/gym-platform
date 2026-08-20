<?php

namespace Modules\Payment\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentResponse;
use Modules\Payment\Models\Payment;
use Modules\Subscription\Models\SubscriptionPlan;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;
use Illuminate\Support\Facades\Notification;
use Modules\Payment\Adapters\StripeAdapter;
use Modules\Payment\Adapters\MockPaymentAdapter;
use Modules\Payment\Adapters\PaymobAdapter;
use Modules\Subscription\Notifications\NewMemberSubscribedNotification;

class PaymentService
{
    protected PaymentGatewayInterface $paymentGateway;
    protected SubscriptionService $subscriptionService;
    protected BookingService $bookingService;

    public function __construct(
        PaymentGatewayInterface $paymentGateway,
        SubscriptionService $subscriptionService,
        BookingService $bookingService
    ) {
        $this->paymentGateway = $paymentGateway;
        $this->subscriptionService = $subscriptionService;
        $this->bookingService = $bookingService;
    }

    /**
     * Resolve gateway adapter dynamically by name.
     */
    public function getGatewayAdapter(string $gatewayName = 'paymob'): PaymentGatewayInterface
    {
        return match (strtolower($gatewayName)) {
            'stripe' => new StripeAdapter(),
            'mock' => new MockPaymentAdapter(),
            'paymob' => new PaymobAdapter(),
            default => new PaymobAdapter(),
        };
    }

    /**
     * Process subscription plan checkout payment dynamically.
     */
    public function processSubscriptionPayment(User $user, SubscriptionPlan $plan, string $gateway = 'paymob'): PaymentResponse
    {
        $prep = $this->subscriptionService->prepareSubscriptionAction($user, $plan);
        $adapter = $this->getGatewayAdapter($gateway);

        $response = $adapter->pay(
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

        DB::transaction(function () use ($user, $plan, $prep, $response, $adapter) {
            $userSub = $this->subscriptionService->executeSubscriptionAction($user, $plan, $prep['action']);

            Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'user_subscription_id' => $userSub->id ?? null,
                'transaction_id' => $response->transactionId,
                'gateway' => $adapter->getName(),
                'amount' => $prep['amount_to_pay'],
                'currency' => $plan->currency ?? 'EGP',
                'status' => 'completed',
                'payload' => $response->rawPayload,
            ]);

            // Dispatch instant real-time notification to all platform Admins
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty() && isset($userSub)) {
                Notification::send($admins, new NewMemberSubscribedNotification($userSub));
            }
        });

        return $response;
    }

    /**
     * Process out-of-pocket PT session checkout payment dynamically.
     */
    public function processPTSessionPayment(User $user, User $trainer, array $bookingData, string $gateway = 'paymob'): PaymentResponse
    {
        $sessionPrice = (float) ($trainer->session_rate ?? $bookingData['price'] ?? 200.00);
        $adapter = $this->getGatewayAdapter($gateway);

        $response = $adapter->pay(
            amount: $sessionPrice,
            currency: 'EGP',
            metadata: [
                'first_name' => $user->name ?? 'Gym',
                'last_name' => 'Member',
                'email' => $user->email,
                'phone_number' => $user->phone ?? '+201000000000',
                'user_id' => $user->id,
                'trainer_id' => $trainer->id,
                'type' => 'pt_session',
                'booking_date' => $bookingData['booking_date'],
                'start_time' => $bookingData['start_time'],
                'end_time' => $bookingData['end_time'],
                'notes' => $bookingData['notes'] ?? null,
            ]
        );

        if (! $response->isSuccessful) {
            throw new \Exception($response->message);
        }

        if ($response->redirectUrl) {
            return $response;
        }

        DB::transaction(function () use ($user, $trainer, $sessionPrice, $bookingData, $response, $adapter) {
            $booking = $this->bookingService->confirmPTSessionAfterPayment($user, $trainer, $bookingData);

            Payment::create([
                'user_id' => $user->id,
                'subscription_plan_id' => null,
                'booking_id' => $booking->id,
                'transaction_id' => $response->transactionId,
                'gateway' => $adapter->getName(),
                'amount' => $sessionPrice,
                'currency' => 'EGP',
                'status' => 'completed',
                'payload' => $response->rawPayload,
            ]);
        });

        return $response;
    }

    /**
     * Verify gateway webhook payload, resolve exact User & Plan dynamically, record transaction & activate subscription or PT session.
     */
    public function handleGatewayWebhook(array $payload): PaymentResponse
    {
        Log::info('Incoming Gateway Webhook Payload:', $payload);

        $isStripe = isset($payload['object']) && ($payload['object'] === 'event' || isset($payload['type']));
        $adapter = $isStripe ? new \Modules\Payment\Adapters\StripeAdapter() : new \Modules\Payment\Adapters\PaymobAdapter();

        $response = $adapter->handleWebhook($payload);

        if ($response->isSuccessful) {
            DB::transaction(function () use ($response) {
                $rawPayload = $response->rawPayload;

                // Dual resolution for Stripe vs Paymob Webhook payload structures
                $isStripe = isset($rawPayload['object']) && ($rawPayload['object'] === 'event' || isset($rawPayload['type']));

                if ($isStripe) {
                    $eventObj = $rawPayload['data']['object'] ?? [];
                    $merchantOrderId = $eventObj['client_reference_id'] ?? $eventObj['metadata']['merchant_order_id'] ?? '';
                    $amountPaid = isset($eventObj['amount_total']) ? ($eventObj['amount_total'] / 100) : (isset($eventObj['amount']) ? ($eventObj['amount'] / 100) : 0);
                    $currency = strtoupper($eventObj['currency'] ?? 'EGP');
                } else {
                    $obj = $rawPayload['obj'] ?? $rawPayload;
                    $merchantOrderId = $obj['order']['merchant_order_id'] ?? '';
                    $amountPaid = isset($obj['amount_cents']) ? ($obj['amount_cents'] / 100) : 0;
                    $currency = strtoupper($obj['currency'] ?? 'EGP');
                }

                // 1. Check if this is a PT Session out-of-pocket payment: FITCLUB-PT-U{userId}-T{trainerId}...
                if (str_contains($merchantOrderId, 'FITCLUB-PT-')) {
                    preg_match('/FITCLUB-PT-U(\d+)-T(\d+)/', $merchantOrderId, $ptMatches);
                    $userId = $ptMatches[1] ?? null;
                    $trainerId = $ptMatches[2] ?? null;

                    preg_match('/D([0-9\-]+)-S([0-9:]+)-E([0-9:]+)/', $merchantOrderId, $timeMatches);
                    $bookingDate = $timeMatches[1] ?? now()->toDateString();
                    $startTime = $timeMatches[2] ?? '17:00';
                    $endTime = $timeMatches[3] ?? '18:00';

                    $user = $userId ? User::where('id', $userId)->first() : User::first();
                    $trainer = $trainerId ? User::where('id', $trainerId)->first() : null;

                    if ($user && $trainer) {
                        $booking = $this->bookingService->confirmPTSessionAfterPayment($user, $trainer, [
                            'booking_date' => $bookingDate,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'price' => $amountPaid > 0 ? $amountPaid : 200.00,
                            'notes' => 'Confirmed via Gateway Webhook PT Payment.',
                        ]);

                        Payment::create([
                            'user_id' => $user->id,
                            'subscription_plan_id' => null,
                            'booking_id' => $booking->id,
                            'transaction_id' => (string) ($response->transactionId ?? 'TXN-PT-' . time()),
                            'gateway' => $isStripe ? 'stripe' : 'paymob',
                            'amount' => $amountPaid,
                            'currency' => $currency,
                            'status' => 'completed',
                            'payload' => $rawPayload,
                        ]);
                    }
                    return;
                }

                // 2. Otherwise process Subscription Plan Payment
                preg_match('/FITCLUB-U(\d+)-P(\d+)-A(\w+)/', $merchantOrderId, $matches);

                $userId = $matches[1] ?? null;
                $planId = $matches[2] ?? null;
                $action = $matches[3] ?? 'new';

                $user = $userId ? User::where('id', $userId)->first() : User::first();
                $plan = $planId ? SubscriptionPlan::where('id', $planId)->first() : SubscriptionPlan::first();

                if ($user && $plan) {
                    $userSub = $this->subscriptionService->executeSubscriptionAction($user, $plan, $action);

                    Payment::create([
                        'user_id' => $user->id,
                        'subscription_plan_id' => $plan->id,
                        'user_subscription_id' => $userSub->id ?? null,
                        'transaction_id' => (string) ($response->transactionId ?? 'TXN-' . time()),
                        'gateway' => $isStripe ? 'stripe' : 'paymob',
                        'amount' => $amountPaid,
                        'currency' => $currency,
                        'status' => 'completed',
                        'payload' => $rawPayload,
                    ]);

                    // Dispatch instant real-time notification to all platform Admins on Webhook execution
                    $admins = User::role('admin')->get();
                    if ($admins->isNotEmpty() && isset($userSub)) {
                        Notification::send($admins, new NewMemberSubscribedNotification($userSub));
                    }
                }
            });
        }

        return $response;
    }
}