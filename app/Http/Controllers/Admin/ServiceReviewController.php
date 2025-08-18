<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceReview;
use App\Models\Service;

class ServiceReviewController extends Controller
{
    /**
     * Display a listing of all service reviews
     */
    public function index(Request $request)
    {
        $query = ServiceReview::with(['service', 'user']);

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', (int) $request->service_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by user name or comment
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);
        $services = Service::orderBy('title')->get();

        return view('admin.service-reviews.index', compact('reviews', 'services'));
    }

    /**
     * Display the specified review
     */
    public function show(ServiceReview $review)
    {
        $review->load(['service', 'user']);
        return view('admin.service-reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(ServiceReview $review)
    {
        $review->update(['status' => 'approved']);

        // Update service trust score
        $review->service->updateTrustScore();

        return redirect()->back()->with('success', 'Ulasan telah berjaya diluluskan.');
    }

    /**
     * Reject a review
     */
    public function reject(ServiceReview $review)
    {
        $review->update(['status' => 'rejected']);

        // Update service trust score
        $review->service->updateTrustScore();

        return redirect()->back()->with('success', 'Ulasan telah berjaya ditolak.');
    }

    /**
     * Delete a review
     */
    public function destroy(ServiceReview $review)
    {
        $review->delete();

        // Update service trust score
        $review->service->updateTrustScore();

        return redirect()->route('admin.service-reviews.index')->with('success', 'Ulasan telah berjaya dipadamkan.');
    }

    /**
     * Bulk actions on reviews
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'reviews' => 'required|array',
            'reviews.*' => 'exists:service_reviews,id'
        ]);

        $reviews = ServiceReview::whereIn('id', $request->reviews);

        switch ($request->action) {
            case 'approve':
                $reviews->update(['status' => 'approved']);
                $message = 'Ulasan yang dipilih telah berjaya diluluskan.';
                break;
            case 'reject':
                $reviews->update(['status' => 'rejected']);
                $message = 'Ulasan yang dipilih telah berjaya ditolak.';
                break;
            case 'delete':
                $reviews->delete();
                $message = 'Ulasan yang dipilih telah berjaya dipadamkan.';
                break;
        }

        // Update trust scores for affected services
        $affectedServiceIds = $reviews->pluck('service_id')->unique();
        Service::whereIn('id', $affectedServiceIds)->each(function($service) {
            $service->updateTrustScore();
        });

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get review statistics
     */
    public function statistics()
    {
        $stats = [
            'total_reviews' => ServiceReview::count(),
            'approved_reviews' => ServiceReview::where('status', 'approved')->count(),
            'pending_reviews' => ServiceReview::where('status', 'pending')->count(),
            'rejected_reviews' => ServiceReview::where('status', 'rejected')->count(),
            'average_rating' => ServiceReview::avg('rating'),
            'reviews_by_rating' => [
                5 => ServiceReview::where('rating', 5)->count(),
                4 => ServiceReview::where('rating', 4)->count(),
                3 => ServiceReview::where('rating', 3)->count(),
                2 => ServiceReview::where('rating', 2)->count(),
                1 => ServiceReview::where('rating', 1)->count(),
            ]
        ];

        return view('admin.service-reviews.statistics', compact('stats'));
    }
}
