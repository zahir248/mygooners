<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class TestInvoiceEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:invoice {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test invoice generation for a specific order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        $this->info("Testing invoice generation for order #{$orderId}");
        
        // Find the order
        $order = Order::find($orderId);
        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return 1;
        }
        
        $this->info("Order found: {$order->order_number}");
        $this->info("Status: {$order->status}");
        $this->info("Payment Status: {$order->payment_status}");
        
        // Test environment
        $this->info("\n=== Environment Information ===");
        $this->info("PHP Version: " . PHP_VERSION);
        $this->info("Current Working Directory: " . getcwd());
        $this->info("PHP User: " . get_current_user());
        $this->info("Storage Path: " . storage_path());
        $this->info("Public Path: " . public_path());
        $this->info("Base Path: " . base_path());
        
        // Test storage directories
        $this->info("\n=== Storage Directory Tests ===");
        $directories = [
            'storage/app/public' => storage_path('app/public'),
            'storage/app/private' => storage_path('app/private'),
            'storage/app/temp' => storage_path('app/temp'),
            'storage/fonts' => storage_path('fonts'),
            'public/storage' => public_path('storage'),
        ];
        
        foreach ($directories as $name => $path) {
            $exists = is_dir($path) ? 'YES' : 'NO';
            $writable = is_dir($path) && is_writable($path) ? 'YES' : 'NO';
            $this->info("{$name}: {$path} - Exists: {$exists}, Writable: {$writable}");
        }
        
        // Test DomPDF availability
        $this->info("\n=== DomPDF Availability Test ===");
        try {
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $this->info("✓ DomPDF Facade class exists");
            } else {
                $this->error("✗ DomPDF Facade class not found");
            }
            
            if (class_exists('\Barryvdh\DomPDF\PDF')) {
                $this->info("✓ DomPDF PDF class exists");
            } else {
                $this->error("✗ DomPDF PDF class not found");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ DomPDF class check failed: " . $e->getMessage());
        }
        
        // Test invoice service
        $this->info("\n=== Invoice Service Test ===");
        try {
            $invoiceService = new InvoiceService();
            $this->info("✓ InvoiceService instantiated successfully");
            
            $pdfPath = $invoiceService->generateInvoice($order);
            
            if ($pdfPath) {
                $this->info("✓ Invoice generated successfully");
                $this->info("✓ Path: {$pdfPath}");
                $this->info("✓ File exists: " . (file_exists($pdfPath) ? 'YES' : 'NO'));
                $this->info("✓ File type: " . pathinfo($pdfPath, PATHINFO_EXTENSION));
                
                if (file_exists($pdfPath)) {
                    $this->info("✓ File size: " . filesize($pdfPath) . " bytes");
                    
                    // Test file content
                    $content = file_get_contents($pdfPath);
                    if (str_contains($content, 'MyGooners')) {
                        $this->info("✓ File content appears correct");
                    } else {
                        $this->warn("⚠ File content may be incorrect");
                    }
                    
                    // Clean up
                    $invoiceService->deleteInvoice($pdfPath);
                    $this->info("✓ Test file cleaned up");
                }
            } else {
                $this->error("✗ Invoice generation failed - returned null");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ Invoice service error: " . $e->getMessage());
            Log::error('Invoice service test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        // Test HTML fallback generation
        $this->info("\n=== HTML Fallback Test ===");
        try {
            $reflection = new \ReflectionClass(InvoiceService::class);
            $method = $reflection->getMethod('generateInvoiceHTML');
            $method->setAccessible(true);
            
            $invoiceService = new InvoiceService();
            $html = $method->invoke($invoiceService, $order);
            
            if ($html && str_contains($html, 'MyGooners')) {
                $this->info("✓ HTML invoice generation successful");
                $this->info("✓ HTML length: " . strlen($html) . " characters");
                
                // Test saving HTML file
                $testHtmlPath = storage_path('app/temp/test_invoice.html');
                if (file_put_contents($testHtmlPath, $html) !== false) {
                    $this->info("✓ HTML file saved successfully");
                    $this->info("✓ HTML file size: " . filesize($testHtmlPath) . " bytes");
                    
                    // Clean up
                    unlink($testHtmlPath);
                    $this->info("✓ Test HTML file cleaned up");
                } else {
                    $this->warn("⚠ HTML file save failed");
                }
            } else {
                $this->error("✗ HTML invoice generation failed");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ HTML fallback test failed: " . $e->getMessage());
        }
        
        // Test file operations
        $this->info("\n=== File Operation Tests ===");
        try {
            $testFile = storage_path('app/temp/test.txt');
            $result = file_put_contents($testFile, 'test content');
            
            if ($result !== false) {
                $this->info("✓ File write test successful");
                $this->info("✓ Test file created at: {$testFile}");
                
                if (file_exists($testFile)) {
                    $this->info("✓ Test file exists and readable");
                    unlink($testFile);
                    $this->info("✓ Test file cleaned up");
                }
            } else {
                $this->error("✗ File write test failed");
            }
            
        } catch (\Exception $e) {
            $this->error("✗ File operation test failed: " . $e->getMessage());
        }
        
        $this->info("\n=== Test Complete ===");
        return 0;
    }
} 