<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BillingDetail;
use App\Models\ShippingDetail;
use App\Services\ToyyibPayService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::getOrCreateCart();
        $cart->load(['items.product', 'items.variation']);
        
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Troli anda kosong. Sila tambah item sebelum meneruskan ke pembayaran.');
        }

        $user = auth()->user();
        $billingDetails = $user->billingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        $defaultBillingDetail = $user->billingDetails()->where('is_default', true)->first();
        $shippingDetails = $user->shippingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        $defaultShippingDetail = $user->shippingDetails()->where('is_default', true)->first();
        
        return view('client.checkout.index', compact('cart', 'user', 'billingDetails', 'defaultBillingDetail', 'shippingDetails', 'defaultShippingDetail'));
    }

    public function store(Request $request)
    {
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

        $cart = Cart::getOrCreateCart();
        $cart->load(['items.product', 'items.variation']);
        
        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Troli anda kosong.');
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
            $checkoutData = [
                'user_id' => auth()->id(),
                'subtotal' => $cart->total,
                'shipping_cost' => 0.00, // Free shipping for now
                'tax' => 0.00, // No tax for now
                'total' => $cart->total,
                'payment_method' => $request->payment_method,
                'shipping_data' => $shippingData,
                'billing_data' => $billingData,
                'notes' => $request->notes,
                'cart_items' => $cart->items->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_variation_id' => $item->product_variation_id,
                        'product_name' => $item->display_name,
                        'variation_name' => $item->variation ? $item->variation->name : null,
                        'price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                    ];
                })->toArray(),
            ];

            session(['pending_checkout' => $checkoutData]);
            
            \Log::info('Pending checkout data stored in session', [
                'session_keys' => array_keys(session()->all()),
                'pending_checkout_keys' => array_keys($checkoutData)
            ]);

            // Force session save to ensure data persistence
            session()->save();

            // Create order immediately in database
            $order = Order::create([
                'order_number' => (new Order())->generateOrderNumber(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $cart->total,
                'shipping_cost' => 0.00,
                'tax' => 0.00,
                'total' => $cart->total,
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

            // Create order items
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variation_id' => $item->product_variation_id,
                    'product_name' => $item->display_name,
                    'variation_name' => $item->variation ? $item->variation->name : null,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            // Store order ID in session for payment processing
            session(['pending_order_id' => $order->id]);
            
            \Log::info('Order created immediately', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method
            ]);

            // Store payment data in database as backup (for 1 hour)
            \Cache::put('payment_data_' . auth()->id() . '_' . $order->order_number, $checkoutData, 3600);
            
            \Log::info('Payment data backed up to cache', [
                'user_id' => auth()->id(),
                'order_number' => $order->order_number,
                'cache_key' => 'payment_data_' . auth()->id() . '_' . $order->order_number
            ]);

            // Handle payment based on method
            if ($request->payment_method === 'toyyibpay') {
                // Create ToyyibPay bill
                $toyyibPayService = new ToyyibPayService();
                $paymentResult = $toyyibPayService->createBill($order);

                \Log::info('ToyyibPay payment result', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'success' => $paymentResult['success'],
                    'message' => $paymentResult['message'] ?? 'No message',
                    'payment_url' => $paymentResult['payment_url'] ?? 'No URL'
                ]);

                if ($paymentResult['success']) {
                    // Store bill code in session
                    session(['pending_bill_code' => $paymentResult['bill_code']]);
                    
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

                \Log::info('Stripe payment result', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'success' => $paymentResult['success'],
                    'payment_intent_id' => $paymentResult['payment_intent_id'] ?? 'No ID'
                ]);

                if ($paymentResult['success']) {
                    // Update order with payment intent ID
                    $order->update(['stripe_payment_intent_id' => $paymentResult['payment_intent_id']]);
                    
                    // Store payment intent ID in session
                    session(['pending_stripe_payment_intent_id' => $paymentResult['payment_intent_id']]);
                    
                    \Log::info('Stripe payment intent stored in session', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'session_keys' => array_keys(session()->all())
                    ]);
                    
                    DB::commit();
                    
                    return redirect()->route('checkout.stripe-payment', [
                        'payment_intent_id' => $paymentResult['payment_intent_id'],
                        'client_secret' => $paymentResult['client_secret']
                    ]);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Gagal membuat pembayaran Stripe. Sila cuba lagi.')
                                ->withInput();
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            
            return back()->with('error', 'Ralat semasa memproses pesanan. Sila cuba lagi.')
                        ->withInput();
        }
    }

    public function success($orderId)
    {
        $order = Order::with(['items.product', 'items.variation'])
                     ->where('user_id', auth()->id())
                     ->findOrFail($orderId);

        return view('client.checkout.success', compact('order'));
    }

    public function show($orderId)
    {
        $order = Order::with(['items.product', 'items.variation'])
                     ->where('user_id', auth()->id())
                     ->findOrFail($orderId);

        return view('client.checkout.show', compact('order'));
    }

    public function indexOrders(Request $request)
    {
        $status = $request->get('status');
        
        $query = auth()->user()->orders()
                    ->with(['items.product', 'items.variation']);
        
        // Filter by status if provided
        if ($status && in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])) {
            $query->where('status', $status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get order counts for each status
        $orderCounts = auth()->user()->orders()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Ensure all statuses have a count (even if 0)
        $allStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];
        foreach ($allStatuses as $statusType) {
            if (!isset($orderCounts[$statusType])) {
                $orderCounts[$statusType] = 0;
            }
        }

        return view('client.checkout.orders', compact('orders', 'orderCounts', 'status'));
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
     * Retry payment for a failed order
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
                $paymentResult = $toyyibPayService->createBill($order);

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_bill_code' => $paymentResult['bill_code'],
                        'pending_order_id' => $order->id,
                        'pending_payment_method' => $order->payment_method,
                        'pending_original_payment_method' => $order->payment_method,
                        'pending_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
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
                        'pending_stripe_payment_intent_id' => $paymentResult['payment_intent_id'],
                        'pending_order_id' => $order->id,
                        'pending_payment_method' => $order->payment_method,
                        'pending_original_payment_method' => $order->payment_method,
                        'pending_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect()->route('checkout.stripe-payment', [
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
            \Log::error('Failed to retry payment', [
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
                $paymentResult = $toyyibPayService->createBill($order, null, true); // true = isRetryPayment

                if ($paymentResult['success']) {
                    // Store new payment details in session (don't update order yet)
                    session([
                        'pending_bill_code' => $paymentResult['bill_code'],
                        'pending_order_id' => $order->id,
                        'pending_payment_method' => $request->payment_method,
                        'pending_original_payment_method' => $originalPaymentMethod,
                        'pending_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
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
                        'pending_stripe_payment_intent_id' => $paymentResult['payment_intent_id'],
                        'pending_order_id' => $order->id,
                        'pending_payment_method' => $request->payment_method,
                        'pending_original_payment_method' => $originalPaymentMethod,
                        'pending_original_stripe_intent_id' => $originalStripeIntentId,
                        'pending_original_toyyibpay_bill_code' => $originalToyyibpayBillCode
                    ]);
                    
                    DB::commit();
                    return redirect()->route('checkout.stripe-payment', [
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
            \Log::error('Failed to retry payment with method', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'payment_method' => $request->payment_method,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Ralat semasa mencuba pembayaran semula. Sila cuba lagi.');
        }
    }

    /**
     * Handle ToyyibPay return URL (when user returns from payment)
     */
    public function toyyibpayReturn(Request $request)
    {
        $billCode = $request->get('billcode');
        $status = $request->get('status');

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
            $pendingOrderId = session('pending_order_id');
            $pendingBillCode = session('pending_bill_code');
            
            if ($pendingOrderId && $pendingBillCode === $billCode) {
                $order = Order::where('id', $pendingOrderId)
                             ->where('user_id', auth()->id())
                             ->first();
                
                \Log::info('Found order from pending session data for ToyyibPay return', [
                    'order_id' => $pendingOrderId,
                    'bill_code' => $billCode,
                    'session_pending_order_id' => $pendingOrderId,
                    'session_pending_bill_code' => $pendingBillCode
                ]);
            }
        }

        if (!$order) {
            \Log::error('Order not found for ToyyibPay bill code', [
                'bill_code' => $billCode,
                'user_id' => auth()->id(),
                'session_pending_order_id' => session('pending_order_id'),
                'session_pending_bill_code' => session('pending_bill_code'),
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
                $pendingPaymentMethod = session('pending_payment_method');
                $pendingOriginalPaymentMethod = session('pending_original_payment_method');
                $pendingBillCode = session('pending_bill_code');
                
                if ($pendingBillCode === $billCode) {
                    // This is a retry payment - update the order
                    if ($pendingPaymentMethod && $pendingPaymentMethod !== $pendingOriginalPaymentMethod) {
                        // Retry with new payment method
                        $order->update([
                            'payment_method' => $pendingPaymentMethod,
                            'stripe_payment_intent_id' => null,
                            'toyyibpay_bill_code' => $billCode
                        ]);
                        
                        \Log::info('Updated order with new payment method after successful payment', [
                            'order_id' => $order->id,
                            'original_method' => $pendingOriginalPaymentMethod,
                            'new_method' => $pendingPaymentMethod,
                            'bill_code' => $billCode
                        ]);
                    } else {
                        // Retry with same payment method - just update the bill code
                        $order->update(['toyyibpay_bill_code' => $billCode]);
                        
                        \Log::info('Updated order with new bill code after successful retry payment', [
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

                // Clear the cart
                $cart = Cart::getOrCreateCart();
                $cart->items()->delete();

                // Clear session data
                session()->forget([
                    'pending_checkout', 
                    'pending_bill_code', 
                    'pending_order_id',
                    'pending_payment_method',
                    'pending_original_payment_method',
                    'pending_original_stripe_intent_id',
                    'pending_original_toyyibpay_bill_code'
                ]);

                DB::commit();

                return redirect()->route('checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating order after payment: ' . $e->getMessage());
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
            }
        } else {
            // Payment failed - update order status and clear pending session data
            $order->update(['payment_status' => 'failed']);
            
            // Clear pending session data since payment failed
            session()->forget([
                'pending_bill_code', 
                'pending_order_id',
                'pending_payment_method',
                'pending_original_payment_method',
                'pending_original_stripe_intent_id',
                'pending_original_toyyibpay_bill_code'
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('warning', 'Pembayaran gagal. Anda boleh cuba bayar semula.');
        }
    }

    /**
     * Handle ToyyibPay callback (server-to-server notification)
     */
    public function toyyibpayCallback(Request $request)
    {
        \Log::info('ToyyibPay callback received', $request->all());

        $billCode = $request->get('billcode');
        $status = $request->get('status');
        $amount = $request->get('amount');

        if (!$billCode) {
            return response('Invalid request', 400);
        }

        // Verify payment status
        $toyyibPayService = new ToyyibPayService();
        $paymentResult = $toyyibPayService->verifyPayment($billCode);

        if ($paymentResult['success'] && $paymentResult['paid']) {
            \Log::info('Payment verified via callback', [
                'bill_code' => $billCode,
                'amount' => $amount
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Handle Stripe payment return
     */
    public function stripeReturn(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');
        $paymentIntentClientSecret = $request->get('payment_intent_client_secret');

        \Log::info('Stripe return called', [
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

        // Get order from database using payment intent ID
        // First try to find order with this payment intent ID in the database
        $order = Order::where('stripe_payment_intent_id', $paymentIntentId)
                     ->where('user_id', auth()->id())
                     ->first();

        // If not found in database, check if it's a pending payment in session
        if (!$order) {
            $pendingOrderId = session('pending_order_id');
            $pendingPaymentIntentId = session('pending_stripe_payment_intent_id');
            
            if ($pendingOrderId && $pendingPaymentIntentId === $paymentIntentId) {
                $order = Order::where('id', $pendingOrderId)
                             ->where('user_id', auth()->id())
                             ->first();
                
                \Log::info('Found order from pending session data', [
                    'order_id' => $pendingOrderId,
                    'payment_intent_id' => $paymentIntentId,
                    'session_pending_order_id' => $pendingOrderId,
                    'session_pending_payment_intent_id' => $pendingPaymentIntentId
                ]);
            }
        }

        if (!$order) {
            \Log::error('Order not found for payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'user_id' => auth()->id(),
                'session_pending_order_id' => session('pending_order_id'),
                'session_pending_payment_intent_id' => session('pending_stripe_payment_intent_id'),
                'all_session_data' => session()->all()
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('error', 'Pesanan tidak dijumpai.');
        }

        // Verify payment status with Stripe
        $stripeService = new StripeService();
        $paymentResult = $stripeService->verifyPayment($paymentIntentId);

        \Log::info('Stripe payment verification result', [
            'payment_intent_id' => $paymentIntentId,
            'success' => $paymentResult['success'],
            'paid' => $paymentResult['paid'] ?? false,
            'status' => $paymentResult['status'] ?? 'unknown'
        ]);

        if (!$paymentResult['success']) {
            return redirect()->route('checkout.orders')
                           ->with('error', 'Gagal mengesahkan status pembayaran. Sila hubungi kami.');
        }

        if ($paymentResult['paid']) {
            try {
                DB::beginTransaction();

                // Check if this is a retry payment with new payment method
                $pendingPaymentMethod = session('pending_payment_method');
                $pendingOriginalPaymentMethod = session('pending_original_payment_method');
                $pendingPaymentIntentId = session('pending_stripe_payment_intent_id');
                
                // Update the order's stripe_payment_intent_id if this is a pending payment
                if ($pendingPaymentIntentId === $paymentIntentId) {
                    $order->update(['stripe_payment_intent_id' => $paymentIntentId]);
                    
                    \Log::info('Updated order with new payment intent ID after successful payment', [
                        'order_id' => $order->id,
                        'payment_intent_id' => $paymentIntentId
                    ]);
                }
                
                if ($pendingPaymentMethod && $pendingPaymentMethod !== $pendingOriginalPaymentMethod) {
                    // This is a retry with new payment method - update the order
                    $order->update([
                        'payment_method' => $pendingPaymentMethod,
                        'toyyibpay_bill_code' => null
                    ]);
                    
                    \Log::info('Updated order with new payment method after successful payment', [
                        'order_id' => $order->id,
                        'original_method' => $pendingOriginalPaymentMethod,
                        'new_method' => $pendingPaymentMethod,
                        'payment_intent_id' => $paymentIntentId
                    ]);
                }

                // Update order status to paid and processing
                $order->update([
                    'status' => 'processing',
                    'payment_status' => 'paid'
                ]);

                // Clear the cart
                $cart = Cart::getOrCreateCart();
                $cart->items()->delete();

                // Clear session data
                session()->forget([
                    'pending_checkout', 
                    'pending_stripe_payment_intent_id', 
                    'pending_order_id',
                    'pending_payment_method',
                    'pending_original_payment_method',
                    'pending_original_stripe_intent_id',
                    'pending_original_toyyibpay_bill_code'
                ]);

                DB::commit();

                return redirect()->route('checkout.success', $order->id)
                               ->with('success', 'Pembayaran berjaya! Pesanan anda sedang diproses.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating order after payment: ' . $e->getMessage());
                
                return redirect()->route('checkout.orders')
                               ->with('error', 'Ralat semasa memproses pesanan. Sila hubungi kami.');
            }
        } else {
            // Payment failed - update order status and clear pending session data
            $order->update(['payment_status' => 'failed']);
            
            // Clear pending session data since payment failed
            session()->forget([
                'pending_stripe_payment_intent_id', 
                'pending_order_id',
                'pending_payment_method',
                'pending_original_payment_method',
                'pending_original_stripe_intent_id',
                'pending_original_toyyibpay_bill_code'
            ]);
            
            return redirect()->route('checkout.orders')
                           ->with('warning', 'Pembayaran gagal. Anda boleh cuba bayar semula.');
        }
    }

    /**
     * Show Stripe payment page
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

        return view('client.checkout.stripe-payment', compact('paymentIntentId', 'clientSecret'));
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
}
