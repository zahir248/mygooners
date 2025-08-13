<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'items.variation']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('billing_name', 'like', "%{$search}%")
                  ->orWhere('shipping_email', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'this_month_orders' => Order::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variation'])
                     ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_courier' => 'nullable|string|max:255',
            'custom_shipping_courier' => 'nullable|string|max:255'
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        
        // Handle custom courier input
        $shippingCourier = $request->shipping_courier;
        if ($request->shipping_courier === 'Other' && $request->filled('custom_shipping_courier')) {
            $shippingCourier = $request->custom_shipping_courier;
        }
        
        $order->update([
            'status' => $request->status,
            'notes' => $request->notes ?: $order->notes,
            'tracking_number' => $request->tracking_number,
            'shipping_courier' => $shippingCourier
        ]);

        // Update timestamps based on status
        if ($request->status === 'shipped' && $oldStatus !== 'shipped') {
            $order->update(['shipped_at' => now()]);
        }

        if ($request->status === 'delivered' && $oldStatus !== 'delivered') {
            $order->update(['delivered_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berjaya dikemas kini.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Status pembayaran berjaya dikemas kini.');
    }

    /**
     * Delete order
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Only allow deletion of pending or cancelled orders
        if (!in_array($order->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Pesanan yang telah diproses tidak boleh dipadamkan.');
        }

        DB::transaction(function() use ($order) {
            // Delete order items
            $order->items()->delete();
            // Delete order
            $order->delete();
        });

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berjaya dipadamkan.');
    }

    /**
     * Get order statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'this_month_orders' => Order::whereMonth('created_at', now()->month)->count(),
            'this_month_revenue' => Order::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        return response()->json($stats);
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'items.variation']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers - Enhanced with complete details
            fputcsv($file, [
                'Order Number',
                'Order Date',
                'Order Status',
                'Payment Status',
                'Payment Method',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Subtotal',
                'Shipping Cost',
                'Tax',
                'Total Amount',
                'Items Count',
                'Complete Shipping Address',
                'Complete Billing Address',
                'Order Notes',
                'FPL Manager Name',
                'FPL Team Name',
                'Tracking Number',
                'Shipping Courier',
                'Shipped Date',
                'Delivered Date',
                'Item Details'
            ]);

            foreach ($orders as $order) {
                // Build complete addresses
                $shippingAddress = $this->buildCompleteAddress([
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'state' => $order->shipping_state,
                    'postal_code' => $order->shipping_postal_code,
                    'country' => $order->shipping_country
                ]);

                $billingAddress = $this->buildCompleteAddress([
                    'address' => $order->billing_address,
                    'city' => $order->billing_city,
                    'state' => $order->billing_state,
                    'postal_code' => $order->billing_postal_code,
                    'country' => $order->billing_country
                ]);

                // Build item details
                $itemDetails = $this->buildItemDetails($order->items);

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->status,
                    $order->payment_status,
                    $order->payment_method,
                    $order->shipping_name,
                    $order->shipping_email,
                    $order->shipping_phone,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->tax,
                    $order->total,
                    $order->items->count(),
                    $shippingAddress,
                    $billingAddress,
                    $order->notes ?? '',
                    $order->fpl_manager_name ?? '',
                    $order->fpl_team_name ?? '',
                    $order->tracking_number ?? '',
                    $order->shipping_courier ?? '',
                    $order->shipped_at ? $order->shipped_at->format('Y-m-d H:i:s') : '',
                    $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : '',
                    $itemDetails
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build complete address string
     */
    private function buildCompleteAddress($addressData)
    {
        $parts = [];
        
        if (!empty($addressData['address'])) {
            $parts[] = $addressData['address'];
        }
        
        if (!empty($addressData['city'])) {
            $parts[] = $addressData['city'];
        }
        
        if (!empty($addressData['state'])) {
            $parts[] = $addressData['state'];
        }
        
        if (!empty($addressData['postal_code'])) {
            $parts[] = $addressData['postal_code'];
        }
        
        if (!empty($addressData['country'])) {
            $parts[] = $addressData['country'];
        }
        
        return implode(', ', $parts);
    }

    /**
     * Build detailed item information
     */
    private function buildItemDetails($items)
    {
        $itemDetails = [];
        
        foreach ($items as $item) {
            $itemInfo = [];
            
            // Product name
            if ($item->product) {
                $itemInfo[] = 'Product: ' . $item->product->title;
            } else {
                $itemInfo[] = 'Product: ' . $item->product_name;
            }
            
            // Variation details
            if ($item->variation && $item->variation->name) {
                $itemInfo[] = 'Variation: ' . $item->variation->name;
            }
            
            // Quantity and price
            $itemInfo[] = 'Qty: ' . $item->quantity;
            $itemInfo[] = 'Price: RM' . number_format($item->price, 2);
            $itemInfo[] = 'Subtotal: RM' . number_format($item->price * $item->quantity, 2);
            
            $itemDetails[] = implode(' | ', $itemInfo);
        }
        
        return implode(' || ', $itemDetails);
    }

    /**
     * View invoice for an order
     */
    public function viewInvoice($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variation'])
                     ->findOrFail($id);

        // Check if order is eligible for invoice viewing
        if (($order->status === 'pending' && $order->payment_status === 'pending') || $order->payment_status === 'failed') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang belum dibayar atau pembayaran gagal.');
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang telah dibatalkan.');
        }

        try {
            $invoiceService = new \App\Services\InvoiceService();
            $pdfPath = $invoiceService->generateInvoice($order);
            
            // Check if invoice generation failed
            if (!$pdfPath) {
                \Log::error('Invoice generation returned null', [
                    'order_id' => $id,
                    'admin_id' => auth()->id()
                ]);
                return back()->with('error', 'Gagal menjana invois. Sila cuba lagi.');
            }
            
            // Check if file exists
            if (!file_exists($pdfPath)) {
                \Log::error('Generated invoice file does not exist', [
                    'order_id' => $id,
                    'admin_id' => auth()->id(),
                    'filepath' => $pdfPath
                ]);
                return back()->with('error', 'Fail invois tidak dijumpai. Sila cuba lagi.');
            }
            
            // Get the filename and determine content type
            $filename = basename($pdfPath);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            $disposition = $extension === 'html' ? 'inline' : 'inline';
            
            // Return the file to be displayed in browser
            return response()->file($pdfPath, [
                'Content-Type' => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to view invoice', [
                'order_id' => $id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal memaparkan invois. Sila cuba lagi.');
        }
    }

    /**
     * Download invoice PDF for an order
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variation'])
                     ->findOrFail($id);

        // Check if order is eligible for invoice download
        if (($order->status === 'pending' && $order->payment_status === 'pending') || $order->payment_status === 'failed') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang belum dibayar atau pembayaran gagal.');
        }

        // Check if order is cancelled
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Invois tidak tersedia untuk pesanan yang telah dibatalkan.');
        }

        try {
            $invoiceService = new \App\Services\InvoiceService();
            $pdfPath = $invoiceService->generateInvoice($order);
            
            // Check if invoice generation failed
            if (!$pdfPath) {
                \Log::error('Invoice generation returned null', [
                    'order_id' => $id,
                    'admin_id' => auth()->id()
                ]);
                return back()->with('error', 'Gagal menjana invois. Sila cuba lagi.');
            }
            
            // Check if file exists
            if (!file_exists($pdfPath)) {
                \Log::error('Generated invoice file does not exist', [
                    'order_id' => $id,
                    'admin_id' => auth()->id(),
                    'filepath' => $pdfPath
                ]);
                return back()->with('error', 'Fail invois tidak dijumpai. Sila cuba lagi.');
            }
            
            // Get the filename and determine content type
            $filename = basename($pdfPath);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            
            // Return the file for download
            return response()->download($pdfPath, $filename, [
                'Content-Type' => $contentType
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to download invoice', [
                'order_id' => $id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal memuat turun invois. Sila cuba lagi.');
        }
    }

    /**
     * Get content type based on file extension
     */
    private function getContentType($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'application/pdf';
            case 'html':
                return 'text/html';
            case 'txt':
                return 'text/plain';
            default:
                return 'application/octet-stream';
        }
    }
} 