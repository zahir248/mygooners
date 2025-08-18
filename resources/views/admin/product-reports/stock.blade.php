@extends('layouts.admin')

@section('title', 'Laporan Stok Produk - Panel Admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Stok Produk</h1>
                    <p class="mt-2 text-gray-600">Status stok terperinci untuk semua produk dan variasi</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.product-reports.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Kembali
                    </a>
                    <a href="{{ route('admin.product-reports.stock.export') }}?{{ http_build_query(request()->all()) }}" 
                       class="px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700">
                        Eksport CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Stock Summary Cards -->
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
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stockSummary['total_products']) }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Ada Stok</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stockSummary['in_stock']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Habis Stok</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stockSummary['out_of_stock']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stockSummary['low_stock']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Nilai Stok</p>
                        <p class="text-2xl font-bold text-gray-900">RM {{ number_format($stockSummary['total_stock_value'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Penapis</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.product-reports.stock') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">Status Stok</label>
                        <select name="stock_status" id="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Ada Stok</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Habis Stok</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Stok Rendah (≤10)</option>
                        </select>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category" id="category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-3">
                        <a href="{{ route('admin.product-reports.stock') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">
                            Terapkan Penapis
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Senarai Stok Produk</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Asas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr class="{{ $product->stock_quantity <= 0 ? 'bg-red-50' : ($product->stock_quantity <= 10 ? 'bg-yellow-50' : '') }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($product->images && count($product->images) > 0)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->title }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->title }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $product->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->user->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $product->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">{{ $product->stock_quantity }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->variations_count > 0)
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $product->variations_count }} variasi</span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @foreach($product->variations as $variation)
                                                <div class="flex justify-between">
                                                    <span>{{ $variation->name }}:</span>
                                                    <span class="{{ $variation->stock_quantity <= 0 ? 'text-red-600' : ($variation->stock_quantity <= 5 ? 'text-yellow-600' : 'text-green-600') }}">
                                                        {{ $variation->stock_quantity }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Tiada variasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->hasVariations())
                                        <span class="font-medium">{{ $product->total_stock }}</span>
                                        <div class="text-xs text-gray-500">
                                            ({{ $product->stock_quantity }} + {{ $product->variations->sum('stock_quantity') }})
                                        </div>
                                    @else
                                        <span class="font-medium">{{ $product->stock_quantity }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->stock_quantity <= 0)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Habis Stok
                                    </span>
                                @elseif($product->stock_quantity <= 10)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Stok Rendah
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Ada Stok
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.products.show', $product->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                                <a href="{{ route('admin.products.edit', $product->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tiada produk dijumpai dengan penapis yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        <!-- Stock Alerts -->
        @if($stockSummary['out_of_stock'] > 0 || $stockSummary['low_stock'] > 0)
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Amaran Stok</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($stockSummary['out_of_stock'] > 0)
                        <div class="border-l-4 border-red-400 bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        {{ $stockSummary['out_of_stock'] }} produk telah habis stok
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>Produk ini perlu diisi semula atau dihentikan sementara.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($stockSummary['low_stock'] > 0)
                        <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-7.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        {{ $stockSummary['low_stock'] }} produk mempunyai stok rendah
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Produk ini perlu diisi semula segera.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
