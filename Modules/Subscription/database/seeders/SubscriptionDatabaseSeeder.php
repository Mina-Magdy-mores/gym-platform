<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionDatabaseSeeder extends Seeder
{
    /**
     * Seed subscription plans and gym schedules from real image data in 100% English.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Seed Real Egyptian Subscription Plans in English
        $plans = [
            [
                'name' => '3 Month Offer',
                'slug' => '3-months-offer',
                'description' => 'Standard 3 Months Membership Package with Full SPA Access',
                'duration_months' => 3,
                'price' => 1750.00,
                'currency' => 'EGP',
                'free_days' => 15,
                'freeze_days' => 15,
                'invitations_count' => 10,
                'inbody_scans' => 3,
                'pt_sessions' => 3,
                'kickboxing_classes' => 2,
                'nutrition_plans' => 1,
                'spa_access' => true,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '6 Month Offer',
                'slug' => '6-months-offer',
                'description' => 'Silver 6 Months Saver Membership Package',
                'duration_months' => 6,
                'price' => 2500.00,
                'currency' => 'EGP',
                'free_days' => 30,
                'freeze_days' => 30,
                'invitations_count' => 20,
                'inbody_scans' => 6,
                'pt_sessions' => 3,
                'kickboxing_classes' => 3,
                'nutrition_plans' => 2,
                'spa_access' => true,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '14 Month VIP Offer',
                'slug' => '14-months-vip-offer',
                'description' => 'Gold 14 Months VIP Extended Membership Package',
                'duration_months' => 14,
                'price' => 3500.00,
                'currency' => 'EGP',
                'free_days' => 60,
                'freeze_days' => 60,
                'invitations_count' => 30,
                'inbody_scans' => 8,
                'pt_sessions' => 4,
                'kickboxing_classes' => 4,
                'nutrition_plans' => 3,
                'spa_access' => true,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '15 Month Platinum VIP',
                'slug' => '15-months-platinum-vip',
                'description' => 'Platinum 15 Months Ultimate VIP Membership Package',
                'duration_months' => 15,
                'price' => 4000.00,
                'currency' => 'EGP',
                'free_days' => 90,
                'freeze_days' => 90,
                'invitations_count' => 35,
                'inbody_scans' => 10,
                'pt_sessions' => 4,
                'kickboxing_classes' => 4,
                'nutrition_plans' => 4,
                'spa_access' => true,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('subscription_plans')->truncate();
        DB::table('subscription_plans')->insert($plans);

        // 2. Seed Real Gym Operating Schedules (Men & Women) in English
        $schedules = [
            // Men Schedules
            [
                'target_gender' => 'men',
                'days_label' => 'Saturday, Monday, and Wednesday',
                'start_time' => '08:00:00',
                'end_time' => '13:00:00',
                'time_label' => '8:00 AM - 1:00 PM',
                'is_off_day' => false,
                'notes' => 'Men Morning Shift',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_gender' => 'men',
                'days_label' => 'Sunday, Tuesday, and Thursday',
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'time_label' => '1:00 PM - 5:00 PM & 8:00 PM - 8:00 AM',
                'is_off_day' => false,
                'notes' => 'Men Morning & Evening Shifts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_gender' => 'men',
                'days_label' => 'Friday',
                'start_time' => '15:00:00',
                'end_time' => '08:00:00',
                'time_label' => '3:00 PM - 8:00 AM',
                'is_off_day' => false,
                'notes' => 'Men Friday Operating Hours',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Women Schedules
            [
                'target_gender' => 'women',
                'days_label' => 'Saturday to Thursday',
                'start_time' => '08:00:00',
                'end_time' => '13:00:00',
                'time_label' => '8:00 AM - 1:00 PM',
                'is_off_day' => false,
                'notes' => 'Women Morning Shift',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_gender' => 'women',
                'days_label' => 'Sunday, Tuesday, and Thursday',
                'start_time' => '17:00:00',
                'end_time' => '20:00:00',
                'time_label' => '8:00 AM - 1:00 PM & 5:00 PM - 8:00 PM',
                'is_off_day' => false,
                'notes' => 'Women Evening Shift',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_gender' => 'women',
                'days_label' => 'Friday',
                'start_time' => '12:00:00',
                'end_time' => '15:00:00',
                'time_label' => '12:00 PM - 3:00 PM',
                'is_off_day' => false,
                'notes' => 'Women Friday Hours',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'target_gender' => 'women',
                'days_label' => 'Monday',
                'start_time' => null,
                'end_time' => null,
                'time_label' => 'Ladies Day Off',
                'is_off_day' => true,
                'notes' => 'Monday is a Day Off for Ladies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('gym_schedules')->truncate();
        DB::table('gym_schedules')->insert($schedules);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}