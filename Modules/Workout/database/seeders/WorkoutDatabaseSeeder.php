<?php

namespace Modules\Workout\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Workout\Models\DietMeal;
use Modules\Workout\Models\DietPlan;
use Modules\Workout\Models\RoutineExercise;
use Modules\Workout\Models\WorkoutRoutine;

class WorkoutDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainer = User::role('trainer')->first() ?? User::role('admin')->first();
        $member = User::where('email', 'member@fitclub.com')->first();

        if (!$trainer || !$member) {
            return;
        }

        // 1. Seed Demo 3-Day Hypertrophy Routine
        $routine = WorkoutRoutine::firstOrCreate(
            [
                'user_id' => $member->id,
                'title' => 'FIT CLUB 3-Day Hypertrophy Split',
            ],
            [
                'trainer_id' => $trainer->id,
                'goal' => 'Muscle Hypertrophy & Strength',
                'status' => 'active',
                'notes' => 'Focus on progressive overload and proper 2-second eccentric contraction tempo.',
            ]
        );

        if ($routine->exercises()->count() === 0) {
            $exercises = [
                // Day 1: Chest & Triceps (Push)
                ['day_name' => 'Day 1 - Push (Chest & Triceps)', 'exercise_name' => 'Barbell Bench Press', 'target_muscle' => 'Chest', 'sets' => 4, 'reps' => '8-10', 'rest_seconds' => 90, 'notes' => 'Full range of motion, control eccentric phase'],
                ['day_name' => 'Day 1 - Push (Chest & Triceps)', 'exercise_name' => 'Incline Dumbbell Press', 'target_muscle' => 'Upper Chest', 'sets' => 3, 'reps' => '10-12', 'rest_seconds' => 60, 'notes' => '30 degree bench incline angle'],
                ['day_name' => 'Day 1 - Push (Chest & Triceps)', 'exercise_name' => 'Cable Chest Flyes', 'target_muscle' => 'Chest', 'sets' => 3, 'reps' => '12-15', 'rest_seconds' => 60, 'notes' => 'Squeeze at peak contraction for 1 second'],
                ['day_name' => 'Day 1 - Push (Chest & Triceps)', 'exercise_name' => 'Tricep Rope Pushdowns', 'target_muscle' => 'Triceps', 'sets' => 4, 'reps' => '12-15', 'rest_seconds' => 45, 'notes' => 'Keep elbows locked at sides'],

                // Day 2: Back & Biceps (Pull)
                ['day_name' => 'Day 2 - Pull (Back & Biceps)', 'exercise_name' => 'Lat Pulldowns', 'target_muscle' => 'Lats', 'sets' => 4, 'reps' => '8-12', 'rest_seconds' => 90, 'notes' => 'Pull to upper clavicle and drive elbows down'],
                ['day_name' => 'Day 2 - Pull (Back & Biceps)', 'exercise_name' => 'Seated Cable Rows', 'target_muscle' => 'Mid Back', 'sets' => 3, 'reps' => '10-12', 'rest_seconds' => 60, 'notes' => 'Retract shoulder blades fully'],
                ['day_name' => 'Day 2 - Pull (Back & Biceps)', 'exercise_name' => 'Dumbbell Hammer Curls', 'target_muscle' => 'Brachialis & Biceps', 'sets' => 3, 'reps' => '10-12', 'rest_seconds' => 60, 'notes' => 'Strict form, no torso swinging'],

                // Day 3: Legs & Shoulders
                ['day_name' => 'Day 3 - Legs & Shoulders', 'exercise_name' => 'Barbell Back Squats', 'target_muscle' => 'Quads & Glutes', 'sets' => 4, 'reps' => '6-8', 'rest_seconds' => 120, 'notes' => 'Depth parallel or below parallel'],
                ['day_name' => 'Day 3 - Legs & Shoulders', 'exercise_name' => 'Dumbbell Shoulder Overhead Press', 'target_muscle' => 'Anterior Deltoids', 'sets' => 4, 'reps' => '8-10', 'rest_seconds' => 90, 'notes' => 'Controlled descent'],
                ['day_name' => 'Day 3 - Legs & Shoulders', 'exercise_name' => 'Lateral Dumbbell Raises', 'target_muscle' => 'Lateral Deltoids', 'sets' => 4, 'reps' => '12-15', 'rest_seconds' => 45, 'notes' => 'Slight forward lean, raise in scapular plane'],
            ];

            foreach ($exercises as $ex) {
                $ex['workout_routine_id'] = $routine->id;
                RoutineExercise::create($ex);
            }
        }

        // 2. Seed Demo High-Protein Lean Nutrition Plan
        $diet = DietPlan::firstOrCreate(
            [
                'user_id' => $member->id,
                'title' => 'Lean Muscle Hypertrophy Meal Plan',
            ],
            [
                'trainer_id' => $trainer->id,
                'daily_calories' => 2650,
                'protein_grams' => 180,
                'carbs_grams' => 280,
                'fats_grams' => 65,
                'status' => 'active',
                'notes' => 'Stay hydrated with at least 3.5L of water daily. Take creatine 5g post-workout.',
            ]
        );

        if ($diet->meals()->count() === 0) {
            $meals = [
                ['meal_name' => 'Meal 1 - Power Breakfast', 'meal_time' => '08:30', 'food_items' => '4 Whole Eggs + 2 Egg Whites, 80g Rolled Oats with 1 Banana and 1tbsp Honey, 1 Cup Black Coffee', 'calories' => 680],
                ['meal_name' => 'Meal 2 - Pre-Workout Lunch', 'meal_time' => '13:00', 'food_items' => '200g Grilled Chicken Breast, 220g Jasmine Rice, Steamed Broccoli with 1tsp Olive Oil', 'calories' => 750],
                ['meal_name' => 'Meal 3 - Post-Workout Refuel', 'meal_time' => '17:30', 'food_items' => '1 Scoop Whey Protein Isolate, 1 Large Green Apple, 30g Raw Almonds', 'calories' => 380],
                ['meal_name' => 'Meal 4 - Recovery Dinner', 'meal_time' => '21:00', 'food_items' => '200g Lean Beef Steak / Salmon Fillet, 250g Baked Sweet Potato, Mixed Green Salad', 'calories' => 840],
            ];

            foreach ($meals as $m) {
                $m['diet_plan_id'] = $diet->id;
                DietMeal::create($m);
            }
        }
    }
}

