<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscription\Models\GymRule;

class GymRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'rule_number' => 1,
                'rule_text' => 'Foul language, profanity, or inappropriate behavior inside the gym is strictly prohibited.',
                'sort_order' => 1,
            ],
            [
                'rule_number' => 2,
                'rule_text' => 'Proper athletic sportswear and training shoes are mandatory (Jeans, slippers, and sandals are strictly forbidden).',
                'sort_order' => 2,
            ],
            [
                'rule_number' => 3,
                'rule_text' => 'Personal hygiene and cleanliness must be maintained at all times.',
                'sort_order' => 3,
            ],
            [
                'rule_number' => 4,
                'rule_text' => 'Respect machinery and equipment usage time limits during peak hours.',
                'sort_order' => 4,
            ],
            [
                'rule_number' => 5,
                'rule_text' => 'All weights, dumbbells, and equipment must be returned to their designated racks after use.',
                'sort_order' => 5,
            ],
            [
                'rule_number' => 6,
                'rule_text' => 'Exercise extreme caution and safety when handling heavy weights and dumbbells.',
                'sort_order' => 6,
            ],
            [
                'rule_number' => 7,
                'rule_text' => 'Using a personal sweat towel during workouts is mandatory.',
                'sort_order' => 7,
            ],
            [
                'rule_number' => 8,
                'rule_text' => 'Maintain overall cleanliness and tidiness of the gym facilities.',
                'sort_order' => 8,
            ],
            [
                'rule_number' => 9,
                'rule_text' => 'Sauna and steam room sessions must be booked at least 1 day in advance.',
                'sort_order' => 9,
            ],
            [
                'rule_number' => 10,
                'rule_text' => 'Guest invitations are limited to a maximum of 2 visits per guest.',
                'sort_order' => 10,
            ],
            [
                'rule_number' => 11,
                'rule_text' => 'Subscription contract fees are strictly non-refundable under any circumstances.',
                'sort_order' => 11,
            ],
            [
                'rule_number' => 12,
                'rule_text' => 'Any remaining unpaid balance must be settled within 5 days from the contract start date.',
                'sort_order' => 12,
            ],
            [
                'rule_number' => 13,
                'rule_text' => 'Children are strictly not allowed inside the gym facilities.',
                'sort_order' => 13,
            ],
            [
                'rule_number' => 14,
                'rule_text' => 'Late payment of remaining balances beyond the agreed date will result in adjusting the subscription duration according to the amount paid without prior notice.',
                'sort_order' => 14,
            ],
            [
                'rule_number' => 15,
                'rule_text' => 'Subscription transfer to another person is allowed within 15 days of contract start date subject to a 10% administrative fee.',
                'sort_order' => 15,
            ],
            [
                'rule_number' => 16,
                'rule_text' => 'Subscription duration modifications are only allowed for plan upgrades within 1 week (7 days) from the contract start date.',
                'sort_order' => 16,
            ],
        ];

        foreach ($rules as $ruleData) {
            GymRule::updateOrCreate(
                ['rule_number' => $ruleData['rule_number']],
                $ruleData
            );
        }
    }
}
