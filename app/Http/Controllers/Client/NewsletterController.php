<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ], [
            'email.required' => __('client_messages.newsletter_email_required'),
            'email.email' => __('client_messages.newsletter_email_invalid'),
            'email.max' => __('client_messages.newsletter_email_max'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $email = $request->email;

        // Check if already subscribed
        $existingSubscription = Newsletter::where('email', $email)->first();

        if ($existingSubscription) {
            if ($existingSubscription->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => __('client_messages.newsletter_already_subscribed')
                ], 422);
            } else {
                // Resubscribe
                $existingSubscription->resubscribe();
                return response()->json([
                    'success' => true,
                    'message' => __('client_messages.newsletter_resubscribed')
                ]);
            }
        }

        // Create new subscription
        Newsletter::create([
            'email' => $email,
            'status' => 'active',
            'subscribed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => __('client_messages.newsletter_subscribed_success')
        ]);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ], [
            'email.required' => __('client_messages.newsletter_email_required'),
            'email.email' => __('client_messages.newsletter_email_invalid'),
            'email.max' => __('client_messages.newsletter_email_max'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = $request->email;
        $subscription = Newsletter::where('email', $email)->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => __('client_messages.newsletter_unsubscribe_not_found')
            ], 404);
        }

        $subscription->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => __('client_messages.newsletter_unsubscribed_success')
        ]);
    }

    /**
     * Unsubscribe via token (for email links)
     */
    public function unsubscribeByToken($token)
    {
        // For now, we'll use a simple approach
        // In production, you might want to use encrypted tokens
        $email = base64_decode($token);
        
        $subscription = Newsletter::where('email', $email)->first();

        if (!$subscription) {
            return redirect()->route('home')->with('error', __('client_messages.msg_fe746d751137'));
        }

        $subscription->unsubscribe();

        return redirect()->route('home')->with('success', __('client_messages.msg_a065b5d104e1'));
    }
}
