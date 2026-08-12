<?php

namespace Modules\Workout\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Workout\Services\DietService;

class TrainerDietController extends Controller
{
    protected DietService $dietService;

    public function __construct(DietService $dietService)
    {
        $this->dietService = $dietService;
    }

    /**
     * Show form to create or edit a diet plan for a member.
     */
    public function create(User $user): View
    {
        $existingDietPlan = $user->activeDietPlan()->with('meals')->first();
        $availableDietPlans = \Modules\Workout\Models\DietPlan::with('meals')
            ->where(function($q) use ($user) {
                $q->where('trainer_id', Auth::id())
                  ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->get();

        return view('workout::trainer.diet.create', compact('user', 'existingDietPlan', 'availableDietPlans'));
    }

    /**
     * Store or update a diet plan for a member.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
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

        $validated['trainer_id'] = Auth::id();
        $validated['user_id'] = $user->id;
        $validated['status'] = $request->input('status', 'active');

        $this->dietService->createDietPlan($validated);

        return redirect()->route('trainer.members.index')->with('success', 'Diet plan updated for member successfully!');
    }
}