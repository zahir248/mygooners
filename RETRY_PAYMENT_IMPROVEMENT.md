# Retry Payment Improvement

## Overview
This document outlines the improvements made to the retry payment functionality to ensure that payment method and related codes/IDs are only updated when the payment creation is successful.

## Problem
Previously, the retry payment functionality had a critical issue:
- When users selected a new payment method or retried payment, the system would immediately update the order's payment method and clear payment IDs in the database
- If the payment creation failed (e.g., network issues, API errors), the order would be left with inconsistent data
- The original payment method and IDs would be lost, making it difficult to retry with the same method

## Solution
Modified the retry payment logic to follow this improved flow:

### Before (Problematic Flow):
1. User selects new payment method
2. **Immediately update database** with new payment method and clear old IDs
3. Try to create payment (ToyyibPay bill or Stripe intent)
4. If payment creation fails, database already has inconsistent data
5. If user cancels payment, order still has new payment method

### After (Improved Flow):
1. User selects new payment method
2. **Store original payment data** for potential rollback
3. Try to create payment (ToyyibPay bill or Stripe intent)
4. **Store new payment details in session** (don't update database yet)
5. **Only update database when payment is actually completed successfully**
6. If payment creation fails or user cancels, original data remains intact

## Files Modified

### 1. `app/Http/Controllers/Client/CheckoutController.php`

#### `retryPayment()` method
- **Before**: Immediately updated payment status and cleared payment IDs
- **After**: Only updates after successful payment creation

#### `retryPaymentWithMethod()` method
- **Before**: Immediately updated payment method and cleared payment IDs
- **After**: Only updates after successful payment creation

### 2. `app/Http/Controllers/Client/DirectCheckoutController.php`

#### `retryPayment()` method
- **Before**: Immediately updated payment status and cleared payment IDs
- **After**: Only updates after successful payment creation

#### `retryPaymentWithMethod()` method
- **Before**: Immediately updated payment method and cleared payment IDs
- **After**: Only updates after successful payment creation

## Key Changes

### 1. Store Original Data
```php
// Store original payment method and IDs for rollback if needed
$originalPaymentMethod = $order->payment_method;
$originalStripeIntentId = $order->stripe_payment_intent_id;
$originalToyyibpayBillCode = $order->toyyibpay_bill_code;
```

### 2. Create Payment First
```php
// Create new ToyyibPay bill first
$toyyibPayService = new ToyyibPayService();
$paymentResult = $toyyibPayService->createBill($order);
```

### 3. Store in Session (Don't Update Database Yet)
```php
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
    // ... proceed with payment
} else {
    DB::rollBack();
    return back()->with('error', 'Gagal membuat bil pembayaran. Sila cuba lagi.');
}
```

### 4. Update Database Only on Successful Payment Completion
```php
// In return/callback methods, only update if payment is successful
if ($paymentResult['success'] && $paymentResult['paid']) {
    // Check if this is a retry payment with new payment method
    $pendingPaymentMethod = session('pending_payment_method');
    $pendingOriginalPaymentMethod = session('pending_original_payment_method');
    
    if ($pendingPaymentMethod && $pendingPaymentMethod !== $pendingOriginalPaymentMethod) {
        // This is a retry with new payment method - update the order
        $order->update([
            'payment_method' => $pendingPaymentMethod,
            'stripe_payment_intent_id' => $paymentIntentId,
            'toyyibpay_bill_code' => null
        ]);
    }
}
```

## Benefits

1. **Data Consistency**: Payment method and IDs are only updated when payment is actually completed successfully
2. **Better Error Handling**: Failed payment attempts don't corrupt the order data
3. **Improved User Experience**: Users can retry with the same method if payment creation fails or if they cancel
4. **Reduced Support Issues**: Fewer orders with inconsistent payment data
5. **Audit Trail**: Original payment method is preserved until successful payment completion
6. **Cancellation Safety**: If user cancels payment, original payment method and IDs remain intact

## Testing Scenarios

### Scenario 1: Successful Payment Method Change
1. User has failed order with ToyyibPay
2. User selects Stripe as new payment method
3. Stripe payment intent creation succeeds
4. Order is updated with new payment method and Stripe intent ID
5. User proceeds to Stripe payment page

### Scenario 2: Failed Payment Method Change
1. User has failed order with ToyyibPay
2. User selects Stripe as new payment method
3. Stripe payment intent creation fails (network error, API issue)
4. Order retains original ToyyibPay method and bill code
5. User sees error message and can retry

### Scenario 3: Cancelled Payment
1. User has failed order with ToyyibPay
2. User selects Stripe as new payment method
3. Stripe payment intent creation succeeds
4. User cancels payment (closes browser, goes back, etc.)
5. Order retains original ToyyibPay method and bill code
6. User can retry with any payment method

### Scenario 4: Retry Same Payment Method
1. User has failed order with ToyyibPay
2. User retries with same ToyyibPay method
3. New ToyyibPay bill creation succeeds
4. Order retains original bill code until payment is completed
5. User proceeds to ToyyibPay payment page
6. If payment succeeds, order is updated with new bill code

## Database Transaction Safety

All retry payment operations are wrapped in database transactions:
- If any step fails, the entire operation is rolled back
- Original order data is preserved
- No partial updates occur

## Error Logging

All payment creation failures are logged with detailed information:
- Order ID and user ID
- Payment method being attempted
- Specific error message
- Timestamp for debugging

This ensures proper monitoring and debugging of payment issues. 