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
     * Show form to create a diet plan for a member.
     */
    public function create(User $user): View
    {
        return view('workout::trainer.diet.create', compact('user'));
    }

    /**
     * Store a new diet plan for a member.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'daily_calories' => 'required|integer|min:500|max:10000',
            'protein_grams' => 'required|integer|min:0',
            'carbs_grams' => 'required|integer|min:0',
            'fats_grams' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'meals' => 'required|array|min:1',
            'meals.*.meal_name' => 'required|string|max:255',
            'meals.*.meal_time' => 'nullable|string|max:100',
            'meals.*.food_items' => 'required|string',
            'meals.*.calories' => 'nullable|integer|min:0',
        ]);

        $validated['trainer_id'] = Auth::id();
        $validated['user_id'] = $user->id;

        $this->dietService->createDietPlan($validated);

        return redirect()->route('trainer.members.index')->with('success', 'Diet nutrition plan assigned to member successfully!');
    }
}