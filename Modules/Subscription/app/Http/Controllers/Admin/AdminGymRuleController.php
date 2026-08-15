<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Modules\Subscription\Models\GymRule;

class AdminGymRuleController extends Controller
{
    /**
     * Display listing of all gym terms & conduct regulations for admin CRUD.
     */
    public function index(): View
    {
        $rules = GymRule::orderBy('sort_order', 'asc')
            ->orderBy('rule_number', 'asc')
            ->get();

        return view('subscription::admin.rules.index', compact('rules'));
    }

    /**
     * Store a newly created gym rule in storage.
     */
    public function store(Request $request): RedirectResponse
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

        $validated['is_active'] = $request->has('is_active') ? true : true;

        GymRule::create($validated);
        Cache::forget('gym_rules_active');

        return redirect()->route('admin.rules.index')->with('success', 'Gym Rule #' . $validated['rule_number'] . ' added successfully!');
    }

    /**
     * Update the specified gym rule in storage.
     */
    public function update(Request $request, GymRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'rule_number' => 'required|integer|min:1',
            'rule_text'   => 'required|string|max:1000',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? $validated['rule_number'];

        $rule->update($validated);
        Cache::forget('gym_rules_active');

        return redirect()->route('admin.rules.index')->with('success', 'Gym Rule #' . $rule->rule_number . ' updated successfully!');
    }

    /**
     * Toggle the active status of a gym rule.
     */
    public function toggle(GymRule $rule): RedirectResponse
    {
        $rule->update(['is_active' => !$rule->is_active]);
        Cache::forget('gym_rules_active');

        $status = $rule->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.rules.index')->with('success', "Rule #{$rule->rule_number} has been {$status} successfully!");
    }

    /**
     * Remove the specified gym rule from storage.
     */
    public function destroy(GymRule $rule): RedirectResponse
    {
        $ruleNumber = $rule->rule_number;
        $rule->delete();
        Cache::forget('gym_rules_active');

        return redirect()->route('admin.rules.index')->with('success', "Gym Rule #{$ruleNumber} deleted successfully!");
    }
}
