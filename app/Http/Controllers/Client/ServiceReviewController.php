<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Support\Facades\Auth;

class ServiceReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the review form for a service
     */
    public function create(Service $service)
    {
        // Check if user has already reviewed this service
        $existingReview = $service->reviews()->where('user_id', Auth::id())->first();
        
        if ($existingReview) {
            return redirect()->route('services.show', $service->slug)
                ->with('error', 'Anda telah memberikan ulasan untuk perkhidmatan ini.');
        }

        return view('client.service-reviews.create', compact('service'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request, Service $service)
    {
        // Check if authenticated user has already reviewed this service
        if (Auth::check()) {
            $existingReview = $service->reviews()->where('user_id', Auth::id())->first();
            
            if ($existingReview) {
                return redirect()->route('services.show', $service->slug)
                    ->with('error', 'Anda telah memberikan ulasan untuk perkhidmatan ini.');
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
        ServiceReview::create([
            'service_id' => $service->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'approved', // Reviews are automatically approved
        ]);

        // Update the service trust score
        $service->updateTrustScore();

        return redirect()->route('services.show', $service->slug)
            ->with('success', 'Ulasan anda telah berjaya dihantar dan dipaparkan! Terima kasih atas maklum balas anda.');
    }

    /**
     * Show the edit form for a review
     */
    public function edit(Service $service, ServiceReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh mengedit ulasan anda sendiri.');
        }

        return view('client.service-reviews.edit', compact('service', 'review'));
    }

    /**
     * Update a review
     */
    public function update(Request $request, Service $service, ServiceReview $review)
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

        // Update the review
        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Update the service trust score
        $service->updateTrustScore();

        return redirect()->route('services.show', $service->slug)
            ->with('success', 'Ulasan anda telah berjaya dikemas kini!');
    }

    /**
     * Delete a review
     */
    public function destroy(Service $service, ServiceReview $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Anda hanya boleh memadam ulasan anda sendiri.');
        }

        // Store the service slug before deleting the review
        $serviceSlug = $service->slug;
        
        // Delete the review
        $review->delete();

        // Update the service trust score
        $service->updateTrustScore();

        // Redirect back to the service detail page
        return redirect()->route('services.show', $serviceSlug)
            ->with('success', 'Ulasan anda telah berjaya dipadam.');
    }
}
