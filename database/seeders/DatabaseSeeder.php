<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Subscription\Database\Seeders\GymRuleSeeder;
use Modules\Subscription\Database\Seeders\SubscriptionDatabaseSeeder;
use Modules\User\Database\Seeders\TrainerSeeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for production and development.
     */
    public function run(): void
    {
        // 1. Reset cached roles & permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create core roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'trainer']);
        Role::firstOrCreate(['name' => 'member']);

        // 3. Create default Master Admin
        $admin = User::firstOrCreate(
            ['email' => 'mina@gym.com'],
            [
                'name' => 'Mina Magdy',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        // 3.1 Create default Demo Member
        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $member = User::firstOrCreate(
            ['email' => 'member@fitclub.com'],
            [
                'name' => 'John Doe (Member)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_blocked' => false,
            ]
        );

        if (! $member->hasRole('member')) {
            $member->assignRole($memberRole);
        }

        // 4. Seed Subscription Plans & Operating Schedules
        $this->call(SubscriptionDatabaseSeeder::class);

        // 5. Seed Gym Rules
        $this->call(GymRuleSeeder::class);

        // 6. Seed Certified Trainers
        $this->call(TrainerSeeder::class);

        // 7. Seed Demo Workout Routines & Nutrition Plans
        $this->call(\Modules\Workout\Database\Seeders\WorkoutDatabaseSeeder::class);
    }
}
