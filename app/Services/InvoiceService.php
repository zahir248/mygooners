<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Generate PDF invoice for an order
     */
    public function generateInvoice(Order $order)
    {
        try {
            // Use the DomPDF facade
            $pdf = Pdf::loadView('pdf.invoice', [
                'order' => $order,
                'invoiceNumber' => $this->generateInvoiceNumber($order),
                'invoiceDate' => now()->format('d/m/Y'),
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'portrait');

            // Create directory if it doesn't exist
            $directory = storage_path('app/public/invoices');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate filename
            $filename = 'Invoice_' . $order->order_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $filepath = $directory . '/' . $filename;

            // Save PDF to file
            $pdf->save($filepath);

            \Log::info('Invoice generated successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'filepath' => $filepath
            ]);

            return $filepath;

        } catch (\Exception $e) {
            \Log::error('Failed to generate invoice', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return null;
        }
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
     * Delete invoice file
     */
    public function deleteInvoice($filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);
            \Log::info('Invoice file deleted', ['filepath' => $filepath]);
        }
    }
} 