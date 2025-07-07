<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        // Sample services for admin management
        $services = collect([
            (object) [
                'id' => 1,
                'title' => 'Football Coaching for Kids',
                'category' => 'Coaching',
                'location' => 'North London',
                'pricing' => '£25/hour',
                'is_verified' => true,
                'trust_score' => 4.8,
                'status' => 'active',
                'views_count' => 234,
                'user' => (object) ['name' => 'John Smith', 'email' => 'john@example.com'],
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Day Transport Service',
                'category' => 'Transport',
                'location' => 'Greater London',
                'pricing' => '£15 per person',
                'is_verified' => true,
                'trust_score' => 4.6,
                'status' => 'active',
                'views_count' => 189,
                'user' => (object) ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com'],
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Memorabilia Authentication',
                'category' => 'Authentication',
                'location' => 'London',
                'pricing' => '£20 per item',
                'is_verified' => false,
                'trust_score' => 4.2,
                'status' => 'pending',
                'views_count' => 156,
                'user' => (object) ['name' => 'Mike Wilson', 'email' => 'mike@example.com'],
                'created_at' => now()->subDays(1)
            ],
        ]);

        return view('admin.services.index', compact('services'));
    }

    public function show($id)
    {
        // Sample service data
        $service = (object) [
            'id' => $id,
            'title' => 'Football Coaching for Kids',
            'description' => 'Professional football coaching sessions for children aged 6-16...',
            'category' => 'Coaching',
            'location' => 'North London',
            'pricing' => '£25/hour',
            'is_verified' => true,
            'trust_score' => 4.8,
            'status' => 'active',
            'views_count' => 234,
            'contact_info' => 'coach@example.com',
            'images' => ['https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=300&fit=crop'],
            'user' => (object) [
                'name' => 'John Smith',
                'email' => 'john@example.com',
                'phone' => '07123456789',
                'bio' => 'Former Arsenal youth coach with 10 years experience...',
                'is_verified' => true,
                'trust_score' => 4.8
            ],
            'created_at' => now()->subDays(5)
        ];

        return view('admin.services.show', compact('service'));
    }

    public function approve($id)
    {
        // Here you would normally update the database
        // Service::findOrFail($id)->update(['is_verified' => true, 'status' => 'active']);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service approved successfully!');
    }

    public function reject($id)
    {
        // Here you would normally update the database
        // Service::findOrFail($id)->update(['status' => 'rejected']);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service rejected successfully!');
    }

    public function destroy($id)
    {
        // Here you would normally delete from database
        // Service::findOrFail($id)->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }
} 