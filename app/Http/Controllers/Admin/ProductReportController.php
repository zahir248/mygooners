<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductReportController extends Controller
{
    public function index(Request $request)
    {
        // Get summary statistics
        $summary = $this->getSummaryStatistics($request);
        
        // Get categories for analytics
        $categories = Product::distinct()->pluck('category')->filter();

        return view('admin.product-reports.index', compact('summary', 'categories'));
    }

    public function stockReport(Request $request)
    {
        $query = Product::with(['variations', 'user'])
            ->withCount(['variations']);

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0);
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('stock_quantity', 'asc')->paginate(20);

        // Get stock summary
        $stockSummary = $this->getStockSummary();
        
        // Get categories for filter
        $categories = Product::distinct()->pluck('category')->filter();

        return view('admin.product-reports.stock', compact('products', 'stockSummary', 'categories'));
    }

    public function salesReport(Request $request)
    {
        $dateRange = $request->get('date_range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }

        // Get sales data for products
        $productSales = OrderItem::with(['product', 'variation'])
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->whereIn('status', ['delivered', 'shipped', 'processing']);
            })
            ->select('product_id', 'product_variation_id', 
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id', 'product_variation_id')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Group by product
        $productSalesData = [];
        foreach ($productSales as $sale) {
            $productId = $sale->product_id;
            if (!isset($productSalesData[$productId])) {
                $productSalesData[$productId] = [
                    'product' => $sale->product,
                    'total_quantity' => 0,
                    'total_revenue' => 0,
                    'variations' => []
                ];
            }
            
            $productSalesData[$productId]['total_quantity'] += $sale->total_quantity;
            $productSalesData[$productId]['total_revenue'] += $sale->total_revenue;
            
            if ($sale->product_variation_id) {
                $productSalesData[$productId]['variations'][] = [
                    'variation' => $sale->variation,
                    'quantity' => $sale->total_quantity,
                    'revenue' => $sale->total_revenue
                ];
            }
        }

        // Get sales summary
        $salesSummary = $this->getSalesSummary($startDate, $endDate);

        return view('admin.product-reports.sales', compact('productSalesData', 'salesSummary', 'startDate', 'endDate'));
    }

    public function exportStockReport(Request $request)
    {
        try {
            $query = Product::with(['variations', 'user'])
                ->withCount(['variations']);

            // Apply filters
            if ($request->filled('stock_status')) {
                if ($request->stock_status === 'in_stock') {
                    $query->where('stock_quantity', '>', 0);
                } elseif ($request->stock_status === 'out_of_stock') {
                    $query->where('stock_quantity', '<=', 0);
                } elseif ($request->stock_status === 'low_stock') {
                    $query->where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0);
                }
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            $products = $query->orderBy('stock_quantity', 'asc')->get();

            $filename = 'stock_report_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($products) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Product ID', 'Product Title', 'Category', 'Seller', 'Base Stock', 
                    'Has Variations', 'Total Stock (with variations)', 'Status', 'Created Date'
                ]);

                foreach ($products as $product) {
                    $totalStock = $product->hasVariations() ? $product->total_stock : $product->stock_quantity;
                    
                    fputcsv($file, [
                        $product->id,
                        $product->title,
                        $product->category,
                        $product->user->name ?? 'N/A',
                        $product->stock_quantity,
                        $product->hasVariations() ? 'Yes' : 'No',
                        $totalStock,
                        $product->status,
                        $product->created_at->format('Y-m-d')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Stock export error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportSalesReport(Request $request)
    {
        try {
            $dateRange = $request->get('date_range', '30');
            $startDate = Carbon::now()->subDays($dateRange);
            $endDate = Carbon::now();

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
            }

            $productSales = OrderItem::with(['product', 'variation'])
                ->whereHas('order', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                          ->whereIn('status', ['delivered', 'shipped', 'processing']);
                })
                ->select('product_id', 'product_variation_id', 
                        DB::raw('SUM(quantity) as total_quantity'),
                        DB::raw('SUM(subtotal) as total_revenue'))
                ->groupBy('product_id', 'product_variation_id')
                ->orderBy('total_quantity', 'desc')
                ->get();

            $filename = 'sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($productSales, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Product ID', 'Product Title', 'Variation Name', 'Total Quantity Sold', 
                    'Total Revenue', 'Date Range'
                ]);

                foreach ($productSales as $sale) {
                    fputcsv($file, [
                        $sale->product_id,
                        $sale->product->title ?? 'N/A',
                        $sale->variation->name ?? 'Base Product',
                        $sale->total_quantity,
                        'RM ' . number_format($sale->total_revenue, 2),
                        $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Sales export error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSummaryStatistics(Request $request)
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalStock = Product::sum('stock_quantity');
        $productsWithVariations = Product::whereHas('variations')->count();
        
        // Get stock status counts for proper percentage calculations
        $inStockCount = Product::where('stock_quantity', '>', 0)->count();
        $lowStockCount = Product::where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)->count();

        // Get total sales (last 30 days)
        $lastMonthSales = OrderItem::whereHas('order', function ($q) {
            $q->where('created_at', '>=', Carbon::now()->subDays(30))
              ->whereIn('status', ['delivered', 'shipped', 'processing']);
        })->sum('subtotal');

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_stock' => $totalStock,
            'products_with_variations' => $productsWithVariations,
            'last_month_sales' => $lastMonthSales,
            'in_stock_count' => $inStockCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount
        ];
    }

    private function getStockSummary()
    {
        $totalProducts = Product::count();
        $inStock = Product::where('stock_quantity', '>', 0)->count();
        $outOfStock = Product::where('stock_quantity', '<=', 0)->count();
        $lowStock = Product::where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->count();
        $totalStockValue = Product::sum(DB::raw('stock_quantity * price'));

        return [
            'total_products' => $totalProducts,
            'in_stock' => $inStock,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'total_stock_value' => $totalStockValue
        ];
    }

    private function getSalesSummary($startDate, $endDate)
    {
        $totalSales = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['delivered', 'shipped', 'processing']);
        })->sum('subtotal');

        $totalQuantity = OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->whereIn('status', ['delivered', 'shipped', 'processing']);
        })->sum('quantity');

        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['delivered', 'shipped', 'processing'])
            ->count();

        return [
            'total_sales' => $totalSales,
            'total_quantity' => $totalQuantity,
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
        ];
    }
}
