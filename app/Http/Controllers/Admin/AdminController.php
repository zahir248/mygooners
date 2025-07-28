<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Service;
use App\Models\Product;
use App\Models\Video;
use App\Models\User;
use App\Models\ProductReview;
use App\Models\ServiceReview;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Enhanced dashboard statistics
        $stats = [
            'total_users' => User::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'active_services' => Service::where('status', 'approved')->count(),
            'total_products' => Product::count(),
            'published_videos' => Video::where('status', 'published')->count(),
            'pending_services' => Service::where('status', 'pending')->count(),
            'pending_sellers' => User::where('seller_status', 'pending')->count(),
            'total_reviews' => ProductReview::count() + ServiceReview::count(),
            'new_users_this_month' => User::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'published_articles_this_month' => Article::where('status', 'published')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
        ];

        // Performance metrics
        $performance = [
            'user_growth' => $this->calculateUserGrowth(),
            'content_growth' => $this->calculateContentGrowth(),
            'engagement_rate' => $this->calculateEngagementRate(),
            'top_performing_content' => $this->getTopPerformingContent(),
        ];

        // Recent activity from actual database records
        $recentActivity = $this->getRecentActivity();

        // Pending items that need attention
        $pendingItems = [
            'services' => Service::where('status', 'pending')->latest()->take(5)->get(),
            'users' => User::where('status', 'pending')->latest()->take(5)->get(),
        ];

        // Monthly statistics for charts
        $monthlyStats = $this->getMonthlyStatistics();

        return view('admin.dashboard', compact(
            'stats', 
            'recentActivity', 
            'pendingItems', 
            'performance',
            'monthlyStats'
        ));
    }

    private function calculateUserGrowth()
    {
        $currentMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonth = User::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function calculateContentGrowth()
    {
        $currentMonth = Article::where('status', 'published')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonth = Article::where('status', 'published')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])->count();

        if ($lastMonth == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    private function calculateEngagementRate()
    {
        $totalReviews = ProductReview::count() + ServiceReview::count();
        $totalUsers = User::count();
        
        if ($totalUsers == 0) return 0;
        
        return round(($totalReviews / $totalUsers) * 100, 1);
    }

    private function getTopPerformingContent()
    {
        // Get top articles by views (if you have a views column)
        $topArticles = Article::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Get top products by reviews
        $topProducts = Product::withCount('reviews')
            ->where('status', 'approved')
            ->orderBy('reviews_count', 'desc')
            ->take(3)
            ->get();

        return [
            'articles' => $topArticles,
            'products' => $topProducts
        ];
    }

    private function getRecentActivity()
    {
        $activities = collect();

        // Recent articles
        $recentArticles = Article::latest()
            ->take(5)
            ->get()
            ->map(function ($article) {
                return [
                    'type' => 'article',
                    'title' => $article->title,
                    'description' => "Artikel baru: {$article->title}",
                    'time' => $article->created_at->diffForHumans(),
                    'user' => 'Admin',
                    'color' => 'green',
                    'icon' => 'document-text',
                    'url' => route('admin.articles.edit', $article->id)
                ];
            });

        // Recent services
        $recentServices = Service::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($service) {
                return [
                    'type' => 'service',
                    'title' => $service->title,
                    'description' => "Perkhidmatan baru: {$service->title}",
                    'time' => $service->created_at->diffForHumans(),
                    'user' => $service->user->name ?? 'System',
                    'color' => 'yellow',
                    'icon' => 'briefcase',
                    'url' => route('admin.services.edit', $service->id)
                ];
            });

        // Recent products
        $recentProducts = Product::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'title' => $product->name,
                    'description' => "Produk baru: {$product->name}",
                    'time' => $product->created_at->diffForHumans(),
                    'user' => $product->user->name ?? 'Admin',
                    'color' => 'red',
                    'icon' => 'shopping-bag',
                    'url' => route('admin.products.edit', $product->id)
                ];
            });

        // Recent users
        $recentUsers = User::latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'title' => $user->name,
                    'description' => "Pengguna baru: {$user->name}",
                    'time' => $user->created_at->diffForHumans(),
                    'user' => 'System',
                    'color' => 'blue',
                    'icon' => 'user',
                    'url' => route('admin.users.show', $user->id)
                ];
            });

        // Recent reviews
        $recentReviews = ProductReview::with(['product', 'user'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($review) {
                return [
                    'type' => 'review',
                    'title' => $review->product->name,
                    'description' => "Ulasan baru untuk: {$review->product->name}",
                    'time' => $review->created_at->diffForHumans(),
                    'user' => $review->user->name ?? 'Anonymous',
                    'color' => 'purple',
                    'icon' => 'star',
                    'url' => route('admin.products.show', $review->product->id)
                ];
            });

        // Merge and sort by creation time
        return $activities->merge($recentArticles)
            ->merge($recentServices)
            ->merge($recentProducts)
            ->merge($recentUsers)
            ->merge($recentReviews)
            ->sortByDesc('time')
            ->take(10)
            ->values();
    }

    private function getMonthlyStatistics()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'articles' => Article::where('status', 'published')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'services' => Service::where('status', 'approved')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'products' => Product::where('status', 'approved')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }

        return $months;
    }

    // AJAX endpoint for real-time updates
    public function getDashboardStats()
    {
        $stats = [
            'total_users' => User::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'active_services' => Service::where('status', 'approved')->count(),
            'total_products' => Product::count(),
            'published_videos' => Video::where('status', 'published')->count(),
            'pending_services' => Service::where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }
} 