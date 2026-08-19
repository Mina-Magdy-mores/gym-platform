<?php

namespace Modules\Subscription\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Subscription\Models\GymRule;
use Modules\Subscription\Transformers\GymRuleResource;
use Modules\User\Traits\ApiResponseTrait;

class ApiAdminGymRuleController extends Controller
{
    use ApiResponseTrait;

    /**
     * Admin: List all gym rules with status and ordering.
     */
    public function index(): JsonResponse
    {
        $rules = GymRule::orderBy('sort_order', 'asc')
            ->orderBy('rule_number', 'asc')
            ->get();

        return $this->successResponse([
            'gym_rules' => GymRuleResource::collection($rules),
        ], 'Gym rules fetched successfully.');
    }

    /**
     * Admin: Store new gym rule.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rule_number' => 'nullable|integer|min:1',
            'rule_text'   => 'required|string|max:1000',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        if (empty($validated['rule_number'])) {
            $maxRuleNumber = GymRule::max('rule_number') ?? 0;
            $validated['rule_number'] = $maxRuleNumber + 1;
        }

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $validated['rule_number'];
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $rule = GymRule::create($validated);
        Cache::forget('gym_rules_active');

        return $this->successResponse([
            'gym_rule' => new GymRuleResource($rule),
        ], 'Gym rule created successfully.', 201);
    }

    /**
     * Admin: Update existing gym rule.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);

        $validated = $request->validate([
            'rule_number' => 'sometimes|required|integer|min:1',
            'rule_text'   => 'sometimes|required|string|max:1000',
            'is_active'   => 'sometimes|boolean',
            'sort_order'  => 'sometimes|integer|min:0',
        ]);

        $rule->update($validated);
        Cache::forget('gym_rules_active');

        return $this->successResponse([
            'gym_rule' => new GymRuleResource($rule),
        ], 'Gym rule updated successfully.');
    }

    /**
     * Admin: Toggle rule active status.
     */
    public function toggleActive(int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);
        $rule->update(['is_active' => !$rule->is_active]);
        Cache::forget('gym_rules_active');

        return $this->successResponse([
            'gym_rule' => new GymRuleResource($rule),
        ], 'Gym rule status updated.');
    }

    /**
     * Admin: Delete gym rule.
     */
    public function destroy(int $id): JsonResponse
    {
        $rule = GymRule::findOrFail($id);
        $rule->delete();
        Cache::forget('gym_rules_active');

        return $this->successResponse(null, 'Gym rule deleted successfully.');
    }
}
