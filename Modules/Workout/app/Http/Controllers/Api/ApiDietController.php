<?php

namespace Modules\Workout\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Workout\Models\DietPlan;
use Modules\Workout\Services\DietService;

class ApiDietController extends Controller
{
    protected DietService $dietService;

    public function __construct(DietService $dietService)
    {
        $this->dietService = $dietService;
    }

    /**
     * Get diet plans for the authenticated user (member or coach).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $diets = DietPlan::with(['meals', 'trainer:id,name,email', 'user:id,name,email'])
            ->where(function ($query) use ($user) {
                if ($user->hasRole('member')) {
                    $query->where('user_id', $user->id);
                } elseif ($user->hasRole('trainer')) {
                    $query->where('trainer_id', $user->id);
                }
            })
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $diets,
        ]);
    }

    /**
     * Display a specific diet plan with its meals and macronutrient breakdown.
     */
    public function show(Request $request, DietPlan $dietPlan): JsonResponse
    {
        $user = $request->user();

        if ($user->hasRole('member') && $dietPlan->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to diet plan.'], 403);
        }

        if ($user->hasRole('trainer') && $dietPlan->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access to diet plan.'], 403);
        }

        $dietPlan->load(['meals', 'trainer:id,name,email', 'user:id,name,email']);

        return response()->json([
            'success' => true,
            'data' => $dietPlan,
        ]);
    }

    /**
     * Coach creates a new diet plan with meals for an athlete.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole(['trainer', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Only certified coaches or admins can design diet plans.'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'daily_calories' => 'required|integer|min:500|max:10000',
            'protein_grams' => 'required|integer|min:0',
            'carbs_grams' => 'required|integer|min:0',
            'fats_grams' => 'required|integer|min:0',
            'status' => 'nullable|in:active,archived',
            'notes' => 'nullable|string',
            'meals' => 'required|array|min:1',
            'meals.*.meal_name' => 'required|string|max:255',
            'meals.*.meal_time' => 'nullable|string|max:100',
            'meals.*.food_items' => 'required|string',
            'meals.*.calories' => 'nullable|integer|min:0',
        ]);

        $validated['trainer_id'] = $user->id;
        $validated['status'] = $validated['status'] ?? 'active';

        $dietPlan = $this->dietService->createDietPlan($validated);

        return response()->json([
            'success' => true,
            'message' => 'Diet plan assigned to athlete successfully.',
            'data' => $dietPlan->load('meals'),
        ], 201);
    }

    /**
     * Coach updates an existing diet plan.
     */
    public function update(Request $request, DietPlan $dietPlan): JsonResponse
    {
        $user = $request->user();

        if ($dietPlan->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to modify this diet plan.'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'daily_calories' => 'sometimes|required|integer|min:500|max:10000',
            'protein_grams' => 'sometimes|required|integer|min:0',
            'carbs_grams' => 'sometimes|required|integer|min:0',
            'fats_grams' => 'sometimes|required|integer|min:0',
            'status' => 'nullable|in:active,archived',
            'notes' => 'nullable|string',
        ]);

        $dietPlan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Diet plan updated successfully.',
            'data' => $dietPlan->fresh(['meals', 'trainer:id,name', 'user:id,name']),
        ]);
    }

    /**
     * Delete or archive a diet plan.
     */
    public function destroy(Request $request, DietPlan $dietPlan): JsonResponse
    {
        $user = $request->user();

        if ($dietPlan->trainer_id !== $user->id && ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to delete this diet plan.'], 403);
        }

        $dietPlan->meals()->delete();
        $dietPlan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Diet plan deleted successfully.',
        ]);
    }
}
