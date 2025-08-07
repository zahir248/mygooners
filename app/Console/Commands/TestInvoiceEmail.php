<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\OrderEmailService;
use App\Services\InvoiceService;

class TestInvoiceEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:invoice-email {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test invoice generation and email sending for an order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        $order = Order::with('items')->find($orderId);
        
        if (!$order) {
            $this->error("Order with ID {$orderId} not found.");
            return 1;
        }

        $this->info("Testing invoice generation and email for Order #{$order->order_number}");

        try {
            // Test invoice generation
            $this->info("Generating invoice...");
            $invoiceService = new InvoiceService();
            $invoicePath = $invoiceService->generateInvoice($order);
            
            if ($invoicePath) {
                $this->info("✓ Invoice generated successfully: {$invoicePath}");
            } else {
                $this->error("✗ Failed to generate invoice");
                return 1;
            }

            // Test email sending
            $this->info("Sending confirmation emails...");
            $emailService = new OrderEmailService($invoiceService);
            $result = $emailService->sendOrderConfirmationEmails($order);
            
            if ($result) {
                $this->info("✓ Emails sent successfully");
                $this->info("  - Shipping email: {$order->shipping_email}");
                if ($order->billing_email !== $order->shipping_email) {
                    $this->info("  - Billing email: {$order->billing_email}");
                }
            } else {
                $this->error("✗ Failed to send emails");
                return 1;
            }

            $this->info("Test completed successfully!");

        } catch (\Exception $e) {
            $this->error("Test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 