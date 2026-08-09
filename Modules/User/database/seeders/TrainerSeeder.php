<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Wallet\Models\TrainerWallet;
use Spatie\Permission\Models\Role;

class TrainerSeeder extends Seeder
{
    /**
     * Seed certified gym trainers with auto-created wallets.
     */
    public function run(): void
    {
        // Ensure 'trainer' role exists seamlessly
        $trainerRole = Role::firstOrCreate(['name' => 'trainer']);

        $trainersData = [
            [
                'name' => 'Captain Ahmed Hassan',
                'email' => 'ahmed.trainer@fitclub.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Captain Mohamed Youssef',
                'email' => 'youssef.trainer@fitclub.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Coach Sarah Mahmoud',
                'email' => 'sarah.trainer@fitclub.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Coach Omar Farouk',
                'email' => 'omar.trainer@fitclub.com',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($trainersData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Assign Spatie Trainer Role
            if (! $user->hasRole('trainer')) {
                $user->assignRole($trainerRole);
            }

            // Auto-initialize Trainer Wallet if not exists
            TrainerWallet::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'balance' => 0.00,
                'total_earned' => 0.00,
            ]);
        }
    }
}