<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $location = $request->get('location');
        $search = $request->get('search');
        
        // Sample services data
        $services = collect([
            (object) [
                'id' => 1,
                'title' => 'Football Coaching for Kids',
                'slug' => 'football-coaching-for-kids',
                'description' => 'Professional football coaching sessions for children aged 6-16. Former Arsenal youth coach with 10 years experience. Individual and group sessions available.',
                'location' => 'North London',
                'pricing' => '£25/hour',
                'category' => 'Coaching',
                'is_verified' => true,
                'trust_score' => 4.8,
                'views_count' => 234,
                'contact_info' => 'coach@example.com',
                'images' => ['https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400&h=300&fit=crop'],
                'user' => (object) [
                    'id' => 1,
                    'name' => 'John Smith',
                    'profile_image' => null,
                    'is_verified' => true
                ],
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Day Transport Service',
                'slug' => 'match-day-transport-service',
                'description' => 'Safe and reliable transport to and from Arsenal matches. Door-to-door service with fellow Gooners. Comfortable 8-seater minibus.',
                'location' => 'Greater London',
                'pricing' => '£15 per person',
                'category' => 'Transport',
                'is_verified' => true,
                'trust_score' => 4.6,
                'views_count' => 189,
                'contact_info' => 'transport@example.com',
                'images' => ['https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=400&h=300&fit=crop'],
                'user' => (object) [
                    'id' => 2,
                    'name' => 'Sarah Johnson',
                    'profile_image' => null,
                    'is_verified' => true
                ],
                'created_at' => now()->subDays(3)
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Memorabilia Authentication',
                'slug' => 'arsenal-memorabilia-authentication',
                'description' => 'Professional authentication service for Arsenal memorabilia, signed items, and vintage merchandise. 15 years experience in sports memorabilia.',
                'location' => 'London',
                'pricing' => '£20 per item',
                'category' => 'Authentication',
                'is_verified' => false,
                'trust_score' => 4.2,
                'views_count' => 156,
                'contact_info' => 'auth@example.com',
                'images' => ['https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=400&h=300&fit=crop'],
                'user' => (object) [
                    'id' => 3,
                    'name' => 'Mike Wilson',
                    'profile_image' => null,
                    'is_verified' => false
                ],
                'created_at' => now()->subDays(7)
            ],
            (object) [
                'id' => 4,
                'title' => 'Arsenal Fan Photography',
                'slug' => 'arsenal-fan-photography',
                'description' => 'Professional photography services for Arsenal fans. Match day photography, family portraits in Arsenal kit, and event coverage.',
                'location' => 'London & Surrounding Areas',
                'pricing' => '£50/hour',
                'category' => 'Photography',
                'is_verified' => true,
                'trust_score' => 4.9,
                'views_count' => 321,
                'contact_info' => 'photo@example.com',
                'images' => ['https://images.unsplash.com/photo-1486286701208-1d58e9338013?w=400&h=300&fit=crop'],
                'user' => (object) [
                    'id' => 4,
                    'name' => 'Emma Davis',
                    'profile_image' => null,
                    'is_verified' => true
                ],
                'created_at' => now()->subDays(2)
            ],
            (object) [
                'id' => 5,
                'title' => 'Arsenal Pub Quiz Hosting',
                'slug' => 'arsenal-pub-quiz-hosting',
                'description' => 'Professional quiz hosting services for Arsenal-themed pub quizzes and fan events. Engaging questions and great atmosphere guaranteed.',
                'location' => 'North London',
                'pricing' => '£80 per event',
                'category' => 'Entertainment',
                'is_verified' => true,
                'trust_score' => 4.4,
                'views_count' => 98,
                'contact_info' => 'quiz@example.com',
                'images' => ['https://images.unsplash.com/photo-1577223625816-7546f30a2b62?w=400&h=300&fit=crop'],
                'user' => (object) [
                    'id' => 5,
                    'name' => 'David Brown',
                    'profile_image' => null,
                    'is_verified' => true
                ],
                'created_at' => now()->subDays(4)
            ]
        ]);

        // Filter by category if provided
        if ($category) {
            $services = $services->filter(function ($service) use ($category) {
                return strtolower($service->category) === strtolower($category);
            });
        }

        // Filter by location if provided
        if ($location) {
            $services = $services->filter(function ($service) use ($location) {
                return str_contains(strtolower($service->location), strtolower($location));
            });
        }

        // Filter by search if provided
        if ($search) {
            $services = $services->filter(function ($service) use ($search) {
                return str_contains(strtolower($service->title), strtolower($search)) ||
                       str_contains(strtolower($service->description), strtolower($search));
            });
        }

        // Get categories for filter
        $categories = [
            'Coaching',
            'Transport',
            'Authentication',
            'Photography',
            'Entertainment',
            'Catering',
            'Cleaning',
            'Tutoring'
        ];

        // Get locations for filter
        $locations = [
            'North London',
            'South London',
            'East London',
            'West London',
            'Greater London',
            'London'
        ];

        return view('client.services.index', compact('services', 'categories', 'locations', 'category', 'location', 'search'));
    }

    public function show($slug)
    {
        // Sample service data
        $service = (object) [
            'id' => 1,
            'title' => 'Football Coaching for Kids',
            'slug' => 'football-coaching-for-kids',
            'description' => 'Professional football coaching sessions for children aged 6-16. Former Arsenal youth coach with 10 years experience. Individual and group sessions available.',
            'location' => 'North London',
            'pricing' => '£25/hour',
            'category' => 'Coaching',
            'is_verified' => true,
            'trust_score' => 4.8,
            'views_count' => 234,
            'contact_info' => 'coach@example.com',
            'images' => [
                'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=600&fit=crop'
            ],
            'tags' => ['coaching', 'kids', 'football', 'arsenal', 'youth'],
            'user' => (object) [
                'id' => 1,
                'name' => 'John Smith',
                'profile_image' => null,
                'is_verified' => true,
                'bio' => 'Former Arsenal youth coach with 10 years experience coaching children. FA Level 2 qualified.',
                'location' => 'North London',
                'trust_score' => 4.8
            ],
            'created_at' => now()->subDays(5)
        ];

        // Sample reviews
        $reviews = collect([
            (object) [
                'id' => 1,
                'rating' => 5,
                'comment' => 'John is an excellent coach! My son has improved so much in just a few sessions. Highly recommend!',
                'user' => (object) ['name' => 'Lisa Thompson'],
                'created_at' => now()->subWeeks(2)
            ],
            (object) [
                'id' => 2,
                'rating' => 5,
                'comment' => 'Professional and patient coach. Great with kids and really knows his stuff about football.',
                'user' => (object) ['name' => 'Mark Johnson'],
                'created_at' => now()->subWeeks(3)
            ],
            (object) [
                'id' => 3,
                'rating' => 4,
                'comment' => 'Good coaching sessions, kids enjoyed them. Would book again.',
                'user' => (object) ['name' => 'Sarah Williams'],
                'created_at' => now()->subMonth()
            ]
        ]);

        // Related services
        $relatedServices = collect([
            (object) [
                'id' => 4,
                'title' => 'Arsenal Fan Photography',
                'slug' => 'arsenal-fan-photography',
                'description' => 'Professional photography services for Arsenal fans.',
                'location' => 'London & Surrounding Areas',
                'pricing' => '£50/hour',
                'category' => 'Photography',
                'is_verified' => true,
                'trust_score' => 4.9,
                'images' => ['https://images.unsplash.com/photo-1486286701208-1d58e9338013?w=400&h=300&fit=crop'],
                'user' => (object) ['name' => 'Emma Davis']
            ],
            (object) [
                'id' => 5,
                'title' => 'Arsenal Pub Quiz Hosting',
                'slug' => 'arsenal-pub-quiz-hosting',
                'description' => 'Professional quiz hosting services for Arsenal-themed events.',
                'location' => 'North London',
                'pricing' => '£80 per event',
                'category' => 'Entertainment',
                'is_verified' => true,
                'trust_score' => 4.4,
                'images' => ['https://images.unsplash.com/photo-1577223625816-7546f30a2b62?w=400&h=300&fit=crop'],
                'user' => (object) ['name' => 'David Brown']
            ]
        ]);

        return view('client.services.show', compact('service', 'reviews', 'relatedServices'));
    }
} 