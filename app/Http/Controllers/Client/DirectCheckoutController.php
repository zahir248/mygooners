<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Order;
use App\Models\BillingDetail;
use App\Models\ShippingDetail;
use App\Services\ToyyibPayService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectCheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Validate required parameters
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variation_id' => 'nullable|exists:product_variations,id',
        ]);

        $product = Product::with(['variations'])->findOrFail($request->product_id);
        $quantity = $request->quantity;
        $variationId = $request->variation_id;
        
        // Get variation if specified
        $variation = null;
        if ($variationId) {
            $variation = $product->variations()->findOrFail($variationId);
        }

        // Check stock availability
        $stockQuantity = $variation ? $variation->stock_quantity : $product->stock_quantity;
        if ($stockQuantity < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk kuantiti yang dipilih.');
        }

        // Calculate prices (use sale price if available, otherwise use regular price)
        $price = $variation ? ($variation->sale_price ?: $variation->price) : ($product->sale_price ?: $product->price);
        $subtotal = $price * $quantity;
        $shippingCost = 0.00; // Free shipping for now
        $tax = 0.00; // No tax for now
        $total = $subtotal + $shippingCost + $tax;

        // Get user and billing details
        $user = auth()->user();
        $billingDetails = $user->billingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        $defaultBillingDetail = $user->billingDetails()->where('is_default', true)->first();
        $shippingDetails = $user->shippingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        $defaultShippingDetail = $user->shippingDetails()->where('is_default', true)->first();

        // Store checkout data in session for the checkout process
        session([
            'direct_checkout' => [
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'product' => $product->toArray(),
                'variation' => $variation ? $variation->toArray() : null,
            ]
        ]);

        return view('client.direct-checkout.index', compact(
            'product', 
            'variation', 
            'quantity', 
            'price', 
            'subtotal', 
            'shippingCost', 
            'tax', 
            'total', 
            'user', 
            'billingDetails', 
            'defaultBillingDetail',
            'shippingDetails',
            'defaultShippingDetail'
        ));
    }

    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'shipping_detail_id' => 'nullable|exists:shipping_details,id',
            'shipping_name' => 'required_without:shipping_detail_id|string|max:255',
            'shipping_email' => 'required_without:shipping_detail_id|email|max:255',
            'shipping_phone' => 'required_without:shipping_detail_id|string|max:20',
            'shipping_address' => 'required_without:shipping_detail_id|string',
            'shipping_city' => 'required_without:shipping_detail_id|string|max:255',
            'shipping_state' => 'required_without:shipping_detail_id|string|max:255',
            'shipping_postal_code' => 'required_without:shipping_detail_id|string|max:10',
            'shipping_country' => 'required_without:shipping_detail_id|string|max:255',
            'billing_detail_id' => 'nullable|exists:billing_details,id',
            'billing_name' => 'required_without:billing_detail_id|string|max:255',
            'billing_email' => 'required_without:billing_detail_id|email|max:255',
            'billing_phone' => 'required_without:billing_detail_id|string|max:20',
            'billing_address' => 'required_without:billing_detail_id|string',
            'billing_city' => 'required_without:billing_detail_id|string|max:255',
            'billing_state' => 'required_without:billing_detail_id|string|max:255',
            'billing_postal_code' => 'required_without:billing_detail_id|string|max:10',
            'billing_country' => 'required_without:billing_detail_id|string|max:255',
            'payment_method' => 'required|in:toyyibpay,stripe',
            'notes' => 'nullable|string',
            'save_shipping_detail' => 'boolean',
            'save_billing_detail' => 'boolean',
        ]);

        // Get checkout data from session
        $checkoutData = session('direct_checkout');
        if (!$checkoutData) {
            return redirect()->back()->with('error', 'Sesi checkout telah tamat. Sila cuba lagi.');
        }

        try {
            DB::beginTransaction();

            // Get shipping details
            $shippingData = [];
            if ($request->shipping_detail_id) {
                $shippingDetail = ShippingDetail::where('id', $request->shipping_detail_id)
                                              ->where('user_id', auth()->id())
                                              ->firstOrFail();
                $shippingData = [
                    'shipping_name' => $shippingDetail->name,
                    'shipping_email' => $shippingDetail->email,
                    'shipping_phone' => $shippingDetail->phone,
                    'shipping_address' => $shippingDetail->address,
                    'shipping_city' => $shippingDetail->city,
                    'shipping_state' => $shippingDetail->state,
                    'shipping_postal_code' => $shippingDetail->postal_code,
                    'shipping_country' => $shippingDetail->country,
                ];
            } else {
                $shippingData = [
                    'shipping_name' => $request->shipping_name,
                    'shipping_email' => $request->shipping_email,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_postal_code' => $request->shipping_postal_code,
                    'shipping_country' => $request->shipping_country,
                ];

                // Save shipping detail if requested
                if ($request->boolean('save_shipping_detail')) {
                    ShippingDetail::create([
                        'user_id' => auth()->id(),
                        'name' => $request->shipping_name,
                        'email' => $request->shipping_email,
                        'phone' => $request->shipping_phone,
                        'address' => $request->shipping_address,
                        'city' => $request->shipping_city,
                        'state' => $request->shipping_state,
                        'postal_code' => $request->shipping_postal_code,
                        'country' => $request->shipping_country,
                        'label' => 'Shipping Address',
                        'is_default' => auth()->user()->shippingDetails()->count() === 0,
                    ]);
                }
            }

            // Get billing details
            $billingData = [];
            if ($request->billing_detail_id) {
                $billingDetail = BillingDetail::where('id', $request->billing_detail_id)
                                            ->where('user_id', auth()->id())
                                            ->firstOrFail();
                $billingData = [
                    'billing_name' => $billingDetail->name,
                    'billing_email' => $billingDetail->email,
                    'billing_phone' => $billingDetail->phone,
                    'billing_address' => $billingDetail->address,
                    'billing_city' => $billingDetail->city,
                    'billing_state' => $billingDetail->state,
                    'billing_postal_code' => $billingDetail->postal_code,
                    'billing_country' => $billingDetail->country,
                ];
            } else {
                $billingData = [
                    'billing_name' => $request->billing_name,
                    'billing_email' => $request->billing_email,
                    'billing_phone' => $request->billing_phone,
                    'billing_address' => $request->billing_address,
                    'billing_city' => $request->billing_city,
                    'billing_state' => $request->billing_state,
                    'billing_postal_code' => $request->billing_postal_code,
                    'billing_country' => $request->billing_country,
                ];

                // Save billing detail if requested
                if ($request->boolean('save_billing_detail')) {
                    BillingDetail::create([
                        'user_id' => auth()->id(),
                        'name' => $request->billing_name,
                        'email' => $request->billing_email,
                        'phone' => $request->billing_phone,
                        'address' => $request->billing_address,
                        'city' => $request->billing_city,
                        'state' => $request->billing_state,
                        'postal_code' => $request->billing_postal_code,
                        'country' => $request->billing_country,
                        'label' => 'Billing Address',
                        'is_default' => auth()->user()->billingDetails()->count() === 0,
                    ]);
                }
            }

            // Store checkout data in session for later use
            $pendingCheckoutData = [
                'user_id' => auth()->id(),
                'subtotal' => $checkoutData['subtotal'],
                'shipping_cost' => $checkoutData['shipping_cost'],
                'tax' => $checkoutData['tax'],
                'total' => $checkoutData['total'],
                'payment_method' => $request->payment_method,
                'shipping_data' => $shippingData,
                'billing_data' => $billingData,
                'notes' => $request->notes,
                'product_id' => $checkoutData['product_id'],
                'variation_id' => $checkoutData['variation_id'],
                'product_name' => $checkoutData['product']['title'],
                'variation_name' => $checkoutData['variation']['name'] ?? null,
                'price' => $checkoutData['price'],
                'quantity' => $checkoutData['quantity'],
            ];

            session(['pending_direct_checkout' => $pendingCheckoutData]);
            
            \Log::info('Pending direct checkout data stored in session', [
                'session_keys' => array_keys(session()->all()),
                'pending_direct_checkout_keys' => array_keys($pendingCheckoutData)
            ]);

            // Force session save to ensure data persistence
            session()->save();

            // Create order immediately in database
            $order = Order::create([
                'order_number' => (new Order())->generateOrderNumber(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $checkoutData['subtotal'],
                'shipping_cost' => $checkoutData['shipping_cost'],
                'tax' => $checkoutData['tax'],
                'total' => $checkoutData['total'],
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_name' => $shippingData['shipping_name'],
                'shipping_email' => $shippingData['shipping_email'],
                'shipping_phone' => $shippingData['shipping_phone'],
                'shipping_address' => $shippingData['shipping_address'],
                'shipping_city' => $shippingData['shipping_city'],
                'shipping_state' => $shippingData['shipping_state'],
                'shipping_postal_code' => $shippingData['shipping_postal_code'],
                'shipping_country' => $shippingData['shipping_country'],
                'billing_name' => $billingData['billing_name'],
                'billing_email' => $billingData['billing_email'],
                'billing_phone' => $billingData['billing_phone'],
                'billing_address' => $billingData['billing_address'],
                'billing_city' => $billingData['billing_city'],
                'billing_state' => $billingData['billing_state'],
                'billing_postal_code' => $billingData['billing_postal_code'],
                'billing_country' => $billingData['billing_country'],
                'notes' => $request->notes,
            ]);

            // Create order item
            $order->items()->create([
                'product_id' => $checkoutData['product_id'],
                'product_variation_id' => $checkoutData['variation_id'],
                'product_name' => $checkoutData['product']['title'],
                'variation_name' => $checkoutData['variation']['name'] ?? null,
                'price' => $checkoutData['price'],
                'quantity' => $checkoutData['quantity'],
                'subtotal' => $checkoutData['subtotal'],
            ]);

            // Store order ID in session for payment processing
            session(['pending_direct_order_id' => $order->id]);
            
            \Log::info('Direct checkout order created immediately', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method
            ]);

            // Store payment data in database as backup (for 1 hour)
            \Cache::put('direct_payment_data_' . auth()->id() . '_' . $order->order_number, $pendingCheckoutData, 3600);
            
            \Log::info('Direct payment data backed up to cache', [
                'user_id' => auth()->id(),
                'order_number' => $order->order_number,
                'cache_key' => 'direct_payment_data_' . auth()->id() . '_' . $order->order_number
            ]);

            // Handle payment based on method
            if ($request->payment_method === 'toyyibpay') {
                // Create ToyyibPay bill
                $toyyibPayService = new ToyyibPayService();
                $paymentResult = $toyyibPayService->createBill($order, route('direct-checkout.toyyibpay.return'));

                \Log::info('ToyyibPay direct checkout payment result', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'success' => $paymentResult['success'],
                    'message' => $paymentResult['message'] ?? 'No message',
                    'payment_url' => $paymentResult['payment_url'] ?? 'No URL'
                ]);

                if ($paymentResult['success']) {
                    // Store bill code in session
                    session(['pending_direct_bill_code' => $paymentResult['bill_code']]);
                    
                    DB::commit();
                    
                    // Redirect to ToyyibPay payment page
                    return redirect($paymentResult['payment_url']);
                } else {
                    DB::rollBack();
                    // If payment creation fails, order still exists but payment failed
                    return back()->with('error', 'Gagal membuat bil pembayaran. Sila cuba lagi.')
                                ->withInput();
                }
            } elseif ($request->payment_method === 'stripe') {
                // Create Stripe payment intent
                $stripeService = new StripeService();
                $paymentResult = $stripeService->createPaymentIntent($order);

                \Log::info('Stripe direct checkout payment result', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'success' => $paymentResult['success'],
                    'payment_intent_id' => $paymentResult['payment_intent_id'] ?? 'No ID'
                ]);

                if ($paymentResult['success']) {
                    // Store payment intent ID in session
                    session(['pending_direct_stripe_payment_intent_id' => $paymentResult['payment_intent_id']]);
                    
                    \Log::info('Direct checkout Stripe payment intent stored in session', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'session_keys' => array_keys(session()->all())
                    ]);
                    
                    // Force session save to ensure data persistence
                    session()->save();
                    
                    DB::commit();
                    
                    // Redirect to Stripe payment page
                    return redirect()->route('direct-checkout.stripe.payment', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'client_secret' => $paymentResult['client_secret']
                    ]);
                } else {
                    DB::rollBack();
                    // If payment creation fails, order still exists but payment failed
                    return back()->with('error', 'Gagal membuat pembayaran Stripe. Sila cuba lagi.')
                                ->withInput();
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Direct checkout error: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Ralat semasa memproses pesanan. Sila cuba lagi.')
                           ->withInput();
        }
    }

    public function success($orderId)
    {
        $order = Order::where('id', $orderId)
                                 ->where('user_id', auth()->id())
                     ->with(['items.product', 'items.variation'])
                                 ->firstOrFail();
        
        return view('client.direct-checkout.success', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->firstOrFail();

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Pesanan ini tidak boleh dibatalkan. Hanya pesanan yang tertunggak atau sedang diproses boleh dibatalkan.');
        }

        // Check if payment was made and if it's within cancellation window
        if ($order->payment_status === 'paid') {
            $hoursSinceOrder = $order->created_at->diffInHours(now());
            if ($hoursSinceOrder > 24) {
                return back()->with('error', 'Pesanan ini tidak boleh dibatalkan kerana telah melebihi 24 jam dari masa pembelian. Sila hubungi admin untuk bantuan.');
            }
        }

        try {
            DB::beginTransaction();

            // Update order status to cancelled
            $order->update([
                'status' => 'cancelled',
                'notes' => $order->notes ? $order->notes . "\n\nDibatalkan oleh pelanggan pada " . now()->format('d/m/Y H:i') : 
                          'Dibatalkan oleh pelanggan pada ' . now()->format('d/m/Y H:i')
            ]);

            // If payment was pending, we might need to handle payment cancellation
            if ($order->payment_status === 'pending') {
                // For Stripe payments, we could potentially cancel the payment intent
                if ($order->stripe_payment_intent_id) {
                    try {
                        $stripeService = new StripeService();
                        $stripeService->cancelPaymentIntent($order->stripe_payment_intent_id);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to cancel Stripe payment intent', [
                            'order_id' => $order->id,
                            'payment_intent_id' => $order->stripe_payment_intent_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // For ToyyibPay, we could potentially cancel the bill
                if ($order->toyyibpay_bill_code) {
                    try {
                        $toyyibpayService = new ToyyibPayService();
                        $toyyibpayService->cancelBill($order->toyyibpay_bill_code);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to cancel ToyyibPay bill', [
                            'order_id' => $order->id,
                            'bill_code' => $order->toyyibpay_bill_code,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // If payment was made, we need to handle refund
            if ($order->payment_status === 'paid') {
                \Log::info('Paid order cancelled - refund may be required', [
                    'order_id' => $order->id,
                    'payment_method' => $order->payment_method,
                    'amount' => $order->total
                ]);
                
                // TODO: Implement refund logic for paid orders
                // This would involve calling Stripe/ToyyibPay refund APIs
            }

            DB::commit();

            return redirect()->route('checkout.orders')->with('success', 'Pesanan anda telah berjaya dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Ralat semasa membatalkan pesanan. Sila cuba lagi atau hubungi admin.');
        }
    }

    /**
     * Mark order as delivered by user
     */
    public function markAsDelivered(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->firstOrFail();

        // Only allow marking as delivered if order is shipped
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan hanya boleh ditandakan sebagai diterima jika statusnya adalah "Telah Dihantar".');
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return back()->with('success', 'Pesanan berjaya ditandakan sebagai diterima. Terima kasih kerana membeli-belah dengan kami!');
    }

    /**
     * Retry payment for a failed direct checkout order
     */
    public function retryPayment(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->where('payment_status', 'failed')
                     ->whereNotIn('status', ['cancelled', 'refunded'])
                     ->firstOrFail();

        try {
            DB::beginTransaction();

            // Store original payment IDs for rollback if needed
            $originalStripeIntentId = $order->stripe_payment_intent_id;
            $originalToyyibpayBillCode = $order->toyyibpay_bill_code;

            // Handle payment based on method
            if ($order->payment_method === 'toyyibpay') {
                // Create new ToyyibPay bill first
                $toyyibPayService = new ToyyibPayService();
                $paymentResult = $toyyibPayService->createBill($order, route('direct-checkout.toyyibpay.return'));

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_direct_bill_code' => $paymentResult['bill_code'],
                        'pending_direct_order_id' => $order->id,
                        'pending_direct_payment_method' => $order->payment_method,
                        'pending_direct_original_payment_method' => $order->payment_method,
                        'pending_direct_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_direct_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect($paymentResult['payment_url']);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat bil pembayaran. Sila cuba lagi.');
                }
            } elseif ($order->payment_method === 'stripe') {
                // Create new Stripe payment intent first
                $stripeService = new StripeService();
                $paymentResult = $stripeService->createPaymentIntent($order);

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_direct_stripe_payment_intent_id' => $paymentResult['payment_intent_id'],
                        'pending_direct_order_id' => $order->id,
                        'pending_direct_payment_method' => $order->payment_method,
                        'pending_direct_original_payment_method' => $order->payment_method,
                        'pending_direct_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_direct_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect()->route('direct-checkout.stripe.payment', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'client_secret' => $paymentResult['client_secret']
                    ]);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat pembayaran Stripe. Sila cuba lagi.');
                }
            }

            DB::rollBack();
            return back()->with('error', 'Kaedah pembayaran tidak sah.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to retry direct checkout payment', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Ralat semasa mencuba pembayaran semula. Sila cuba lagi.');
        }
    }

    /**
     * Show payment method selection page for retry payment
     */
    public function showRetryPayment($orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->whereIn('payment_status', ['failed', 'pending'])
                     ->whereNotIn('status', ['cancelled', 'refunded'])
                     ->with(['items.product', 'items.variation'])
                     ->firstOrFail();

        return view('client.checkout.retry-payment', compact('order'));
    }

    /**
     * Retry payment with selected payment method
     */
    public function retryPaymentWithMethod(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|in:toyyibpay,stripe',
        ]);

        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->whereIn('payment_status', ['failed', 'pending'])
                     ->whereNotIn('status', ['cancelled', 'refunded'])
                     ->firstOrFail();

        try {
            DB::beginTransaction();

            // Store original payment method and IDs for rollback if needed
            $originalPaymentMethod = $order->payment_method;
            $originalStripeIntentId = $order->stripe_payment_intent_id;
            $originalToyyibpayBillCode = $order->toyyibpay_bill_code;

            // Handle payment based on selected method
            if ($request->payment_method === 'toyyibpay') {
                // Create new ToyyibPay bill first
                $toyyibPayService = new ToyyibPayService();
                $paymentResult = $toyyibPayService->createBill($order, route('direct-checkout.toyyibpay.return'), true); // true = isRetryPayment

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_direct_bill_code' => $paymentResult['bill_code'],
                        'pending_direct_order_id' => $order->id,
                        'pending_direct_payment_method' => $request->payment_method,
                        'pending_direct_original_payment_method' => $originalPaymentMethod,
                        'pending_direct_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_direct_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect($paymentResult['payment_url']);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat bil pembayaran. Sila cuba lagi.');
                }
            } elseif ($request->payment_method === 'stripe') {
                // Create new Stripe payment intent first
                $stripeService = new StripeService();
                $paymentResult = $stripeService->createPaymentIntent($order);

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_direct_stripe_payment_intent_id' => $paymentResult['payment_intent_id'],
                        'pending_direct_order_id' => $order->id,
                        'pending_direct_payment_method' => $request->payment_method,
                        'pending_direct_original_payment_method' => $originalPaymentMethod,
                        'pending_direct_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_direct_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect()->route('direct-checkout.stripe.payment', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'client_secret' => $paymentResult['client_secret']
                    ]);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat pembayaran Stripe. Sila cuba lagi.');
                }
            }

            DB::rollBack();
            return back()->with('error', 'Kaedah pembayaran tidak sah.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to retry direct checkout payment with method', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Ralat semasa mencuba pembayaran semula. Sila cuba lagi.');
        }
    }

    /**
     * Handle ToyyibPay return URL for direct checkout
     */
    public function toyyibpayReturn(Request $request)
    {
        $billCode = $request->get('billcode');
        $status = $request->get('status');

        \Log::info('ToyyibPay return called', [
            'bill_code' => $billCode,
            'status' => $status,
            'all_request_params' => $request->all()
        ]);

        if (!$billCode) {
            return redirect()->route('checkout.orders')
                           ->with('error', 'Maklumat pembayaran tidak lengkap.');
        }

        // Get order from database using bill code
        // First try to find order with this bill code in the database
        $order = Order::where('toyyibpay_bill_code', $billCode)
                     ->where('user_id', auth()->id())
                     ->first();

        // If not found in database, check if it's a pending payment in session
        if (!$order) {
            $pendingOrderId = session('pending_direct_order_id');
            $pendingBillCode = session('pending_direct_bill_code');
            
            if ($pendingOrderId && $pendingBillCode === $billCode) {
                $order = Order::where('id', $pendingOrderId)
                             ->where('user_id', auth()->id())
                             ->first();
                
                \Log::info('Found direct checkout order from pending session data for ToyyibPay return', [
                    'order_id' => $pendingOrderId,
                    'bill_code' => $billCode,
                    'session_pending_order_id' => $pendingOrderId,
                    'session_pending_bill_code' => $pendingBillCode
                ]);
            }
        }

        \Log::info('Order lookup result', [
            'bill_code' => $billCode,
            'user_id' => auth()->id(),
            'order_found' => $order ? true : false,
            'order_id' => $order ? $order->id : null,
            'order_number' => $order ? $order->order_number : null
        ]);

        if (!$order) {
            \Log::error('Direct checkout order not found for ToyyibPay bill code', [
                'bill_code' => $billCode,
                'user_id' => auth()->id(),
                'session_pending_order_id' => session('pending_direct_order_id'),
                'session_pending_bill_code' => session('pending_direct_bill_code'),
                'all_session_data' => session()->all()
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('error', 'Pesanan tidak dijumpai.');
        }

        // Verify payment status
        $toyyibPayService = new ToyyibPayService();
        $paymentResult = $toyyibPayService->verifyPayment($billCode);

        if ($paymentResult['success'] && $paymentResult['paid']) {
            try {
                DB::beginTransaction();

                // Check if this is a retry payment
                $pendingPaymentMethod = session('pending_direct_payment_method');
                $pendingOriginalPaymentMethod = session('pending_direct_original_payment_method');
                $pendingBillCode = session('pending_direct_bill_code');
                
                if ($pendingBillCode === $billCode) {
                    // This is a retry payment - update the order
                    if ($pendingPaymentMethod && $pendingPaymentMethod !== $pendingOriginalPaymentMethod) {
                        // Retry with new payment method
                        $order->update([
                            'payment_method' => $pendingPaymentMethod,
                            'stripe_payment_intent_id' => null,
                            'toyyibpay_bill_code' => $billCode
                        ]);
                        
                        \Log::info('Updated direct checkout order with new payment method after successful payment', [
                            'order_id' => $order->id,
                            'original_method' => $pendingOriginalPaymentMethod,
                            'new_method' => $pendingPaymentMethod,
                            'bill_code' => $billCode
                        ]);
                    } else {
                        // Retry with same payment method - just update the bill code
                        $order->update(['toyyibpay_bill_code' => $billCode]);
                        
                        \Log::info('Updated direct checkout order with new bill code after successful retry payment', [
                            'order_id' => $order->id,
                            'payment_method' => $pendingPaymentMethod,
                            'bill_code' => $billCode
                        ]);
                    }
                }

                // Update order status to paid and processing
                $order->update([
                    'status' => 'processing',
                    'payment_status' => 'paid'
                ]);

                // Update stock
                if ($order->items->first()->product_variation_id) {
                    $variation = ProductVariation::find($order->items->first()->product_variation_id);
                    $variation->decrement('stock_quantity', $order->items->first()->quantity);
                } else {
                    $product = Product::find($order->items->first()->product_id);
                    $product->decrement('stock_quantity', $order->items->first()->quantity);
                }

                // Clear session data
                session()->forget([
                    'pending_direct_checkout', 
                    'pending_direct_bill_code', 
                    'pending_direct_order_id', 
                    'direct_checkout',
                    'pending_direct_payment_method',
                    'pending_direct_original_payment_method',
                    'pending_direct_original_stripe_intent_id',
                    'pending_direct_original_toyyibpay_bill_code'
                ]);

                DB::commit();

                return redirect()->route('direct-checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating direct checkout order after payment: ' . $e->getMessage());
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
            }
        } else {
            // Payment failed - update order status and clear pending session data
            $order->update(['payment_status' => 'failed']);
            
            // Clear pending session data since payment failed
            session()->forget([
                'pending_direct_bill_code', 
                'pending_direct_order_id',
                'pending_direct_payment_method',
                'pending_direct_original_payment_method',
                'pending_direct_original_stripe_intent_id',
                'pending_direct_original_toyyibpay_bill_code'
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('warning', 'Pembayaran gagal. Anda boleh cuba bayar semula.');
        }
    }

    /**
     * Handle Stripe payment return for direct checkout
     */
    public function stripeReturn(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');
        $paymentIntentClientSecret = $request->get('payment_intent_client_secret');

        \Log::info('Direct checkout Stripe return called', [
            'payment_intent_id' => $paymentIntentId,
            'payment_intent_client_secret' => $paymentIntentClientSecret,
            'all_request_params' => $request->all(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);

        if (!$paymentIntentId) {
            return redirect()->route('checkout.orders')
                           ->with('error', 'Maklumat pembayaran tidak lengkap.');
        }

        // Get pending direct checkout data from session
        $pendingCheckout = session('pending_direct_checkout');
        $pendingPaymentIntentId = session('pending_direct_stripe_payment_intent_id');

        \Log::info('Direct checkout session data check', [
            'pending_direct_checkout_exists' => !empty($pendingCheckout),
            'pending_direct_payment_intent_id' => $pendingPaymentIntentId,
            'payment_intent_id' => $paymentIntentId,
            'session_keys' => array_keys(session()->all()),
            'session_id' => session()->getId()
        ]);

        // Verify payment status first
        $stripeService = new StripeService();
        $paymentResult = $stripeService->verifyPayment($paymentIntentId);

        \Log::info('Direct checkout payment verification result', [
            'payment_intent_id' => $paymentIntentId,
            'success' => $paymentResult['success'],
            'paid' => $paymentResult['paid'] ?? false,
            'status' => $paymentResult['status'] ?? 'unknown'
        ]);

        if (!$paymentResult['success']) {
            return redirect()->route('checkout.orders')
                           ->with('error', 'Gagal mengesahkan status pembayaran. Sila hubungi kami.');
        }

        if (!$paymentResult['paid']) {
            return redirect()->route('checkout.orders')
                           ->with('warning', 'Pembayaran belum diselesaikan. Sila cuba lagi atau hubungi kami.');
        }

        // Check if session data is available
        if (!$pendingCheckout || $pendingPaymentIntentId !== $paymentIntentId) {
            \Log::warning('Direct checkout session validation failed - attempting fallback', [
                'pending_direct_checkout' => $pendingCheckout,
                'pending_direct_payment_intent_id' => $pendingPaymentIntentId,
                'payment_intent_id' => $paymentIntentId,
                'session_id' => session()->getId()
            ]);

            // Special case: If we have pending_direct_checkout but missing payment_intent_id, use session data
            if ($pendingCheckout && !$pendingPaymentIntentId) {
                \Log::info('Using direct checkout session data despite missing payment intent ID', [
                    'payment_intent_id' => $paymentIntentId,
                    'session_data_keys' => array_keys($pendingCheckout)
                ]);
                
                // Use the existing session data to create order
            try {
                DB::beginTransaction();

                // Create the actual order
                $order = \App\Models\Order::create([
                    'order_number' => (new \App\Models\Order())->generateOrderNumber(),
                    'user_id' => $pendingCheckout['user_id'],
                    'status' => 'processing',
                    'subtotal' => $pendingCheckout['subtotal'],
                    'shipping_cost' => $pendingCheckout['shipping_cost'],
                    'tax' => $pendingCheckout['tax'],
                    'total' => $pendingCheckout['total'],
                    'payment_method' => $pendingCheckout['payment_method'],
                    'payment_status' => 'paid',
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'shipping_name' => $pendingCheckout['shipping_data']['shipping_name'],
                    'shipping_email' => $pendingCheckout['shipping_data']['shipping_email'],
                    'shipping_phone' => $pendingCheckout['shipping_data']['shipping_phone'],
                    'shipping_address' => $pendingCheckout['shipping_data']['shipping_address'],
                    'shipping_city' => $pendingCheckout['shipping_data']['shipping_city'],
                    'shipping_state' => $pendingCheckout['shipping_data']['shipping_state'],
                    'shipping_postal_code' => $pendingCheckout['shipping_data']['shipping_postal_code'],
                    'shipping_country' => $pendingCheckout['shipping_data']['shipping_country'],
                    'billing_name' => $pendingCheckout['billing_data']['billing_name'],
                    'billing_email' => $pendingCheckout['billing_data']['billing_email'],
                    'billing_phone' => $pendingCheckout['billing_data']['billing_phone'],
                    'billing_address' => $pendingCheckout['billing_data']['billing_address'],
                    'billing_city' => $pendingCheckout['billing_data']['billing_city'],
                    'billing_state' => $pendingCheckout['billing_data']['billing_state'],
                    'billing_postal_code' => $pendingCheckout['billing_data']['billing_postal_code'],
                    'billing_country' => $pendingCheckout['billing_data']['billing_country'],
                    'notes' => $pendingCheckout['notes'],
                ]);

                // Create order item
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $pendingCheckout['product_id'],
                    'product_variation_id' => $pendingCheckout['variation_id'],
                    'product_name' => $pendingCheckout['product_name'],
                    'variation_name' => $pendingCheckout['variation_name'],
                    'price' => $pendingCheckout['price'],
                    'quantity' => $pendingCheckout['quantity'],
                    'subtotal' => $pendingCheckout['subtotal'],
                ]);

                // Update stock
                if ($pendingCheckout['variation_id']) {
                    $variation = ProductVariation::find($pendingCheckout['variation_id']);
                    $variation->decrement('stock_quantity', $pendingCheckout['quantity']);
                } else {
                    $product = Product::find($pendingCheckout['product_id']);
                    $product->decrement('stock_quantity', $pendingCheckout['quantity']);
                }

                // Clear session data
                session()->forget([
                    'pending_direct_checkout', 
                    'pending_direct_stripe_payment_intent_id', 
                    'direct_checkout',
                    'pending_direct_payment_method',
                    'pending_direct_original_payment_method',
                    'pending_direct_original_stripe_intent_id',
                    'pending_direct_original_toyyibpay_bill_code'
                ]);

                DB::commit();

                    \Log::info('Direct checkout order created successfully from session data', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'payment_intent_id' => $paymentIntentId
                    ]);

                return redirect()->route('direct-checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
                    \Log::error('Error creating direct checkout order from session data: ' . $e->getMessage(), [
                        'payment_intent_id' => $paymentIntentId,
                        'user_id' => auth()->id(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Fallback: Try to find existing order with this payment intent
            $existingOrder = \App\Models\Order::where('stripe_payment_intent_id', $paymentIntentId)
                                            ->where('user_id', auth()->id())
                                            ->first();

            if ($existingOrder) {
                \Log::info('Found existing direct checkout order with payment intent', [
                    'order_id' => $existingOrder->id,
                    'order_number' => $existingOrder->order_number,
                    'payment_intent_id' => $paymentIntentId
                ]);

                // Check if this is a retry payment with new payment method
                $pendingPaymentMethod = session('pending_direct_payment_method');
                $pendingOriginalPaymentMethod = session('pending_direct_original_payment_method');
                
                if ($pendingPaymentMethod && $pendingPaymentMethod !== $pendingOriginalPaymentMethod) {
                    // This is a retry with new payment method - update the order
                    $existingOrder->update([
                        'payment_method' => $pendingPaymentMethod,
                        'stripe_payment_intent_id' => $paymentIntentId,
                        'toyyibpay_bill_code' => null
                    ]);
                    
                    \Log::info('Updated existing direct checkout order with new payment method after successful payment', [
                        'order_id' => $existingOrder->id,
                        'original_method' => $pendingOriginalPaymentMethod,
                        'new_method' => $pendingPaymentMethod,
                        'payment_intent_id' => $paymentIntentId
                    ]);
                }

                // Clear any stale session data
                session()->forget([
                    'pending_direct_checkout', 
                    'pending_direct_stripe_payment_intent_id', 
                    'direct_checkout',
                    'pending_direct_payment_method',
                    'pending_direct_original_payment_method',
                    'pending_direct_original_stripe_intent_id',
                    'pending_direct_original_toyyibpay_bill_code'
                ]);

                return redirect()->route('direct-checkout.success', $existingOrder->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');
            }

            // Try to find payment data in cache using payment intent metadata
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            $orderNumber = $paymentIntent->metadata->order_number ?? null;
            
            if ($orderNumber) {
                $cachedPaymentData = \Cache::get('direct_payment_data_' . auth()->id() . '_' . $orderNumber);
                
                if ($cachedPaymentData) {
                    \Log::info('Found cached direct payment data', [
                        'order_number' => $orderNumber,
            'payment_intent_id' => $paymentIntentId,
                        'cached_data_keys' => array_keys($cachedPaymentData)
                    ]);
                    
                    // Use cached data to create order
                    try {
                        DB::beginTransaction();

                        // Create the actual order
                        $order = \App\Models\Order::create([
                            'order_number' => (new \App\Models\Order())->generateOrderNumber(),
                            'user_id' => $cachedPaymentData['user_id'],
                            'status' => 'processing',
                            'subtotal' => $cachedPaymentData['subtotal'],
                            'shipping_cost' => $cachedPaymentData['shipping_cost'],
                            'tax' => $cachedPaymentData['tax'],
                            'total' => $cachedPaymentData['total'],
                            'payment_method' => $cachedPaymentData['payment_method'],
                            'payment_status' => 'paid',
                            'stripe_payment_intent_id' => $paymentIntentId,
                            'shipping_name' => $cachedPaymentData['shipping_data']['shipping_name'],
                            'shipping_email' => $cachedPaymentData['shipping_data']['shipping_email'],
                            'shipping_phone' => $cachedPaymentData['shipping_data']['shipping_phone'],
                            'shipping_address' => $cachedPaymentData['shipping_data']['shipping_address'],
                            'shipping_city' => $cachedPaymentData['shipping_data']['shipping_city'],
                            'shipping_state' => $cachedPaymentData['shipping_data']['shipping_state'],
                            'shipping_postal_code' => $cachedPaymentData['shipping_data']['shipping_postal_code'],
                            'shipping_country' => $cachedPaymentData['shipping_data']['shipping_country'],
                            'billing_name' => $cachedPaymentData['billing_data']['billing_name'],
                            'billing_email' => $cachedPaymentData['billing_data']['billing_email'],
                            'billing_phone' => $cachedPaymentData['billing_data']['billing_phone'],
                            'billing_address' => $cachedPaymentData['billing_data']['billing_address'],
                            'billing_city' => $cachedPaymentData['billing_data']['billing_city'],
                            'billing_state' => $cachedPaymentData['billing_data']['billing_state'],
                            'billing_postal_code' => $cachedPaymentData['billing_data']['billing_postal_code'],
                            'billing_country' => $cachedPaymentData['billing_data']['billing_country'],
                            'notes' => $cachedPaymentData['notes'],
                        ]);

                        // Create order item
                        \App\Models\OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $cachedPaymentData['product_id'],
                            'product_variation_id' => $cachedPaymentData['variation_id'],
                            'product_name' => $cachedPaymentData['product_name'],
                            'variation_name' => $cachedPaymentData['variation_name'],
                            'price' => $cachedPaymentData['price'],
                            'quantity' => $cachedPaymentData['quantity'],
                            'subtotal' => $cachedPaymentData['subtotal'],
                        ]);

                        // Update stock
                        if ($cachedPaymentData['variation_id']) {
                            $variation = ProductVariation::find($cachedPaymentData['variation_id']);
                            $variation->decrement('stock_quantity', $cachedPaymentData['quantity']);
                        } else {
                            $product = Product::find($cachedPaymentData['product_id']);
                            $product->decrement('stock_quantity', $cachedPaymentData['quantity']);
                        }

                        // Clear session data and cache
                        session()->forget(['pending_direct_checkout', 'pending_direct_stripe_payment_intent_id', 'direct_checkout']);
                        \Cache::forget('direct_payment_data_' . auth()->id() . '_' . $orderNumber);

                        DB::commit();

                        \Log::info('Direct checkout order created successfully from cached data', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntentId
            ]);

                        return redirect()->route('direct-checkout.success', $order->id)
                                       ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error('Error creating direct checkout order from cached data: ' . $e->getMessage(), [
                            'payment_intent_id' => $paymentIntentId,
                            'user_id' => auth()->id(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            }

            // If no existing order found, show error
            return redirect()->route('checkout.orders')
                           ->with('error', 'Sesi pembayaran tidak sah atau telah tamat. Sila cuba lagi.');
        }

        // Session data is valid, proceed with order creation
            try {
                DB::beginTransaction();

                // Create the actual order
                $order = \App\Models\Order::create([
                    'order_number' => (new \App\Models\Order())->generateOrderNumber(),
                    'user_id' => $pendingCheckout['user_id'],
                    'status' => 'processing',
                    'subtotal' => $pendingCheckout['subtotal'],
                    'shipping_cost' => $pendingCheckout['shipping_cost'],
                    'tax' => $pendingCheckout['tax'],
                    'total' => $pendingCheckout['total'],
                    'payment_method' => $pendingCheckout['payment_method'],
                    'payment_status' => 'paid',
                    'stripe_payment_intent_id' => $paymentIntentId,
                    'shipping_name' => $pendingCheckout['shipping_data']['shipping_name'],
                    'shipping_email' => $pendingCheckout['shipping_data']['shipping_email'],
                    'shipping_phone' => $pendingCheckout['shipping_data']['shipping_phone'],
                    'shipping_address' => $pendingCheckout['shipping_data']['shipping_address'],
                    'shipping_city' => $pendingCheckout['shipping_data']['shipping_city'],
                    'shipping_state' => $pendingCheckout['shipping_data']['shipping_state'],
                    'shipping_postal_code' => $pendingCheckout['shipping_data']['shipping_postal_code'],
                    'shipping_country' => $pendingCheckout['shipping_data']['shipping_country'],
                    'billing_name' => $pendingCheckout['billing_data']['billing_name'],
                    'billing_email' => $pendingCheckout['billing_data']['billing_email'],
                    'billing_phone' => $pendingCheckout['billing_data']['billing_phone'],
                    'billing_address' => $pendingCheckout['billing_data']['billing_address'],
                    'billing_city' => $pendingCheckout['billing_data']['billing_city'],
                    'billing_state' => $pendingCheckout['billing_data']['billing_state'],
                    'billing_postal_code' => $pendingCheckout['billing_data']['billing_postal_code'],
                    'billing_country' => $pendingCheckout['billing_data']['billing_country'],
                    'notes' => $pendingCheckout['notes'],
                ]);

            \Log::info('Direct checkout order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntentId
            ]);

                // Create order item
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $pendingCheckout['product_id'],
                    'product_variation_id' => $pendingCheckout['variation_id'],
                    'product_name' => $pendingCheckout['product_name'],
                    'variation_name' => $pendingCheckout['variation_name'],
                    'price' => $pendingCheckout['price'],
                    'quantity' => $pendingCheckout['quantity'],
                    'subtotal' => $pendingCheckout['subtotal'],
                ]);

                // Update stock
                if ($pendingCheckout['variation_id']) {
                    $variation = ProductVariation::find($pendingCheckout['variation_id']);
                    $variation->decrement('stock_quantity', $pendingCheckout['quantity']);
                } else {
                    $product = Product::find($pendingCheckout['product_id']);
                    $product->decrement('stock_quantity', $pendingCheckout['quantity']);
                }

                // Clear session data
                session()->forget([
                    'pending_direct_checkout', 
                    'pending_direct_stripe_payment_intent_id', 
                    'direct_checkout',
                    'pending_direct_payment_method',
                    'pending_direct_original_payment_method',
                    'pending_direct_original_stripe_intent_id',
                    'pending_direct_original_toyyibpay_bill_code'
                ]);

                DB::commit();

            \Log::info('Direct checkout Stripe payment completed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntentId
            ]);

                return redirect()->route('direct-checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
            \Log::error('Error creating direct checkout order after Stripe payment: ' . $e->getMessage(), [
                'payment_intent_id' => $paymentIntentId,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
        }
    }

    /**
     * Show Stripe payment page for direct checkout
     */
    public function stripePayment(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent_id');
        $clientSecret = $request->get('client_secret');
        $orderId = $request->get('order_id');

        // If payment_intent_id and client_secret are not provided, try to get them from the order
        if (!$paymentIntentId || !$clientSecret) {
            if ($orderId) {
                $order = Order::where('id', $orderId)
                             ->where('user_id', auth()->id())
                             ->where('payment_status', 'pending')
                             ->first();

                if ($order && $order->stripe_payment_intent_id) {
                    $paymentIntentId = $order->stripe_payment_intent_id;
                    
                    // Get client secret from Stripe
                    try {
                        $stripeService = new StripeService();
                        $paymentIntent = $stripeService->getPaymentIntent($paymentIntentId);
                        $clientSecret = $paymentIntent['client_secret'] ?? null;
                    } catch (\Exception $e) {
                        \Log::error('Failed to get payment intent', [
                            'order_id' => $orderId,
                            'payment_intent_id' => $paymentIntentId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        if (!$paymentIntentId || !$clientSecret) {
            return redirect()->route('checkout.orders')
                           ->with('error', 'Maklumat pembayaran tidak lengkap.');
        }

        return view('client.direct-checkout.stripe-payment', compact('paymentIntentId', 'clientSecret'));
    }
}
