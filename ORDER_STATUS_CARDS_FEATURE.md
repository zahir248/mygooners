# Order Status Cards Feature

## Overview
The "Pesanan Saya" (My Orders) page has been refactored to include a card-based interface for filtering orders by status, similar to Shopee's order management interface.

## Features

### Status Cards
- **All Orders Card**: Shows total count of all orders
- **Pending Orders Card**: Shows count of orders awaiting payment
- **Processing Orders Card**: Shows count of orders being processed
- **Shipped Orders Card**: Shows count of orders that have been shipped
- **Delivered Orders Card**: Shows count of orders that have been delivered
- **Cancelled/Refunded Orders Card**: Shows combined count of cancelled and refunded orders

### Visual Design
- Cards are arranged in a responsive grid (2 columns on mobile, 3 on tablet, 6 on desktop)
- Active status card is highlighted with colored border and background
- Each card shows the count and status name
- Hover effects for better user interaction

### Functionality
- Clicking a status card filters orders by that specific status
- URL includes status parameter (e.g., `/orders?status=pending`)
- Pagination preserves the status filter
- "View All Orders" link to return to unfiltered view
- Empty state messages are contextual to the selected filter

## Technical Implementation

### Backend Changes
**File**: `app/Http/Controllers/Client/CheckoutController.php`
- Modified `indexOrders()` method to accept `Request $request`
- Added status filtering logic
- Added order count calculation for each status
- Passes `orderCounts`, `status` variables to view

### Frontend Changes
**File**: `resources/views/client/checkout/orders.blade.php`
- Added status cards section with responsive grid layout
- Added status filter header when a filter is active
- Updated pagination to preserve query parameters
- Enhanced empty state messages for filtered views
- Maintained all existing functionality (modals, buttons, etc.)

### Status Mapping
- `pending` → "Menunggu Pembayaran" (Awaiting Payment)
- `processing` → "Sedang Diproses" (Processing)
- `shipped` → "Telah Dihantar" (Shipped)
- `delivered` → "Telah Diterima" (Delivered)
- `cancelled` → "Dibatalkan" (Cancelled)
- `refunded` → "Dikembalikan" (Refunded)

## User Experience

### Default View
- Shows all orders with status cards at the top
- "All Orders" card is highlighted by default

### Filtered View
- Shows only orders matching the selected status
- Header indicates which filter is active
- Order count badge shows filtered results
- "View All Orders" link to return to unfiltered view

### Empty States
- Different messages for filtered vs. unfiltered empty states
- Appropriate action buttons based on context

## Benefits
1. **Better Organization**: Users can quickly find orders by status
2. **Visual Clarity**: Status counts provide immediate overview
3. **Shopee-like Experience**: Familiar interface pattern for users
4. **Improved Navigation**: Easy switching between different order states
5. **Mobile Responsive**: Works well on all device sizes

## Future Enhancements
- Add date range filtering
- Add search functionality
- Add bulk actions for orders
- Add order status timeline view
- Add export functionality for order history 