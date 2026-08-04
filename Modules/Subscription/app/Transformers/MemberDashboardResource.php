<?php

namespace Modules\Subscription\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberDashboardResource extends JsonResource
{
    /**
     * Transform the member dashboard payload into a structured resource.
     */
    public function toArray(Request $request): array
    {
        $user = $this['user'];
        $activeSub = $this['active_subscription'];
        $upcomingBookingsCount = $this['upcoming_bookings_count'];
        $agreedGymRulesCount = $this['agreed_gym_rules_count'];

        return [
            'member' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'active_subscription' => $activeSub ? [
                'id' => $activeSub->id,
                'plan_name' => $activeSub->plan?->name ?? 'Custom Plan',
                'starts_at' => $activeSub->starts_at->toDateString(),
                'ends_at' => $activeSub->ends_at->toDateString(),
                'remaining_days' => (int) max(0, ceil(now()->diffInDays($activeSub->ends_at))),
                'status' => $activeSub->status,
                'benefits_balance' => [
                    'freeze_days' => [
                        'remaining' => $activeSub->remaining_freeze_days,
                        'total' => $activeSub->plan?->freeze_days ?? 0,
                    ],
                    'invitations' => [
                        'remaining' => $activeSub->remaining_invitations,
                        'total' => $activeSub->plan?->invitations_count ?? 0,
                    ],
                    'inbody_scans' => [
                        'remaining' => $activeSub->remaining_inbody_scans,
                        'total' => $activeSub->plan?->inbody_scans ?? 0,
                    ],
                    'pt_sessions' => [
                        'remaining' => $activeSub->remaining_pt_sessions,
                        'total' => $activeSub->plan?->pt_sessions ?? 0,
                    ],
                    'kickboxing_classes' => [
                        'remaining' => $activeSub->remaining_kickboxing_classes,
                        'total' => $activeSub->plan?->kickboxing_classes ?? 0,
                    ],
                    'nutrition_plans' => [
                        'remaining' => $activeSub->remaining_nutrition_plans,
                        'total' => $activeSub->plan?->nutrition_plans ?? 0,
                    ],
                ],
            ] : null,
            'upcoming_bookings_count' => $upcomingBookingsCount,
            'agreed_gym_rules_count' => $agreedGymRulesCount,
        ];
    }
}
