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
        // Real dashboard statistics
        $stats = [
            'total_users' => User::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'active_services' => Service::where('status', 'approved')->count(),
            'total_products' => Product::count(),
            'published_videos' => Video::where('status', 'published')->count(),
            'pending_services' => Service::where('status', 'pending')->count(),
            'pending_products' => Product::where('status', 'pending')->count(),
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