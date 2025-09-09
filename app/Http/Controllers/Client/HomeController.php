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
        // Fetch featured articles (published)
        $featuredArticles = Article::with('author')
            ->where('status', 'published')
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->orderBy('views_count', 'desc')
            ->take(4)
            ->get();

        // Fetch featured video (most recent published video)
        $featuredVideo = Video::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->first();

        // Fetch recent active services
        $newServices = Service::where('status', 'active')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Fetch popular products (active and in stock)
        $popularProducts = Product::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->orderBy('is_featured', 'desc')
            ->orderBy('views_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('client.home', compact('featuredArticles', 'featuredVideo', 'newServices', 'popularProducts'));
    }
} 