@extends('layouts.app')

@section('title', 'Pratonton Produk - MyGooners')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $product->title }}</h1>
                <p class="text-gray-600">Pratonton produk yang menunggu kelulusan admin</p>
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span class="font-semibold text-yellow-800">Status: Menunggu Kelulusan</span>
        </div>
        <p class="text-yellow-700 mt-2 text-sm">
            Produk ini akan dipaparkan kepada pengguna lain setelah diluluskan oleh admin. 
            Proses semakan biasanya mengambil masa 1-3 hari bekerja.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Product Images -->
        @if($product->images && is_array($product->images) && count($product->images) > 0)
        <div class="relative h-64 bg-gray-200">
            <img src="{{ asset('storage/' . $product->images[0]) }}" 
                 alt="{{ $product->title }}" 
                 class="w-full h-full object-cover">
        </div>
        @else
        <div class="h-64 bg-gray-200 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif

        <div class="p-6">
            <!-- Product Header -->
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->title }}</h2>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            {{ $product->category }}
                        </span>
                        <span class="text-gray-500 text-sm">Stok: {{ $product->stock_quantity }}</span>
                    </div>
                </div>
                <div class="text-right">
                    @if($product->sale_price)
                        <div class="text-sm text-gray-500 line-through">RM {{ number_format($product->price, 2) }}</div>
                        <div class="text-2xl font-bold text-red-600">RM {{ number_format($product->sale_price, 2) }}</div>
                    @else
                        <div class="text-2xl font-bold text-gray-900">RM {{ number_format($product->price, 2) }}</div>
                    @endif
                    <div class="text-sm text-gray-500">Harga Produk</div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Penerangan Produk</h3>
                <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
            </div>

            <!-- Product Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Kategori</h4>
                    <p class="text-gray-700">{{ $product->category }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Kuantiti Stok</h4>
                    <p class="text-gray-700">{{ $product->stock_quantity }} unit</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Harga Asal</h4>
                    <p class="text-gray-700">RM {{ number_format($product->price, 2) }}</p>
                </div>
                @if($product->sale_price)
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Harga Jualan</h4>
                    <p class="text-red-600 font-semibold">RM {{ number_format($product->sale_price, 2) }}</p>
                </div>
                @endif
            </div>

            <!-- Tags -->
            @if($product->tags && is_array($product->tags) && count($product->tags) > 0)
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 mb-2">Tag</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->tags as $tag)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $tag }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Meta Information -->
            @if(($product->meta_title && !empty($product->meta_title)) || ($product->meta_description && !empty($product->meta_description)))
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 mb-2">Maklumat SEO</h4>
                @if($product->meta_title && !empty($product->meta_title))
                <div class="mb-2">
                    <span class="text-sm font-medium text-gray-600">Meta Title:</span>
                    <p class="text-gray-700 text-sm">{{ $product->meta_title }}</p>
                </div>
                @endif
                @if($product->meta_description && !empty($product->meta_description))
                <div>
                    <span class="text-sm font-medium text-gray-600">Meta Description:</span>
                    <p class="text-gray-700 text-sm">{{ $product->meta_description }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Product Variations -->
            @if($product->variations && $product->variations->count() > 0)
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 mb-2">
                    @if($product->variation_label)
                        {{ $product->variation_label }}
                    @else
                        Varian Produk
                    @endif
                </h4>
                <div class="space-y-4">
                    @foreach($product->variations as $variation)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900 mb-2">{{ $variation->name }}</h5>
                                @if($variation->sku)
                                <p class="text-sm text-gray-600 mb-2">SKU: {{ $variation->sku }}</p>
                                @endif
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-600">Harga:</span>
                                        <span class="text-gray-900">RM {{ number_format($variation->price, 2) }}</span>
                                    </div>
                                    @if($variation->sale_price)
                                    <div>
                                        <span class="font-medium text-gray-600">Harga Jualan:</span>
                                        <span class="text-red-600 font-medium">RM {{ number_format($variation->sale_price, 2) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <span class="font-medium text-gray-600">Stok:</span>
                                        <span class="text-gray-900">{{ $variation->stock_quantity }}</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $variation->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $variation->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                            @if($variation->images && is_array($variation->images) && count($variation->images) > 0)
                            <div class="ml-4">
                                <img src="{{ asset('storage/' . $variation->images[0]) }}" 
                                     alt="{{ $variation->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Submission Info -->
            <div class="border-t border-gray-200 pt-4">
                <div class="text-sm text-gray-500">
                    <p><strong>Tarikh Permohonan:</strong> {{ $product->created_at->format('d F Y, H:i') }}</p>
                    <p><strong>ID Permohonan:</strong> #{{ $product->id }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('dashboard') }}" 
           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
            Kembali ke Dashboard
        </a>
        <div class="flex-1">
            @include('client.partials.cancel-modal', [
                'action' => route('product.cancel', $product->id),
                'message' => 'Adakah anda pasti mahu membatalkan permohonan produk ini? Tindakan ini tidak boleh diundur.',
                'buttonClass' => 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center'
            ])
        </div>
    </div>
</div>
@endsection 