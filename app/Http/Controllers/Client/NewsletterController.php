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
            'email.required' => 'Alamat emel diperlukan.',
            'email.email' => 'Sila masukkan alamat emel yang sah.',
            'email.max' => 'Alamat emel terlalu panjang.'
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
                    'message' => 'Alamat emel ini sudah dilanggani newsletter kami.'
                ], 422);
            } else {
                // Resubscribe
                $existingSubscription->resubscribe();
                return response()->json([
                    'success' => true,
                    'message' => 'Terima kasih! Anda telah berjaya melanggani newsletter kami semula.'
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
            'message' => 'Terima kasih! Anda telah berjaya melanggani newsletter kami.'
        ]);
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat emel tidak sah.'
            ], 422);
        }

        $email = $request->email;
        $subscription = Newsletter::where('email', $email)->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat emel tidak ditemui dalam senarai langganan.'
            ], 404);
        }

        $subscription->unsubscribe();

        return response()->json([
            'success' => true,
            'message' => 'Anda telah berjaya berhenti melanggani newsletter kami.'
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
            return redirect()->route('home')->with('error', 'Alamat emel tidak ditemui dalam senarai langganan.');
        }

        $subscription->unsubscribe();

        return redirect()->route('home')->with('success', 'Anda telah berjaya berhenti melanggani newsletter kami.');
    }
}
