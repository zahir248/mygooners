<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        
        // Sample videos data
        $videos = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Weekly Podcast: Transfer Window Analysis',
                'slug' => 'arsenal-weekly-podcast-transfer-window-analysis',
                'description' => 'Join us as we dive deep into Arsenal\'s transfer activities and what it means for the upcoming season. We discuss the latest signings, potential targets, and tactical analysis.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '45:32',
                'category' => 'Weekly Podcast',
                'tags' => ['arsenal', 'podcast', 'transfer', 'analysis', 'weekly'],
                'is_featured' => true,
                'published_at' => now()->subDays(2),
                'views_count' => 3421,
                'created_at' => now()->subDays(2)
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Analysis: Arsenal vs Liverpool Breakdown',
                'slug' => 'match-analysis-arsenal-vs-liverpool-breakdown',
                'description' => 'Detailed tactical breakdown of Arsenal\'s performance against Liverpool. We analyze key moments, player performances, and tactical decisions.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '28:15',
                'category' => 'Match Analysis',
                'tags' => ['arsenal', 'liverpool', 'match', 'analysis', 'tactical'],
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'views_count' => 2156,
                'created_at' => now()->subDays(5)
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Legends: Thierry Henry Career Special',
                'slug' => 'arsenal-legends-thierry-henry-career-special',
                'description' => 'A special episode dedicated to Arsenal legend Thierry Henry. We look back at his incredible career, best goals, and lasting impact on the club.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '52:18',
                'category' => 'Legends',
                'tags' => ['arsenal', 'henry', 'legend', 'career', 'special'],
                'is_featured' => true,
                'published_at' => now()->subWeek(),
                'views_count' => 4832,
                'created_at' => now()->subWeek()
            ],
            (object) [
                'id' => 4,
                'title' => 'Arsenal Youth Academy: Future Stars',
                'slug' => 'arsenal-youth-academy-future-stars',
                'description' => 'Meet the next generation of Arsenal talent coming through the youth academy. We profile promising youngsters and their development journey.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '35:44',
                'category' => 'Youth Academy',
                'tags' => ['arsenal', 'youth', 'academy', 'future', 'stars'],
                'is_featured' => false,
                'published_at' => now()->subDays(10),
                'views_count' => 1876,
                'created_at' => now()->subDays(10)
            ],
            (object) [
                'id' => 5,
                'title' => 'Arsenal Women: Season Review and Outlook',
                'slug' => 'arsenal-women-season-review-and-outlook',
                'description' => 'Comprehensive review of Arsenal Women\'s season with analysis of key performances, achievements, and what to expect next season.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '41:27',
                'category' => 'Women\'s Team',
                'tags' => ['arsenal', 'women', 'season', 'review', 'outlook'],
                'is_featured' => false,
                'published_at' => now()->subDays(8),
                'views_count' => 2543,
                'created_at' => now()->subDays(8)
            ],
            (object) [
                'id' => 6,
                'title' => 'Arsenal Fan Zone: Matchday Experience',
                'slug' => 'arsenal-fan-zone-matchday-experience',
                'description' => 'Join us for a behind-the-scenes look at the Arsenal matchday experience. From pre-match rituals to post-match celebrations.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '33:12',
                'category' => 'Fan Zone',
                'tags' => ['arsenal', 'fan', 'matchday', 'experience', 'behind-scenes'],
                'is_featured' => false,
                'published_at' => now()->subDays(3),
                'views_count' => 1654,
                'created_at' => now()->subDays(3)
            ]
        ]);

        // Filter by category if provided
        if ($category) {
            $videos = $videos->filter(function ($video) use ($category) {
                return strtolower($video->category) === strtolower($category);
            });
        }

        // Filter by search if provided
        if ($search) {
            $videos = $videos->filter(function ($video) use ($search) {
                return str_contains(strtolower($video->title), strtolower($search)) ||
                       str_contains(strtolower($video->description), strtolower($search));
            });
        }

        // Get categories for filter
        $categories = [
            'Weekly Podcast',
            'Match Analysis',
            'Legends',
            'Youth Academy',
            'Women\'s Team',
            'Fan Zone',
            'Transfer Talk',
            'Historical'
        ];

        return view('client.videos.index', compact('videos', 'categories', 'category', 'search'));
    }

    public function show($slug)
    {
        // Sample video data
        $video = (object) [
            'id' => 1,
            'title' => 'Arsenal Weekly Podcast: Transfer Window Analysis',
            'slug' => 'arsenal-weekly-podcast-transfer-window-analysis',
            'description' => 'Join us as we dive deep into Arsenal\'s transfer activities and what it means for the upcoming season. We discuss the latest signings, potential targets, and tactical analysis.
            
In this episode, we cover:
- Latest transfer news and rumors
- Analysis of new signings
- Potential targets and their fit
- Squad depth and tactical flexibility
- Fan reactions and expectations

Our expert panel breaks down each transfer and discusses how they will impact Arsenal\'s performance in the coming season.',
            'youtube_video_id' => 'dQw4w9WgXcQ',
            'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'duration' => '45:32',
            'category' => 'Weekly Podcast',
            'tags' => ['arsenal', 'podcast', 'transfer', 'analysis', 'weekly'],
            'is_featured' => true,
            'published_at' => now()->subDays(2),
            'views_count' => 3421,
            'created_at' => now()->subDays(2)
        ];

        // Related videos
        $relatedVideos = collect([
            (object) [
                'id' => 2,
                'title' => 'Match Analysis: Arsenal vs Liverpool Breakdown',
                'slug' => 'match-analysis-arsenal-vs-liverpool-breakdown',
                'description' => 'Detailed tactical breakdown of Arsenal\'s performance against Liverpool.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '28:15',
                'category' => 'Match Analysis',
                'published_at' => now()->subDays(5),
                'views_count' => 2156
            ],
            (object) [
                'id' => 3,
                'title' => 'Arsenal Legends: Thierry Henry Career Special',
                'slug' => 'arsenal-legends-thierry-henry-career-special',
                'description' => 'A special episode dedicated to Arsenal legend Thierry Henry.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '52:18',
                'category' => 'Legends',
                'published_at' => now()->subWeek(),
                'views_count' => 4832
            ],
            (object) [
                'id' => 4,
                'title' => 'Arsenal Youth Academy: Future Stars',
                'slug' => 'arsenal-youth-academy-future-stars',
                'description' => 'Meet the next generation of Arsenal talent coming through the youth academy.',
                'youtube_video_id' => 'dQw4w9WgXcQ',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '35:44',
                'category' => 'Youth Academy',
                'published_at' => now()->subDays(10),
                'views_count' => 1876
            ]
        ]);

        return view('client.videos.show', compact('video', 'relatedVideos'));
    }
} 