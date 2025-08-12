# Settings System for Admin Side

## Overview

This system allows administrators to control various application settings through a web interface, including the visibility of payment methods like Stripe.

## Features

- **Admin Settings Page**: Centralized location to manage all application settings
- **Payment Method Control**: Enable/disable Stripe and ToyyibPay payment methods
- **Dynamic UI**: Checkout forms automatically show/hide payment options based on settings
- **Caching**: Settings are cached for performance
- **Type Support**: Supports boolean, string, integer, and JSON setting types

## How It Works

### 1. Settings Storage

Settings are stored in the `settings` table with the following structure:
- `key`: Unique identifier for the setting
- `value`: The setting value
- `type`: Data type (boolean, string, integer, json)
- `group`: Logical grouping (payment, general, etc.)
- `description`: Human-readable description

### 2. Default Settings

The system comes with these default settings:

**Payment Settings:**
- `stripe_payment_enabled`: false (Stripe payment method visibility)
- `toyyibpay_payment_enabled`: true (ToyyibPay payment method visibility)

**General Settings:**
- `site_name`: "MyGooners"
- `site_description`: "Your trusted marketplace for products and services"
- `maintenance_mode`: false

### 3. Usage in Views

Use the `setting()` helper function to check settings:

```php
@if(setting('stripe_payment_enabled', false))
    <!-- Show Stripe payment option -->
@else
    <!-- Hide Stripe payment option -->
@endif
```

### 4. Admin Interface

Access the settings page at `/admin/settings` to:
- View all current settings
- Modify setting values
- Reset settings to defaults
- Group settings by category

## Implementation Details

### Files Created/Modified

1. **Migration**: `database/migrations/2025_01_01_000000_create_settings_table.php`
2. **Model**: `app/Models/Setting.php`
3. **Controller**: `app/Http/Controllers/Admin/SettingsController.php`
4. **View**: `resources/views/admin/settings/index.blade.php`
5. **Routes**: Added to `routes/web.php`
6. **Helper**: `app/helpers.php`
7. **Seeder**: `database/seeders/SettingsSeeder.php`
8. **Sidebar**: Updated `resources/views/layouts/admin.blade.php`

### Checkout Forms Updated

The following checkout forms now dynamically show/hide Stripe payment based on settings:
- `resources/views/client/checkout/index.blade.php`
- `resources/views/client/direct-checkout/index.blade.php`
- `resources/views/client/checkout/retry-payment.blade.php`

## How to Use

### 1. Access Settings

1. Login as admin
2. Navigate to "Tetapan" in the admin sidebar
3. Modify settings as needed
4. Click "Simpan Tetapan" to save

### 2. Enable Stripe Payments

1. Go to Admin → Tetapan
2. Find "Stripe Payment Enabled" under Payment group
3. Check the checkbox
4. Save settings

### 3. Disable Stripe Payments

1. Go to Admin → Tetapan
2. Find "Stripe Payment Enabled" under Payment group
3. Uncheck the checkbox
4. Save settings

## Technical Notes

### Caching

Settings are cached for 1 hour (3600 seconds) to improve performance. Cache is automatically cleared when settings are updated.

### Helper Function

The `setting()` helper function provides a clean way to access settings:
```php
// Get setting with default
$stripeEnabled = setting('stripe_payment_enabled', false);

// Get setting without default
$siteName = setting('site_name');
```

### Adding New Settings

To add new settings:

1. **Database**: Add via seeder or direct database insert
2. **Admin Interface**: Settings will automatically appear
3. **Usage**: Use `setting('new_setting_key', default_value)` in views

### Example of Adding a New Setting

```php
// In a seeder or controller
Setting::set(
    'new_feature_enabled',
    'false',
    'boolean',
    'features',
    'Enable new feature'
);
```

## Benefits

1. **No More Hardcoding**: Payment method visibility is now configurable
2. **Admin Control**: Non-technical users can manage settings
3. **Flexibility**: Easy to add new settings and payment methods
4. **Performance**: Settings are cached for optimal performance
5. **Maintainability**: Centralized configuration management

## Future Enhancements

- Setting validation rules
- Setting change history
- Environment-specific settings
- Setting import/export
- Role-based setting access
- Setting change notifications 