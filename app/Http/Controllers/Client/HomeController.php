<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Video;
use App\Models\Service;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Sample data for homepage
        $featuredArticles = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Signs New Striker in Record Deal',
                'slug' => 'arsenal-signs-new-striker-record-deal',
                'excerpt' => 'Arsenal has completed the signing of a world-class striker in what is being called the biggest transfer of the season.',
                'cover_image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=400&fit=crop',
                'category' => 'Transfer News',
                'published_at' => now()->subHours(2),
                'views_count' => 1543,
                'is_featured' => true
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Report: Arsenal vs Manchester United',
                'slug' => 'match-report-arsenal-vs-manchester-united',
                'excerpt' => 'A thrilling encounter at the Emirates Stadium as Arsenal secured a crucial victory against Manchester United.',
                'cover_image' => 'https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=800&h=400&fit=crop',
                'category' => 'Match Reports',
                'published_at' => now()->subHours(6),
                'views_count' => 2156,
                'is_featured' => true
            ],
            (object) [
                'id' => 3,
                'title' => 'Training Ground Updates and Player Fitness',
                'slug' => 'training-ground-updates-player-fitness',
                'excerpt' => 'Latest updates from the Arsenal training ground including player fitness reports and tactical preparations.',
                'cover_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=400&fit=crop',
                'category' => 'Training',
                'published_at' => now()->subHours(12),
                'views_count' => 876,
                'is_featured' => false
            ]
        ]);

        $featuredVideo = (object) [
            'id' => 1,
            'title' => 'Arsenal Weekly Podcast: Transfer Window Analysis',
            'slug' => 'arsenal-weekly-podcast-transfer-window-analysis',
            'description' => 'Join us as we dive deep into Arsenal\'s transfer activities and what it means for the upcoming season.',
            'youtube_video_id' => 'dQw4w9WgXcQ',
            'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'duration' => '45:32',
            'category' => 'Weekly Podcast',
            'published_at' => now()->subDays(2),
            'views_count' => 3421
        ];

        $newServices = collect([
            (object) [
                'id' => 1,
                'title' => 'Football Coaching for Kids',
                'slug' => 'football-coaching-for-kids',
                'description' => 'Professional football coaching sessions for children aged 6-16. Former Arsenal youth coach with 10 years experience.',
                'location' => 'North London',
                'pricing' => '£25/hour',
                'category' => 'Coaching',
                'is_verified' => true,
                'trust_score' => 4.8,
                'user' => (object) ['name' => 'John Smith', 'profile_image' => null]
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Day Transport Service',
                'slug' => 'match-day-transport-service',
                'description' => 'Safe and reliable transport to and from Arsenal matches. Door-to-door service with fellow Gooners.',
                'location' => 'Greater London',
                'pricing' => '£15 per person',
                'category' => 'Transport',
                'is_verified' => true,
                'trust_score' => 4.6,
                'user' => (object) ['name' => 'Sarah Johnson', 'profile_image' => null]
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Memorabilia Authentication',
                'slug' => 'arsenal-memorabilia-authentication',
                'description' => 'Professional authentication service for Arsenal memorabilia, signed items, and vintage merchandise.',
                'location' => 'London',
                'pricing' => '£20 per item',
                'category' => 'Authentication',
                'is_verified' => false,
                'trust_score' => 4.2,
                'user' => (object) ['name' => 'Mike Wilson', 'profile_image' => null]
            ]
        ]);

        $popularProducts = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Home Jersey 2024/25',
                'slug' => 'arsenal-home-jersey-2024-25',
                'description' => 'Official Arsenal home jersey for the 2024/25 season. Available in all sizes.',
                'price' => 75.00,
                'sale_price' => 65.00,
                'images' => ['https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=400&fit=crop'],
                'category' => 'Jerseys',
                'is_featured' => true,
                'stock_quantity' => 15
            ],
            (object) [
                'id' => 2,
                'title' => 'Arsenal Scarf - Classic Design',
                'slug' => 'arsenal-scarf-classic-design',
                'description' => 'Classic Arsenal scarf with traditional design. Perfect for match days and cold weather.',
                'price' => 25.00,
                'sale_price' => null,
                'images' => ['https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=400&h=400&fit=crop'],
                'category' => 'Accessories',
                'is_featured' => false,
                'stock_quantity' => 8
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Stadium Print - Framed',
                'slug' => 'arsenal-stadium-print-framed',
                'description' => 'Beautiful framed print of the Emirates Stadium. Perfect for any Arsenal fan\'s home or office.',
                'price' => 45.00,
                'sale_price' => 35.00,
                'images' => ['https://images.unsplash.com/photo-1579952363873-27d3bfad9c0d?w=400&h=400&fit=crop'],
                'category' => 'Art & Prints',
                'is_featured' => true,
                'stock_quantity' => 3
            ]
        ]);

        return view('client.home', compact('featuredArticles', 'featuredVideo', 'newServices', 'popularProducts'));
    }
} 