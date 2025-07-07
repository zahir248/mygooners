<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Service;
use App\Models\Product;
use App\Models\Video;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Sample dashboard statistics
        $stats = [
            'total_articles' => 15,
            'total_services' => 8,
            'total_products' => 12,
            'total_videos' => 6,
            'total_users' => 245,
            'pending_services' => 3,
            'low_stock_products' => 2,
            'new_registrations_today' => 5,
        ];

        // Recent activity
        $recentActivity = collect([
            (object) [
                'type' => 'article',
                'title' => 'New article published: Arsenal Transfer News',
                'time' => now()->subMinutes(30),
                'user' => 'Admin'
            ],
            (object) [
                'type' => 'service',
                'title' => 'Service approval needed: Football Coaching',
                'time' => now()->subHours(2),
                'user' => 'John Smith'
            ],
            (object) [
                'type' => 'product',
                'title' => 'Product stock updated: Arsenal Jersey',
                'time' => now()->subHours(4),
                'user' => 'Admin'
            ],
            (object) [
                'type' => 'user',
                'title' => 'New user registered: Sarah Johnson',
                'time' => now()->subHours(6),
                'user' => 'System'
            ],
        ]);

        return view('admin.dashboard', compact('stats', 'recentActivity'));
    }
} 