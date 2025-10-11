<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        
        // Query articles from database
        $query = Article::with('author')
                       ->where('status', 'published')
                       ->where('published_at', '<=', now())
                       ->orderBy('is_featured', 'desc')
                       ->orderBy('published_at', 'desc');

        // Filter by category if provided
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Get articles with pagination
        $articles = $query->paginate(12);

        // Get unique categories for filter
        $categories = Article::where('status', 'published')
                            ->where('published_at', '<=', now())
                            ->distinct()
                            ->pluck('category')
                            ->filter()
                            ->values()
                            ->toArray();

        // Get top 5 most viewed articles (only show when not searching or filtering by category)
        $topViewedArticles = collect();
        if (!$search && !$category) {
            $topViewedArticles = Article::with('author')
                                      ->where('status', 'published')
                                      ->where('published_at', '<=', now())
                                      ->orderBy('views_count', 'desc')
                                      ->limit(5)
                                      ->get();
        }
        
        return view('client.blog.index', compact('articles', 'categories', 'category', 'search', 'topViewedArticles'));
    }

    public function show($slug)
    {
        // Find article by slug
        $article = Article::with('author')
                         ->where('slug', $slug)
                         ->where('status', 'published')
                         ->where('published_at', '<=', now())
                         ->firstOrFail();

        // Increment view count
        $article->increment('views_count');

        // Get related articles based on tags only (excluding current article)
        $relatedArticles = collect();
        
        if ($article->tags && count($article->tags) > 0) {
            $relatedArticles = Article::with('author')
                                     ->where('status', 'published')
                                     ->where('published_at', '<=', now())
                                     ->where('id', '!=', $article->id)
                                     ->where(function($query) use ($article) {
                                         foreach ($article->tags as $tag) {
                                             $query->orWhereJsonContains('tags', $tag);
                                         }
                                     })
                                     ->orderBy('published_at', 'desc')
                                     ->limit(3)
                                     ->get();
        }

        // Ensure relatedArticles is always a collection
        if (!$relatedArticles) {
            $relatedArticles = collect();
        }

        return view('client.blog.show', compact('article', 'relatedArticles'));
    }

    public function category($category)
    {
        // Convert URL-encoded category back to original format
        $originalCategory = str_replace('-', ' ', $category);
        
        // Get all available categories to find the exact match
        $availableCategories = Article::where('status', 'published')
                                    ->where('published_at', '<=', now())
                                    ->distinct()
                                    ->pluck('category')
                                    ->filter()
                                    ->values()
                                    ->toArray();
        
        // Find the exact category match (case-insensitive)
        $matchedCategory = null;
        foreach ($availableCategories as $cat) {
            if (strtolower(str_replace(' ', '-', $cat)) === strtolower($category)) {
                $matchedCategory = $cat;
                break;
            }
        }
        
        // If no exact match found, try direct conversion
        if (!$matchedCategory) {
            $matchedCategory = ucwords($originalCategory);
        }
        
        return $this->index(request()->merge(['category' => $matchedCategory]));
    }
} 