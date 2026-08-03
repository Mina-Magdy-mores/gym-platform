<?php

namespace Modules\Subscription\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGymScheduleRequest extends FormRequest
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
            'target_gender' => 'required|in:men,women',
            'days_label' => 'required|string|max:255',
            'start_time' => 'nullable|date_format:H:i:s,H:i',
            'end_time' => 'nullable|date_format:H:i:s,H:i',
            'time_label' => 'required|string|max:255',
            'is_off_day' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ];
    }
}
