<?php

namespace Modules\Subscription\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'target_gender' => $this->target_gender,
            'days_label' => $this->days_label,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time_label' => $this->time_label,
            'is_off_day' => (bool) $this->is_off_day,
            'notes' => $this->notes,
        ];
    }
}