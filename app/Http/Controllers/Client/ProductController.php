<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        $sort = $request->get('sort', 'newest');
        
        // Query only active products
        $query = Product::with(['variations'])->where('status', 'active');

        // Filter by category if provided
        if ($category) {
            $query->where('category', $category);
        }

        // Filter by search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Sort products
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);

        // Get categories for filter
        $categories = Product::where('status', 'active')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        return view('client.shop.index', compact('products', 'categories', 'category', 'search', 'sort'));
    }

    public function show($slug, Request $request)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['reviews', 'variations'])
            ->firstOrFail();

        // Increment view count
        $product->increment('views_count');

        // Get related products
        $relatedProducts = Product::where('status', 'active')
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        // Get selected variant from URL parameter (now using variant name)
        $selectedVariantName = $request->get('variant');
        $selectedVariantId = null;
        $selectedVariation = null;
        
        if ($selectedVariantName) {
            // URL decode the variant name
            $decodedVariantName = urldecode($selectedVariantName);
            // Find the variation by name
            $selectedVariation = $product->variations()->where('name', $decodedVariantName)->first();
            if ($selectedVariation) {
                $selectedVariantId = $selectedVariation->id;
            }
        }

        return view('client.shop.show', compact('product', 'relatedProducts', 'selectedVariantId', 'selectedVariation'));
    }

    public function category($category)
    {
        $products = Product::where('status', 'active')
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Product::where('status', 'active')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        return view('client.shop.index', compact('products', 'categories', 'category'));
    }
} 