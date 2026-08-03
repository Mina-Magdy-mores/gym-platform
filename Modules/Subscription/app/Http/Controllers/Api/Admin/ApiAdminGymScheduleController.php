<?php

namespace Modules\Subscription\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Subscription\Http\Requests\Admin\StoreGymScheduleRequest;
use Modules\Subscription\Http\Requests\Admin\UpdateGymScheduleRequest;
use Modules\Subscription\Services\SubscriptionService;
use Modules\Subscription\Transformers\GymScheduleResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiAdminGymScheduleController extends Controller
{
    use ApiResponseTrait;

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get all gym schedules for Admin.
     */
    public function index(): JsonResponse
    {
        $schedules = $this->subscriptionService->getAllSchedules();

        return $this->successResponse([
            'schedules' => GymScheduleResource::collection($schedules),
        ], 'All gym schedules fetched successfully.');
    }

    /**
     * Store a newly created schedule via API.
     */
    public function store(StoreGymScheduleRequest $request): JsonResponse
    {
        $schedule = $this->subscriptionService->createSchedule($request->validated());

        return $this->successResponse([
            'schedule' => new GymScheduleResource($schedule),
        ], 'Gym schedule created successfully.', 201);
    }

    /**
     * Show a single schedule by ID.
     */
    public function show(int $id): JsonResponse
    {
        $schedule = $this->subscriptionService->getScheduleById($id);

        return $this->successResponse([
            'schedule' => new GymScheduleResource($schedule),
        ], 'Gym schedule details fetched.');
    }

    /**
     * Update an existing schedule via API.
     */
    public function update(UpdateGymScheduleRequest $request, int $id): JsonResponse
    {
        $schedule = $this->subscriptionService->updateSchedule($id, $request->validated());

        return $this->successResponse([
            'schedule' => new GymScheduleResource($schedule),
        ], 'Gym schedule updated successfully.');
    }

    /**
     * Delete a schedule via API.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->subscriptionService->deleteSchedule($id);

        return $this->successResponse(null, 'Gym schedule deleted successfully.');
    }
}
