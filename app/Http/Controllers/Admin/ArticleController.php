<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // Sample articles for admin management
        $articles = collect([
            (object) [
                'id' => 1,
                'title' => 'Arsenal Signs New Striker in Record Deal',
                'slug' => 'arsenal-signs-new-striker-record-deal',
                'category' => 'Transfer News',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 1543,
                'published_at' => now()->subHours(2),
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(2)
            ],
            (object) [
                'id' => 2,
                'title' => 'Match Report: Arsenal vs Manchester United',
                'slug' => 'match-report-arsenal-vs-manchester-united',
                'category' => 'Match Reports',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 2156,
                'published_at' => now()->subHours(6),
                'created_at' => now()->subHours(7),
                'updated_at' => now()->subHours(6)
            ],
            (object) [
                'id' => 3,
                'title' => 'Training Ground Updates',
                'slug' => 'training-ground-updates-player-fitness',
                'category' => 'Training',
                'status' => 'draft',
                'is_featured' => false,
                'views_count' => 0,
                'published_at' => null,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1)
            ],
        ]);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = [
            'Transfer News',
            'Match Reports',
            'Training',
            'Women\'s Team',
            'Transfer Rumours',
            'Analysis',
            'History'
        ];

        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'cover_image' => 'nullable|url',
            'youtube_video_id' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published'
        ]);

        // Here you would normally save to database
        // Article::create($request->all());

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully!');
    }

    public function edit($id)
    {
        // Sample article data
        $article = (object) [
            'id' => $id,
            'title' => 'Arsenal Signs New Striker in Record Deal',
            'slug' => 'arsenal-signs-new-striker-record-deal',
            'content' => 'Arsenal Football Club has today announced...',
            'excerpt' => 'Arsenal has completed the signing of a world-class striker...',
            'cover_image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&h=400&fit=crop',
            'category' => 'Transfer News',
            'tags' => ['Arsenal', 'Transfer', 'Striker', 'Premier League'],
            'youtube_video_id' => null,
            'is_featured' => true,
            'status' => 'published',
            'meta_title' => 'Arsenal Signs New Striker in Record Deal',
            'meta_description' => 'Arsenal has completed the signing of a world-class striker...'
        ];

        $categories = [
            'Transfer News',
            'Match Reports',
            'Training',
            'Women\'s Team',
            'Transfer Rumours',
            'Analysis',
            'History'
        ];

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'cover_image' => 'nullable|url',
            'youtube_video_id' => 'nullable|string',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published'
        ]);

        // Here you would normally update the database
        // Article::findOrFail($id)->update($request->all());

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully!');
    }

    public function destroy($id)
    {
        // Here you would normally delete from database
        // Article::findOrFail($id)->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully!');
    }
} 