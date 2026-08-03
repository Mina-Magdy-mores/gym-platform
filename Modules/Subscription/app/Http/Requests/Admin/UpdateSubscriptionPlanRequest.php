<?php

namespace Modules\Subscription\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_months' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'free_days' => 'nullable|integer|min:0',
            'freeze_days' => 'nullable|integer|min:0',
            'invitations_count' => 'nullable|integer|min:0',
            'inbody_scans' => 'nullable|integer|min:0',
            'pt_sessions' => 'nullable|integer|min:0',
            'kickboxing_classes' => 'nullable|integer|min:0',
            'nutrition_plans' => 'nullable|integer|min:0',
            'spa_access' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ];
    }
}
