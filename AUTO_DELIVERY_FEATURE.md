# Auto-Delivery Feature Documentation

## Overview
The Auto-Delivery feature automatically marks shipped orders as "delivered" after 7 days if the user has not manually updated the status themselves. This ensures order completion and provides a better user experience.

## Features

### 1. Automatic Status Update
- Orders are automatically marked as "delivered" 7 days after being shipped
- Only applies to orders with status "shipped" that haven't been manually delivered
- Runs daily via scheduled command

### 2. User-Friendly Countdown Display
- **Smart Countdown Format**: Shows days, hours, or minutes as appropriate
- **Progressive Warnings**: Different messages based on countdown status
- **Visual Indicators**: Color-coded warnings (orange for countdown, red for overdue)

### 3. Manual Override
- Users can still manually mark orders as delivered before the auto-delivery
- Manual delivery takes precedence over automatic delivery
- Clear distinction between manual and automatic delivery

## Technical Implementation

### Backend Components

#### 1. Console Command
- **File**: `app/Console/Commands/AutoMarkOrdersAsDelivered.php`
- **Schedule**: Daily at 9:00 AM via `routes/console.php`
- **Logic**: Finds shipped orders older than 7 days and updates their status

#### 2. Order Model Methods
```php
// Check if order should be auto-delivered
public function shouldBeAutoDelivered()

// Get days since shipped
public function getDaysSinceShipped()

// Check if order was auto-delivered
public function wasAutoDelivered()

// Get countdown days (0-7)
public function getAutoDeliveryCountdown()

// Get formatted countdown string
public function getFormattedAutoDeliveryCountdown()

// Check if countdown is active
public function isAutoDeliveryCountdownActive()

// Check if auto-delivery is overdue
public function isAutoDeliveryOverdue()
```

### Frontend Components

#### 1. Countdown Display Logic
- **Active Countdown** (1-7 days): Shows formatted countdown (e.g., "3 hari lagi", "2 jam lagi")
- **Overdue** (7+ days): Shows "Pesanan sepatutnya ditandakan sebagai diterima secara automatik"
- **Auto-Delivered**: Shows "(Auto)" label on delivered status

#### 2. Countdown Format Examples
- **Days**: "5 hari lagi", "3 hari lagi"
- **Hours**: "12 jam lagi", "2 jam lagi"  
- **Minutes**: "30 minit lagi", "5 minit lagi"
- **Immediate**: "Sekarang"

## User Experience

### 1. Order List Page (`orders.blade.php`)
- Shows countdown warnings for shipped orders approaching auto-delivery
- Different colors for different states (orange for countdown, red for overdue)
- Clear visual indicators with warning icons

### 2. Order Detail Page (`show.blade.php`)
- Detailed countdown information in the order status banner
- Same color coding and formatting as the list page
- Additional context about the auto-delivery process

### 3. Status Badges
- Delivered orders show "(Auto)" if automatically delivered
- Helps distinguish between manual and automatic delivery

## Configuration

### 1. Auto-Delivery Period
- **Default**: 7 days after shipping
- **Configurable**: Can be modified in the command logic
- **Timezone**: Uses application timezone setting

### 2. Schedule
- **Frequency**: Daily
- **Time**: 9:00 AM (configurable in `routes/console.php`)
- **Timezone**: Server timezone

### 3. Warning Thresholds
- **Countdown Display**: Shows for all shipped orders within 7 days
- **Color Coding**: Orange for countdown, red for overdue
- **Icon**: Warning triangle (⚠️) for visual emphasis

## Benefits

### 1. User Experience
- **Clear Expectations**: Users know when auto-delivery will occur
- **Progressive Warnings**: Gradual escalation of urgency
- **Smart Formatting**: Appropriate time units (days/hours/minutes)

### 2. Business Operations
- **Order Completion**: Ensures orders don't remain in "shipped" status indefinitely
- **Customer Satisfaction**: Reduces confusion about order status
- **Automated Process**: Reduces manual intervention needed

### 3. Technical Benefits
- **Robust Logic**: Handles edge cases and prevents negative numbers
- **Comprehensive Logging**: Tracks all auto-delivery actions
- **Error Handling**: Graceful failure handling with detailed logging

## Monitoring and Logging

### 1. Command Logging
- Logs successful auto-deliveries with order details
- Logs failed attempts with error messages
- Includes timing information for debugging

### 2. Database Tracking
- `delivered_at` timestamp for all delivered orders
- `shipped_at` timestamp for calculation reference
- Status field tracks delivery method (manual vs auto)

### 3. Frontend Indicators
- Visual feedback for countdown status
- Clear distinction between manual and automatic delivery
- Progressive warning system

## Troubleshooting

### 1. Countdown Display Issues
- **Negative Numbers**: Fixed with `max(0, countdown)` logic
- **Incorrect Formatting**: Uses `getFormattedAutoDeliveryCountdown()` method
- **Timezone Issues**: Ensure server timezone matches application setting

### 2. Auto-Delivery Not Working
- **Check Schedule**: Verify cron job is set up correctly
- **Check Logs**: Review `storage/logs/laravel.log` for errors
- **Test Manually**: Run `php artisan orders:auto-mark-delivered` manually

### 3. Frontend Issues
- **Cache**: Clear application cache if changes don't appear
- **Timezone**: Ensure consistent timezone settings
- **Database**: Verify order timestamps are correct

## Future Enhancements

### 1. Email Notifications
- Send email reminders before auto-delivery
- Notify users when auto-delivery occurs
- Customizable notification preferences

### 2. Configurable Periods
- Allow different auto-delivery periods for different order types
- User-configurable auto-delivery preferences
- Business rule-based auto-delivery logic

### 3. Advanced Countdown
- Real-time countdown updates via JavaScript
- Push notifications for approaching deadlines
- Integration with mobile apps 