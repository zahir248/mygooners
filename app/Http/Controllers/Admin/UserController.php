<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Sample users for admin management
        $users = collect([
            (object) [
                'id' => 1,
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'role' => 'user',
                'is_verified' => true,
                'trust_score' => 4.8,
                'status' => 'active',
                'services_count' => 2,
                'last_login' => now()->subHours(2),
                'created_at' => now()->subMonths(6)
            ],
            (object) [
                'id' => 2,
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'role' => 'user',
                'is_verified' => true,
                'trust_score' => 4.6,
                'status' => 'active',
                'services_count' => 1,
                'last_login' => now()->subHours(5),
                'created_at' => now()->subMonths(3)
            ],
            (object) [
                'id' => 3,
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'role' => 'user',
                'is_verified' => false,
                'trust_score' => 4.2,
                'status' => 'pending',
                'services_count' => 1,
                'last_login' => now()->subDays(2),
                'created_at' => now()->subWeeks(2)
            ],
            (object) [
                'id' => 4,
                'name' => 'Admin User',
                'email' => 'admin@mygooners.com',
                'role' => 'admin',
                'is_verified' => true,
                'trust_score' => 5.0,
                'status' => 'active',
                'services_count' => 0,
                'last_login' => now()->subMinutes(30),
                'created_at' => now()->subYear()
            ],
        ]);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        // Sample user data
        $user = (object) [
            'id' => $id,
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '07123456789',
            'bio' => 'Former Arsenal youth coach with 10 years experience...',
            'location' => 'North London',
            'role' => 'user',
            'is_verified' => true,
            'trust_score' => 4.8,
            'status' => 'active',
            'services_count' => 2,
            'last_login' => now()->subHours(2),
            'created_at' => now()->subMonths(6),
            'profile_image' => null
        ];

        $userServices = collect([
            (object) [
                'title' => 'Football Coaching for Kids',
                'category' => 'Coaching',
                'status' => 'active',
                'views_count' => 234,
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'title' => 'Match Day Transport Service',
                'category' => 'Transport',
                'status' => 'active',
                'views_count' => 189,
                'created_at' => now()->subDays(3)
            ],
        ]);

        return view('admin.users.show', compact('user', 'userServices'));
    }

    public function verify($id)
    {
        // Here you would normally update the database
        // User::findOrFail($id)->update(['is_verified' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User verified successfully!');
    }

    public function suspend($id)
    {
        // Here you would normally update the database
        // User::findOrFail($id)->update(['status' => 'suspended']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User suspended successfully!');
    }

    public function activate($id)
    {
        // Here you would normally update the database
        // User::findOrFail($id)->update(['status' => 'active']);

        return redirect()->route('admin.users.index')
            ->with('success', 'User activated successfully!');
    }

    public function destroy($id)
    {
        // Here you would normally delete from database
        // User::findOrFail($id)->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }
} 