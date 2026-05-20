<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MobilePaymentController extends Controller
{
    /**
     * POST /api/mobile/checkout/toyyibpay/create
     */
    public function createToyyibPayPayment(Request $request)
    {
        if (!setting('toyyibpay_payment_enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kaedah pembayaran ToyyibPay tidak tersedia buat masa ini.',
            ], 422);
        }

        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.product_variation_id' => 'nullable|integer|exists:product_variations,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping' => 'required|array',
                'shipping.name' => 'required|string|max:255',
                'shipping.email' => 'required|email|max:255',
                'shipping.phone' => 'required|string|max:20',
                'shipping.address' => 'required|string',
                'shipping.city' => 'required|string|max:255',
                'shipping.state' => 'required|string|max:255',
                'shipping.postal_code' => 'required|string|max:10',
                'shipping.country' => 'required|string|max:255',
                'billing' => 'required|array',
                'billing.name' => 'required|string|max:255',
                'billing.email' => 'required|email|max:255',
                'billing.phone' => 'required|string|max:20',
                'billing.address' => 'required|string',
                'billing.city' => 'required|string|max:255',
                'billing.state' => 'required|string|max:255',
                'billing.postal_code' => 'required|string|max:10',
                'billing.country' => 'required|string|max:255',
                'fees' => 'required|array',
                'fees.shipping_fee' => 'required|numeric|min:0',
                'fees.tax_fee' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0.01',
                'notes' => 'nullable|string',
                'fpl_manager_name' => 'nullable|string|max:255',
                'fpl_team_name' => 'nullable|string|max:255',
                'return_url' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to process your order. Please check your cart and try again.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $lineItems = [];
            $subtotal = 0.0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->where('id', $item['product_id'])
                    ->where('status', 'active')
                    ->first();

                if (!$product) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak dijumpai atau tidak aktif.',
                        'product_id' => $item['product_id'],
                    ], 422);
                }

                $quantity = (int) $item['quantity'];
                $variation = null;
                $unitPrice = null;
                $variationName = null;

                if (!empty($item['product_variation_id'])) {
                    $variation = ProductVariation::query()
                        ->where('id', $item['product_variation_id'])
                        ->where('product_id', $product->id)
                        ->where('is_active', true)
                        ->first();

                    if (!$variation) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Something went wrong. Please try again.',
                            'product_variation_id' => $item['product_variation_id'],
                        ], 422);
                    }

                    if ((int) $variation->stock_quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected size is out of stock.',
                            'product_variation_id' => $variation->id,
                            'available_stock' => (int) $variation->stock_quantity,
                        ], 422);
                    }

                    $unitPrice = (float) ($variation->sale_price ?? $variation->price ?? $product->sale_price ?? $product->price);
                    $variationName = $variation->name;
                } else {
                    if ((int) $product->stock_quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'This product is currently out of stock.',
                            'product_id' => $product->id,
                            'available_stock' => (int) $product->stock_quantity,
                        ], 422);
                    }

                    $unitPrice = (float) ($product->sale_price ?? $product->price);
                }

                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;

                $lineItems[] = [
                    'product_id' => $product->id,
                    'product_variation_id' => $variation?->id,
                    'product_name' => $product->title,
                    'variation_name' => $variationName,
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $shippingFee = (float) $validated['fees']['shipping_fee'];
            $taxFee = (float) $validated['fees']['tax_fee'];
            $serverTotal = round($subtotal + $shippingFee + $taxFee, 2);
            $clientTotal = round((float) $validated['total'], 2);
            $debugContext = $this->buildCheckoutDebugContext($shippingFee, $taxFee, $subtotal, $serverTotal, $clientTotal);

            if (abs($serverTotal - $clientTotal) > 0.01) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to process your order. Please check your cart and try again.',
                    'expected_total' => $serverTotal,
                    'received_total' => $clientTotal,
                ], 422);
            }

            $order = Order::create([
                'order_number' => (new Order())->generateOrderNumber(),
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingFee,
                'tax' => $taxFee,
                'total' => $serverTotal,
                'payment_method' => 'toyyibpay',
                'payment_status' => 'pending',
                'shipping_name' => $validated['shipping']['name'],
                'shipping_email' => $validated['shipping']['email'],
                'shipping_phone' => $validated['shipping']['phone'],
                'shipping_address' => $validated['shipping']['address'],
                'shipping_city' => $validated['shipping']['city'],
                'shipping_state' => $validated['shipping']['state'],
                'shipping_postal_code' => $validated['shipping']['postal_code'],
                'shipping_country' => $validated['shipping']['country'],
                'billing_name' => $validated['billing']['name'],
                'billing_email' => $validated['billing']['email'],
                'billing_phone' => $validated['billing']['phone'],
                'billing_address' => $validated['billing']['address'],
                'billing_city' => $validated['billing']['city'],
                'billing_state' => $validated['billing']['state'],
                'billing_postal_code' => $validated['billing']['postal_code'],
                'billing_country' => $validated['billing']['country'],
                'fpl_manager_name' => $validated['fpl_manager_name'] ?? null,
                'fpl_team_name' => $validated['fpl_team_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lineItems as $lineItem) {
                $order->items()->create($lineItem);
            }

            $toyyibPayService = new ToyyibPayService();
            $callbackUrl = route('mobile.checkout.toyyibpay.callback');
            $returnUrl = $validated['return_url'] ?? null;
            $paymentResult = $toyyibPayService->createBill($order, $returnUrl, false, $callbackUrl);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Something went wrong. Please try again.',
                    'error_code' => $paymentResult['error_code'] ?? 'TOYYIBPAY_CREATE_BILL_FAILED',
                ], 422);
            }

            $order->update([
                'toyyibpay_bill_code' => $paymentResult['bill_code'],
                'payment_status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'bill_code' => $paymentResult['bill_code'],
                'payment_url' => $paymentResult['payment_url'],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mobile ToyyibPay create payment failed', [
                'user_id' => optional($request->user())->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /api/mobile/checkout/toyyibpay/status/{billCode}
     */
    public function getToyyibPayStatus(Request $request, string $billCode)
    {
        $order = Order::query()
            ->where('toyyibpay_bill_code', $billCode)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dijumpai.',
            ], 404);
        }

        $toyyibPayService = new ToyyibPayService();
        $verification = $toyyibPayService->verifyPayment($billCode);

        if ($verification['success']) {
            $targetPaymentStatus = $this->mapToyyibStatusToPaymentStatus($verification['status']);
            if ($targetPaymentStatus !== $order->payment_status) {
                $paymentUpdate = ['payment_status' => $targetPaymentStatus];
                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_completed_at')) {
                    $paymentUpdate['payment_completed_at'] = $targetPaymentStatus === 'paid' ? now() : null;
                }
                $order->update($paymentUpdate);
            }

            Log::info('Mobile ToyyibPay status refresh', [
                'order_id' => $order->id,
                'bill_code' => $billCode,
                'gateway_status' => $verification['status'] ?? null,
                'payment_status' => $order->fresh()->payment_status,
                'status' => $order->fresh()->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'bill_code' => $order->toyyibpay_bill_code,
            'payment_status' => $order->fresh()->payment_status,
            'verification' => [
                'success' => $verification['success'],
                'gateway_status' => $verification['status'] ?? null,
                'paid' => $verification['paid'] ?? null,
            ],
        ], 200);
    }

    /**
     * POST /api/mobile/checkout/toyyibpay/callback
     */
    public function handleToyyibPayCallback(Request $request)
    {
        $billCode = $request->input('billcode') ?? $request->input('billCode');
        if (!$billCode) {
            return response()->json([
                'success' => false,
                'message' => 'billCode diperlukan.',
            ], 422);
        }

        $order = Order::query()->where('toyyibpay_bill_code', $billCode)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dijumpai untuk billCode ini.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $lockedOrder = Order::query()->where('id', $order->id)->lockForUpdate()->first();
            if (!$lockedOrder) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dijumpai.',
                ], 404);
            }

            if ($lockedOrder->payment_status === 'paid') {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran telah diproses sebelum ini.',
                    'order_id' => $lockedOrder->id,
                    'bill_code' => $billCode,
                    'payment_status' => $lockedOrder->payment_status,
                ], 200);
            }

            $toyyibPayService = new ToyyibPayService();
            $verification = $toyyibPayService->verifyPayment($billCode);

            if (!$verification['success']) {
                $lockedOrder->update([
                    'status' => 'pending',
                    'payment_status' => 'pending',
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment is not completed yet.',
                    'order_id' => $lockedOrder->id,
                    'bill_code' => $billCode,
                    'payment_status' => $lockedOrder->payment_status,
                ], 202);
            }

            $targetPaymentStatus = $this->mapToyyibStatusToPaymentStatus($verification['status']);
            $orderUpdate = [
                'payment_status' => $targetPaymentStatus,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_completed_at')) {
                $orderUpdate['payment_completed_at'] = $targetPaymentStatus === 'paid' ? now() : null;
            }

            $lockedOrder->update($orderUpdate);

            if ($targetPaymentStatus === 'paid') {
                foreach ($lockedOrder->items as $item) {
                    if ($item->product_variation_id) {
                        $variation = ProductVariation::find($item->product_variation_id);
                        if ($variation) {
                            $variation->decrement('stock_quantity', $item->quantity);
                        }
                    } else {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->decrement('stock_quantity', $item->quantity);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $lockedOrder->id,
                'bill_code' => $billCode,
                'payment_status' => $lockedOrder->fresh()->payment_status,
                'gateway_status' => $verification['status'],
                'paid' => $verification['paid'],
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mobile ToyyibPay callback failed', [
                'bill_code' => $billCode,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ralat semasa memproses callback.',
            ], 500);
        }
    }

    private function mapToyyibStatusToPaymentStatus(?string $gatewayStatus): string
    {
        return match ((string) $gatewayStatus) {
            '1' => 'paid',
            '0' => 'pending',
            default => 'failed',
        };
    }

    private function buildCheckoutDebugContext(
        float $shippingFee,
        float $taxFee,
        float $subtotal,
        float $serverTotal,
        float $clientTotal
    ): array {
        return [
            'calculation' => [
                'subtotal' => round($subtotal, 2),
                'shipping_fee' => round($shippingFee, 2),
                'tax_fee' => round($taxFee, 2),
                'server_total' => round($serverTotal, 2),
                'client_total' => round($clientTotal, 2),
            ],
            'app_url' => config('app.url'),
            'environment' => app()->environment(),
        ];
    }
}
