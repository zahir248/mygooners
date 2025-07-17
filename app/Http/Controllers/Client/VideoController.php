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
        
        // Start with published videos query
        $query = Video::published();
        
        // Filter by category if provided
        if ($category) {
            // Convert URL-friendly category back to original format
            $categoryMap = [
                'match-highlights' => 'Match Highlights',
                'player-interviews' => 'Player Interviews',
                'manager-press-conferences' => 'Manager Press Conferences',
                'behind-the-scenes' => 'Behind the Scenes',
                'training-sessions' => 'Training Sessions',
                'fan-content' => 'Fan Content',
                'analysis' => 'Analysis',
                'news' => 'News'
            ];
            
            $originalCategory = $categoryMap[$category] ?? $category;
            $query->where('category', $originalCategory);
        }
        
        // Filter by search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }
        
        // Get videos with pagination
        $videos = $query->orderBy('is_featured', 'desc')
                       ->orderBy('published_at', 'desc')
                       ->paginate(12);
        
        // Get categories for filter
        $categories = Video::published()
                          ->distinct()
                          ->pluck('category')
                          ->filter()
                          ->values();
        
        return view('client.videos.index', compact('videos', 'categories', 'category', 'search'));
    }

    public function show($slug)
    {
        // Find the video by slug
        $video = Video::published()->where('slug', $slug)->firstOrFail();
        
        // Increment view count
        $video->increment('views_count');
        
        // Get related videos (same category, excluding current video)
        $relatedVideos = Video::published()
                             ->where('id', '!=', $video->id)
                             ->where('category', $video->category)
                             ->orderBy('published_at', 'desc')
                             ->limit(3)
                             ->get();
        
        // If not enough related videos in same category, get recent videos
        if ($relatedVideos->count() < 3) {
            $additionalVideos = Video::published()
                                   ->where('id', '!=', $video->id)
                                   ->whereNotIn('id', $relatedVideos->pluck('id'))
                                   ->orderBy('published_at', 'desc')
                                   ->limit(3 - $relatedVideos->count())
                                   ->get();
            
            $relatedVideos = $relatedVideos->merge($additionalVideos);
        }
        
        return view('client.videos.show', compact('video', 'relatedVideos'));
    }
} 