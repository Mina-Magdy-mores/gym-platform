<?php

namespace Modules\Subscription\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'plan' => new SubscriptionPlanResource($this->whenLoaded('plan')),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'status' => $this->status,
            'price_paid' => (float) $this->price_paid,
            'remaining_benefits' => [
                'freeze_days' => $this->remaining_freeze_days,
                'invitations' => $this->remaining_invitations,
                'inbody_scans' => $this->remaining_inbody_scans,
                'pt_sessions' => $this->remaining_pt_sessions,
                'kickboxing_classes' => $this->remaining_kickboxing_classes,
                'nutrition_plans' => $this->remaining_nutrition_plans,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}