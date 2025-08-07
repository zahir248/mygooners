# Invoice Generation and Email Feature

## Overview

This feature automatically generates PDF invoices and sends confirmation emails to customers after successful payment. Emails are sent to both the shipping and billing email addresses (if different).

## Features

### 1. PDF Invoice Generation
- **Automatic Generation**: Invoices are automatically generated when payment is successful
- **Professional Design**: Clean, professional PDF layout with company branding
- **Complete Information**: Includes order details, customer information, and payment details
- **Unique Invoice Numbers**: Each invoice has a unique number (format: INV + YYYYMMDD + last 6 chars of order number)

### 2. Email Notifications
- **Dual Email Sending**: Sends emails to both shipping and billing addresses
- **Invoice Attachment**: PDF invoice is automatically attached to emails
- **Professional Template**: Beautiful HTML email template with order details
- **Responsive Design**: Email template works on all devices

### 3. Integration Points
- **Stripe Payments**: Integrated with Stripe payment success flow
- **ToyyibPay Payments**: Integrated with ToyyibPay payment success flow
- **Direct Checkout**: Works with both regular and direct checkout flows
- **Error Handling**: Graceful error handling - payment process continues even if email fails

## Files Created/Modified

### New Files
- `app/Mail/OrderConfirmationMail.php` - Email mail class
- `app/Services/InvoiceService.php` - PDF invoice generation service
- `app/Services/OrderEmailService.php` - Email sending service
- `resources/views/emails/order-confirmation.blade.php` - Email template
- `resources/views/pdf/invoice.blade.php` - PDF invoice template
- `config/dompdf.php` - DomPDF configuration
- `app/Console/Commands/TestInvoiceEmail.php` - Test command

### Modified Files
- `composer.json` - Added DomPDF dependency
- `app/Http/Controllers/Client/CheckoutController.php` - Added email sending after payment
- `app/Http/Controllers/Client/DirectCheckoutController.php` - Added email sending after payment

## Configuration

### Email Configuration
Make sure your email settings are configured in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mygooners.com
MAIL_FROM_NAME="MyGooners"
```

### DomPDF Configuration
The DomPDF configuration is in `config/dompdf.php`. Key settings:
- Paper size: A4
- Orientation: Portrait
- Font: Arial (fallback to serif)

## Usage

### Automatic Usage
The feature works automatically after successful payment. No manual intervention required.

### Manual Testing
To test the functionality manually:

```bash
# Test with an existing order
php artisan test:invoice-email {order_id}
```

### Manual Invoice Generation
```php
use App\Services\InvoiceService;

$invoiceService = new InvoiceService();
$invoicePath = $invoiceService->generateInvoice($order);
```

### Manual Email Sending
```php
use App\Services\OrderEmailService;
use App\Services\InvoiceService;

$emailService = new OrderEmailService(new InvoiceService());
$emailService->sendOrderConfirmationEmails($order);
```

## Email Template Features

### Order Confirmation Email
- **Header**: Company branding with order confirmation title
- **Order Details**: Order number, status, payment method, date
- **Item List**: Complete list of ordered items with prices
- **Totals**: Subtotal, shipping, tax, and grand total
- **Addresses**: Both shipping and billing addresses
- **Next Steps**: Information about order processing
- **Contact Information**: Support email and company details

### PDF Invoice
- **Company Header**: MyGooners branding and tagline
- **Invoice Information**: Invoice number, order number, date
- **Customer Information**: Bill-to and ship-to addresses
- **Item Table**: Detailed product list with variations, quantities, and prices
- **Totals Section**: Clear breakdown of all costs
- **Payment Information**: Payment method and status
- **Professional Footer**: Contact information and copyright

## Error Handling

### Invoice Generation Errors
- Logs errors but doesn't fail the payment process
- Returns null if invoice generation fails
- Continues with email sending (without attachment)

### Email Sending Errors
- Logs detailed error information
- Doesn't fail the payment process
- Attempts to send to both addresses independently

### File Management
- Invoice files are automatically cleaned up after email sending
- Temporary files are stored in `storage/app/public/invoices/`
- Files are deleted after successful email delivery

## Dependencies

### Required Packages
- `barryvdh/laravel-dompdf` - PDF generation
- Laravel Mail system - Email sending

### Installation
```bash
composer install
php artisan storage:link
```

## Testing

### Test Command
```bash
# Test with a specific order
php artisan test:invoice-email 1

# Expected output:
# Testing invoice generation and email for Order #MG20241201ABC123
# Generating invoice...
# ✓ Invoice generated successfully: /path/to/invoice.pdf
# Sending confirmation emails...
# ✓ Emails sent successfully
#   - Shipping email: customer@example.com
#   - Billing email: billing@example.com
# Test completed successfully!
```

### Manual Testing Steps
1. Create a test order with payment status 'paid'
2. Run the test command
3. Check email delivery
4. Verify PDF attachment
5. Check logs for any errors

## Logging

The system logs all activities:
- Invoice generation success/failure
- Email sending success/failure
- File creation and deletion
- Error details for debugging

Log entries include:
- Order ID and order number
- Email addresses
- File paths
- Error messages and stack traces

## Future Enhancements

### Potential Improvements
1. **Email Templates**: Add more email templates for different order statuses
2. **Invoice Customization**: Allow custom invoice templates per order type
3. **Email Queue**: Implement queued email sending for better performance
4. **Invoice Storage**: Store invoices permanently in database
5. **Admin Interface**: Add admin panel to resend invoices/emails
6. **SMS Notifications**: Add SMS notifications alongside emails
7. **Invoice Download**: Add customer-facing invoice download feature

### Configuration Options
- Custom email templates
- Invoice styling options
- Email sending preferences
- File storage options
- Notification preferences

## Troubleshooting

### Common Issues

1. **PDF Generation Fails**
   - Check DomPDF installation
   - Verify template syntax
   - Check file permissions

2. **Email Not Sending**
   - Verify SMTP configuration
   - Check email credentials
   - Review mail logs

3. **File Permission Errors**
   - Ensure storage directory is writable
   - Check storage link exists
   - Verify file permissions

### Debug Commands
```bash
# Check storage link
ls -la public/storage

# Test email configuration
php artisan tinker
Mail::raw('Test email', function($message) { $message->to('test@example.com')->subject('Test'); });

# Check logs
tail -f storage/logs/laravel.log
```

## Security Considerations

- Invoice files are temporary and automatically deleted
- Email addresses are validated before sending
- No sensitive payment information in emails
- Secure file handling with proper permissions
- Error messages don't expose sensitive information 