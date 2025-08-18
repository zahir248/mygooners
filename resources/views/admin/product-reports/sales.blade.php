@extends('layouts.admin')

@section('title', 'Laporan Jualan Produk - Panel Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Jualan Produk</h1>
                    <p class="mt-2 text-gray-600">Analisis prestasi jualan produk dan variasi</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.product-reports.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ route('admin.product-reports.sales.export') }}?{{ http_build_query(request()->all()) }}" 
                       class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                        Eksport CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Sales Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Jualan</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($salesSummary['total_sales'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Kuantiti</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salesSummary['total_quantity']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Pesanan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salesSummary['total_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Purata Nilai</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($salesSummary['average_order_value'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Julat Tarikh</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.product-reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">Julat Pantas</label>
                        <select name="date_range" id="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="7" {{ request('date_range') == '7' ? 'selected' : '' }}>7 hari lepas</option>
                            <option value="30" {{ request('date_range') == '30' ? 'selected' : '' }}>30 hari lepas</option>
                            <option value="90" {{ request('date_range') == '90' ? 'selected' : '' }}>90 hari lepas</option>
                            <option value="365" {{ request('date_range') == '365' ? 'selected' : '' }}>1 tahun lepas</option>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tarikh Mula</label>
                        <input type="date" name="start_date" id="start_date" 
                               value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tarikh Akhir</label>
                        <input type="date" name="end_date" id="end_date" 
                               value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex items-end space-x-3">
                        <a href="{{ route('admin.product-reports.sales') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sales Data Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Data Jualan Produk</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Tempoh: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Jualan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Kuantiti</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purata Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($productSalesData as $productId => $data)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($data['product']->images && count($data['product']->images) > 0)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ route('product.image', basename($data['product']->images[0])) }}" alt="{{ $data['product']->title }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $data['product']->title }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $data['product']->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $data['product']->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $data['product']->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">RM {{ number_format($data['total_revenue'], 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">{{ number_format($data['total_quantity']) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($data['total_quantity'] > 0)
                                        <span class="font-medium">RM {{ number_format($data['total_revenue'] / $data['total_quantity'], 2) }}</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(count($data['variations']) > 0)
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ count($data['variations']) }} variasi</span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @foreach($data['variations'] as $variationData)
                                                <div class="flex justify-between">
                                                    <span>{{ $variationData['variation']->name }}:</span>
                                                    <span>{{ $variationData['quantity'] }} unit</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Tiada variasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <!-- Show Product Details -->
                                    <a href="{{ route('admin.products.show', $data['product']->id) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Butiran Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Edit Product -->
                                    <a href="{{ route('admin.products.edit', $data['product']->id) }}"
                                       class="text-red-600 hover:text-red-900"
                                       title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tiada data jualan dijumpai untuk tempoh yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Insights -->
        @if(count($productSalesData) > 0)
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Analisis Jualan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Top Performing Products -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Produk Terlaris (Mengikut Kuantiti)</h4>
                            <div class="space-y-3">
                                @php
                                    $topProducts = collect($productSalesData)->sortByDesc('total_quantity')->take(5);
                                @endphp
                                @foreach($topProducts as $productId => $data)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $loop->iteration }}.</span>
                                        <span class="ml-3 text-sm text-gray-700">{{ Str::limit($data['product']->title, 30) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($data['total_quantity']) }} unit</div>
                                        <div class="text-xs text-gray-500">RM {{ number_format($data['total_revenue'], 2) }}</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Top Revenue Products -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-4">Produk Tertinggi Pendapatan</h4>
                            <div class="space-y-3">
                                @php
                                    $topRevenue = collect($productSalesData)->sortByDesc('total_revenue')->take(5);
                                @endphp
                                @foreach($topRevenue as $productId => $data)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $loop->iteration }}.</span>
                                        <span class="ml-3 text-sm text-gray-700">{{ Str::limit($data['product']->title, 30) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">RM {{ number_format($data['total_revenue'], 2) }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($data['total_quantity']) }} unit</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update end date when date range changes
    document.getElementById('date_range').addEventListener('change', function() {
        const days = parseInt(this.value);
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - days);
        
        document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
    });
});
</script>
@endsection
