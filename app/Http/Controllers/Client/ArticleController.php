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
        
        return view('client.blog.index', compact('articles', 'categories', 'category', 'search'));
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

        // Get related articles (same category, excluding current article)
        $relatedArticles = Article::with('author')
                                 ->where('status', 'published')
                                 ->where('published_at', '<=', now())
                                 ->where('category', $article->category)
                                 ->where('id', '!=', $article->id)
                                 ->orderBy('published_at', 'desc')
                                 ->limit(3)
                                 ->get();

        // If not enough related articles in same category, get recent articles
        if ($relatedArticles->count() < 3) {
            $additionalArticles = Article::with('author')
                                       ->where('status', 'published')
                                       ->where('published_at', '<=', now())
                                       ->where('id', '!=', $article->id)
                                       ->whereNotIn('id', $relatedArticles->pluck('id'))
                                       ->orderBy('published_at', 'desc')
                                       ->limit(3 - $relatedArticles->count())
                                       ->get();
            
            $relatedArticles = $relatedArticles->merge($additionalArticles);
        }

        // Ensure relatedArticles is always a collection
        if (!$relatedArticles) {
            $relatedArticles = collect();
        }

        return view('client.blog.show', compact('article', 'relatedArticles'));
    }

    public function category($category)
    {
        return $this->index(request()->merge(['category' => $category]));
    }
} 