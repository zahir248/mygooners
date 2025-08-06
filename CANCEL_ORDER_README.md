# Cancel Order Functionality

## Overview
Users can now cancel their own orders through the MyGooners platform. This feature allows customers to cancel orders that are still in pending or processing status, or paid orders within 24 hours of purchase.

## Features

### ✅ **User-Friendly Cancellation**
- Cancel orders directly from the orders list
- Cancel orders from the order detail page
- **Beautiful custom modal confirmation** instead of browser alerts
- **Informative banners** explaining cancellation policy and order status
- Automatic status updates and logging
- **24-hour cancellation window** for paid orders
- **Loading state** during cancellation process

### 🔒 **Security & Validation**
- Only order owners can cancel their own orders
- Only pending/processing orders can be cancelled
- **Paid orders can be cancelled within 24 hours** of purchase
- Proper error handling and logging

### 💳 **Payment Integration**
- Automatic cancellation of Stripe payment intents
- Logging of ToyyibPay bill cancellation requests
- Proper cleanup of payment data
- **Refund logging for paid order cancellations**

## How It Works

### **Cancellation Rules**
1. **Order Status**: Only orders with status `pending` or `processing` can be cancelled
2. **Payment Status**: 
   - Orders with payment status `pending` or `failed` can always be cancelled
   - **Orders with payment status `paid` can be cancelled within 24 hours of purchase**
3. **Ownership**: Users can only cancel their own orders
4. **Timing**: Cancellation is available until payment is completed OR within 24 hours for paid orders

### **Cancellation Process**
1. User clicks "Batalkan Pesanan" button
2. **Beautiful modal appears with order confirmation**
3. **Modal shows order number and warning message**
4. User confirms cancellation in modal
5. **Loading state shows during processing**
6. System validates cancellation eligibility
7. Order status is updated to `cancelled`
8. Payment cancellation is attempted (if applicable)
9. **Refund logging is added for paid orders**
10. Cancellation note is added to order
11. User is redirected to orders list with success message

## Implementation Details

### **Controllers**
- **`CheckoutController::cancelOrder()`**: Handles order cancellation for standard checkout
- **`DirectCheckoutController::cancelOrder()`**: Handles order cancellation for direct checkout
- Both methods include 24-hour cancellation window validation
- Proper error handling and logging for payment cancellation attempts

### **Routes**
- **`POST /checkout/orders/{order}/cancel`**: Standard checkout cancellation
- **`POST /direct-checkout/orders/{order}/cancel`**: Direct checkout cancellation
- Both routes require authentication middleware

### **Payment Service Integration**
- **`StripeService::cancelPaymentIntent()`**: Cancels Stripe payment intents
- **`ToyyibPayService::cancelBill()`**: Logs ToyyibPay bill cancellation requests
- Proper error handling for payment provider API calls

### **Views & UI**
- **Modal Confirmation**: Beautiful custom modal instead of browser alerts
- **Loading States**: Spinner animation during cancellation process
- **Responsive Design**: Works on all device sizes
- **Accessibility**: Keyboard navigation and proper focus management

### **Informative Banners**
- **Orders List Page**: General information about cancellation policy and order management
- **Order Detail Page**: Status-specific information with dynamic icons and helpful tips
- **Success Pages**: "What's Next" information for both checkout and direct checkout
- **Dismissible**: Users can close banners temporarily (shows again on next page load)
- **Status-Specific Content**: Different information based on order status (pending, processing, shipped, etc.)

## User Interface

### **Orders List Page**
```html
@if(in_array($order->status, ['pending', 'processing']) && $order->payment_status !== 'paid')
    <form method="POST" action="{{ route('checkout.cancel-order', $order->id) }}" 
          onsubmit="return confirm('Adakah anda pasti mahu membatalkan pesanan ini?')" 
          class="inline">
        @csrf
        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Batalkan Pesanan
        </button>
    </form>
@endif
```

### **Order Detail Page**
```html
@if(in_array($order->status, ['pending', 'processing']) && $order->payment_status !== 'paid')
    <form method="POST" action="{{ route('checkout.cancel-order', $order->id) }}" 
          onsubmit="return confirm('Adakah anda pasti mahu membatalkan pesanan ini?')">
        @csrf
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
            Batalkan Pesanan
        </button>
    </form>
@endif
```

## Error Handling

### **Validation Errors**
- **Order not found**: 404 error
- **Unauthorized access**: 403 error
- **Invalid order status**: Error message
- **Payment already made**: Error message

### **Payment Cancellation Errors**
- **Stripe errors**: Logged but don't prevent order cancellation
- **ToyyibPay limitations**: Logged with note about manual intervention

### **Database Errors**
- **Transaction rollback**: Automatic rollback on errors
- **Logging**: All errors are logged for debugging

## Logging

### **Successful Cancellations**
```php
Log::info('Order cancelled by user', [
    'order_id' => $order->id,
    'user_id' => auth()->id(),
    'previous_status' => $oldStatus,
    'cancellation_reason' => 'User requested cancellation'
]);
```

### **Payment Cancellation Attempts**
```php
Log::info('Stripe payment intent cancelled', [
    'payment_intent_id' => $paymentIntentId,
    'status' => $paymentIntent->status
]);
```

## Security Considerations

### **Authorization**
- Middleware ensures only authenticated users can access
- Controller validates order ownership
- Users can only cancel their own orders

### **Input Validation**
- Order ID validation
- Status validation
- Payment status validation

### **CSRF Protection**
- All forms include CSRF tokens
- Laravel automatically validates CSRF tokens

## Future Enhancements

### **Potential Improvements**
1. **Email Notifications**: Send cancellation confirmation emails
2. **Refund Processing**: Automatic refund processing for paid orders
3. **Cancellation Reasons**: Allow users to specify cancellation reasons
4. **Partial Cancellations**: Cancel specific items in an order
5. **Cancellation Time Limits**: Set time limits for cancellation
6. **Admin Override**: Allow admins to cancel any order

### **Advanced Features**
1. **Cancellation History**: Track all cancellation attempts
2. **Cancellation Analytics**: Monitor cancellation patterns
3. **Automated Refunds**: Integrate with payment providers for automatic refunds
4. **Cancellation Policies**: Implement different policies for different order types

## Testing

### **Test Cases**
1. **Valid Cancellation**: Cancel pending order successfully
2. **Invalid Status**: Try to cancel shipped order
3. **Paid Order**: Try to cancel paid order
4. **Unauthorized**: Try to cancel another user's order
5. **Payment Integration**: Test Stripe payment cancellation
6. **Error Handling**: Test database errors and rollback

### **Manual Testing**
1. Create a test order
2. Navigate to orders list
3. Click cancel button
4. Confirm cancellation
5. Verify order status updated
6. Check payment cancellation logs

## Troubleshooting

### **Common Issues**
1. **Cancel button not showing**: Check order status and payment status
2. **Cancellation fails**: Check logs for specific error messages
3. **Payment not cancelled**: Check payment provider integration
4. **Permission denied**: Verify user authentication and order ownership

### **Debug Information**
- Check Laravel logs in `storage/logs/laravel.log`
- Verify order status and payment status in database
- Check payment provider logs for cancellation attempts
- Ensure all required routes are properly defined 