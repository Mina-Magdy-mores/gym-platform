<?php

namespace Modules\Subscription\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Http\Requests\SubscribePlanRequest;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Transformers\GymScheduleResource;
use Modules\Subscription\Transformers\SubscriptionPlanResource;
use Modules\Subscription\Transformers\UserSubscriptionResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiSubscriptionController extends Controller
{
    use ApiResponseTrait;

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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
     * Subscribe current user to a plan.
     */
    public function subscribe(SubscribePlanRequest $request): JsonResponse
    {
        $subscription = $this->subscriptionService->subscribeUser(
            $request->user(),
            $request->validated('subscription_plan_id')
        );

        $subscription->load('plan');

        $data = [
            'subscription' => new UserSubscriptionResource($subscription),
        ];

        return $this->successResponse($data, 'Subscribed to plan successfully.', 201);
    }
}