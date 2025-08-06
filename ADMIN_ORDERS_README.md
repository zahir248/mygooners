# Admin Orders Management System

## Overview
The admin orders management system allows administrators to view, manage, and track all customer orders in the MyGooners platform.

## Features

### 📊 **Order Statistics Dashboard**
- Total orders count
- Pending orders count
- Total revenue
- Today's orders count
- Monthly order statistics

### 🔍 **Advanced Filtering & Search**
- Search by order number, customer name, or email
- Filter by order status (pending, processing, shipped, delivered, cancelled)
- Filter by payment status (pending, paid, failed, refunded)
- Filter by payment method (ToyyibPay, Stripe)
- Date range filtering

### 📋 **Order Management**
- View detailed order information
- Update order status
- Update payment status
- Add/edit order notes
- Delete orders (only pending/cancelled orders)

### 📦 **Order Details**
- Complete order information
- Customer shipping and billing details
- Order items with product images
- Payment information
- Order timeline (created, shipped, delivered dates)

### 📤 **Export Functionality**
- Export orders to CSV format
- Filtered exports based on search criteria

## Admin Routes

### Order Listing
- **URL**: `/admin/orders`
- **Route**: `admin.orders.index`
- **Controller**: `Admin\OrderController@index`
- **Features**: List all orders with filtering and search

### Order Details
- **URL**: `/admin/orders/{id}`
- **Route**: `admin.orders.show`
- **Controller**: `Admin\OrderController@show`
- **Features**: View detailed order information

### Update Order Status
- **URL**: `/admin/orders/{id}/status`
- **Route**: `admin.orders.update-status`
- **Controller**: `Admin\OrderController@updateStatus`
- **Method**: PATCH
- **Features**: Update order status and notes

### Update Payment Status
- **URL**: `/admin/orders/{id}/payment-status`
- **Route**: `admin.orders.update-payment-status`
- **Controller**: `Admin\OrderController@updatePaymentStatus`
- **Method**: PATCH
- **Features**: Update payment status

### Delete Order
- **URL**: `/admin/orders/{id}`
- **Route**: `admin.orders.destroy`
- **Controller**: `Admin\OrderController@destroy`
- **Method**: DELETE
- **Features**: Delete order (only for pending/cancelled orders)

### Export Orders
- **URL**: `/admin/orders/export`
- **Route**: `admin.orders.export`
- **Controller**: `Admin\OrderController@export`
- **Features**: Export orders to CSV

### Get Statistics
- **URL**: `/admin/orders/stats`
- **Route**: `admin.orders.stats`
- **Controller**: `Admin\OrderController@getStats`
- **Features**: Get order statistics for dashboard

## Order Statuses

### Order Status
- **pending**: Tertunggak (Awaiting processing)
- **processing**: Sedang Diproses (Being processed)
- **shipped**: Telah Dihantar (Shipped)
- **delivered**: Telah Diterima (Delivered)
- **cancelled**: Dibatalkan (Cancelled)

### Payment Status
- **pending**: Tertunggak (Payment pending)
- **paid**: Telah Dibayar (Payment completed)
- **failed**: Gagal (Payment failed)
- **refunded**: Dikembalikan (Payment refunded)

## Files Created/Modified

### New Files
- `app/Http/Controllers/Admin/OrderController.php` - Main controller for order management
- `resources/views/admin/orders/index.blade.php` - Order listing page
- `resources/views/admin/orders/show.blade.php` - Order detail page
- `ADMIN_ORDERS_README.md` - This documentation file

### Modified Files
- `routes/web.php` - Added admin order routes
- `resources/views/layouts/admin.blade.php` - Added Orders menu item

## Usage Instructions

### Accessing Orders Management
1. Log in to the admin panel
2. Click on "Pesanan" in the left sidebar
3. You'll see the orders listing page with statistics

### Viewing Order Details
1. From the orders list, click "Lihat" next to any order
2. View complete order information including:
   - Order items with images
   - Customer details
   - Payment information
   - Order timeline

### Updating Order Status
1. Go to order details page
2. In the "Pengurusan Status" section
3. Select new status from dropdown
4. Add optional notes
5. Click "Kemas Kini Status"

### Updating Payment Status
1. Go to order details page
2. In the "Pengurusan Status" section
3. Select new payment status from dropdown
4. Click "Kemas Kini Pembayaran"

### Filtering Orders
1. Use the search box to find orders by number or customer name
2. Use status filters to show specific order types
3. Use payment status filters to show specific payment states
4. Use payment method filters to show specific payment methods
5. Click "Cari" to apply filters or "Reset" to clear

### Exporting Orders
1. Apply any desired filters
2. Click "Export CSV" button
3. Download will start automatically

## Security Features

- **Admin Middleware**: All routes are protected by admin authentication
- **Status Validation**: Only valid status values are accepted
- **Delete Restrictions**: Only pending/cancelled orders can be deleted
- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: All inputs are validated before processing

## Integration with Existing System

### Order Model Methods Used
- `getStatusBadgeClass()` - Returns CSS classes for status badges
- `getPaymentStatusBadgeClass()` - Returns CSS classes for payment status badges
- `getFormattedTotal()` - Returns formatted total amount
- `getFormattedSubtotal()` - Returns formatted subtotal
- `getFormattedShippingCost()` - Returns formatted shipping cost
- `getFormattedTax()` - Returns formatted tax amount
- `getPaymentMethodDisplayName()` - Returns user-friendly payment method name

### OrderItem Model Methods Used
- `getFormattedPrice()` - Returns formatted item price
- `getFormattedSubtotal()` - Returns formatted item subtotal

## Future Enhancements

### Potential Features
- **Email Notifications**: Send status update emails to customers
- **Bulk Actions**: Update multiple orders at once
- **Order History**: Track all status changes with timestamps
- **Print Invoices**: Generate printable order invoices
- **Shipping Labels**: Generate shipping labels for orders
- **Order Comments**: Add internal comments for admin use
- **Customer Communication**: Send messages to customers from admin panel

### Technical Improvements
- **Real-time Updates**: WebSocket integration for live order updates
- **Advanced Analytics**: Detailed order analytics and reporting
- **API Integration**: REST API for external order management
- **Mobile Admin**: Mobile-optimized admin interface

## Troubleshooting

### Common Issues
1. **Orders not showing**: Check if user has admin privileges
2. **Status not updating**: Verify the order status is valid
3. **Export not working**: Ensure proper file permissions
4. **Images not loading**: Check product/variation image routes

### Debug Information
- Check Laravel logs in `storage/logs/laravel.log`
- Verify database connections and table structure
- Ensure all required model methods exist
- Check route caching with `php artisan route:clear` 