# cPanel Deployment Guide for Order Management Features

## Overview
This guide covers the deployment of the recently implemented order management features on a cPanel-hosted Laravel application.

## ✅ Features That Work Automatically

### 1. Manual "Mark as Delivered" Functionality
- Users can mark their shipped orders as delivered
- Works immediately after deployment
- No additional setup required

### 2. Order Status Cards and Filtering
- Shopee-style status card interface
- Filter orders by status (pending, processing, shipped, delivered, cancelled, refunded)
- Responsive design with Malay language support
- Works immediately after deployment

### 3. Auto-Delivery Logic
- The command logic works when executed manually
- Helper methods in Order model function correctly
- Frontend indicators and warnings display properly

## ⚠️ Setup Required for Auto-Delivery Scheduling

### Option 1: cPanel Cron Jobs (Recommended)

1. **Access cPanel Cron Jobs:**
   - Log into your cPanel account
   - Navigate to "Advanced" section
   - Click on "Cron Jobs"

2. **Add New Cron Job:**
   - **Common Settings**: Daily
   - **Minute**: 0
   - **Hour**: 9 (or your preferred time)
   - **Day**: *
   - **Month**: *
   - **Weekday**: *
   - **Command**: 
     ```bash
     cd /home/YOUR_USERNAME/public_html && php artisan orders:auto-mark-delivered
     ```

3. **Important Notes:**
   - Replace `YOUR_USERNAME` with your actual cPanel username
   - Adjust the path if your Laravel app is in a subdirectory
   - Ensure the path points to your Laravel application root

### Option 2: cPanel Task Scheduler (If Available)

Some cPanel versions have a "Task Scheduler" feature that's more user-friendly:

1. Look for "Task Scheduler" in your cPanel
2. Create a new scheduled task
3. Set it to run daily at 9:00 AM
4. Use the same command as above

### Option 3: Manual Testing

You can test the command manually via SSH or cPanel Terminal:

```bash
cd /home/YOUR_USERNAME/public_html
php artisan orders:auto-mark-delivered
```

## 🔧 Verification Steps

### 1. Test Command Manually
```bash
php artisan orders:auto-mark-delivered --help
```
Should show the command description and options.

### 2. Check Logs
After running the command, check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

### 3. Verify Database Updates
Check if orders are being updated correctly in your database.

## 📁 Files Deployed

### Backend Files:
- `app/Http/Controllers/Client/CheckoutController.php` (updated)
- `app/Http/Controllers/Client/DirectCheckoutController.php` (updated)
- `app/Models/Order.php` (updated)
- `app/Console/Commands/AutoMarkOrdersAsDelivered.php` (new)
- `routes/web.php` (updated)
- `routes/console.php` (updated)

### Frontend Files:
- `resources/views/client/checkout/orders.blade.php` (updated)
- `resources/views/client/checkout/show.blade.php` (updated)

### Documentation:
- `mark_as_delivered_functionality.md`
- `ORDER_STATUS_CARDS_FEATURE.md`
- `AUTO_DELIVERY_FEATURE.md`

## 🚨 Important Considerations

### 1. File Permissions
Ensure these directories have proper write permissions:
- `storage/logs/`
- `storage/framework/cache/`
- `storage/framework/sessions/`

### 2. Database Connection
Verify your `.env` file has correct database credentials for production.

### 3. Timezone Settings
Ensure your server timezone matches your business requirements:
```php
// In config/app.php
'timezone' => 'Asia/Kuala_Lumpur',
```

### 4. Error Handling
The command includes comprehensive error handling and logging. Check logs if issues occur.

## 🔍 Troubleshooting

### Command Not Found
- Verify the path to your Laravel installation
- Ensure PHP is accessible from command line
- Check file permissions

### Permission Denied
- Ensure proper file permissions (755 for directories, 644 for files)
- Check if the web server user can execute the command

### Database Connection Issues
- Verify database credentials in `.env`
- Check if the database server is accessible
- Ensure the database user has proper permissions

### Cron Job Not Running
- Check cPanel cron job logs
- Verify the command syntax
- Test the command manually first

## 📞 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Test commands manually first
3. Verify file permissions and paths
4. Contact your hosting provider for cron job support

## ✅ Deployment Checklist

- [ ] Upload all updated files to cPanel
- [ ] Verify database migrations are up to date
- [ ] Test manual "Mark as Delivered" functionality
- [ ] Test order status cards and filtering
- [ ] Set up cron job for auto-delivery
- [ ] Test auto-delivery command manually
- [ ] Verify logs are being written
- [ ] Check timezone settings
- [ ] Test with sample orders

## 🎯 Expected Behavior After Deployment

1. **Users can mark orders as delivered** when status is "shipped"
2. **Order status cards** display with correct counts
3. **Filtering works** for all order statuses
4. **Auto-delivery warnings** appear for orders approaching 7 days
5. **Scheduled command runs daily** at 9:00 AM (after cron setup)
6. **Logs are created** for all auto-delivery actions
7. **Frontend indicators** show "(Auto)" for automatically delivered orders 