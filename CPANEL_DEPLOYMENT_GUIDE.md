# cPanel Deployment Guide for MyGooners

## Overview
This guide covers deploying the MyGooners Laravel application on cPanel hosting and resolving common deployment issues.

## Pre-Deployment Checklist

### 1. Server Requirements
- PHP 8.1 or higher
- Composer 2.0 or higher
- MySQL 5.7 or higher
- cPanel with SSH access (recommended)

### 2. PHP Extensions Required
```bash
# Required PHP extensions
- BCMath
- Ctype
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD
- ZIP
- Fileinfo
```

## Deployment Steps

### Step 1: Upload Files
1. Upload your Laravel project files to your cPanel public_html directory
2. Ensure all files are uploaded (including hidden files like .env)

### Step 2: Set File Permissions
```bash
# Set proper permissions for Laravel
chmod -R 755 public_html
chmod -R 775 public_html/storage
chmod -R 775 public_html/bootstrap/cache
chmod -R 775 public_html/storage/app/public
chmod -R 775 public_html/storage/app/private
chmod -R 775 public_html/storage/logs
chmod -R 775 public_html/storage/framework
```

### Step 3: Install Dependencies
```bash
# Navigate to your project directory
cd public_html

# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Generate application key
php artisan key:generate
```

### Step 4: Environment Configuration
1. Create/update `.env` file with your production settings
2. Set `APP_ENV=production`
3. Set `APP_DEBUG=false`
4. Configure database connection
5. Configure mail settings

### Step 5: Database Setup
```bash
# Run migrations
php artisan migrate --force

# Run seeders (if needed)
php artisan db:seed --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Storage Setup
```bash
# Create storage link
php artisan storage:link

# Ensure storage directories exist and are writable
mkdir -p storage/app/public/invoices
mkdir -p storage/app/private/invoices
chmod -R 775 storage/app/public/invoices
chmod -R 775 storage/app/private/invoices
```

## Common Issues and Solutions

### Issue 1: "Cannot resolve public path" Error
**Symptoms**: Invoice generation fails with "Cannot resolve public path" error
**Cause**: Storage directory permissions or path resolution issues in cPanel

**Solutions**:
1. Check storage directory permissions:
```bash
ls -la storage/app/
ls -la storage/app/public/
ls -la storage/app/private/
```

2. Verify storage link exists:
```bash
ls -la public/storage
```

3. Test invoice generation:
```bash
php artisan test:invoice {order_id}
```

4. Check PHP user and permissions:
```bash
whoami
id
```

### Issue 2: DomPDF Generation Fails
**Symptoms**: PDF generation fails silently or with errors
**Cause**: DomPDF configuration or font issues

**Solutions**:
1. Check DomPDF configuration in `config/dompdf.php`
2. Ensure font directory exists and is writable:
```bash
mkdir -p storage/fonts
chmod 775 storage/fonts
```

3. Test DomPDF directly:
```bash
php artisan tinker
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => null, 'invoiceNumber' => 'TEST', 'invoiceDate' => '01/01/2024']);
$pdf->save('test.pdf');
```

### Issue 3: File Permission Errors
**Symptoms**: Cannot create or write files
**Cause**: Incorrect file permissions or ownership

**Solutions**:
1. Set correct permissions:
```bash
find storage -type d -exec chmod 775 {} \;
find storage -type f -exec chmod 664 {} \;
```

2. Check ownership:
```bash
ls -la storage/
chown -R yourusername:yourusername storage/
```

### Issue 4: Storage Link Issues
**Symptoms**: Public storage not accessible
**Cause**: Storage link not created or broken

**Solutions**:
1. Remove existing link and recreate:
```bash
rm public/storage
php artisan storage:link
```

2. Verify link target:
```bash
ls -la public/storage
readlink public/storage
```

## Testing and Debugging

### 1. Test Invoice Generation
```bash
# Test with a specific order
php artisan test:invoice 11

# This will show detailed information about:
# - Environment details
# - Directory permissions
# - DomPDF functionality
# - Invoice service
```

### 2. Check Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Check for specific invoice errors
grep "invoice" storage/logs/laravel.log
grep "Failed to generate invoice" storage/logs/laravel.log
```

### 3. Test File Operations
```bash
# Test file creation
php artisan tinker
file_put_contents(storage_path('test.txt'), 'test');
echo file_exists(storage_path('test.txt')) ? 'File created' : 'Failed';
unlink(storage_path('test.txt'));
```

## Performance Optimization

### 1. Enable Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Optimize Autoloader
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Database Optimization
```bash
# Optimize database tables
php artisan db:optimize
```

## Security Considerations

### 1. File Permissions
- Never set 777 permissions
- Use 755 for directories, 644 for files
- Restrict access to sensitive directories

### 2. Environment Variables
- Keep `.env` file secure
- Use strong database passwords
- Enable HTTPS in production

### 3. Error Reporting
- Set `APP_DEBUG=false` in production
- Monitor logs for security issues
- Implement proper error handling

## Maintenance

### 1. Regular Tasks
```bash
# Clear old logs
php artisan log:clear

# Clean up temporary files
php artisan temp:clear

# Update dependencies
composer update --no-dev
```

### 2. Backup
- Regular database backups
- File system backups
- Configuration backups

## Support and Troubleshooting

### 1. Common Commands
```bash
# Check application status
php artisan about

# List all routes
php artisan route:list

# Check configuration
php artisan config:show

# Test mail configuration
php artisan tinker
Mail::raw('Test', function($message) { $message->to('test@example.com')->subject('Test'); });
```

### 2. Debug Mode
If you need to debug in production temporarily:
```bash
# Enable debug mode
php artisan config:set app.debug true

# Clear config cache
php artisan config:clear

# Remember to disable after debugging
php artisan config:set app.debug false
php artisan config:cache
```

### 3. Contact Information
For additional support:
- Check Laravel documentation
- Review application logs
- Contact hosting provider for server-level issues 