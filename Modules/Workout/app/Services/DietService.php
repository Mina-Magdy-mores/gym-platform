<?php

namespace Modules\Workout\Services;

use Illuminate\Support\Facades\DB;
use Modules\Workout\Models\DietPlan;

class DietService
{
    /**
     * Create or update a diet plan with meals for a member.
     */
    public function createDietPlan(array $data): DietPlan
    {
        return DB::transaction(function () use ($data) {
            $status = $data['status'] ?? 'active';

            // Check if member already has an existing active diet plan
            $plan = DietPlan::where('user_id', $data['user_id'])
                ->where('status', 'active')
                ->first();

            if ($plan) {
                // In-place update existing diet plan
                $plan->update([
                    'trainer_id' => $data['trainer_id'],
                    'title' => $data['title'],
                    'daily_calories' => $data['daily_calories'] ?? 2000,
                    'protein_grams' => $data['protein_grams'] ?? 150,
                    'carbs_grams' => $data['carbs_grams'] ?? 200,
                    'fats_grams' => $data['fats_grams'] ?? 60,
                    'status' => $status,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Clear old meals to replace with updated ones
                $plan->meals()->delete();
            } else {
                // Create brand new diet plan
                $plan = DietPlan::create([
                    'trainer_id' => $data['trainer_id'],
                    'user_id' => $data['user_id'],
                    'title' => $data['title'],
                    'daily_calories' => $data['daily_calories'] ?? 2000,
                    'protein_grams' => $data['protein_grams'] ?? 150,
                    'carbs_grams' => $data['carbs_grams'] ?? 200,
                    'fats_grams' => $data['fats_grams'] ?? 60,
                    'status' => $status,
                    'notes' => $data['notes'] ?? null,
                ]);
            }

            if (!empty($data['meals']) && is_array($data['meals'])) {
                foreach ($data['meals'] as $meal) {
                    if (!empty($meal['meal_name']) && !empty($meal['food_items'])) {
                        $plan->meals()->create([
                            'meal_name' => $meal['meal_name'],
                            'meal_time' => $meal['meal_time'] ?? null,
                            'food_items' => $meal['food_items'],
                            'calories' => $meal['calories'] ?? 0,
                        ]);
                    }
                }
            }

            return $plan->load('meals');
        });
    }

    /**
     * Get active diet plan for a member.
     */
    public function getMemberActiveDietPlan(int $userId): ?DietPlan
    {
        return DietPlan::with('meals', 'trainer')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->latest()
            ->first();
    }
}