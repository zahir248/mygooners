# MyGooners Mobile ToyyibPay API Analysis & Implementation

## A) Project Analysis
The existing Laravel project already contains reusable payment gateway logic:
- `app/Services/ToyyibPayService.php`
- `app/Http/Controllers/Client/CheckoutController.php`
- `app/Http/Controllers/Client/DirectCheckoutController.php`
- `app/Models/Order.php`
- `routes/web.php`

Existing order payment fields already support mobile checkout:
- `payment_method`
- `payment_status`
- `toyyibpay_bill_code`
- `stripe_payment_intent_id`

The mobile API now reuses this existing gateway layer without exposing any ToyyibPay secret to Flutter.

Hardening included:
- Server-side payment method enablement check with `setting('toyyibpay_payment_enabled', true)`
- Idempotent callback processing with DB lock (`lockForUpdate`) and early return when already paid
- Public callback endpoint kept accessible for ToyyibPay server

## B) Files Created
- `app/Http/Controllers/Mobile/MobilePaymentController.php`
- `MOBILE_TOYYIBPAY_API_ANALYSIS.md` (this file)

## C) Files Modified
- `routes/api.php`

## D) Full Code for New File
### `app/Http/Controllers/Mobile/MobilePaymentController.php`
```php
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
        ]);

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
                            'message' => 'Variasi produk tidak sah.',
                            'product_variation_id' => $item['product_variation_id'],
                        ], 422);
                    }

                    if ((int) $variation->stock_quantity < $quantity) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok variasi tidak mencukupi.',
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
                            'message' => 'Stok produk tidak mencukupi.',
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

            if (abs($serverTotal - $clientTotal) > 0.01) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran tidak sepadan.',
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
            $paymentResult = $toyyibPayService->createBill($order);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Gagal mencipta bil ToyyibPay.',
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
                'message' => 'Ralat semasa mencipta pembayaran.',
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
                $order->update(['payment_status' => $targetPaymentStatus]);
            }
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
                    'message' => $verification['message'] ?? 'Gagal mengesahkan pembayaran ToyyibPay.',
                    'order_id' => $lockedOrder->id,
                    'bill_code' => $billCode,
                    'payment_status' => $lockedOrder->payment_status,
                ], 202);
            }

            $targetPaymentStatus = $this->mapToyyibStatusToPaymentStatus($verification['status']);
            $targetOrderStatus = $targetPaymentStatus === 'paid' ? 'processing' : 'pending';

            $lockedOrder->update([
                'payment_status' => $targetPaymentStatus,
                'status' => $targetOrderStatus,
            ]);

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
}
```

## E) Necessary Route Changes
### `routes/api.php` additions
```php
use App\Http\Controllers\Mobile\MobilePaymentController;

Route::prefix('mobile')->group(function () {
    // ...existing routes

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/checkout/toyyibpay/create', [MobilePaymentController::class, 'createToyyibPayPayment']);
        Route::get('/checkout/toyyibpay/status/{billCode}', [MobilePaymentController::class, 'getToyyibPayStatus']);
    });

    // Keep callback public so ToyyibPay server can access it
    Route::post('/checkout/toyyibpay/callback', [MobilePaymentController::class, 'handleToyyibPayCallback']);
});
```

## F) Example Flutter Request
### Create payment
`POST /api/mobile/checkout/toyyibpay/create`

Headers:
- `Authorization: Bearer <sanctum_token>`
- `Content-Type: application/json`

Body:
```json
{
  "items": [
    { "product_id": 12, "product_variation_id": 33, "quantity": 1 },
    { "product_id": 17, "quantity": 2 }
  ],
  "shipping": {
    "name": "Ali Bin Abu",
    "email": "ali@example.com",
    "phone": "0123456789",
    "address": "No 1, Jalan Stadium",
    "city": "Shah Alam",
    "state": "Selangor",
    "postal_code": "40100",
    "country": "Malaysia"
  },
  "billing": {
    "name": "Ali Bin Abu",
    "email": "ali@example.com",
    "phone": "0123456789",
    "address": "No 1, Jalan Stadium",
    "city": "Shah Alam",
    "state": "Selangor",
    "postal_code": "40100",
    "country": "Malaysia"
  },
  "fees": {
    "shipping_fee": 10,
    "tax_fee": 0
  },
  "total": 189.9,
  "notes": "Please ship fast"
}
```

## G) Example JSON Responses
### Create success
```json
{
  "success": true,
  "order_id": 501,
  "bill_code": "abc123xyz",
  "payment_url": "https://toyyibpay.com/abc123xyz"
}
```

### Status response
```json
{
  "success": true,
  "order_id": 501,
  "bill_code": "abc123xyz",
  "payment_status": "pending",
  "verification": {
    "success": true,
    "gateway_status": "0",
    "paid": false
  }
}
```

### Callback success
```json
{
  "success": true,
  "order_id": 501,
  "bill_code": "abc123xyz",
  "payment_status": "paid",
  "gateway_status": "1",
  "paid": true
}
```

## H) Postman Testing Steps
1. Login using `POST /api/mobile/login` and copy Sanctum token.
2. Call `POST /api/mobile/checkout/toyyibpay/create` with bearer token.
3. Open returned `payment_url` and complete ToyyibPay payment.
4. Call `GET /api/mobile/checkout/toyyibpay/status/{billCode}` with bearer token.
5. Simulate callback using:
   - `POST /api/mobile/checkout/toyyibpay/callback`
   - Body contains `billcode=<BillCode>` (form-data or JSON)
6. Re-check status endpoint and confirm payment transition.
7. Re-send callback for same bill and confirm idempotent behavior (already paid).

## Security Notes
- Flutter never receives `TOYYIBPAY_SECRET_KEY` or `TOYYIBPAY_CATEGORY_CODE`.
- Secret usage remains server-side in `ToyyibPayService`.
- Callback route is intentionally public for gateway-to-server communication.

## Implementation Note
Local CLI verification (`php -l`, `php artisan route:list`) could not be executed in this environment because `php` command is unavailable in the session path. Please run route and syntax checks on your Laragon runtime.

