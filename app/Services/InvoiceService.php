<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate PDF invoice for an order
     */
    public function generateInvoice(Order $order)
    {
        try {
            // First, try to create necessary directories
            $this->ensureDirectoriesExist();
            
            // Try multiple DomPDF approaches
            $pdfPath = $this->tryDomPDFGeneration($order);
            
            if ($pdfPath) {
                return $pdfPath;
            }
            
            // If DomPDF fails, try alternative approach
            return $this->tryAlternativeGeneration($order);
            
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'storage_path' => storage_path('app/public/invoices'),
                'public_path' => public_path('storage/invoices'),
                'base_path' => base_path('storage/app/public/invoices'),
                'current_working_dir' => getcwd(),
                'php_user' => get_current_user(),
                'php_version' => PHP_VERSION
            ]);
            
            return null;
        }
    }

    /**
     * Ensure all necessary directories exist
     */
    private function ensureDirectoriesExist()
    {
        $directories = [
            storage_path('app/public/invoices'),
            storage_path('app/private/invoices'),
            storage_path('app/temp'),
            storage_path('fonts'),
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Try DomPDF generation with multiple approaches
     */
    private function tryDomPDFGeneration(Order $order)
    {
        try {
            // Approach 1: Try with Facade
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
                'order' => $order,
                'invoiceNumber' => $this->generateInvoiceNumber($order),
                'invoiceDate' => now()->format('d/m/Y'),
            ]);

            $pdf->setPaper('A4', 'portrait');
            
            $filepath = $this->savePDF($pdf, $order);
            if ($filepath) {
                Log::info('DomPDF generation successful (Facade approach)', [
                    'order_id' => $order->id,
                    'filepath' => $filepath
                ]);
                return $filepath;
            }
            
        } catch (\Exception $e) {
            Log::warning('DomPDF Facade approach failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        try {
            // Approach 2: Try with direct instantiation using correct constructor
            $dompdf = new \Dompdf\Dompdf([
                'enable_remote' => false,
                'enable_php' => false,
                'enable_javascript' => false,
                'enable_html5_parser' => false,
                'is_remote_enabled' => false,
                'is_php_enabled' => false,
                'is_javascript_enabled' => false,
                'is_html5_parser_enabled' => false,
                'temp_dir' => storage_path('app/temp'),
                'font_dir' => storage_path('fonts'),
                'font_cache' => storage_path('fonts'),
                'chroot' => base_path(),
            ]);
            
            $dompdf->loadHtml(view('pdf.invoice', [
                'order' => $order,
                'invoiceNumber' => $this->generateInvoiceNumber($order),
                'invoiceDate' => now()->format('d/m/Y'),
            ])->render());
            
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $filepath = $this->saveDomPDFOutput($dompdf, $order);
            if ($filepath) {
                Log::info('DomPDF generation successful (Direct approach)', [
                    'order_id' => $order->id,
                    'filepath' => $filepath
                ]);
                return $filepath;
            }
            
        } catch (\Exception $e) {
            Log::warning('DomPDF Direct approach failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        try {
            // Approach 3: Try with DomPDF wrapper
            $dompdf = app('dompdf.wrapper');
            $dompdf->loadView('pdf.invoice', [
                'order' => $order,
                'invoiceNumber' => $this->generateInvoiceNumber($order),
                'invoiceDate' => now()->format('d/m/Y'),
            ]);
            
            $dompdf->setPaper('A4', 'portrait');
            
            $filepath = $this->savePDF($dompdf, $order);
            if ($filepath) {
                Log::info('DomPDF generation successful (Wrapper approach)', [
                    'order_id' => $order->id,
                    'filepath' => $filepath
                ]);
                return $filepath;
            }
            
        } catch (\Exception $e) {
            Log::warning('DomPDF Wrapper approach failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        try {
            // Approach 4: Try with minimal DomPDF configuration
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->setOptions(new \Dompdf\Options([
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'isHtml5ParserEnabled' => false,
                'tempDir' => storage_path('app/temp'),
                'fontDir' => storage_path('fonts'),
                'fontCache' => storage_path('fonts'),
                'chroot' => base_path(),
            ]));
            
            $dompdf->loadHtml(view('pdf.invoice', [
                'order' => $order,
                'invoiceNumber' => $this->generateInvoiceNumber($order),
                'invoiceDate' => now()->format('d/m/Y'),
            ])->render());
            
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $filepath = $this->saveDomPDFOutput($dompdf, $order);
            if ($filepath) {
                Log::info('DomPDF generation successful (Minimal approach)', [
                    'order_id' => $order->id,
                    'filepath' => $filepath
                ]);
                return $filepath;
            }
            
        } catch (\Exception $e) {
            Log::warning('DomPDF Minimal approach failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Try alternative generation methods
     */
    private function tryAlternativeGeneration(Order $order)
    {
        try {
            // Try to generate a simple HTML file instead of PDF
            $html = $this->generateInvoiceHTML($order);
            
            $filename = 'Invoice_' . $order->order_number . '_' . now()->format('Y-m-d_H-i-s') . '.html';
            
            // Try multiple locations for HTML file
            $locations = [
                storage_path('app/private/invoices/' . $filename),
                storage_path('app/temp/' . $filename),
                storage_path('app/public/invoices/' . $filename),
            ];
            
            foreach ($locations as $filepath) {
                try {
                    // Ensure directory exists
                    $directory = dirname($filepath);
                    if (!is_dir($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    $result = file_put_contents($filepath, $html);
                    
                    if ($result !== false && file_exists($filepath)) {
                        Log::info('HTML invoice generated as fallback', [
                            'order_id' => $order->id,
                            'filepath' => $filepath,
                            'filesize' => filesize($filepath)
                        ]);
                        return $filepath;
                    }
                    
                } catch (\Exception $e) {
                    Log::warning('Failed to save HTML to location', [
                        'order_id' => $order->id,
                        'filepath' => $filepath,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
            
            // If all locations fail, try to save to a simple location
            $simplePath = storage_path('app/temp/simple_invoice_' . $order->id . '.html');
            if (file_put_contents($simplePath, $html) !== false) {
                Log::info('HTML invoice saved to simple location', [
                    'order_id' => $order->id,
                    'filepath' => $simplePath
                ]);
                return $simplePath;
            }
            
        } catch (\Exception $e) {
            Log::warning('HTML fallback generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        // Final fallback: try to create a very simple text file
        try {
            $textContent = $this->generateInvoiceText($order);
            $textPath = storage_path('app/temp/text_invoice_' . $order->id . '.txt');
            
            if (file_put_contents($textPath, $textContent) !== false) {
                Log::info('Text invoice generated as final fallback', [
                    'order_id' => $order->id,
                    'filepath' => $textPath
                ]);
                return $textPath;
            }
            
        } catch (\Exception $e) {
            Log::warning('Text fallback generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Save PDF to file
     */
    private function savePDF($pdf, Order $order)
    {
        try {
            $filename = 'Invoice_' . $order->order_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            // Try multiple save locations
            $locations = [
                storage_path('app/public/invoices/' . $filename),
                storage_path('app/private/invoices/' . $filename),
                storage_path('app/temp/' . $filename),
            ];

            foreach ($locations as $filepath) {
                try {
                    $pdf->save($filepath);
                    
                    if (file_exists($filepath)) {
                        Log::info('PDF saved successfully', [
                            'order_id' => $order->id,
                            'filepath' => $filepath,
                            'filesize' => filesize($filepath)
                        ]);
                        return $filepath;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to save to location', [
                        'order_id' => $order->id,
                        'filepath' => $filepath,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('PDF save failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Save DomPDF output to file
     */
    private function saveDomPDFOutput($dompdf, Order $order)
    {
        try {
            $filename = 'Invoice_' . $order->order_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            // Try multiple save locations
            $locations = [
                storage_path('app/public/invoices/' . $filename),
                storage_path('app/private/invoices/' . $filename),
                storage_path('app/temp/' . $filename),
            ];

            foreach ($locations as $filepath) {
                try {
                    // Ensure directory exists
                    $directory = dirname($filepath);
                    if (!is_dir($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    // Get PDF output and save to file
                    $output = $dompdf->output();
                    if (file_put_contents($filepath, $output) !== false) {
                        
                        if (file_exists($filepath)) {
                            Log::info('DomPDF output saved successfully', [
                                'order_id' => $order->id,
                                'filepath' => $filepath,
                                'filesize' => filesize($filepath)
                            ]);
                            return $filepath;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to save DomPDF output to location', [
                        'order_id' => $order->id,
                        'filepath' => $filepath,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('DomPDF output save failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Generate simple HTML invoice as fallback
     */
    private function generateInvoiceHTML(Order $order)
    {
        $html = '<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Invoice #' . $this->generateInvoiceNumber($order) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 20px; }
        .items { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MyGooners</h1>
        <p>Invoice #' . $this->generateInvoiceNumber($order) . '</p>
        <p>Date: ' . now()->format('d/m/Y') . '</p>
    </div>
    
    <div class="invoice-info">
        <h3>Order Information</h3>
        <p><strong>Order Number:</strong> ' . $order->order_number . '</p>
        <p><strong>Order Date:</strong> ' . $order->created_at->format('d/m/Y H:i') . '</p>
        <p><strong>Status:</strong> ' . ucfirst($order->status) . '</p>
        <p><strong>Payment Status:</strong> ' . ucfirst($order->payment_status) . '</p>
    </div>
    
    <div class="items">
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($order->items as $item) {
            $html .= '<tr>
                <td>' . ($item->product_name ?: 'Product') . '</td>
                <td>' . $item->quantity . '</td>
                <td>RM ' . number_format($item->price, 2) . '</td>
                <td>RM ' . number_format($item->subtotal, 2) . '</td>
            </tr>';
        }

        $html .= '</tbody>
        </table>
    </div>
    
    <div class="total">
        <h3>Total: RM ' . number_format($order->total, 2) . '</h3>
    </div>
    
    <div style="margin-top: 40px; text-align: center; color: #666;">
        <p>Thank you for your order!</p>
        <p>MyGooners</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber(Order $order)
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $orderSuffix = substr($order->order_number, -6); // Last 6 characters of order number
        
        return $prefix . $date . $orderSuffix;
    }

    /**
     * Generate simple text invoice as final fallback
     */
    private function generateInvoiceText(Order $order)
    {
        $text = "INVOICE\n";
        $text .= "=======\n\n";
        $text .= "Invoice #: " . $this->generateInvoiceNumber($order) . "\n";
        $text .= "Date: " . now()->format('d/m/Y') . "\n";
        $text .= "Order #: " . $order->order_number . "\n";
        $text .= "Order Date: " . $order->created_at->format('d/m/Y H:i') . "\n";
        $text .= "Status: " . ucfirst($order->status) . "\n";
        $text .= "Payment Status: " . ucfirst($order->payment_status) . "\n\n";
        
        $text .= "ORDER ITEMS:\n";
        $text .= "============\n";
        
        foreach ($order->items as $item) {
            $text .= "- " . ($item->product_name ?: 'Product') . "\n";
            $text .= "  Quantity: " . $item->quantity . "\n";
            $text .= "  Price: RM " . number_format($item->price, 2) . "\n";
            $text .= "  Total: RM " . number_format($item->subtotal, 2) . "\n\n";
        }
        
        $text .= "TOTAL: RM " . number_format($order->total, 2) . "\n\n";
        $text .= "Thank you for your order!\n";
        $text .= "MyGooners\n";
        
        return $text;
    }

    /**
     * Delete invoice file
     */
    public function deleteInvoice($filepath)
    {
        if ($filepath && file_exists($filepath)) {
            unlink($filepath);
            Log::info('Invoice file deleted', ['filepath' => $filepath]);
        }
    }
} 