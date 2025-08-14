<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\Product;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of all product reviews
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
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
        $products = Product::orderBy('title')->get();

        return view('admin.reviews.index', compact('reviews', 'products'));
    }

    /**
     * Display the specified review
     */
    public function show(ProductReview $review)
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(ProductReview $review)
    {
        $review->update(['is_verified' => true]);

        return redirect()->back()->with('success', 'Ulasan telah berjaya diluluskan.');
    }

    /**
     * Reject a review
     */
    public function reject(ProductReview $review)
    {
        $review->update(['is_verified' => false]);

        return redirect()->back()->with('success', 'Ulasan telah berjaya ditolak.');
    }

    /**
     * Delete a review
     */
    public function destroy(ProductReview $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Ulasan telah berjaya dipadamkan.');
    }

    /**
     * Bulk actions on reviews
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'reviews' => 'required|array',
            'reviews.*' => 'exists:product_reviews,id'
        ]);

        $reviews = ProductReview::whereIn('id', $request->reviews);

        switch ($request->action) {
            case 'approve':
                $reviews->update(['is_verified' => true]);
                $message = 'Ulasan yang dipilih telah berjaya diluluskan.';
                break;
            case 'reject':
                $reviews->update(['is_verified' => false]);
                $message = 'Ulasan yang dipilih telah berjaya ditolak.';
                break;
            case 'delete':
                $reviews->delete();
                $message = 'Ulasan yang dipilih telah berjaya dipadamkan.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get review statistics
     */
    public function statistics()
    {
        $stats = [
            'total_reviews' => ProductReview::count(),
            'verified_reviews' => ProductReview::where('is_verified', true)->count(),
            'pending_reviews' => ProductReview::where('is_verified', false)->count(),
            'average_rating' => ProductReview::avg('rating'),
            'reviews_by_rating' => [
                5 => ProductReview::where('rating', 5)->count(),
                4 => ProductReview::where('rating', 4)->count(),
                3 => ProductReview::where('rating', 3)->count(),
                2 => ProductReview::where('rating', 2)->count(),
                1 => ProductReview::where('rating', 1)->count(),
            ]
        ];

        return view('admin.reviews.statistics', compact('stats'));
    }
}
