#!/bin/bash

# Invoice System Fix Deployment Script for cPanel
# This script sets up the necessary directories and permissions for invoice generation

echo "🚀 Setting up Invoice System for cPanel..."

# Get the current directory
CURRENT_DIR=$(pwd)
echo "Current directory: $CURRENT_DIR"

# Create necessary directories
echo "📁 Creating necessary directories..."

# Storage directories
mkdir -p storage/app/public/invoices
mkdir -p storage/app/private/invoices
mkdir -p storage/app/temp
mkdir -p storage/fonts
mkdir -p storage/logs

# Set permissions
echo "🔐 Setting directory permissions..."
chmod -R 775 storage/app/public/invoices
chmod -R 775 storage/app/private/invoices
chmod -R 775 storage/app/temp
chmod -R 775 storage/fonts
chmod -R 775 storage/logs

# Create storage link if it doesn't exist
echo "🔗 Checking storage link..."
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
else
    echo "Storage link already exists"
fi

# Test file creation
echo "🧪 Testing file operations..."
TEST_FILE="storage/app/temp/test_deploy.txt"
echo "Test content" > "$TEST_FILE"

if [ -f "$TEST_FILE" ]; then
    echo "✅ File creation test successful"
    rm "$TEST_FILE"
    echo "✅ File cleanup successful"
else
    echo "❌ File creation test failed"
fi

# Test directory permissions
echo "🔍 Checking directory permissions..."
for dir in "storage/app/public/invoices" "storage/app/private/invoices" "storage/app/temp" "storage/fonts"; do
    if [ -w "$dir" ]; then
        echo "✅ $dir is writable"
    else
        echo "❌ $dir is not writable"
    fi
done

# Check PHP configuration
echo "🐘 Checking PHP configuration..."
php -r "
echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
echo 'Current User: ' . get_current_user() . PHP_EOL;
echo 'Storage Path: ' . storage_path() . PHP_EOL;
echo 'Public Path: ' . public_path() . PHP_EOL;
echo 'Base Path: ' . base_path() . PHP_EOL;
"

# Test DomPDF availability
echo "📄 Testing DomPDF availability..."
php -r "
if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
    echo '✅ DomPDF Facade class exists' . PHP_EOL;
} else {
    echo '❌ DomPDF Facade class not found' . PHP_EOL;
}

if (class_exists('Barryvdh\\DomPDF\\PDF')) {
    echo '✅ DomPDF PDF class exists' . PHP_EOL;
} else {
    echo '❌ DomPDF PDF class not found' . PHP_EOL;
}
"

echo "🎯 Deployment script completed!"
echo ""
echo "Next steps:"
echo "1. Test invoice generation: php artisan test:invoice 12"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Try generating an invoice from the web interface"
echo ""
echo "If you encounter issues, check the logs and run the test command for detailed diagnostics." 