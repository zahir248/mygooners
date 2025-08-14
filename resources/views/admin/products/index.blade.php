@extends('layouts.admin')

@section('title', 'Pengurusan Produk')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengurusan Produk</h1>
            <p class="mt-2 text-sm text-gray-700">Urus semua produk Arsenal yang ditawarkan</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Cipta Produk
            </a>
        </div>
    </div>
</div>

<!-- Debug Info (temporary) -->
@if(session('debug_info'))
    <div class="mx-4 mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
        <h4 class="font-bold text-yellow-800 mb-2">Debug Info:</h4>
        <pre class="text-sm text-yellow-700">{{ json_encode(session('debug_info'), JSON_PRETTY_PRINT) }}</pre>
    </div>
@endif

<!-- Filters and Search -->
<div class="mx-4 bg-white shadow rounded-lg mb-6">
    <form method="GET" action="{{ route('admin.products.index') }}" class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Tapis Produk</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari produk..."
                           value="{{ request('search') }}"
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Status Filter -->
                <select name="status" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Status</option>
                                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <!-- Category Filter -->
                <select name="category" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                <!-- Stock Filter -->
                <select name="stock" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Stok</option>
                    <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>Dalam Stok</option>
                    <option value="low_stock" {{ request('stock') === 'low_stock' ? 'selected' : '' }}>Stok Rendah</option>
                    <option value="out_of_stock" {{ request('stock') === 'out_of_stock' ? 'selected' : '' }}>Kehabisan Stok</option>
                </select>
                <!-- Featured Filter -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="featured"
                           id="featured"
                           value="1"
                           {{ request('featured') ? 'checked' : '' }}
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="featured" class="ml-2 block text-sm text-gray-900">
                        Ditampilkan Sahaja
                    </label>
                </div>
                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                        Tapis
                    </button>
                    <a href="{{ route('admin.products.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="mx-4 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Produk ({{ $products->count() }})
            </h3>
            @if(request('search') || request('status') || request('category') || request('stock') || request('featured'))
                <div class="mt-2 sm:mt-0">
                    <p class="text-sm text-gray-600">
                        Tapisan aktif:
                        @if(request('search'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                Cari: "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-1">
                                Status: {{ request('status') === 'active' ? 'Aktif' : (request('status') === 'inactive' ? 'Tidak Aktif' : (request('status') === 'rejected' ? 'Ditolak' : '')) }}
                            </span>
                        @endif
                        @if(request('category'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                Kategori: {{ request('category') }}
                            </span>
                        @endif
                        @if(request('stock'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-1">
                                Stok: {{ request('stock') === 'in_stock' ? 'Dalam Stok' : (request('stock') === 'low_stock' ? 'Stok Rendah' : 'Kehabisan Stok') }}
                            </span>
                        @endif
                        @if(request('featured'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mr-1">
                                Ditampilkan Sahaja
                            </span>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
    @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stok
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Varian
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penilaian
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tontonan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarikh
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Tindakan</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-16 w-24">
                                        @if($product->images && count($product->images) > 0)
                                                                                            <img class="h-16 w-24 rounded-lg object-cover" src="{{ route('product.image', basename($product->images[0])) }}" alt="{{ $product->title }}">
                                        @else
                                            <div class="h-16 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="text-sm font-bold text-gray-900 truncate">
                                                {{ $product->title }}
                                            </p>
                                            @if($product->is_featured)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                    Ditampilkan
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 line-clamp-2">
                                            ID Produk: #{{ $product->id }} • {{ $product->category }} • {{ $product->stock_quantity }} dalam stok
                                        </p>
                                        @if($product->sale_price)
                                            <p class="text-sm text-red-600 font-medium">Jualan Aktif!</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $product->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->sale_price)
                                        <div class="flex items-center space-x-1">
                                            <span class="font-semibold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                                            <span class="text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                                        </div>
                                    @else
                                        <span class="font-semibold">RM{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->stock_quantity > 10)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $product->stock_quantity }} Dalam Stok
                                        </span>
                                    @elseif($product->stock_quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $product->stock_quantity }} Stok Rendah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Kehabisan Stok
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $product->variations->count() }} Varian
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @elseif($product->status === 'inactive')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak Aktif
                                    </span>

                                @elseif($product->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product->average_rating))
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-1 text-sm text-gray-600">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }})</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ number_format($product->views_count) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>
                                    <div class="font-medium">{{ $product->created_at->format('j M Y') }}</div>
                                    <div class="text-xs">{{ $product->updated_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($product->status !== 'rejected')
                                <div class="flex justify-end space-x-3">
                                        <!-- Status Modal Button -->
                                        <button type="button" 
                                                onclick="openStatusModal({{ $product->id }}, '{{ $product->title }}', '{{ $product->status }}')"
                                                class="text-gray-600 hover:text-gray-900"
                                                title="Ubah Status">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31.826 2.37 2.37a1.724 1.724 0 002.572 1.065c.426 1.756 2.924 1.756 3.35 0a1.724 1.724 0 002.573-1.066c1.543-.94 3.31.826 2.37-2.37a1.724 1.724 0 011.065-2.572c1.756-.426 1.756-2.924 0-3.35a1.724 1.724 0 00-1.066-2.573c.94-1.543-.826-3.31.826-2.37-2.37a1.724 1.724 0 00-2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                    
                                    <!-- Show Product Details -->
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Butiran Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Edit Product -->
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="text-red-600 hover:text-red-900"
                                       title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Delete Product -->
                                    <form id="deleteProductForm{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDeleteProduct({{ $product->id }}, '{{ $product->title }}')" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Padam Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <span class="text-gray-400 text-xs">Tiada tindakan tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $products->total() }}</span> results
                </div>
                <div class="flex space-x-2">
                    @if($products->onFirstPage())
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Previous
                        </button>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Next
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada produk</h3>
            <p class="mt-1 text-sm text-gray-500">Mula dengan mencipta produk pertama anda.</p>
            <div class="mt-6">
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Cipta Produk
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Padam Produk</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu memadamkan "<span id="deleteProductTitle"></span>"? Tindakan ini tidak boleh diundur.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeDeleteProductModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <form id="deleteProductFormModal" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Padam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31.826 2.37 2.37a1.724 1.724 0 002.572 1.065c.426 1.756 2.924 1.756 3.35 0a1.724 1.724 0 002.573-1.066c1.543-.94 3.31.826 2.37-2.37a1.724 1.724 0 011.065-2.572c1.756-.426 1.756-2.924 0-3.35a1.724 1.724 0 00-1.066-2.573c.94-1.543-.826-3.31.826-2.37-2.37a1.724 1.724 0 00-2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Ubah Status Produk</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center mb-4">
                    Pilih status baru untuk "<span id="statusProductTitle"></span>"
                </p>
                <form id="statusFormModal" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-green-50 transition-colors duration-150">
                            <input type="radio" name="status" value="active" class="sr-only">
                            <span class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full hidden"></span>
                            </span>
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            <span class="text-sm font-medium text-gray-700">Aktif</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors duration-150">
                            <input type="radio" name="status" value="inactive" class="sr-only">
                            <span class="w-4 h-4 border-2 border-gray-300 rounded-full mr-3 flex items-center justify-center">
                                <span class="w-2 h-2 bg-gray-400 rounded-full hidden"></span>
                            </span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                            <span class="text-sm font-medium text-gray-700">Tidak Aktif</span>
                        </label>

                    </div>
                </form>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeStatusModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <button onclick="submitStatusForm()" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Kemas Kini
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentProductId = null;

function openStatusModal(productId, productTitle, currentStatus) {
    currentProductId = productId;
    document.getElementById('statusProductTitle').textContent = productTitle;
    
    // Set the current status as selected
    const radioButtons = document.querySelectorAll('#statusFormModal input[name="status"]');
    radioButtons.forEach(radio => {
        if (radio.value === currentStatus) {
            radio.checked = true;
            // Update the visual indicator
            const label = radio.closest('label');
            const indicator = label.querySelector('.w-4.h-4.border-2.border-gray-300.rounded-full.mr-3 .w-2.h-2');
            if (radio.checked) {
                indicator.classList.remove('hidden');
            }
        }
    });
    
    // Set the form action using the correct route
    const baseUrl = '{{ route("admin.products.update-status", ":id") }}';
    document.getElementById('statusFormModal').action = baseUrl.replace(':id', productId);
    
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    currentProductId = null;
}

function submitStatusForm() {
    const form = document.getElementById('statusFormModal');
    const selectedStatus = form.querySelector('input[name="status"]:checked');
    
    if (!selectedStatus) {
        alert('Sila pilih status baru');
        return;
    }
    
    form.submit();
}

// Handle radio button selection
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('#statusFormModal input[name="status"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove all selected indicators
            document.querySelectorAll('#statusFormModal .w-4.h-4.border-2.border-gray-300.rounded-full.mr-3 .w-2.h-2').forEach(indicator => {
                indicator.classList.add('hidden');
            });
            
            // Add selected indicator to the checked radio
            if (this.checked) {
                const label = this.closest('label');
                const indicator = label.querySelector('.w-4.h-4.border-2.border-gray-300.rounded-full.mr-3 .w-2.h-2');
                indicator.classList.remove('hidden');
            }
        });
    });
});

    function confirmDeleteProduct(productId, productTitle) {
        document.getElementById('deleteProductTitle').textContent = productTitle;
        document.getElementById('deleteProductFormModal').action = `/admin/products/${productId}`;
        document.getElementById('deleteProductModal').classList.remove('hidden');
    }

    function closeDeleteProductModal() {
        document.getElementById('deleteProductModal').classList.add('hidden');
    }

// Close modals when clicking outside
document.getElementById('deleteProductModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteProductModal();
    }
});

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>
@endsection 