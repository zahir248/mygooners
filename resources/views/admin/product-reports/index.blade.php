@extends('layouts.admin')

@section('title', 'Laporan Produk - Panel Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Laporan Produk</h1>
            <p class="mt-2 text-gray-600">Gambaran keseluruhan semua produk, stok, dan prestasi jualan</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Produk</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_products']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Produk Aktif</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['active_products']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Stok</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_stock']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dengan Variasi</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($summary['products_with_variations']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jualan Bulan Lepas</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($summary['last_month_sales'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Tindakan Pantas</h3>
            </div>
            <div class="p-6">
                                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <a href="{{ route('admin.product-reports.stock') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Laporan Stok</h4>
                            <p class="text-sm text-gray-500">Lihat status stok semua produk</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.product-reports.sales') }}" 
                       class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">Laporan Jualan</h4>
                            <p class="text-sm text-gray-500">Analisis prestasi jualan produk</p>
                        </div>
                    </a>

                                                              <a href="{{ route('admin.product-reports.stock.export') }}" 
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                         <div class="p-2 bg-purple-100 rounded-lg">
                             <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                             </svg>
                         </div>
                         <div class="ml-4">
                             <h4 class="text-sm font-medium text-gray-900">Eksport Stok</h4>
                             <p class="text-sm text-gray-500">Muat turun laporan stok dalam CSV</p>
                         </div>
                     </a>

                     <a href="{{ route('admin.products.index') }}" 
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                         <div class="p-2 bg-indigo-100 rounded-lg">
                             <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                             </svg>
                         </div>
                         <div class="ml-4">
                             <h4 class="text-sm font-medium text-gray-900">Semua Produk</h4>
                             <p class="text-sm text-gray-500">Lihat senarai lengkap semua produk</p>
                         </div>
                     </a>

                     <a href="{{ route('admin.product-reports.sales.export') }}" 
                        class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                         <div class="p-2 bg-orange-100 rounded-lg">
                             <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                             </svg>
                         </div>
                         <div class="ml-4">
                             <h4 class="text-sm font-medium text-gray-900">Eksport Jualan</h4>
                             <p class="text-sm text-gray-500">Muat turun laporan jualan dalam CSV</p>
                         </div>
                     </a>
                </div>
            </div>
        </div>

        <!-- Additional Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Stock Status Distribution -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status Stok</h3>
                    <p class="text-sm text-gray-600">Taburan status stok produk</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Ada Stok</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $summary['in_stock_count'] }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $summary['total_products'] > 0 ? round(($summary['in_stock_count'] / $summary['total_products']) * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Stok Rendah</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $summary['low_stock_count'] }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $summary['total_products'] > 0 ? round(($summary['low_stock_count'] / $summary['total_products']) * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-700">Habis Stok</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $summary['out_of_stock_count'] }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $summary['total_products'] > 0 ? round(($summary['out_of_stock_count'] / $summary['total_products']) * 100) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Taburan Kategori</h3>
                    <p class="text-sm text-gray-600">Produk mengikut kategori</p>
                </div>
                <div class="p-6">
                    @if($categories->count() > 0)
                        <div class="space-y-3">
                            @foreach($categories->take(5) as $category)
                                @php
                                    $categoryCount = \App\Models\Product::where('category', $category)->count();
                                    $percentage = $summary['total_products'] > 0 ? round(($categoryCount / $summary['total_products']) * 100) : 0;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $category }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $categoryCount }}</span>
                                        <span class="text-xs text-gray-500">({{ $percentage }}%)</span>
                                    </div>
                                </div>
                            @endforeach
                            @if($categories->count() > 5)
                                <div class="text-xs text-gray-500 text-center pt-2">
                                    Dan {{ $categories->count() - 5 }} kategori lain
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-sm text-gray-500 text-center py-4">
                            Tiada kategori dijumpai
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity & Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Stock Alerts -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Amaran Stok</h3>
                    <p class="text-sm text-gray-600">Produk yang memerlukan perhatian</p>
                </div>
                <div class="p-6">
                    @php
                        $lowStockProducts = \App\Models\Product::where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->take(5)->get();
                        $outOfStockProducts = \App\Models\Product::where('stock_quantity', '<=', 0)->take(5)->get();
                    @endphp
                    
                    @if($lowStockProducts->count() > 0 || $outOfStockProducts->count() > 0)
                        @if($lowStockProducts->count() > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-yellow-800 mb-2">Stok Rendah (≤10)</h4>
                                <div class="space-y-2">
                                    @foreach($lowStockProducts as $product)
                                        <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                                            <span class="text-sm text-gray-700">{{ Str::limit($product->title, 25) }}</span>
                                            <span class="text-xs text-yellow-600 font-medium">{{ $product->stock_quantity }} unit</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($outOfStockProducts->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-red-800 mb-2">Habis Stok</h4>
                                <div class="space-y-2">
                                    @foreach($outOfStockProducts as $product)
                                        <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                                            <span class="text-sm text-gray-700">{{ Str::limit($product->title, 25) }}</span>
                                            <span class="text-xs text-red-600 font-medium">0 unit</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-sm text-gray-500 text-center py-4">
                            Tiada amaran stok
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sales Performance -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Prestasi Jualan</h3>
                    <p class="text-sm text-gray-600">Jualan 30 hari lepas</p>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <div class="text-3xl font-bold text-green-600">RM {{ number_format($summary['last_month_sales'], 2) }}</div>
                        <div class="text-sm text-gray-500">Jumlah Jualan</div>
                    </div>
                    
                    @php
                        $topSellingProducts = \App\Models\OrderItem::with('product')
                            ->whereHas('order', function($q) {
                                $q->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
                                  ->whereIn('status', ['delivered', 'shipped', 'processing']);
                            })
                            ->select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
                            ->groupBy('product_id')
                            ->orderBy('total_quantity', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($topSellingProducts->count() > 0)
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Produk Terlaris</h4>
                            @foreach($topSellingProducts as $item)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm text-gray-700">{{ Str::limit($item->product->title ?? 'N/A', 25) }}</span>
                                    <span class="text-xs text-gray-600 font-medium">{{ $item->total_quantity }} unit</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 text-center py-4">
                            Tiada data jualan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
