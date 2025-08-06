<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for Stripe
     */
    public function createPaymentIntent($order)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($order->total * 100), // Convert to cents
                'currency' => 'myr',
                'metadata' => [
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'order_id' => $order->id ?? null,
                ],
                'description' => 'Payment for order ' . $order->order_number,
            ]);

            Log::info('Stripe payment intent created', [
                'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'status' => $paymentIntent->status,
                'metadata' => $paymentIntent->metadata
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat pembayaran Stripe: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment intent status
     */
    public function verifyPayment($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            Log::info('Stripe payment verification', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount
            ]);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'paid' => $paymentIntent->status === 'succeeded',
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe payment verification failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengesahkan pembayaran Stripe'
            ];
        }
    }

    /**
     * Cancel a payment intent
     */
    public function cancelPaymentIntent($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            // Only cancel if it's not already succeeded or cancelled
            if ($paymentIntent->status === 'requires_payment_method' || 
                $paymentIntent->status === 'requires_confirmation' ||
                $paymentIntent->status === 'requires_action') {
                
                $paymentIntent->cancel();
                
                Log::info('Stripe payment intent cancelled', [
                    'payment_intent_id' => $paymentIntentId,
                    'status' => $paymentIntent->status
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment intent cancelled successfully'
                ];
            } else {
                Log::info('Stripe payment intent cannot be cancelled', [
                    'payment_intent_id' => $paymentIntentId,
                    'status' => $paymentIntent->status
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment intent cannot be cancelled in current status'
                ];
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent cancellation failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membatalkan pembayaran Stripe: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook($payload, $sigHeader)
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;
                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
            }

            return ['success' => true];

        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Invalid payload'];
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Invalid signature'];
        } catch (\Exception $e) {
            Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Webhook error'];
        }
    }

    /**
     * Handle successful payment intent
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'metadata' => $paymentIntent->metadata
        ]);

        // Check if order already exists
        $existingOrder = \App\Models\Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        
        if ($existingOrder) {
            Log::info('Order already exists for payment intent', [
                'order_id' => $existingOrder->id,
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }

        // If no order exists, we might need to create one from session data
        // This is a fallback mechanism
        Log::warning('No order found for successful payment intent', [
            'payment_intent_id' => $paymentIntent->id,
            'metadata' => $paymentIntent->metadata
        ]);
    }

    /**
     * Handle failed payment intent
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        Log::info('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error
        ]);
    }

    /**
     * Get payment intent details from Stripe
     */
    public function getPaymentIntent($paymentIntentId)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'status' => $paymentIntent->status,
                'metadata' => $paymentIntent->metadata
            ];

        } catch (\Exception $e) {
            \Log::error('Failed to get payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve payment intent'
            ];
        }
    }
} 