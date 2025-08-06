# Auto-Cancel Pending Orders Feature

## Overview
The auto-cancel feature automatically cancels orders that have been in pending status for more than 24 hours without payment. This helps clean up abandoned orders and free up inventory that's been held by pending orders.

## Features

### ✅ **Automatic Cleanup**
- Automatically cancels orders that are `pending` status with `pending` or `failed` payment status
- Runs every hour via scheduled task
- Cancels both Stripe payment intents and ToyyibPay bills
- Updates order status to `cancelled` with detailed notes
- Comprehensive logging for tracking and debugging

### 🔒 **Payment Integration**
- **Stripe Integration**: Automatically cancels payment intents using Stripe API
- **ToyyibPay Integration**: Automatically cancels bills using ToyyibPay API
- **Error Handling**: Gracefully handles payment cancellation failures
- **Logging**: Detailed logs for successful and failed cancellations

### 📊 **Monitoring & Logging**
- Console output with progress information
- Detailed Laravel logs for each cancelled order
- Error tracking for failed cancellations
- Summary reports of successful vs failed operations

## How It Works

### **Cancellation Criteria**
1. **Order Status**: Must be `pending`
2. **Payment Status**: Must be `pending` or `failed`
3. **Time Limit**: Must be older than 24 hours from creation
4. **Ownership**: System processes all eligible orders regardless of user

### **Cancellation Process**
1. **Query**: Find all orders meeting the criteria
2. **Payment Cancellation**: Cancel any existing payment intents/bills
3. **Order Update**: Change status to `cancelled` and add notes
4. **Logging**: Record the cancellation with details
5. **Cleanup**: Clear any associated payment data

### **Scheduling**
- **Frequency**: Runs every hour
- **Time**: 24/7 automated execution
- **Command**: `php artisan orders:auto-cancel-pending`

## Technical Implementation

### **Console Command**
- **File**: `app/Console/Commands/AutoCancelPendingOrders.php`
- **Signature**: `orders:auto-cancel-pending`
- **Description**: Automatically cancel orders that have been pending for more than 24 hours without payment

### **Scheduled Task**
- **File**: `routes/console.php`
- **Schedule**: `->hourly()`
- **Description**: Automatically cancel orders that have been pending for more than 24 hours without payment

### **Database Updates**
When an order is auto-cancelled:
```sql
UPDATE orders SET 
    status = 'cancelled',
    notes = CONCAT(COALESCE(notes, ''), '\n\nDibatalkan secara automatik oleh sistem pada [timestamp] kerana tidak dibayar dalam masa 24 jam')
WHERE id = [order_id];
```

### **Payment Cancellation**
- **Stripe**: Calls `StripeService::cancelPaymentIntent()`
- **ToyyibPay**: Calls `ToyyibPayService::cancelBill()`
- **Error Handling**: Continues processing even if payment cancellation fails

## Logging

### **Success Logs**
```php
Log::info('Order auto-cancelled', [
    'order_id' => $order->id,
    'order_number' => $order->order_number,
    'user_id' => $order->user_id,
    'cancelled_at' => now(),
    'reason' => 'Auto-cancelled due to non-payment after 24 hours'
]);
```

### **Error Logs**
```php
Log::error('Failed to auto-cancel order', [
    'order_id' => $order->id,
    'order_number' => $order->order_number,
    'user_id' => $order->user_id,
    'error' => $e->getMessage()
]);
```

## Manual Testing

### **Test the Command**
```bash
php artisan orders:auto-cancel-pending
```

### **Expected Output**
```
Starting automatic cancellation of pending orders...
Found 0 orders to cancel.
No orders to cancel.
```

### **With Orders to Cancel**
```
Starting automatic cancellation of pending orders...
Found 2 orders to cancel.
Processing order MG202508050DB01A (ID: 1)
  - Cancelled Stripe payment intent: pi_1234567890
✓ Successfully cancelled order MG202508050DB01A
Processing order MG202508050DB02A (ID: 2)
  - Cancelled ToyyibPay bill: 123456789
✓ Successfully cancelled order MG202508050DB02A
Auto-cancellation completed:
- Successfully cancelled: 2 orders
- Errors: 0 orders
```

## Deployment

### **Local Development**
The command can be run manually for testing:
```bash
php artisan orders:auto-cancel-pending
```

### **Production (cPanel)**
1. **Cron Job Setup**: Add to cPanel cron jobs
2. **Command**: `php /path/to/your/project/artisan orders:auto-cancel-pending`
3. **Frequency**: Every hour (0 * * * *)
4. **Logging**: Check Laravel logs for execution details

### **Example Cron Job**
```bash
# Auto-cancel pending orders every hour
0 * * * * cd /home/username/public_html && php artisan orders:auto-cancel-pending >> /dev/null 2>&1
```

## Benefits

### **For Business**
- **Inventory Management**: Frees up inventory held by abandoned orders
- **System Cleanup**: Maintains clean order database
- **Payment Processing**: Prevents payment intents from accumulating
- **User Experience**: Clear order status for users

### **For Users**
- **Clear Status**: Users see cancelled status instead of stuck pending
- **Payment Clarity**: No confusion about pending payments
- **Re-order**: Users can place new orders if needed

### **For System**
- **Performance**: Reduces database queries for old pending orders
- **Storage**: Prevents accumulation of abandoned order data
- **Monitoring**: Clear logs for system health monitoring

## Future Enhancements

### **Potential Improvements**
1. **Email Notifications**: Notify users when their orders are auto-cancelled
2. **Configurable Time**: Make 24-hour limit configurable
3. **Admin Dashboard**: Show auto-cancellation statistics
4. **Retry Logic**: Retry failed payment cancellations
5. **Bulk Operations**: Process orders in batches for better performance

### **Monitoring Dashboard**
- Auto-cancellation statistics
- Failed cancellation reports
- Order age distribution
- Payment method breakdown 