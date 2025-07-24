@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Rejection Banner -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-red-100 text-red-600 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-red-800">Permohonan Ditolak</h1>
                    <p class="text-red-700 mt-1">Permohonan produk anda telah ditolak oleh admin.</p>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Product Header -->
            <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $product->title }}</h2>
                        <p class="text-gray-600 mt-1">{{ $product->category }}</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                            Ditolak
                        </span>
                        <p class="text-sm text-gray-500 mt-1">{{ $product->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason -->
            @if($product->rejection_reason)
            <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Sebab Penolakan</h3>
                <div class="bg-white border border-red-200 rounded-lg p-4">
                    <p class="text-red-700">{{ $product->rejection_reason }}</p>
                </div>
            </div>
            @endif

            <!-- Product Images -->
            @if($product->images && count($product->images) > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Gambar Produk</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                    <div class="aspect-w-1 aspect-h-1">
                        <img src="{{ asset('storage/' . $image) }}" 
                             alt="Product Image" 
                             class="w-full h-32 object-cover rounded-lg">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Product Information -->
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Maklumat Produk</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Penerangan</label>
                                <p class="text-gray-900 mt-1">{{ $product->description }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Harga Asal</label>
                                    <p class="text-gray-900 mt-1 font-semibold">RM {{ number_format($product->price, 2) }}</p>
                                </div>
                                @if($product->sale_price)
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Harga Jualan</label>
                                    <p class="text-green-600 mt-1 font-semibold">RM {{ number_format($product->sale_price, 2) }}</p>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Stok</label>
                                <p class="text-gray-900 mt-1">{{ $product->stock_quantity }} unit</p>
                            </div>
                            @if($product->tags && count($product->tags) > 0)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tag</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach($product->tags as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $tag }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Variations -->
            @if($product->variations && $product->variations->count() > 0)
            <div class="px-6 py-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                    @if($product->variation_label)
                        {{ $product->variation_label }}
                    @else
                        Varian Produk
                    @endif
                </h3>
                <div class="space-y-3">
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

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('rejected.product.edit', $product->id) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Kemaskini & Hantar Semula
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Apa Seterusnya?</h3>
                    <ul class="text-blue-800 space-y-2">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Sila semak sebab penolakan yang diberikan oleh admin</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Perbaiki maklumat yang diperlukan berdasarkan sebab penolakan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Buat permohonan baharu dengan maklumat yang telah diperbaiki</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 