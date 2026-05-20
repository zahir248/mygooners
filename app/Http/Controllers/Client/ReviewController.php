<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'rating.required' => 'Rating diperlukan.',
            'rating.integer' => 'Rating mesti nombor bulat.',
            'rating.min' => 'Rating mesti sekurang-kurangnya 1.',
            'rating.max' => 'Rating tidak boleh melebihi 5.',
            'comment.required' => 'Komen diperlukan.',
            'comment.min' => 'Komen mesti sekurang-kurangnya 10 aksara.',
            'comment.max' => 'Komen tidak boleh melebihi 1000 aksara.',
            'photos.max' => 'Maksimum 5 gambar dibenarkan.',
            'photos.*.image' => 'Fail mesti imej yang sah.',
        ]);

        try {
            DB::transaction(function () use ($request, $product, $validated) {
                $review = ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'],
                    'is_verified' => true,
                ]);

                if (Schema::hasTable('product_review_photos') && $request->hasFile('photos')) {
                    foreach ((array) $request->file('photos') as $photo) {
                        $stored = $photo->store('review_images', 'public');
                        $review->photos()->create(['image_path' => $stored]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Web review submission failed', [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('shop.show', $product->slug)
                ->with('error', 'Ulasan gagal dihantar. Sila cuba lagi.');
        }

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dihantar dan dipaparkan! Terima kasih atas maklum balas anda.');
    }

    /**
     * Show the edit form for a review
     */
    public function edit(Product $product, ProductReview $review)
    {
        // Check if user owns this review
        if ((int)$review->user_id !== Auth::id()) {
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
        if ((int)$review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh mengemas kini ulasan anda sendiri.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'rating.required' => 'Rating diperlukan.',
            'rating.integer' => 'Rating mesti nombor bulat.',
            'rating.min' => 'Rating mesti sekurang-kurangnya 1.',
            'rating.max' => 'Rating tidak boleh melebihi 5.',
            'comment.required' => 'Komen diperlukan.',
            'comment.min' => 'Komen mesti sekurang-kurangnya 10 aksara.',
            'comment.max' => 'Komen tidak boleh melebihi 1000 aksara.',
            'photos.max' => 'Maksimum 5 gambar dibenarkan.',
            'photos.*.image' => 'Fail mesti imej yang sah.',
        ]);

        try {
            DB::transaction(function () use ($request, $review, $validated) {
                $review->update([
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'],
                    'is_verified' => true,
                ]);

                if (Schema::hasTable('product_review_photos') && $request->hasFile('photos')) {
                    foreach ($review->photos as $existingPhoto) {
                        if (!empty($existingPhoto->image_path)) {
                            Storage::disk('public')->delete($existingPhoto->image_path);
                        }
                    }
                    $review->photos()->delete();

                    foreach ((array) $request->file('photos') as $photo) {
                        $stored = $photo->store('review_images', 'public');
                        $review->photos()->create(['image_path' => $stored]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('Web review update failed', [
                'review_id' => $review->id,
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('shop.show', $product->slug)
                ->with('error', 'Ulasan gagal dikemas kini. Sila cuba lagi.');
        }

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dikemas kini!');
    }

    /**
     * Delete a review
     */
    public function destroy(Product $product, ProductReview $review)
    {
        // Check if user owns this review
        if ((int)$review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh memadamkan ulasan anda sendiri.');
        }

        foreach ($review->photos as $photo) {
            if (!empty($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }
        }

        $review->delete();

        return redirect()->route('shop.show', $product->slug)
            ->with('success', 'Ulasan anda telah berjaya dipadamkan.');
    }
}
