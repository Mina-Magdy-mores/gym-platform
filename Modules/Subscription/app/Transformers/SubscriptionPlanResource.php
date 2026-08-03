<?php

namespace Modules\Subscription\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'duration_months' => $this->duration_months,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'benefits' => [
                'free_days' => $this->free_days,
                'freeze_days' => $this->freeze_days,
                'invitations_count' => $this->invitations_count,
                'inbody_scans' => $this->inbody_scans,
                'pt_sessions' => $this->pt_sessions,
                'kickboxing_classes' => $this->kickboxing_classes,
                'nutrition_plans' => $this->nutrition_plans,
                'spa_access' => $this->spa_access,
            ],
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}