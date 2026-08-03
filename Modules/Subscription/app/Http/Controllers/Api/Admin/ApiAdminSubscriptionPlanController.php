<?php

namespace Modules\Subscription\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Subscription\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use Modules\Subscription\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Transformers\SubscriptionPlanResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiAdminSubscriptionPlanController extends Controller
{
    use ApiResponseTrait;

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get all subscription plans for Admin.
     */
    public function index(): JsonResponse
    {
        $plans = $this->subscriptionService->getAllPlans();

        return $this->successResponse([
            'plans' => SubscriptionPlanResource::collection($plans),
        ], 'All subscription plans fetched successfully.');
    }

    /**
     * Store a newly created plan via API.
     */
    public function store(StoreSubscriptionPlanRequest $request): JsonResponse
    {
        $plan = $this->subscriptionService->createPlan($request->validated());

        return $this->successResponse([
            'plan' => new SubscriptionPlanResource($plan),
        ], 'Subscription plan created successfully.', 201);
    }

    /**
     * Show a single plan by ID.
     */
    public function show(int $id): JsonResponse
    {
        $plan = $this->subscriptionService->getPlanById($id);

        return $this->successResponse([
            'plan' => new SubscriptionPlanResource($plan),
        ], 'Subscription plan details fetched.');
    }

    /**
     * Update an existing plan via API.
     */
    public function update(UpdateSubscriptionPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->subscriptionService->updatePlan($id, $request->validated());

        return $this->successResponse([
            'plan' => new SubscriptionPlanResource($plan),
        ], 'Subscription plan updated successfully.');
    }

    /**
     * Toggle active/inactive status of a plan via API.
     */
    public function toggleActive(int $id): JsonResponse
    {
        $plan = $this->subscriptionService->togglePlanStatus($id);

        return $this->successResponse([
            'plan' => new SubscriptionPlanResource($plan),
        ], 'Subscription plan active status toggled.');
    }

    /**
     * Delete a plan via API.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->subscriptionService->deletePlan($id);

        return $this->successResponse(null, 'Subscription plan deleted successfully.');
    }
}
