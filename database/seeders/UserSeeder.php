<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mygooners.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'trust_score' => 5.0,
            'is_verified' => true,
            'status' => 'active',
            'bio' => 'Super Administrator of MyGooners platform',
            'location' => 'London',
            'phone' => '07123456789',
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@mygooners.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'trust_score' => 4.8,
            'is_verified' => true,
            'status' => 'active',
            'bio' => 'Administrator of MyGooners platform',
            'location' => 'Manchester',
            'phone' => '07123456790',
        ]);

        // Create regular users with different statuses
        $users = [
            [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'role' => 'user',
                'trust_score' => 4.8,
                'is_verified' => true,
                'status' => 'active',
                'bio' => 'Former Arsenal youth coach with 10 years experience',
                'location' => 'North London',
                'phone' => '07123456791',
                'last_login' => now()->subHours(2),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'role' => 'user',
                'trust_score' => 4.6,
                'is_verified' => true,
                'status' => 'active',
                'bio' => 'Football equipment supplier and Arsenal fan',
                'location' => 'South London',
                'phone' => '07123456792',
                'last_login' => now()->subHours(5),
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'role' => 'user',
                'trust_score' => 4.2,
                'is_verified' => false,
                'status' => 'pending',
                'bio' => 'New member looking to provide transport services',
                'location' => 'East London',
                'phone' => '07123456793',
                'last_login' => now()->subDays(2),
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'role' => 'user',
                'trust_score' => 3.8,
                'is_verified' => true,
                'status' => 'suspended',
                'bio' => 'Suspended user for policy violation',
                'location' => 'West London',
                'phone' => '07123456794',
                'last_login' => now()->subWeeks(1),
            ],
            [
                'name' => 'David Brown',
                'email' => 'david@example.com',
                'role' => 'user',
                'trust_score' => 4.5,
                'is_verified' => false,
                'status' => 'pending',
                'bio' => 'Photographer specializing in match day photos',
                'location' => 'Central London',
                'phone' => '07123456795',
                'last_login' => null,
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa@example.com',
                'role' => 'user',
                'trust_score' => 4.9,
                'is_verified' => true,
                'status' => 'active',
                'bio' => 'Professional football coach and Arsenal season ticket holder',
                'location' => 'Islington',
                'phone' => '07123456796',
                'last_login' => now()->subMinutes(30),
            ],
            [
                'name' => 'Tom Roberts',
                'email' => 'tom@example.com',
                'role' => 'user',
                'trust_score' => 4.1,
                'is_verified' => true,
                'status' => 'active',
                'bio' => 'Match day catering service provider',
                'location' => 'Holloway',
                'phone' => '07123456797',
                'last_login' => now()->subDays(1),
            ],
            [
                'name' => 'Rachel Green',
                'email' => 'rachel@example.com',
                'role' => 'user',
                'trust_score' => 4.3,
                'is_verified' => false,
                'status' => 'pending',
                'bio' => 'New member offering merchandise services',
                'location' => 'Camden',
                'phone' => '07123456798',
                'last_login' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'password' => Hash::make('password'),
            ]));
        }
    }
}
