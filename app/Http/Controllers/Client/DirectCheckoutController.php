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
        // Check if there's a failed payment in session that needs to be retried
        $failedPaymentOrderId = session('failed_payment_order_id');
        
        if ($failedPaymentOrderId) {
            // Find the failed order
            $failedOrder = Order::where('id', $failedPaymentOrderId)
                               ->where('user_id', auth()->id())
                               ->whereIn('payment_status', ['failed', 'pending'])
                               ->whereNotIn('status', ['cancelled', 'refunded'])
                               ->first();
            
            if ($failedOrder) {
                \Log::info('Redirecting to retry payment from direct checkout page', [
                    'order_id' => $failedOrder->id,
                    'payment_status' => $failedOrder->payment_status,
                    'payment_method' => $failedOrder->payment_method
                ]);
                
                // Clear the failed payment session data
                session()->forget([
                    'failed_payment_order_id',
                    'failed_payment_bill_code',
                    'failed_payment_payment_intent_id',
                    'failed_payment_method'
                ]);
                
                return redirect()->route('checkout.show-retry-payment', $failedOrder->id)
                               ->with('warning', 'Pembayaran sebelumnya gagal. Anda boleh cuba bayar semula.');
            } else {
                // Clear invalid failed payment session data
                session()->forget([
                    'failed_payment_order_id',
                    'failed_payment_bill_code',
                    'failed_payment_payment_intent_id',
                    'failed_payment_method'
                ]);
            }
        }

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
            'fpl_manager_name' => 'required|string|max:255',
            'fpl_team_name' => 'required|string|max:255',
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
                'fpl_manager_name' => $request->fpl_manager_name,
                'fpl_team_name' => $request->fpl_team_name,
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

            // For ToyyibPay, don't create order yet - wait for response
            // For Stripe, create order immediately since we need it for payment intent
            if ($request->payment_method === 'toyyibpay') {
                // Store checkout data in session for ToyyibPay response
                session(['pending_direct_checkout' => $pendingCheckoutData]);
                
                \Log::info('Direct checkout data stored in session for ToyyibPay - order will be created after response', [
                    'user_id' => auth()->id(),
                    'payment_method' => $request->payment_method,
                    'session_keys' => array_keys(session()->all())
                ]);
            } else {
                // Create order immediately for Stripe
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
                    'fpl_manager_name' => $request->fpl_manager_name,
                    'fpl_team_name' => $request->fpl_team_name,
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
                
                \Log::info('Direct checkout order created immediately for Stripe', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => auth()->id(),
                    'payment_method' => $request->payment_method
                ]);
            }

            // Handle payment based on method
            if ($request->payment_method === 'toyyibpay') {
                // Store payment data in database as backup (for 1 hour) - use session ID since no order number yet
                $cacheKey = 'direct_payment_data_' . auth()->id() . '_' . session()->getId();
                \Cache::put($cacheKey, $pendingCheckoutData, 3600);
                
                \Log::info('Direct payment data backed up to cache for ToyyibPay', [
                    'user_id' => auth()->id(),
                    'cache_key' => $cacheKey
                ]);

                // Create ToyyibPay bill (pass checkout data instead of order)
                $toyyibPayService = new ToyyibPayService();
                $paymentResult = $toyyibPayService->createBill($pendingCheckoutData, route('direct-checkout.toyyibpay.return'));

                \Log::info('ToyyibPay direct checkout payment result', [
                    'user_id' => auth()->id(),
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
                    return back()->with('error', 'Gagal membuat bil pembayaran. Sila cuba lagi.')
                                ->withInput();
                }
            } elseif ($request->payment_method === 'stripe') {
                // Store payment data in database as backup (for 1 hour)
                \Cache::put('direct_payment_data_' . auth()->id() . '_' . $order->order_number, $pendingCheckoutData, 3600);
                
                \Log::info('Direct payment data backed up to cache for Stripe', [
                    'user_id' => auth()->id(),
                    'order_number' => $order->order_number,
                    'cache_key' => 'direct_payment_data_' . auth()->id() . '_' . $order->order_number
                ]);

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

            // Restore stock quantities for all order items
            foreach ($order->items as $item) {
                if ($item->product_variation_id) {
                    // Restore stock for product variation
                    $variation = \App\Models\ProductVariation::find($item->product_variation_id);
                    if ($variation) {
                        $variation->increment('stock_quantity', $item->quantity);
                        \Log::info('Stock restored for variation after direct checkout order cancellation', [
                            'order_id' => $order->id,
                            'variation_id' => $variation->id,
                            'variation_name' => $variation->name,
                            'quantity_restored' => $item->quantity,
                            'remaining_stock' => $variation->fresh()->stock_quantity
                        ]);
                    }
                } else {
                    // Restore stock for base product
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock_quantity', $item->quantity);
                        \Log::info('Stock restored for product after direct checkout order cancellation', [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'product_title' => $product->title,
                            'quantity_restored' => $item->quantity,
                            'remaining_stock' => $product->fresh()->stock_quantity
                        ]);
                    }
                }
            }

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
                // Try to reuse existing bill code first, then create new one if needed
                $toyyibPayService = new ToyyibPayService();
                
                // If we have an existing bill code, try to reuse it
                if ($originalToyyibpayBillCode) {
                    $paymentResult = $toyyibPayService->reuseBill($originalToyyibpayBillCode, $order);
                } else {
                    // No existing bill code, create new one
                    $paymentResult = $toyyibPayService->createBill($order, route('direct-checkout.toyyibpay.return'), true);
                }

                if ($paymentResult['success']) {
                    // Store payment details in session (don't update order yet)
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
                // Try to reuse existing payment intent first, then create new one if needed
                $stripeService = new StripeService();
                
                // If we have an existing payment intent ID, try to reuse it
                if ($originalStripeIntentId) {
                    $paymentResult = $stripeService->reusePaymentIntent($originalStripeIntentId, $order);
                } else {
                    // No existing payment intent ID, create new one
                    $paymentResult = $stripeService->createPaymentIntent($order);
                }

                if ($paymentResult['success']) {
                    // Store payment details in session (don't update order yet)
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

        // If still no order found, check if we have pending checkout data (new flow)
        if (!$order) {
            $pendingCheckout = session('pending_direct_checkout');
            
            if ($pendingCheckout) {
                \Log::info('Found pending direct checkout data - will create order after payment verification', [
                    'bill_code' => $billCode,
                    'user_id' => auth()->id(),
                    'pending_checkout_keys' => array_keys($pendingCheckout)
                ]);
            } else {
                \Log::error('No order or pending checkout data found for direct checkout ToyyibPay bill code', [
                    'bill_code' => $billCode,
                    'user_id' => auth()->id(),
                    'session_pending_order_id' => session('pending_direct_order_id'),
                    'session_pending_bill_code' => session('pending_direct_bill_code'),
                    'session_pending_direct_checkout' => session('pending_direct_checkout'),
                    'all_session_data' => session()->all()
                ]);
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Pesanan tidak dijumpai.');
            }
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
                
                if ($order) {
                    // Order already exists - this is a retry payment
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
                } else {
                    // No order exists - create new order from pending checkout data
                    $pendingCheckout = session('pending_direct_checkout');
                    
                    if ($pendingCheckout) {
                        // Create the order
                        $order = Order::create([
                            'order_number' => (new Order())->generateOrderNumber(),
                            'user_id' => auth()->id(),
                            'status' => 'processing',
                            'subtotal' => $pendingCheckout['subtotal'],
                            'shipping_cost' => $pendingCheckout['shipping_cost'],
                            'tax' => $pendingCheckout['tax'],
                            'total' => $pendingCheckout['total'],
                            'payment_method' => $pendingCheckout['payment_method'],
                            'payment_status' => 'paid',
                            'toyyibpay_bill_code' => $billCode,
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
                            'fpl_manager_name' => $pendingCheckout['fpl_manager_name'],
                            'fpl_team_name' => $pendingCheckout['fpl_team_name'],
                            'notes' => $pendingCheckout['notes'],
                        ]);

                        // Create order item
                        $order->items()->create([
                            'product_id' => $pendingCheckout['product_id'],
                            'product_variation_id' => $pendingCheckout['variation_id'],
                            'product_name' => $pendingCheckout['product_name'],
                            'variation_name' => $pendingCheckout['variation_name'],
                            'price' => $pendingCheckout['price'],
                            'quantity' => $pendingCheckout['quantity'],
                            'subtotal' => $pendingCheckout['subtotal'],
                        ]);

                        \Log::info('Created new direct checkout order from pending checkout data after successful ToyyibPay payment', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'bill_code' => $billCode,
                            'user_id' => auth()->id()
                        ]);
                    } else {
                        throw new \Exception('No pending checkout data found for order creation');
                    }
                }

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

                // Send order confirmation emails with invoice
                try {
                    $orderEmailService = new \App\Services\OrderEmailService(new \App\Services\InvoiceService());
                    $orderEmailService->sendOrderConfirmationEmails($order);
                } catch (\Exception $e) {
                    \Log::error('Failed to send order confirmation emails', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the payment process if email fails
                }

                return redirect()->route('direct-checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating direct checkout order after payment: ' . $e->getMessage());
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
            }
        } else {
            // Payment failed or user clicked back - create order if it doesn't exist and redirect to retry
            if (!$order) {
                // Create order from pending checkout data even for failed payment
                $pendingCheckout = session('pending_direct_checkout');
                
                if ($pendingCheckout) {
                    try {
                        DB::beginTransaction();
                        
                        // Create the order with failed payment status
                        $order = Order::create([
                            'order_number' => (new Order())->generateOrderNumber(),
                            'user_id' => auth()->id(),
                            'status' => 'pending',
                            'subtotal' => $pendingCheckout['subtotal'],
                            'shipping_cost' => $pendingCheckout['shipping_cost'],
                            'tax' => $pendingCheckout['tax'],
                            'total' => $pendingCheckout['total'],
                            'payment_method' => $pendingCheckout['payment_method'],
                            'payment_status' => 'failed',
                            'toyyibpay_bill_code' => $billCode,
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
                            'fpl_manager_name' => $pendingCheckout['fpl_manager_name'],
                            'fpl_team_name' => $pendingCheckout['fpl_team_name'],
                            'notes' => $pendingCheckout['notes'],
                        ]);

                        // Create order item
                        $order->items()->create([
                            'product_id' => $pendingCheckout['product_id'],
                            'product_variation_id' => $pendingCheckout['variation_id'],
                            'product_name' => $pendingCheckout['product_name'],
                            'variation_name' => $pendingCheckout['variation_name'],
                            'price' => $pendingCheckout['price'],
                            'quantity' => $pendingCheckout['quantity'],
                            'subtotal' => $pendingCheckout['subtotal'],
                        ]);

                        DB::commit();
                        
                        \Log::info('Created new direct checkout order from pending checkout data for failed payment', [
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'bill_code' => $billCode,
                            'user_id' => auth()->id(),
                            'payment_status' => 'failed'
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error('Error creating order for failed direct checkout payment: ' . $e->getMessage());
                        
                        return redirect()->route('checkout.orders')
                                       ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
                    }
                } else {
                    \Log::warning('Direct checkout ToyyibPay payment failed but no pending checkout data found', [
                        'bill_code' => $billCode,
                        'user_id' => auth()->id()
                    ]);
                    
                    return redirect()->route('checkout.orders')
                                   ->with('error', 'Pembayaran gagal dan maklumat pesanan tidak dijumpai.');
                }
            } else {
                // Order exists - update payment status to failed
                $order->update(['payment_status' => 'failed']);
            }
            
            // Store order information in session for retry payment
            session([
                'failed_payment_order_id' => $order->id,
                'failed_payment_bill_code' => $billCode,
                'failed_payment_method' => $order->payment_method
            ]);
            
            \Log::info('Direct checkout ToyyibPay payment failed - redirecting to retry payment', [
                'order_id' => $order->id,
                'bill_code' => $billCode,
                'payment_status' => $order->payment_status
            ]);
            
            return redirect()->route('checkout.show-retry-payment', $order->id)
                           ->with('warning', 'Pembayaran gagal atau dibatalkan. Anda boleh cuba bayar semula.');
        }
    }

    /**
     * Handle ToyyibPay payment cancellation for direct checkout (when user clicks back or cancels)
     */
    public function toyyibpayCancel(Request $request)
    {
        $billCode = $request->get('billcode');
        $orderId = $request->get('order_id');

        \Log::info('Direct checkout ToyyibPay payment cancellation requested', [
            'bill_code' => $billCode,
            'order_id' => $orderId,
            'user_id' => auth()->id(),
            'all_request_params' => $request->all()
        ]);

        // Try to find the order
        $order = null;
        
        if ($orderId) {
            $order = Order::where('id', $orderId)
                         ->where('user_id', auth()->id())
                         ->first();
        } elseif ($billCode) {
            $order = Order::where('toyyibpay_bill_code', $billCode)
                         ->where('user_id', auth()->id())
                         ->first();
        }

        // If no order found, check session data
        if (!$order) {
            $pendingOrderId = session('pending_direct_order_id');
            $pendingBillCode = session('pending_direct_bill_code');
            
            if ($pendingOrderId && (!$billCode || $pendingBillCode === $billCode)) {
                $order = Order::where('id', $pendingOrderId)
                             ->where('user_id', auth()->id())
                             ->first();
            }
        }

        if (!$order) {
            \Log::warning('No order found for direct checkout ToyyibPay cancellation', [
                'bill_code' => $billCode,
                'order_id' => $orderId,
                'user_id' => auth()->id(),
                'session_pending_direct_order_id' => session('pending_direct_order_id'),
                'session_pending_direct_bill_code' => session('pending_direct_bill_code')
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('error', 'Pesanan tidak dijumpai.');
        }

        // Update order status to failed
        $order->update(['payment_status' => 'failed']);

        // Store order information in session for retry payment
        session([
            'failed_payment_order_id' => $order->id,
            'failed_payment_bill_code' => $order->toyyibpay_bill_code,
            'failed_payment_method' => $order->payment_method
        ]);

        // Clear pending session data but keep order info for retry
        session()->forget([
            'pending_direct_checkout',
            'pending_direct_bill_code', 
            'pending_direct_order_id',
            'pending_direct_payment_method',
            'pending_direct_original_payment_method',
            'pending_direct_original_stripe_intent_id',
            'pending_direct_original_toyyibpay_bill_code'
        ]);

        \Log::info('Direct checkout ToyyibPay payment cancelled - redirecting to retry payment', [
            'order_id' => $order->id,
            'bill_code' => $order->toyyibpay_bill_code,
            'payment_status' => $order->payment_status
        ]);

        return redirect()->route('checkout.show-retry-payment', $order->id)
                       ->with('warning', 'Pembayaran dibatalkan. Anda boleh cuba bayar semula.');
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
            // Payment failed or user clicked back - preserve order for retry
            // Try to find existing order with this payment intent
            $existingOrder = \App\Models\Order::where('stripe_payment_intent_id', $paymentIntentId)
                                            ->where('user_id', auth()->id())
                                            ->first();
            
            if ($existingOrder) {
                $existingOrder->update(['payment_status' => 'failed']);
                
                // Store order information in session for retry payment
                session([
                    'failed_payment_order_id' => $existingOrder->id,
                    'failed_payment_payment_intent_id' => $paymentIntentId,
                    'failed_payment_method' => $existingOrder->payment_method
                ]);
                
                \Log::info('Direct checkout Stripe payment failed - redirecting to retry payment', [
                    'order_id' => $existingOrder->id,
                    'payment_intent_id' => $paymentIntentId,
                    'payment_status' => $existingOrder->payment_status
                ]);
                
                return redirect()->route('checkout.show-retry-payment', $existingOrder->id)
                               ->with('warning', 'Pembayaran gagal atau dibatalkan. Anda boleh cuba bayar semula.');
            }
            
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

            // Try to find existing order with this payment intent first
            $existingOrder = \App\Models\Order::where('stripe_payment_intent_id', $paymentIntentId)
                                            ->where('user_id', auth()->id())
                                            ->first();

            if ($existingOrder) {
                \Log::info('Found existing direct checkout order with payment intent', [
                    'order_id' => $existingOrder->id,
                    'order_number' => $existingOrder->order_number,
                    'payment_intent_id' => $paymentIntentId
                ]);

                // Update the existing order to paid status
                $existingOrder->update([
                    'status' => 'processing',
                    'payment_status' => 'paid'
                ]);

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

                // Send order confirmation emails with invoice
                try {
                    $orderEmailService = new \App\Services\OrderEmailService(new \App\Services\InvoiceService());
                    $orderEmailService->sendOrderConfirmationEmails($existingOrder);
                } catch (\Exception $e) {
                    \Log::error('Failed to send order confirmation emails', [
                        'order_id' => $existingOrder->id,
                        'error' => $e->getMessage()
                    ]);
                }

                return redirect()->route('direct-checkout.success', $existingOrder->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');
            }

            // If no existing order found, show error
            return redirect()->route('checkout.orders')
                           ->with('error', 'Sesi pembayaran tidak sah atau telah tamat. Sila cuba lagi.');
        }

        // Session data is valid, proceed with order update
        try {
            DB::beginTransaction();

            // Find the existing order that was created during checkout
            $pendingOrderId = session('pending_direct_order_id');
            $order = \App\Models\Order::where('id', $pendingOrderId)
                                     ->where('user_id', auth()->id())
                                     ->first();

            if (!$order) {
                \Log::error('No pending order found for direct checkout', [
                    'pending_order_id' => $pendingOrderId,
                    'user_id' => auth()->id(),
                    'payment_intent_id' => $paymentIntentId
                ]);
                return redirect()->route('checkout.orders')
                               ->with('error', 'Pesanan tidak dijumpai. Sila cuba lagi.');
            }

            // Update the existing order with payment success
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid',
                'stripe_payment_intent_id' => $paymentIntentId
            ]);

            \Log::info('Direct checkout order updated successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntentId
            ]);

            // Update stock (only if not already updated)
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
                'pending_direct_order_id',
                'pending_direct_payment_method',
                'pending_direct_original_payment_method',
                'pending_direct_original_stripe_intent_id',
                'pending_direct_original_toyyibpay_bill_code'
            ]);

            DB::commit();

            // Send order confirmation emails with invoice
            try {
                $orderEmailService = new \App\Services\OrderEmailService(new \App\Services\InvoiceService());
                $orderEmailService->sendOrderConfirmationEmails($order);
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation emails', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the payment process if email fails
            }

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

    /**
     * Download invoice PDF for an order
     */
    public function downloadInvoice($orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->with(['items.product', 'items.variation'])
                     ->firstOrFail();

        // Check if order is eligible for invoice download
        if (($order->status === 'pending' && $order->payment_status === 'pending') || $order->payment_status === 'failed') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang belum dibayar atau pembayaran gagal.');
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang telah dibatalkan.');
        }

        try {
            $invoiceService = new \App\Services\InvoiceService();
            $pdfPath = $invoiceService->generateInvoice($order);
            
            // Check if invoice generation failed
            if (!$pdfPath) {
                \Log::error('Invoice generation returned null', [
                    'order_id' => $orderId,
                    'user_id' => auth()->id()
                ]);
                return back()->with('error', 'Gagal menjana invois. Sila cuba lagi.');
            }
            
            // Check if file exists
            if (!file_exists($pdfPath)) {
                \Log::error('Generated invoice file does not exist', [
                    'order_id' => $orderId,
                    'user_id' => auth()->id(),
                    'filepath' => $pdfPath
                ]);
                return back()->with('error', 'Fail invois tidak dijumpai. Sila cuba lagi.');
            }
            
            // Get the filename from the path
            $filename = basename($pdfPath);
            
            // Determine content type based on file extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            
            // Return the file as a download response
            return response()->download($pdfPath, $filename, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to download invoice: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Gagal memuat turun invois. Sila cuba lagi.');
        }
    }

    /**
     * View invoice PDF in browser
     */
    public function viewInvoice($orderId)
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', auth()->id())
                     ->with(['items.product', 'items.variation'])
                     ->firstOrFail();

        // Check if order is eligible for invoice viewing
        if (($order->status === 'pending' && $order->payment_status === 'pending') || $order->payment_status === 'failed') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang belum dibayar atau pembayaran gagal.');
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang telah dibatalkan.');
        }

        try {
            $invoiceService = new \App\Services\InvoiceService();
            $pdfPath = $invoiceService->generateInvoice($order);
            
            // Check if invoice generation failed
            if (!$pdfPath) {
                \Log::error('Invoice generation returned null', [
                    'order_id' => $orderId,
                    'user_id' => auth()->id()
                ]);
                return back()->with('error', 'Gagal menjana invois. Sila cuba lagi.');
            }
            
            // Check if file exists
            if (!file_exists($pdfPath)) {
                \Log::error('Generated invoice file does not exist', [
                    'order_id' => $orderId,
                    'user_id' => auth()->id(),
                    'filepath' => $pdfPath
                ]);
                return back()->with('error', 'Fail invois tidak dijumpai. Sila cuba lagi.');
            }
            
            // Get the filename and determine content type
            $filename = basename($pdfPath);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            $disposition = $extension === 'html' ? 'inline' : 'inline';
            
            // Return the file to be displayed in browser
            return response()->file($pdfPath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to view invoice: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'user_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Gagal memaparkan invois. Sila cuba lagi.');
        }
    }

    private function getContentType($extension)
    {
        switch ($extension) {
            case 'pdf':
                return 'application/pdf';
            case 'doc':
            case 'docx':
                return 'application/msword';
            case 'xls':
            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            case 'txt':
                return 'text/plain';
            case 'html':
                return 'text/html';
            default:
                return 'application/octet-stream'; // Default for unknown types
        }
    }
}
