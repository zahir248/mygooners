<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReview;
use App\Services\InvoiceService;
use App\Services\ToyyibPayService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class MobileOrderController extends Controller
{
    private const STATUS_FLOW = ['pending', 'processing', 'shipped', 'delivered'];
    private const TO_PAY_STATUSES = ['pending', 'unpaid', 'failed', 'not paid', 'not_paid'];
    private const PAID_STATUSES = ['paid', 'success', 'successful', 'completed', 'complete'];
    private const CANCELLABLE_PAYMENT_STATUSES = ['pending', 'unpaid', 'failed', 'not paid', 'not_paid'];
    private const NON_CANCELLABLE_ORDER_STATUSES = ['processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        $orders->each(function (Order $order) {
            $this->syncToyyibPayPaymentStatus($order);
        });

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn (Order $order) => $this->formatOrderListItem($request->user()->id, $order))->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::query()
            ->with(['items.product', 'items.variation'])
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $this->syncToyyibPayPaymentStatus($order);
        $order->refresh();

        return response()->json([
            'success' => true,
            'data' => $this->formatOrderDetail($request, $order),
        ]);
    }

    public function markReceived(Request $request, int $id): JsonResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if ($order->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Only delivered orders can be marked as received.',
            ], 422);
        }

        $updates = [];
        if (Schema::hasColumn('orders', 'is_received')) {
            $updates['is_received'] = true;
        }
        if (Schema::hasColumn('orders', 'received_at')) {
            $updates['received_at'] = now();
        }

        if (!empty($updates)) {
            $order->update($updates);
            $order->refresh();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order Received',
            'data' => [
                'is_received' => Schema::hasColumn('orders', 'is_received') ? (bool) $order->is_received : null,
                'received_at' => Schema::hasColumn('orders', 'received_at') ? optional($order->received_at)->format('Y-m-d H:i:s') : null,
                'can_review' => $this->canReviewOrder($request->user()->id, $order),
            ],
        ]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $this->syncToyyibPayPaymentStatus($order);
        $order->refresh();

        if (!$this->canCancelOrder($order)) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.',
            ], 422);
        }

        $currentPaymentStatus = strtolower(trim((string) $order->payment_status));
        $nextPaymentStatus = in_array($currentPaymentStatus, ['failed'], true) ? 'failed' : 'cancelled';

        $updates = ['status' => 'cancelled'];
        if ($order->payment_status !== $nextPaymentStatus) {
            $updates['payment_status'] = $nextPaymentStatus;
        }

        $order->update($updates);
        $order->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data' => $this->formatOrderDetail($request, $order),
        ]);
    }

    public function viewInvoice(Request $request, int $id): JsonResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (!$this->canAccessInvoice($order)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to download invoice. Please try again later.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'invoice_url' => $this->invoiceViewUrl($order->id),
            'invoice_download_url' => $this->invoiceDownloadUrl($order->id),
        ]);
    }

    public function downloadInvoice(Request $request, int $id)
    {
        $order = Order::query()
            ->with(['items.product', 'items.variation'])
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (!$this->canAccessInvoice($order)) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is not available for this order.',
            ], 422);
        }

        try {
            $invoiceService = new InvoiceService();
            $filePath = $invoiceService->generateInvoice($order);
            if (!$filePath || !file_exists($filePath)) {
                Log::error('Mobile invoice generation failed', [
                    'order_id' => $order->id,
                    'user_id' => $request->user()->id,
                    'file_path' => $filePath,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to download invoice. Please try again later.',
                ], 500);
            }

            $filename = basename($filePath);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $contentType = match ($extension) {
                'pdf' => 'application/pdf',
                'html' => 'text/html',
                'txt' => 'text/plain',
                default => 'application/octet-stream',
            };

            return response()->download($filePath, $filename, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            Log::error('Mobile invoice download failed', [
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to download invoice. Please try again later.',
            ], 500);
        }
    }

    public function submitReview(Request $request, int $id): JsonResponse
    {
        $order = Order::query()
            ->with('items')
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $isReceived = Schema::hasColumn('orders', 'is_received') ? (bool) $order->is_received : false;
        if ($order->status !== 'delivered' || !$isReceived) {
            return response()->json([
                'success' => false,
                'message' => 'Reviews can only be submitted for delivered and received orders.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'order_item_id' => 'required|integer|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $productId = (int) $request->input('product_id');
        $orderItemId = (int) $request->input('order_item_id');
        $orderedProductIds = $order->items->pluck('product_id')->map(fn ($value) => (int) $value)->all();

        if (!in_array($productId, $orderedProductIds, true)) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review products from this order.',
            ], 403);
        }

        $targetOrderItem = $order->items->first(fn (OrderItem $item) => (int) $item->id === $orderItemId);
        if (!$targetOrderItem) {
            return response()->json([
                'success' => false,
                'message' => 'The selected order item does not belong to this order.',
            ], 422);
        }

        if ((int) $targetOrderItem->product_id !== $productId) {
            return response()->json([
                'success' => false,
                'message' => 'The selected order item does not match the product.',
            ], 422);
        }

        $hasOrderIdColumn = Schema::hasColumn('product_reviews', 'order_id');
        $hasOrderItemIdColumn = Schema::hasColumn('product_reviews', 'order_item_id');

        $reviewExistsQuery = ProductReview::query()->where('user_id', $request->user()->id);
        if ($hasOrderItemIdColumn) {
            $reviewExistsQuery->where('order_item_id', $orderItemId);
        } else {
            $reviewExistsQuery->where('product_id', $productId);
            if ($hasOrderIdColumn) {
                $reviewExistsQuery->where('order_id', $order->id);
            }
        }
        $reviewExists = $reviewExistsQuery->exists();

        if ($reviewExists) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this item.',
            ], 422);
        }

        $reviewPayload = [
            'user_id' => $request->user()->id,
            'product_id' => $productId,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_verified' => true,
        ];

        if ($hasOrderIdColumn) {
            $reviewPayload['order_id'] = $order->id;
        }
        if ($hasOrderItemIdColumn) {
            $reviewPayload['order_item_id'] = $orderItemId;
        }

        DB::beginTransaction();
        try {
            $review = ProductReview::create($reviewPayload);

            if (Schema::hasTable('product_review_photos') && $request->hasFile('photos')) {
                foreach ((array) $request->file('photos') as $photo) {
                    $stored = $photo->store('review_images', 'public');
                    $review->photos()->create(['image_path' => $stored]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mobile review submit failed', [
                'order_id' => $order->id,
                'product_id' => $productId,
                'order_item_id' => $orderItemId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank You !',
        ], 201);
    }

    private function formatOrderListItem(int $userId, Order $order): array
    {
        $status = $this->normalizeStatus($order->status);
        $payment = $this->determinePaymentStatus($order);
        $isReceived = Schema::hasColumn('orders', 'is_received') ? (bool) $order->is_received : false;
        $hasReviewed = $this->hasReviewedForOrder($userId, $order);
        $canReview = $this->determineCanReview($userId, $order, $hasReviewed, $isReceived);
        $isToPay = $this->determineIsToPay($order, $payment['key']);
        $isPaid = $payment['key'] === 'paid';
        $paymentCompletedAt = $this->resolvePaymentCompletedAt($order, $isPaid);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $status['key'],
            'status_label' => $status['label'],
            'payment_status' => $payment['key'],
            'payment_status_label' => $payment['label'],
            'is_to_pay' => $isToPay,
            'is_paid' => $isPaid,
            'is_received' => $isReceived,
            'received_at' => Schema::hasColumn('orders', 'received_at') ? optional($order->received_at)?->format('Y-m-d H:i:s') : null,
            'has_reviewed' => $hasReviewed,
            'can_review' => $canReview,
            'payment_completed_at' => $paymentCompletedAt,
            'toyyibpay_bill_code' => $order->toyyibpay_bill_code ?: null,
            'payment_url' => $this->resolvePaymentUrl($order, $isToPay),
            'can_cancel' => $this->canCancelOrder($order),
            'invoice_view_url' => $this->invoiceViewUrl($order->id),
            'invoice_download_url' => $this->invoiceDownloadUrl($order->id),
            'order_date' => optional($order->created_at)->format('d/m/Y'),
            'created_at' => optional($order->created_at)?->toISOString(),
            'total' => (float) $order->total,
        ];
    }

    private function formatOrderDetail(Request $request, Order $order): array
    {
        $status = $this->normalizeStatus($order->status);
        $payment = $this->determinePaymentStatus($order);
        $hasTrackingNumber = Schema::hasColumn('orders', 'tracking_number');
        $hasShippingCourier = Schema::hasColumn('orders', 'shipping_courier');
        $isTrackingStage = in_array($status['key'], ['shipped', 'delivered'], true);
        $isReceived = Schema::hasColumn('orders', 'is_received') ? (bool) $order->is_received : false;
        $hasReviewed = $this->hasReviewedForOrder($request->user()->id, $order);
        $canReview = $this->determineCanReview($request->user()->id, $order, $hasReviewed, $isReceived);
        $isToPay = $this->determineIsToPay($order, $payment['key']);
        $isPaid = $payment['key'] === 'paid';
        $paymentCompletedAt = $this->resolvePaymentCompletedAt($order, $isPaid);

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $status['key'],
            'status_label' => $status['label'],
            'payment_status' => $payment['key'],
            'payment_status_label' => $payment['label'],
            'is_to_pay' => $isToPay,
            'is_paid' => $isPaid,
            'is_received' => $isReceived,
            'received_at' => Schema::hasColumn('orders', 'received_at') ? optional($order->received_at)?->format('Y-m-d H:i:s') : null,
            'has_reviewed' => $hasReviewed,
            'can_review' => $canReview,
            'payment_completed_at' => $paymentCompletedAt,
            'toyyibpay_bill_code' => $order->toyyibpay_bill_code ?: null,
            'payment_url' => $this->resolvePaymentUrl($order, $isToPay),
            'can_cancel' => $this->canCancelOrder($order),
            'invoice_view_url' => $this->invoiceViewUrl($order->id),
            'invoice_download_url' => $this->invoiceDownloadUrl($order->id),
            'order_date' => optional($order->created_at)->format('d/m/Y'),
            'tracking_number' => ($hasTrackingNumber && $isTrackingStage) ? ($order->tracking_number ?: null) : null,
            'delivery_courier' => $hasShippingCourier ? ($order->shipping_courier ?: '-') : '-',
            'total' => (float) $order->total,
            'items' => $order->items->map(fn (OrderItem $item) => $this->formatOrderItem($request, $item))->values(),
            'timeline' => $this->buildTimeline($order, $status['key'], $isToPay),
        ];
    }

    private function formatOrderItem(Request $request, OrderItem $item): array
    {
        $variationName = $item->variation_name ?: optional($item->variation)->name;

        return [
            'id' => $item->id,
            'order_item_id' => $item->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product_name ?: optional($item->product)->title,
            'product_image_url' => $this->resolveItemImageUrl($request, $item),
            'image' => $this->resolveItemImageUrl($request, $item),
            'quantity' => (int) $item->quantity,
            'price' => (float) $item->price,
            'subtotal' => (float) $item->subtotal,
            'variation_id' => $item->product_variation_id ? (int) $item->product_variation_id : null,
            'variation_name' => $variationName,
            'size' => $variationName,
        ];
    }

    private function resolveItemImageUrl(Request $request, OrderItem $item): ?string
    {
        $baseUrl = rtrim(config('app.url') ?: $request->getSchemeAndHttpHost(), '/');

        $variationImage = optional($item->variation)->images[0] ?? null;
        if (is_string($variationImage) && $variationImage !== '') {
            if (str_starts_with($variationImage, 'http://') || str_starts_with($variationImage, 'https://')) {
                return $variationImage;
            }

            return $baseUrl . '/variation-image/' . ltrim(basename($variationImage), '/');
        }

        $productImage = optional($item->product)->images[0] ?? null;
        if (is_string($productImage) && $productImage !== '') {
            if (str_starts_with($productImage, 'http://') || str_starts_with($productImage, 'https://')) {
                return $productImage;
            }

            return $baseUrl . '/product-image/' . ltrim(basename($productImage), '/');
        }

        return null;
    }

    private function normalizeStatus(?string $status): array
    {
        $raw = strtolower(trim((string) $status));

        return match ($raw) {
            'tertunggak', 'pending' => ['key' => 'pending', 'label' => 'Pending'],
            'sedang diproses', 'processing' => ['key' => 'processing', 'label' => 'Processing'],
            'telah dihantar', 'shipped' => ['key' => 'shipped', 'label' => 'Shipped'],
            'telah diterima', 'delivered' => ['key' => 'delivered', 'label' => 'Delivered'],
            'dibatalkan', 'cancelled' => ['key' => 'cancelled', 'label' => 'Cancelled'],
            default => ['key' => 'pending', 'label' => 'Pending'],
        };
    }

    private function normalizePaymentStatus(?string $paymentStatus): array
    {
        $raw = strtolower(trim((string) $paymentStatus));

        return match ($raw) {
            'paid' => ['key' => 'paid', 'label' => 'Payment Complete'],
            'refunded' => ['key' => 'refunded', 'label' => 'Refunded'],
            'failed' => ['key' => 'failed', 'label' => 'Failed'],
            'unpaid' => ['key' => 'unpaid', 'label' => 'Unpaid'],
            'not paid', 'not_paid' => ['key' => 'not paid', 'label' => 'Not Paid'],
            'pending', '' => ['key' => 'pending', 'label' => 'Pending'],
            default => ['key' => $raw, 'label' => ucwords(str_replace('_', ' ', $raw))],
        };
    }

    private function determinePaymentStatus(Order $order): array
    {
        $normalized = strtolower(trim((string) $order->payment_status));
        if (in_array($normalized, self::PAID_STATUSES, true)) {
            $order->payment_status = 'paid';
        }

        return $this->normalizePaymentStatus($order->payment_status);
    }

    private function hasReviewedForOrder(int $userId, Order $order): bool
    {
        $query = ProductReview::query()
            ->where('user_id', $userId);

        if (Schema::hasColumn('product_reviews', 'order_id')) {
            return $query->where('order_id', $order->id)->exists();
        }

        $productIds = $order->items()->pluck('product_id')->all();
        if (empty($productIds)) {
            return false;
        }

        return $query->whereIn('product_id', $productIds)->exists();
    }

    private function determineCanReview(int $userId, Order $order, ?bool $hasReviewed = null, ?bool $isReceived = null): bool
    {
        $resolvedHasReviewed = $hasReviewed ?? $this->hasReviewedForOrder($userId, $order);
        $resolvedIsReceived = $isReceived ?? (Schema::hasColumn('orders', 'is_received') ? (bool) $order->is_received : false);

        return $order->status === 'delivered' && $resolvedIsReceived && !$resolvedHasReviewed;
    }

    private function canReviewOrder(int $userId, Order $order, ?bool $hasReviewed = null, ?bool $isReceived = null): bool
    {
        return $this->determineCanReview($userId, $order, $hasReviewed, $isReceived);
    }

    private function determineIsToPay(Order $order, string $paymentKey): bool
    {
        if ($paymentKey === 'paid') {
            $this->logPaymentClassification($order, $paymentKey, false, 'payment_marked_paid');
            return false;
        }

        if (in_array($paymentKey, self::TO_PAY_STATUSES, true)) {
            $this->logPaymentClassification($order, $paymentKey, true, 'to_pay_status_bucket');
            return true;
        }

        $isToPay = !empty($order->toyyibpay_bill_code);
        $this->logPaymentClassification($order, $paymentKey, $isToPay, 'fallback_bill_check');
        return $isToPay;
    }

    private function resolvePaymentUrl(Order $order, bool $isToPay): ?string
    {
        if (!$isToPay || empty($order->toyyibpay_bill_code)) {
            return null;
        }

        $baseUrl = rtrim((string) config('services.toyyibpay.base_url', 'https://toyyibpay.com'), '/');
        return $baseUrl . '/' . $order->toyyibpay_bill_code;
    }

    private function resolvePaymentCompletedAt(Order $order, bool $isPaid): ?string
    {
        if (!$isPaid) {
            return null;
        }

        if (Schema::hasColumn('orders', 'payment_completed_at') && $order->payment_completed_at) {
            return Carbon::parse($order->payment_completed_at)->format('Y-m-d H:i:s');
        }

        return optional($order->updated_at)->format('Y-m-d H:i:s');
    }

    private function syncToyyibPayPaymentStatus(Order $order): void
    {
        $currentPaymentStatus = strtolower(trim((string) $order->payment_status));
        $hasBill = !empty($order->toyyibpay_bill_code);
        $isToyyibPay = strtolower((string) $order->payment_method) === 'toyyibpay';

        if (!$isToyyibPay || !$hasBill || in_array($currentPaymentStatus, self::PAID_STATUSES, true)) {
            return;
        }

        try {
            $verification = (new ToyyibPayService())->verifyPayment($order->toyyibpay_bill_code);
            if (!($verification['success'] ?? false)) {
                Log::info('Mobile order payment sync skipped (verification failed)', [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'bill_code' => $order->toyyibpay_bill_code,
                ]);
                return;
            }

            $mappedStatus = $this->mapToyyibGatewayStatusToOrderPaymentStatus((string) ($verification['status'] ?? ''));
            if ($mappedStatus === $order->payment_status) {
                return;
            }

            $updates = ['payment_status' => $mappedStatus];
            if (Schema::hasColumn('orders', 'payment_completed_at')) {
                $updates['payment_completed_at'] = $mappedStatus === 'paid' ? now() : null;
            }

            $order->update($updates);

            Log::info('Mobile order payment sync updated order', [
                'order_id' => $order->id,
                'old_payment_status' => $currentPaymentStatus,
                'new_payment_status' => $mappedStatus,
                'status' => $order->fresh()->status,
                'bill_code' => $order->toyyibpay_bill_code,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Mobile order payment sync error', [
                'order_id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function mapToyyibGatewayStatusToOrderPaymentStatus(string $gatewayStatus): string
    {
        return match ($gatewayStatus) {
            '1' => 'paid',
            '0' => 'pending',
            default => 'failed',
        };
    }

    private function logPaymentClassification(Order $order, string $paymentKey, bool $isToPay, string $reason): void
    {
        Log::info('Mobile order payment classification', [
            'order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_key' => $paymentKey,
            'is_to_pay' => $isToPay,
            'reason' => $reason,
            'has_bill_code' => !empty($order->toyyibpay_bill_code),
        ]);
    }

    private function canCancelOrder(Order $order): bool
    {
        $status = strtolower(trim((string) $order->status));
        $paymentStatus = strtolower(trim((string) $order->payment_status));

        if (in_array($status, self::NON_CANCELLABLE_ORDER_STATUSES, true)) {
            return false;
        }

        if (in_array($paymentStatus, self::PAID_STATUSES, true)) {
            return false;
        }

        return in_array($status, ['pending'], true) || in_array($paymentStatus, self::CANCELLABLE_PAYMENT_STATUSES, true);
    }

    private function canAccessInvoice(Order $order): bool
    {
        $status = strtolower(trim((string) $order->status));
        $paymentStatus = strtolower(trim((string) $order->payment_status));

        if ($status === 'cancelled') {
            return false;
        }

        return !(($status === 'pending' && $paymentStatus === 'pending') || $paymentStatus === 'failed');
    }

    private function invoiceViewUrl(int $orderId): string
    {
        return url("/api/mobile/orders/{$orderId}/invoice/view");
    }

    private function invoiceDownloadUrl(int $orderId): string
    {
        return url("/api/mobile/orders/{$orderId}/invoice/download");
    }

    private function buildTimeline(Order $order, string $normalizedStatus, bool $isToPay = false): array
    {
        if ($normalizedStatus === 'cancelled') {
            return [
                $this->makeTimelinePoint(
                    'cancelled',
                    'Cancelled',
                    true,
                    true,
                    'Order has been cancelled',
                    $order->updated_at
                ),
            ];
        }

        $currentIndex = array_search($normalizedStatus, self::STATUS_FLOW, true);
        $currentIndex = $currentIndex === false ? 0 : $currentIndex;

        $courier = $order->shipping_courier ?: 'courier';

        $timelineMeta = [
            'pending' => [
                'label' => 'Pending',
                'description' => $isToPay ? 'Waiting For Payment' : 'Payment complete and order is placed',
                'datetime' => $order->created_at,
            ],
            'processing' => [
                'label' => 'Processing',
                'description' => 'Sender is preparing your parcel',
                'datetime' => $order->created_at,
            ],
            'shipped' => [
                'label' => 'Shipped',
                'description' => 'Your parcel has been shipped by ' . $courier,
                'datetime' => $order->shipped_at,
            ],
            'delivered' => [
                'label' => 'Delivered',
                'description' => 'Parcel has been delivered',
                'datetime' => $order->delivered_at,
            ],
        ];

        $timeline = [];
        foreach (self::STATUS_FLOW as $index => $statusKey) {
            $timeline[] = $this->makeTimelinePoint(
                $statusKey,
                $timelineMeta[$statusKey]['label'],
                $index <= $currentIndex,
                $index === $currentIndex,
                $timelineMeta[$statusKey]['description'],
                $timelineMeta[$statusKey]['datetime']
            );
        }

        return $timeline;
    }

    private function makeTimelinePoint(
        string $status,
        string $label,
        bool $completed,
        bool $current,
        string $description,
        $datetime
    ): array {
        $dateTimeString = $datetime ? Carbon::parse($datetime)->format('Y-m-d H:i:s') : null;

        return [
            'status' => $status,
            'label' => $label,
            'completed' => $completed,
            'current' => $current,
            'description' => $description,
            'datetime' => $dateTimeString,
            'formatted_datetime' => $datetime ? Carbon::parse($datetime)->format('d M Y - g:i A') : null,
        ];
    }
}
