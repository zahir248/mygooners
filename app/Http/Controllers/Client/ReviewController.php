<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the review form for a product
     */
    public function create(Product $product)
    {
        // Check if user has already reviewed this product
        $existingReview = $product->reviews()->where('user_id', Auth::id())->first();
        
        if ($existingReview) {
            return redirect()->route('shop.show', $product->slug)
                ->with('error', 'Anda telah memberikan ulasan untuk produk ini.');
        }

        return view('client.reviews.create', compact('product'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        // Check if authenticated user has already reviewed this product
        if (Auth::check()) {
            $existingReview = $product->reviews()->where('user_id', Auth::id())->first();
            
            if ($existingReview) {
                return redirect()->route('shop.show', $product->slug)
                    ->with('error', 'Anda telah memberikan ulasan untuk produk ini.');
            }
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ], [
            'rating.required' => 'Rating diperlukan.',
            'rating.integer' => 'Rating mesti nombor bulat.',
            'rating.min' => 'Rating mesti sekurang-kurangnya 1.',
            'rating.max' => 'Rating tidak boleh melebihi 5.',
            'comment.required' => 'Komen diperlukan.',
            'comment.min' => 'Komen mesti sekurang-kurangnya 10 aksara.',
            'comment.max' => 'Komen tidak boleh melebihi 1000 aksara.',
        ]);

        // Create the review
        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified' => true, // Reviews are automatically verified
        ]);

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dihantar dan dipaparkan! Terima kasih atas maklum balas anda.');
    }

    /**
     * Show the edit form for a review
     */
    public function edit(Product $product, ProductReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh mengedit ulasan anda sendiri.');
        }

        return view('client.reviews.edit', compact('product', 'review'));
    }

    /**
     * Update a review
     */
    public function update(Request $request, Product $product, ProductReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh mengemas kini ulasan anda sendiri.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ], [
            'rating.required' => 'Rating diperlukan.',
            'rating.integer' => 'Rating mesti nombor bulat.',
            'rating.min' => 'Rating mesti sekurang-kurangnya 1.',
            'rating.max' => 'Rating tidak boleh melebihi 5.',
            'comment.required' => 'Komen diperlukan.',
            'comment.min' => 'Komen mesti sekurang-kurangnya 10 aksara.',
            'comment.max' => 'Komen tidak boleh melebihi 1000 aksara.',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified' => true, // Reviews remain verified when updated
        ]);

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dikemas kini!');
    }

    /**
     * Delete a review
     */
    public function destroy(Product $product, ProductReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh memadamkan ulasan anda sendiri.');
        }

        $review->delete();

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dipadamkan.');
    }
}
