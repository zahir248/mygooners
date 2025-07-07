<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'admin@mygooners.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@mygooners.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'trust_score' => 10.0,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Regular Admin
        User::updateOrCreate(
            ['email' => 'moderator@mygooners.com'],
            [
                'name' => 'Content Moderator',
                'email' => 'moderator@mygooners.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'trust_score' => 8.5,
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Regular User
        User::updateOrCreate(
            ['email' => 'user@mygooners.com'],
            [
                'name' => 'Regular User',
                'email' => 'user@mygooners.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'trust_score' => 5.0,
                'is_verified' => false,
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: admin@mygooners.com / password');
        $this->command->info('Admin: moderator@mygooners.com / password');
        $this->command->info('User: user@mygooners.com / password');
    }
}
