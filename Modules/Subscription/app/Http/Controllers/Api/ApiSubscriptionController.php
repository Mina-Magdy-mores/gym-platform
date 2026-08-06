<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Payment\Services\PaymentService;
use Modules\Subscription\Http\Requests\SubscribePlanRequest;
use Modules\Subscription\Services\BookingService;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Transformers\GymRuleResource;
use Modules\Subscription\Transformers\GymScheduleResource;
use Modules\Subscription\Transformers\MemberDashboardResource;
use Modules\Subscription\Transformers\SubscriptionPlanResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiSubscriptionController extends Controller
{
    use ApiResponseTrait;

    protected SubscriptionService $subscriptionService;
    protected BookingService $bookingService;
    protected PaymentService $paymentService;

    public function __construct(
        SubscriptionService $subscriptionService,
        BookingService $bookingService,
        PaymentService $paymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->bookingService = $bookingService;
        $this->paymentService = $paymentService;
    }

    /**
     * Get all active subscription plans.
     */
    public function plans(): JsonResponse
    {
        $plans = $this->subscriptionService->getActivePlans();

        $data = [
            'plans' => SubscriptionPlanResource::collection($plans),
        ];

        return $this->successResponse($data, 'Subscription plans fetched successfully.');
    }

    /**
     * Get gym operating schedules for Men & Women.
     */
    public function schedules(Request $request): JsonResponse
    {
        $gender = $request->query('gender');
        $schedules = $this->subscriptionService->getGymSchedules($gender);

        $data = [
            'schedules' => GymScheduleResource::collection($schedules),
        ];

        return $this->successResponse($data, 'Gym schedules fetched successfully.');
    }

    /**
     * Get active gym terms & regulations via GymRuleResource.
     */
    public function gymRules(): JsonResponse
    {
        $gymRules = $this->subscriptionService->getActiveGymRules();

        $data = [
            'gym_rules' => GymRuleResource::collection($gymRules),
        ];

        return $this->successResponse($data, 'Gym rules fetched successfully.');
    }

    /**
     * Get member dashboard real-time usage balance API via MemberDashboardResource.
     */
    public function memberDashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $activeSub = $user->activeSubscription;
        $gymRules = $this->subscriptionService->getActiveGymRules();
        $userBookings = $this->bookingService->getUserBookings($user);

        $payload = [
            'user' => $user,
            'active_subscription' => $activeSub,
            'upcoming_bookings_count' => $userBookings->count(),
            'agreed_gym_rules_count' => $gymRules->count(),
        ];

        $data = [
            'dashboard' => new MemberDashboardResource($payload),
        ];

        return $this->successResponse($data, 'Member dashboard usage balance fetched successfully.');
    }

    /**
     * Subscribe current user to a plan via payment checkout process API.
     */
    public function subscribe(SubscribePlanRequest $request): JsonResponse
    {
        $plan = $this->subscriptionService->getPlanById(
            $request->validated('subscription_plan_id')
        );

        $paymentResponse = $this->paymentService->processSubscriptionPayment(
            $request->user(),
            $plan
        );

        $data = [
            'is_successful' => $paymentResponse->isSuccessful,
            'transaction_id' => $paymentResponse->transactionId,
            'redirect_url' => $paymentResponse->redirectUrl,
            'message' => $paymentResponse->message,
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'currency' => $plan->currency ?? 'EGP',
            ],
        ];

        return $this->successResponse($data, 'Payment checkout session initiated successfully.', 201);
    }
}