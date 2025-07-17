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

        $query = Service::with(['user', 'reviews'])
            ->where('status', 'active')
            ->where('is_verified', true);

        if ($category) {
            $query->where('category', $category);
        }
        if ($location) {
            $query->where('location', 'like', "%$location%");
        }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%")
                  ->orWhere('pricing', 'like', "%$search%")
                  ->orWhere('contact_info', 'like', "%$search%")
                  ;
            });
        }

        $services = $query->orderBy('created_at', 'desc')->get();

        // Get unique categories and locations for filters
        $categories = Service::where('status', 'active')->where('is_verified', true)->distinct()->pluck('category')->filter()->sort()->values();
        $locations = Service::where('status', 'active')->where('is_verified', true)->distinct()->pluck('location')->filter()->sort()->values();

        return view('client.services.index', compact('services', 'categories', 'locations', 'category', 'location', 'search'));
    }

    public function show($slug)
    {
        $service = Service::with(['user', 'reviews.user'])
            ->where('status', 'active')
            ->where('is_verified', true)
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $service->increment('views_count');

        $reviews = $service->reviews;
        $relatedServices = Service::where('status', 'active')
            ->where('is_verified', true)
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        return view('client.services.show', compact('service', 'reviews', 'relatedServices'));
    }
} 