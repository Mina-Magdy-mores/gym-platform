<?php

namespace Modules\Subscription\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Subscription\Models\GymRule;
use Modules\Subscription\Services\SubscriptionService;

class ApiAdminGymRuleController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Admin: List all gym rules with status and ordering.
     */
    public function index(): JsonResponse
    {
        $rules = GymRule::orderBy('order')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $rules,
        ]);
    }

    /**
     * Admin: Store new gym rule.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:general,equipment,hygiene,safety',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['order'] = $validated['order'] ?? 0;

        $rule = $this->subscriptionService->createGymRule($validated);

        return response()->json([
            'success' => true,
            'message' => 'Gym rule added successfully.',
            'data' => $rule,
        ], 201);
    }

    /**
     * Admin: Update existing gym rule.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'category' => 'sometimes|required|in:general,equipment,hygiene,safety',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $rule = $this->subscriptionService->updateGymRule($rule, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Gym rule updated successfully.',
            'data' => $rule,
        ]);
    }

    /**
     * Admin: Toggle rule active status.
     */
    public function toggleActive(int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);
        $rule = $this->subscriptionService->updateGymRule($rule, [
            'is_active' => ! $rule->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gym rule status updated.',
            'data' => $rule,
        ]);
    }

    /**
     * Admin: Delete gym rule.
     */
    public function destroy(int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);
        $this->subscriptionService->deleteGymRule($rule);

        return response()->json([
            'success' => true,
            'message' => 'Gym rule deleted successfully.',
        ]);
    }
}
