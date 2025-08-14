@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('title', $product->title . ' - Arsenal Shop - MyGooners')
@section('meta_description', $product->description)

@section('meta_tags')
<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $product->title }} - Arsenal Shop">
<meta property="og:description" content="{{ $product->description }}">
@if($product->images && count($product->images) > 0)
<meta property="og:image" content="{{ route('product.image', basename($product->images[0])) }}">
@endif
<meta property="og:type" content="product">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:site_name" content="MyGooners">
<meta property="product:price:amount" content="{{ $product->sale_price ?? $product->price }}">
<meta property="product:price:currency" content="GBP">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->title }} - Arsenal Shop">
<meta name="twitter:description" content="{{ $product->description }}">
@if($product->images && count($product->images) > 0)
<meta name="twitter:image" content="{{ route('product.image', basename($product->images[0])) }}">
@endif

<!-- Additional Meta Tags -->
<meta name="keywords" content="Arsenal, {{ implode(', ', $product->tags) }}, merchandise, shop">
@endsection

@section('content')
<!-- Success Message Display -->
<div id="success-message" class="fixed top-20 right-4 z-50 hidden">
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" id="success-message-text"></p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="hideSuccessMessage()" class="inline-flex text-green-400 hover:text-green-600 focus:outline-none focus:text-green-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Login Required Modal -->
<div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Log Masuk Diperlukan</h3>
            <p class="text-gray-600 text-center mb-6">
                Anda perlu log masuk terlebih dahulu untuk menambah item ke troli. Sila log masuk untuk meneruskan.
            </p>
            <div class="flex space-x-3">
                <button onclick="hideLoginModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button onclick="goToLogin()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Log Masuk
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .variation-option.active {
        border-color: #ef4444 !important;
        border-width: 2px !important;
    }
    
    .variation-option:hover {
        border-color: #ef4444 !important;
        border-width: 2px !important;
    }
</style>


<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
        <nav class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors whitespace-nowrap">Utama</a>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.index') }}" class="hover:text-red-600 transition-colors whitespace-nowrap">Kedai</a>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.index', ['category' => strtolower($product->category)]) }}" class="hover:text-red-600 transition-colors whitespace-nowrap">{{ $product->category }}</a>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium whitespace-nowrap">{{ Str::limit($product->title, 30) }}</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-12 mb-8 lg:mb-16">
        <!-- Product Images -->
        <div>
            
            <!-- Image Carousel -->
            <div class="space-y-4">
                @php
                    // Combine all images into one array
                    $allImages = [];
                    
                    // Add product images first
                    if($product->images && count($product->images) > 0) {
                        foreach($product->images as $index => $image) {
                            $allImages[] = [
                                'url' => $image,
                                'type' => 'product',
                                'title' => $product->title,
                                'variation_id' => null,
                                'variation_name' => null
                            ];
                        }
                    }
                    
                    // Add variant images
                    if($product->hasVariations()) {
                        foreach($product->activeVariations as $variation) {
                            if($variation->images && count($variation->images) > 0) {
                                foreach($variation->images as $image) {
                                    $allImages[] = [
                                        'url' => $image,
                                        'type' => 'variant',
                                        'title' => $variation->name,
                                        'variation_id' => $variation->id,
                                        'variation_name' => $variation->name
                                    ];
                                }
                            }
                        }
                    }
                @endphp
                
                @if(count($allImages) > 0)
                    <!-- Main Carousel -->
                    <div class="relative group">
                        <div class="overflow-hidden rounded-xl">
                            <div id="image-carousel" class="flex transition-transform duration-300 ease-in-out" style="width: {{ count($allImages) * 100 }}%;">
                                @foreach($allImages as $index => $imageData)
                                    <div class="w-full flex-shrink-0" style="width: {{ 100 / count($allImages) }}%;">
                                        <div class="aspect-square rounded-xl overflow-hidden bg-gray-200 relative">
                                            <img src="{{ $imageData['type'] === 'product' ? route('product.image', basename($imageData['url'])) : route('variation.image', basename($imageData['url'])) }}" 
                                                 alt="{{ $imageData['title'] }}" 
                                                 class="w-full h-full object-cover"
                                                 data-index="{{ $index }}"
                                                 data-type="{{ $imageData['type'] }}"
                                                 @if($imageData['variation_id']) data-variation-id="{{ $imageData['variation_id'] }}" @endif
                                                 @if($imageData['variation_name']) data-variation-name="{{ $imageData['variation_name'] }}" @endif>
                
                <!-- Badges -->
                <div class="absolute top-4 left-4 space-y-2">
                                                @if($imageData['type'] === 'product')
                                                    <!-- Base product badges -->
                    @if($product->sale_price)
                        <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                            {{ $product->discount_percentage }}% OFF
                        </span>
                    @endif
                    @if($product->is_featured)
                        <span class="bg-yellow-400 text-gray-900 px-4 py-2 rounded-full text-sm font-bold">
                            UTAMA
                        </span>
                                                    @endif
                                                @elseif($imageData['type'] === 'variant')
                                                    <!-- Variant badges -->
                                                    @php
                                                        $variant = $product->activeVariations->firstWhere('id', $imageData['variation_id']);
                                                        $variantDiscountPercentage = 0;
                                                        if ($variant && $variant->sale_price && $variant->price > $variant->sale_price) {
                                                            $variantDiscountPercentage = round((($variant->price - $variant->sale_price) / $variant->price) * 100);
                                                        }
                                                    @endphp
                                                    @if($variant && $variant->sale_price && $variantDiscountPercentage > 0)
                                                        <span class="bg-red-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                                            {{ $variantDiscountPercentage }}% OFF
                                                        </span>
                                                    @endif
                    @endif
                </div>

                                            @if($imageData['type'] === 'product' && $product->stock_quantity <= 5)
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                            Hanya {{ $product->stock_quantity }} tinggal dalam stok
                        </span>
                    </div>
                @endif
            </div>
                        </div>
                    @endforeach
                </div>
                        </div>
                        
                        <!-- Navigation Arrows -->
                        @if(count($allImages) > 1)
                            <button id="prev-btn" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 shadow-lg transition-all duration-200 opacity-0 group-hover:opacity-100 z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button id="next-btn" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 rounded-full p-3 shadow-lg transition-all duration-200 opacity-0 group-hover:opacity-100 z-10">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
            @endif
                        
                        <!-- Image Counter -->
                        @if(count($allImages) > 1)
                            <div class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-2 rounded-lg text-sm font-medium">
                                <span id="current-image">1</span> / <span id="total-images">{{ count($allImages) }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Share Options - Desktop Only (Above Reviews on Mobile) -->
                <div class="mt-6 hidden lg:block">
                    <h3 class="font-semibold text-gray-900 mb-4">Kongsi Produk Ini</h3>
                    <div class="flex flex-wrap gap-3">
                        <button onclick="shareOnFacebook()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex-1 min-w-[120px]">
                            Facebook
                        </button>
                        <button onclick="shareOnTwitter()" class="!bg-sky-500 !hover:bg-sky-600 !text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex-1 min-w-[120px] border-0" style="background-color: #0ea5e9 !important; color: white !important;">
                            Twitter
                        </button>
                        <button onclick="copyToClipboard()" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex-1 min-w-[120px]">
                            Salin Pautan
                        </button>
                    </div>
                </div>

                <!-- Copy Link Modal -->
                <div id="copy-link-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2 sm:p-4">
                    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-2">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Salin Pautan</h3>
                            <button onclick="closeCopyLinkModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-4 sm:p-6">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pautan Produk:</label>
                                <div class="flex items-center space-x-2">
                                    <input type="text" id="product-link-input" value="{{ request()->url() }}" 
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 bg-gray-50" 
                                           readonly>
                                    <button onclick="copyLinkToClipboard()" 
                                            class="bg-arsenal hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Salin
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Pautan telah disalin ke papan klip</span>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex justify-end p-4 sm:p-6 pt-0 border-t border-gray-200">
                            <button onclick="closeCopyLinkModal()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div>
            <!-- Product Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $product->category }}
                    </span>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{ number_format($product->views_count) }} tontonan
                    </div>
                </div>
                
                <h1 id="product-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
                    @if($selectedVariation)
                        {{ $selectedVariation->name }}
                    @else
                        {{ $product->title }}
                    @endif
                </h1>
                
                <!-- Rating and Reviews -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-base sm:text-lg font-semibold text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                    <span class="text-sm sm:text-base text-gray-500">({{ $product->reviews_count }} ulasan)</span>
                    <a href="#reviews" class="text-red-600 hover:text-red-700 font-medium text-sm sm:text-base">Baca Ulasan</a>
                </div>

                <!-- Price -->
                <div class="flex items-center space-x-4 mb-4 sm:mb-6">
                    <div id="product-price-display">
                        @if($product->hasVariations())
                            @if($selectedVariation)
                                <!-- Show selected variant price and stock -->
                                <div class="mb-2">
                                    <span class="text-sm text-gray-600">Harga Varian Terpilih:</span>
                                </div>
                                @if($selectedVariation->sale_price)
                                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-600">RM{{ number_format($selectedVariation->sale_price, 2) }}</span>
                                    <span class="text-lg sm:text-xl lg:text-2xl text-gray-500 line-through">RM{{ number_format($selectedVariation->price, 2) }}</span>
                                <span class="bg-red-100 text-red-800 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold">
                                        Jimat RM{{ number_format($selectedVariation->price - $selectedVariation->sale_price, 2) }}
                                </span>
                            @else
                                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">RM{{ number_format($selectedVariation->price, 2) }}</span>
                                @endif
                                <div class="mt-2">
                                    @if($selectedVariation->stock_quantity > 0)
                                        <span class="text-green-600 font-medium text-sm sm:text-base">✓ Dalam Stok ({{ $selectedVariation->stock_quantity }} tersedia)</span>
                                    @else
                                        <span class="text-red-600 font-medium text-sm sm:text-base">✗ Kehabisan Stok</span>
                            @endif
                                </div>
                        @else
                                <!-- Show base product price and stock -->
                                <div class="mb-2">
                                    <span class="text-sm text-gray-600">Harga Produk:</span>
                                </div>
                                @if($product->sale_price)
                                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-lg sm:text-xl lg:text-2xl text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                                    <span class="bg-red-100 text-red-800 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold">
                                        Jimat RM{{ number_format($product->price - $product->sale_price, 2) }}
                                    </span>
                                @else
                                    <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                                @endif
                                <div class="mt-2">
                                    @if($product->stock_quantity > 0)
                                        <span class="text-green-600 font-medium text-sm sm:text-base">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                                    @else
                                        <span class="text-red-600 font-medium text-sm sm:text-base">✗ Kehabisan Stok</span>
                                    @endif
                                </div>
                            @endif
                        @else
                            @if($product->sale_price)
                                <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-lg sm:text-xl lg:text-2xl text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                                <span class="bg-red-100 text-red-800 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-bold">
                                    Jimat RM{{ number_format($product->price - $product->sale_price, 2) }}
                                </span>
                            @else
                                <span class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                            @endif
                            <div class="mt-2">
                                @if($product->stock_quantity > 0)
                                    <span class="text-green-600 font-medium text-sm sm:text-base">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                                @else
                                    <span class="text-red-600 font-medium text-sm sm:text-base">✗ Kehabisan Stok</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>



            <!-- Product Description -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2 sm:mb-3">Penerangan</h3>
                <div class="prose text-gray-700 leading-relaxed text-sm sm:text-base">
                    <p class="whitespace-pre-line">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Product Variations -->
            @if($product->hasVariations())
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-3 sm:mb-4">
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-900">{{ $product->variation_label ?: 'Pilihan Varian' }}</h3>
                        @if($selectedVariation)
                            <button 
                                onclick="clearVariationSelection()" 
                                class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 sm:px-3 py-2 rounded-lg transition-colors flex items-center self-start sm:self-auto"
                                title="Kembali ke produk asas">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Padam Varian
                        </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($product->activeVariations as $variation)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-red-500 transition-all duration-200 cursor-pointer variation-option relative {{ $selectedVariantId == $variation->id ? 'border-red-500 active' : '' }}" 
                                    data-variation-id="{{ $variation->id }}"
                                 onclick="toggleVariation({{ $variation->id }})"
                                 title="{{ $selectedVariantId == $variation->id ? 'Klik untuk buang pilihan' : 'Klik untuk pilih varian ini' }}">
                                <!-- Selection indicator (hidden) -->
                                <div class="absolute top-2 right-2 w-6 h-6 border-2 border-gray-300 rounded-full selection-indicator hidden">
                                    <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex items-center justify-center">
                                    <h4 class="font-medium text-gray-900 text-center">{{ $variation->name }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    

                </div>
            @endif

            <!-- Favourite Button -->
            @auth
                <div class="mb-4">
                    <button class="favourite-btn inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:border-red-300 hover:bg-red-50 transition-colors" 
                            data-product-id="{{ $product->id }}"
                            title="Add to favourites">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Tambah ke Kegemaran</span>
                    </button>
                </div>
            @endauth

            <!-- Quantity and Add to Cart -->
            <div class="mb-6 sm:mb-8">
                @php
                    // Check if all variants and base product are out of stock
                    $allVariationsOutOfStock = $product->activeVariations->every(function($variation) {
                        return $variation->stock_quantity <= 0;
                    });
                    $baseProductOutOfStock = $product->stock_quantity <= 0;
                    $everythingOutOfStock = $allVariationsOutOfStock && $baseProductOutOfStock;
                @endphp
                
                <div class="flex items-center space-x-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti</label>
                        <select id="quantity" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent w-full sm:w-auto">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                </div>

                <div class="space-y-3">
                    @if($product->hasVariations())
                        <div class="flex flex-col sm:flex-row gap-3">
                            @if($everythingOutOfStock)
                                <button class="flex-1 bg-gray-300 text-gray-600 py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg cursor-not-allowed" disabled>
                                    Kehabisan Stok
                                </button>
                                <button class="flex-1 border-2 border-gray-300 text-gray-600 py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg cursor-not-allowed" disabled>
                                    Maklumkan Apabila Tersedia
                                </button>
                            @else
                                <button id="add-to-cart-btn" onclick="addToCart()" class="flex-1 bg-arsenal hover:bg-arsenal text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg transition-colors">
                                    Tambah ke Troli
                                </button>
                                <button id="buy-now-btn" onclick="buyNow()" class="flex-1 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg transition-colors">
                                    Beli Sekarang
                                </button>
                            @endif
                        </div>
                    @else
                        @if($product->stock_quantity > 0)
                            <button onclick="addToCartSimple()" class="w-full bg-arsenal hover:bg-arsenal text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg transition-colors">
                                Tambah ke Troli
                            </button>
                            <button onclick="buyNowSimple()" class="w-full border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg transition-colors">
                                Beli Sekarang
                            </button>
                        @else
                            <button class="w-full bg-gray-300 text-gray-600 py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg cursor-not-allowed" disabled>
                                Kehabisan Stok
                            </button>
                            <button class="w-full border-2 border-gray-300 text-gray-600 py-3 sm:py-4 px-4 sm:px-6 rounded-lg font-bold text-base sm:text-lg cursor-not-allowed" disabled>
                                Maklumkan Apabila Tersedia
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Product Features -->
            <div class="border-t border-gray-200 pt-4 sm:pt-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Produk MyGooners Rasmi
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                        </svg>
                        Penghantaran Percuma Seluruh Malaysia
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414-1.414l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Pulangan 30 Hari
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        Sokongan Pelanggan
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($product->tags)
                <div class="mt-4 sm:mt-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->tags as $tag)
                            <span class="bg-gray-100 text-gray-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif


        </div>
    </div>

    <!-- Share Options - All Screen Sizes (Above Reviews on Mobile) -->
    <div class="mt-8 lg:hidden">
        <h3 class="font-semibold text-gray-900 mb-4">Kongsi Produk Ini</h3>
        <div class="flex flex-wrap gap-3">
            <button onclick="shareOnFacebook()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex-1 min-w-[120px]">
                Facebook
            </button>
            <button onclick="shareOnTwitter()" class="!bg-sky-500 !hover:bg-sky-600 !text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex-1 min-w-[120px] border-0" style="background-color: #0ea5e9 !important; color: white !important;">
                Twitter
            </button>
            <button onclick="copyToClipboard()" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex-1 min-w-[120px]">
                Salin Pautan
            </button>
        </div>
    </div>

    <!-- Reviews Section -->
    <div id="reviews" class="mt-12 lg:mt-8 mb-8 lg:mb-16">
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-0 mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Ulasan Pelanggan</h2>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                    <span class="text-sm sm:text-base text-gray-500">({{ $product->reviews_count }} ulasan)</span>
                </div>
            </div>

            <div class="space-y-6 sm:space-y-8">
                @if($product->reviews->count() > 0)
                    @foreach($product->reviews->take(3) as $review)
                    <div class="border-b border-gray-200 pb-6 sm:pb-8 last:border-b-0 last:pb-0">
                            <div class="flex items-start space-x-4 sm:space-x-5">
                                <!-- User Avatar -->
                                                                    <div class="flex-shrink-0">
                                        <img src="{{ $review->reviewer_avatar }}" 
                                             alt="{{ $review->reviewer_name }}" 
                                             class="w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-gray-100 shadow-sm object-cover">
                                    </div>
                                
                                <!-- Review Content -->
                            <div class="flex-1 min-w-0">
                                    <!-- Review Header -->
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-base sm:text-lg mb-1">{{ $review->reviewer_name }}</h4>
                                            <div class="flex items-center space-x-3 mb-2">
                                                <!-- Rating Stars -->
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                 fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $review->rating }}/5</span>
                                    </div>
                                                

                                </div>
                                        </div>
                                        
                                        <!-- Review Date -->
                                        <div class="text-right">
                                    <span class="text-xs sm:text-sm text-gray-500">{{ malayDiffForHumans($review->created_at) }}</span>
                                </div>
                                    </div>
                                    
                                    <!-- Review Comment -->
                                    <div class="bg-gray-50 rounded-lg p-4 sm:p-5">
                                        <p class="text-gray-800 leading-relaxed text-sm sm:text-base">{{ $review->comment }}</p>
                                    </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tiada ulasan lagi</h3>
                        <p class="text-gray-600 text-sm sm:text-base">Jadilah yang pertama untuk berkongsi pengalaman anda dengan produk ini!</p>
                    </div>
                @endif
                
                <!-- Show "View All Reviews" button if there are more than 3 reviews -->
                @if($product->reviews->count() > 3)
                    <div class="text-center pt-4">
                        <button onclick="openReviewsModal()" 
                                class="inline-flex items-center px-6 py-3 bg-arsenal hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Semua Ulasan ({{ $product->reviews->count() }})
                        </button>
                    </div>
                @endif
            </div>

            <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                @auth
                    @php
                        $userReview = $product->reviews()->where('user_id', auth()->id())->first();
                    @endphp
                    
                    @if($userReview)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sm:p-5">
                            <div class="flex items-start space-x-4">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0">
                                    @php $profileImg = trim(auth()->user()->profile_image ?? ''); @endphp
                                    @if($profileImg !== '')
                                        @if(Str::startsWith($profileImg, 'http'))
                                            <img src="{{ $profileImg }}" alt="Anda" 
                                                 class="w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-blue-200 shadow-sm object-cover">
                                        @else
                                            <img src="{{ asset('storage/' . $profileImg) }}" alt="Anda" 
                                                 class="w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-blue-200 shadow-sm object-cover">
                                        @endif
                                    @else
                                        <img src="{{ asset('images/profile-image-default.png') }}" alt="Anda" 
                                             class="w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 border-blue-200 shadow-sm object-cover">
                                    @endif
                                </div>
                                
                                <!-- Review Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                                        <div class="flex-1">
                                                                                         <h4 class="font-semibold text-blue-900 text-base sm:text-lg mb-1">Anda</h4>
                                            <p class="text-blue-700 text-sm mb-2">Ulasan anda untuk produk ini</p>
                                            <div class="flex items-center space-x-3 mb-2">
                                                <!-- Rating Stars -->
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                             fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                    <span class="ml-2 text-sm font-medium text-blue-700">{{ $userReview->rating }}/5</span>
                                                </div>
                                                
                                                <!-- Review Date -->
                                                <span class="text-xs sm:text-sm text-blue-600">{{ malayDiffForHumans($userReview->created_at) }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex space-x-3">
                                            <a href="{{ route('reviews.edit', [$product, $userReview]) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-blue-300 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Sunting
                                            </a>
                                            <button type="button" 
                                                    onclick="openDeleteReviewModal({{ $userReview->id }})" 
                                                    class="inline-flex items-center px-3 py-2 border border-red-300 text-red-700 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Padam
                </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Review Comment -->
                                    <div class="bg-white rounded-lg p-4 border border-blue-100">
                                        <p class="text-gray-800 leading-relaxed text-sm sm:text-base">{{ $userReview->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('reviews.create', $product) }}" 
                           class="inline-block bg-arsenal hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            Tulis Ulasan
                        </a>
                    @endif
                @else
                    <div class="text-center">
                        <p class="text-gray-600 mb-3">Log masuk untuk menulis ulasan</p>
                        <a href="{{ route('login') }}" 
                           class="inline-block bg-arsenal hover:bg-red-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            Log Masuk
                        </a>
                        <p class="text-sm text-gray-500 mt-2">Atau <a href="{{ route('register') }}" class="text-arsenal hover:text-red-700 underline">daftar akaun baru</a></p>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div>
        <div class="mb-6 sm:mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Anda Mungkin Suka</h2>
            <p class="text-gray-600 text-sm sm:text-base">Lebih banyak barangan Arsenal yang mungkin menarik minat anda</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                    <div class="relative h-64">
                        @if($relatedProduct->images && is_array($relatedProduct->images) && count($relatedProduct->images) > 0)
                            <img src="{{ route('product.image', basename($relatedProduct->images[0])) }}" 
                                 alt="{{ $relatedProduct->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($relatedProduct->sale_price)
                            <div class="absolute top-3 left-3">
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    SALE
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 sm:p-6">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium mb-2 inline-block">
                            {{ $relatedProduct->category }}
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2">
                            <a href="{{ route('shop.show', $relatedProduct->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $relatedProduct->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">{{ Str::limit($relatedProduct->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($relatedProduct->sale_price)
                                    <span class="text-lg sm:text-xl font-bold text-red-600">RM{{ number_format($relatedProduct->sale_price, 2) }}</span>
                                    <span class="text-xs sm:text-sm text-gray-500 line-through">RM{{ number_format($relatedProduct->price, 2) }}</span>
                                @else
                                    <span class="text-lg sm:text-xl font-bold text-gray-900">RM{{ number_format($relatedProduct->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center text-xs sm:text-sm">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span>{{ number_format($relatedProduct->average_rating, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Delete Review Modal -->
<div id="delete-review-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Padam Ulasan</h3>
            <button onclick="closeDeleteReviewModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-base font-medium text-gray-900 mb-2">Adakah anda pasti?</h4>
                    <p class="text-sm text-gray-600">
                        Tindakan ini tidak boleh dibatalkan. Ulasan anda akan dipadamkan secara kekal dan tidak dapat dipulihkan.
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200">
            <button onclick="closeDeleteReviewModal()" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <form id="delete-review-form" action="" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Padam Ulasan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delete Review Modal Functions
function openDeleteReviewModal(reviewId) {
    const modal = document.getElementById('delete-review-modal');
    const form = document.getElementById('delete-review-form');
    
    // Set the form action to the correct delete route
    form.action = `/shop/{{ $product->slug }}/review/${reviewId}`;
    
    // Show the modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteReviewModal() {
    const modal = document.getElementById('delete-review-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('delete-review-modal');
    if (e.target === modal) {
        closeDeleteReviewModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('delete-review-modal');
        if (!modal.classList.contains('hidden')) {
            closeDeleteReviewModal();
        }
    }
});

// Global variables for variation selection
let selectedVariation = null;
let productVariations = @json($product->activeVariationsWithComputedProperties);
let isClearingSelection = false; // Flag to prevent selectVariation during clearing

// Debug: Log product variations
console.log('Product variations loaded:', productVariations);

// Carousel variables
let currentImageIndex = 0;
let totalImages = {{ count($allImages ?? []) }};
let isDragging = false;
let startX = 0;
let currentX = 0;

// Carousel functions
function goToImage(index) {
    if (index < 0 || index >= totalImages) return;
    
    currentImageIndex = index;
    updateCarousel();
    updateCounter();
    handleImageSelection(index);
}

function updateCarousel() {
    const carousel = document.getElementById('image-carousel');
    if (carousel) {
        const translateX = -(currentImageIndex * (100 / totalImages));
        carousel.style.transform = `translateX(${translateX}%)`;
    }
}

function updateCounter() {
    // Update counter
    const currentImageSpan = document.getElementById('current-image');
    if (currentImageSpan) {
        currentImageSpan.textContent = currentImageIndex + 1;
    }
}

function handleImageSelection(index) {
    const carousel = document.getElementById('image-carousel');
    if (!carousel) return;
    
    const images = carousel.querySelectorAll('img');
    if (images[index]) {
        const img = images[index];
        const variationId = img.dataset.variationId;
        
        // Only clear and select if we're not already on a variant page
        const urlParams = new URLSearchParams(window.location.search);
        const currentVariant = urlParams.get('variant');
        
        if (!currentVariant) {
            // Clear ALL variant selections first
            clearVariantSelection();
            
            // If this is a variation image, then select that specific variation
            if (variationId) {
                console.log('Selecting variation from carousel:', variationId);
                selectVariation(parseInt(variationId));
            } else {
                console.log('No variation ID from carousel, keeping base product selection');
            }
        } else {
            console.log('Already on variant page, skipping carousel selection');
        }
    }
}

function nextImage() {
    if (currentImageIndex < totalImages - 1) {
        goToImage(currentImageIndex + 1);
    }
}

function prevImage() {
    if (currentImageIndex > 0) {
        goToImage(currentImageIndex - 1);
    }
}

// Touch/swipe functionality
function initCarouselTouch() {
    const carousel = document.getElementById('image-carousel');
    if (!carousel) return;
    
    carousel.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].clientX;
        currentX = startX;
    });
    
    carousel.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        e.preventDefault();
        currentX = e.touches[0].clientX;
        const diff = currentX - startX;
        const translateX = -(currentImageIndex * (100 / totalImages)) + (diff / carousel.offsetWidth * (100 / totalImages));
        carousel.style.transform = `translateX(${translateX}%)`;
    });
    
    carousel.addEventListener('touchend', () => {
        if (!isDragging) return;
        isDragging = false;
        
        const diff = currentX - startX;
        const threshold = carousel.offsetWidth * 0.3;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0 && currentImageIndex > 0) {
                prevImage();
            } else if (diff < 0 && currentImageIndex < totalImages - 1) {
                nextImage();
            } else {
                updateCarousel(); // Reset to current position
            }
        } else {
            updateCarousel(); // Reset to current position
        }
    });
}

// Legacy function for backward compatibility
function changeMainImage(imageSrc, thumbnail, variationId = null) {
    console.log('changeMainImage called with:', { imageSrc, variationId });
    
    document.getElementById('mainImage').src = imageSrc;
    
    // Clear ALL thumbnails first (both product and variant)
    document.querySelectorAll('.product-thumbnail, .variation-thumbnail').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-red-500');
    });
    
    // Add active state ONLY to the clicked thumbnail
    thumbnail.classList.add('ring-2', 'ring-red-500');
    
    // Clear ALL variant selections first
    clearVariantSelection();
    
    // If this is a variation image, then select that specific variation
    if (variationId) {
        console.log('Selecting variation:', variationId);
        selectVariation(variationId);
    } else {
        console.log('No variation ID, keeping base product selection');
    }
}

function toggleVariation(variationId) {
    // Check if we're currently on a variant page
    const urlParams = new URLSearchParams(window.location.search);
    const currentVariant = urlParams.get('variant');
    
    console.log('Toggle clicked for variant:', variationId);
    console.log('Current variant in URL:', currentVariant);
    console.log('Current URL:', window.location.href);
    
    // Find the clicked variation to get its name
    const clickedVariation = productVariations.find(v => v.id === variationId);
    if (!clickedVariation) {
        console.log('Variation not found:', variationId);
        return;
    }
    
    const clickedVariantName = clickedVariation.name;
    console.log('Clicked variant name:', clickedVariantName);
    
    if (currentVariant) {
        // We're on a variant page
        const decodedCurrentVariant = decodeURIComponent(currentVariant);
        console.log('Decoded current variant:', decodedCurrentVariant);
        
        if (decodedCurrentVariant === clickedVariantName) {
            // Same variant clicked, just update the UI without reloading
            console.log('Same variant clicked, updating UI only');
            selectVariation(variationId);
        } else {
            // Different variant clicked, switch to that variant
            console.log('Different variant clicked, switching to:', clickedVariantName);
            refreshPageWithVariant(variationId);
        }
    } else {
        // We're on base page, select the variant
        console.log('Currently on base page, selecting variant:', variationId);
        refreshPageWithVariant(variationId);
    }
}

function refreshPageWithVariant(variationId) {
    const currentUrl = new URL(window.location);
    
    if (variationId) {
        // Find the variation name by ID
        const variation = productVariations.find(v => v.id === variationId);
        if (variation) {
            // Use the raw variant name - URLSearchParams will handle encoding properly
            currentUrl.searchParams.set('variant', variation.name);
            
            // Debug log
            console.log('Setting variant:', variation.name);
        }
    } else {
        currentUrl.searchParams.delete('variant');
        console.log('Removing variant parameter');
    }
    
    console.log('Redirecting to:', currentUrl.toString());
    window.location.href = currentUrl.toString();
}

function selectVariation(variationId) {
    console.log('selectVariation called with:', variationId);
    
    // Check if we're currently clearing selections
    if (isClearingSelection) {
        console.log('selectVariation blocked - currently clearing selections');
        return;
    }
    
    // Remove active state from all variation options
    document.querySelectorAll('.variation-option').forEach(option => {
        console.log('Clearing option in selectVariation:', option);
        option.classList.remove('border-red-500', 'bg-red-50', 'shadow-lg', 'scale-105', 'active');
        option.classList.add('border-gray-200');
        
        // Also remove any inline styles
        option.style.borderColor = '';
        option.style.borderWidth = '';
        option.style.backgroundColor = '';
        
        // Hide selection indicator for all options
        const indicator = option.querySelector('.selection-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
            indicator.classList.remove('bg-red-500', 'border-red-500');
        }
    });
    
    // Add active state to selected variation (only variant boxes, not thumbnails)
    const selectedButton = document.querySelector(`.variation-option[data-variation-id="${variationId}"]`);
    if (selectedButton) {
        console.log('Found selected variant box:', selectedButton);
        selectedButton.classList.remove('border-gray-200');
        selectedButton.classList.add('border-red-500', 'active');
        
        // Force the border to be visible by adding !important equivalent
        selectedButton.style.borderColor = '#ef4444'; // red-500 color
        selectedButton.style.borderWidth = '2px';
    } else {
        console.log('Selected variant box not found for variation ID:', variationId);
    }
    
    // Find the selected variation
    const variation = productVariations.find(v => v.id === variationId);
    if (variation) {
        selectedVariation = variation;
        updateProductPrice(variation);
        enableAddToCart();
        
        // Find and display the variant image in the carousel
        const variantImage = document.querySelector(`img[data-variation-id="${variationId}"]`);
        if (variantImage) {
            const imageIndex = parseInt(variantImage.getAttribute('data-index'));
            console.log('Found variant image at index:', imageIndex);
            goToImage(imageIndex);
        }
    }
}



function clearVariantSelection() {
    console.log('Clearing variant selection...');
    isClearingSelection = true; // Set flag to prevent selectVariation
    
    // Remove active state from all variation options
    document.querySelectorAll('.variation-option').forEach(option => {
        console.log('Clearing option:', option);
        
        // Remove all possible active classes
        option.classList.remove('border-red-500', 'bg-red-50', 'shadow-lg', 'scale-105', 'active');
        option.classList.add('border-gray-200');
        
        // Also remove any inline styles that might override CSS
        option.style.borderColor = '';
        option.style.borderWidth = '';
        option.style.backgroundColor = '';
        
        // Hide selection indicator for all options
        const indicator = option.querySelector('.selection-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
            indicator.classList.remove('bg-red-500', 'border-red-500');
        }
    });
    
    // Reset product price and title to base product
    resetProductPrice();
    enableAddToCart();
    
    // Clear selected variation only if we're not in the process of setting it
    if (!window.location.search.includes('variant=')) {
        selectedVariation = null;
    }
    
    // Reset flag after a short delay to allow any pending operations to complete
    setTimeout(() => {
        isClearingSelection = false;
    }, 100);
    
    console.log('Variant selection cleared');
}

function clearVariationSelection() {
    // Force redirect to base product URL
    console.log('Clearing variation selection - returning to base product');
    window.location.replace(window.location.pathname);
}

function updateProductPrice(variation) {
    console.log('updateProductPrice called with variation:', variation);
    
    const priceDisplay = document.getElementById('product-price-display');
    const productTitle = document.getElementById('product-title');
    const stockStatus = document.getElementById('stock-status');
    
    console.log('Found elements:', {
        priceDisplay: priceDisplay,
        productTitle: productTitle,
        stockStatus: stockStatus
    });
    
    if (!priceDisplay || !productTitle) {
        console.error('Required elements not found!');
        return;
    }
    
    const finalPrice = variation.final_price;
    const originalPrice = variation.original_price;
    
    console.log('Price data:', {
        finalPrice: finalPrice,
        originalPrice: originalPrice,
        salePrice: variation.sale_price,
        stockQuantity: variation.stock_quantity
    });
    
    // Update product title to show variant name
    productTitle.textContent = variation.name;
    console.log('Updated product title to:', variation.name);
    
    // Update stock status
    if (stockStatus) {
        if (variation.stock_quantity > 0) {
            stockStatus.textContent = `✓ Dalam Stok (${variation.stock_quantity} tersedia)`;
            stockStatus.className = 'text-green-600 font-medium';
        } else {
            stockStatus.textContent = '✗ Kehabisan Stok';
            stockStatus.className = 'text-red-600 font-medium';
        }
        console.log('Updated stock status to:', stockStatus.textContent);
    }
    
    if (variation.sale_price && variation.sale_price < originalPrice) {
        const savings = originalPrice - finalPrice;
        const newHTML = `
            <div class="mb-2">
                <span class="text-sm text-gray-600">Harga Varian Terpilih:</span>
            </div>
            <span class="text-4xl font-bold text-red-600">RM${parseFloat(finalPrice).toFixed(2)}</span>
            <span class="text-2xl text-gray-500 line-through">RM${parseFloat(originalPrice).toFixed(2)}</span>
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                Jimat RM${parseFloat(savings).toFixed(2)}
            </span>
            <div class="mt-2">
                ${variation.stock_quantity > 0 ? 
                    `<span class="text-green-600 font-medium">✓ Dalam Stok (${variation.stock_quantity} tersedia)</span>` :
                    `<span class="text-red-600 font-medium">✗ Kehabisan Stok</span>`
                }
            </div>
        `;
        priceDisplay.innerHTML = newHTML;
        console.log('Updated price display with sale price HTML');
    } else {
        const newHTML = `
            <div class="mb-2">
                <span class="text-sm text-gray-600">Harga Varian Terpilih:</span>
            </div>
            <span class="text-4xl font-bold text-gray-900">RM${parseFloat(finalPrice).toFixed(2)}</span>
            <div class="mt-2">
                ${variation.stock_quantity > 0 ? 
                    `<span class="text-green-600 font-medium">✓ Dalam Stok (${variation.stock_quantity} tersedia)</span>` :
                    `<span class="text-red-600 font-medium">✗ Kehabisan Stok</span>`
                }
            </div>
        `;
        priceDisplay.innerHTML = newHTML;
        console.log('Updated price display with regular price HTML');
    }
    
    console.log('Price display update completed');
}

function resetProductPrice() {
    const priceDisplay = document.getElementById('product-price-display');
    const productTitle = document.getElementById('product-title');
    const stockStatus = document.getElementById('stock-status');
    
    // Reset product title to original product name
    productTitle.textContent = '{{ $product->title }}';
    
    // Reset stock status
    if (stockStatus) {
        stockStatus.textContent = 'Pilih varian atau beli produk asas';
        stockStatus.className = 'text-gray-600';
    }
    @if($product->hasVariations())
        @if($product->sale_price)
            priceDisplay.innerHTML = `
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Harga Produk:</span>
                </div>
                <span class="text-4xl font-bold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                <span class="text-2xl text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                    Jimat RM{{ number_format($product->price - $product->sale_price, 2) }}
                </span>
                <div class="mt-2">
                    @if($product->stock_quantity > 0)
                        <span class="text-green-600 font-medium">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                    @else
                        <span class="text-red-600 font-medium">✗ Kehabisan Stok</span>
                    @endif
                </div>
            `;
        @else
            priceDisplay.innerHTML = `
                <div class="mb-2">
                    <span class="text-sm text-gray-600">Harga Produk:</span>
                </div>
                <span class="text-4xl font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                <div class="mt-2">
                    @if($product->stock_quantity > 0)
                        <span class="text-green-600 font-medium">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                    @else
                        <span class="text-red-600 font-medium">✗ Kehabisan Stok</span>
                    @endif
                </div>
            `;
        @endif
    @else
        @if($product->sale_price)
            priceDisplay.innerHTML = `
                <span class="text-4xl font-bold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                <span class="text-2xl text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                    Jimat RM{{ number_format($product->price - $product->sale_price, 2) }}
                </span>
                <div class="mt-2">
                    @if($product->stock_quantity > 0)
                        <span class="text-green-600 font-medium">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                    @else
                        <span class="text-red-600 font-medium">✗ Kehabisan Stok</span>
                    @endif
                </div>
            `;
        @else
            priceDisplay.innerHTML = `
                <span class="text-4xl font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                <div class="mt-2">
                    @if($product->stock_quantity > 0)
                        <span class="text-green-600 font-medium">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                    @else
                        <span class="text-red-600 font-medium">✗ Kehabisan Stok</span>
                    @endif
                </div>
            `;
        @endif
    @endif
}



function enableAddToCart() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    
    if (selectedVariation) {
        // Variation selected
        if (selectedVariation.stock_quantity > 0) {
            addToCartBtn.textContent = 'Tambah ke Troli';
            addToCartBtn.className = 'flex-1 bg-arsenal hover:bg-arsenal text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
            addToCartBtn.disabled = false;
            
            buyNowBtn.textContent = 'Beli Sekarang';
            buyNowBtn.className = 'flex-1 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
            buyNowBtn.disabled = false;
        } else {
            addToCartBtn.textContent = 'Kehabisan Stok';
            addToCartBtn.className = 'flex-1 bg-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg cursor-not-allowed';
            addToCartBtn.disabled = true;
            
            buyNowBtn.textContent = 'Kehabisan Stok';
            buyNowBtn.className = 'flex-1 border-2 border-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg cursor-not-allowed';
            buyNowBtn.disabled = true;
        }
    } else {
        // No variation selected - check if base product has stock
        const baseProductStock = {{ $product->stock_quantity }};
        if (baseProductStock > 0) {
            addToCartBtn.textContent = 'Tambah ke Troli';
            addToCartBtn.className = 'flex-1 bg-arsenal hover:bg-arsenal text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
            addToCartBtn.disabled = false;
            
            buyNowBtn.textContent = 'Beli Sekarang';
            buyNowBtn.className = 'flex-1 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
            buyNowBtn.disabled = false;
        } else {
            addToCartBtn.textContent = 'Kehabisan Stok';
            addToCartBtn.className = 'flex-1 bg-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg cursor-not-allowed';
            addToCartBtn.disabled = true;
            
            buyNowBtn.textContent = 'Kehabisan Stok';
            buyNowBtn.className = 'flex-1 border-2 border-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg cursor-not-allowed';
            buyNowBtn.disabled = true;
        }
    }
}

function disableAddToCart() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const buyNowBtn = document.getElementById('buy-now-btn');
    
    addToCartBtn.textContent = 'Tambah ke Troli';
    addToCartBtn.className = 'flex-1 bg-arsenal hover:bg-arsenal text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
    addToCartBtn.disabled = false;
    
    buyNowBtn.textContent = 'Beli Sekarang';
    buyNowBtn.className = 'flex-1 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors';
    buyNowBtn.disabled = false;
}

function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $product->title }} - Arsenal Shop');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out this Arsenal merchandise: {{ $product->title }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function copyToClipboard() {
    const modal = document.getElementById('copy-link-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCopyLinkModal() {
    const modal = document.getElementById('copy-link-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function copyLinkToClipboard() {
    const linkInput = document.getElementById('product-link-input');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(linkInput.value).then(function() {
        // Show success message
        const successMessage = document.querySelector('#copy-link-modal .text-gray-600');
        successMessage.innerHTML = `
            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-green-600 font-medium">Pautan berjaya disalin!</span>
        `;
        
        // Reset message after 3 seconds
        setTimeout(() => {
            successMessage.innerHTML = `
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span>Pautan telah disalin ke papan klip</span>
            `;
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Gagal menyalin pautan. Sila cuba lagi.');
    });
}

function addToCart() {
    // Check if user is logged in
    @auth
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};
        const variationId = selectedVariation ? selectedVariation.id : null;
        
        // Debug logging
        console.log('addToCart called with:', {
            quantity: quantity,
            productId: productId,
            variationId: variationId,
            selectedVariation: selectedVariation
        });
        console.log('All product variations:', productVariations);
        console.log('Current URL:', window.location.href);
        console.log('URL search params:', window.location.search);
        console.log('selectedVariation details:', selectedVariation ? {
            id: selectedVariation.id,
            name: selectedVariation.name,
            price: selectedVariation.price
        } : 'null');
        
        // Validate stock
        const stockQuantity = selectedVariation ? selectedVariation.stock_quantity : {{ $product->stock_quantity }};
        if (stockQuantity < quantity) {
            alert('Stok tidak mencukupi');
            return;
        }
        
        // Show loading state
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const originalText = addToCartBtn.textContent;
        addToCartBtn.textContent = 'Menambah...';
        addToCartBtn.disabled = true;
        
        const requestData = {
            product_id: productId,
            variation_id: variationId,
            quantity: quantity
        };
        
        console.log('Sending request data:', requestData);
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response received:', data);
            if (data.success) {
                // Store success message in session storage
                sessionStorage.setItem('cartSuccessMessage', data.message);
                // Refresh the page
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat semasa menambah ke troli');
        })
        .finally(() => {
            // Reset button state
            addToCartBtn.textContent = originalText;
            addToCartBtn.disabled = false;
        });
    @else
        // User is not logged in, show login modal
        showLoginModal();
    @endauth
}

function addToCartSimple() {
    // Check if user is logged in
    @auth
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};
        
        // Debug logging
        console.log('addToCartSimple called with:', {
            quantity: quantity,
            productId: productId
        });
        
        // Validate stock
        const stockQuantity = {{ $product->stock_quantity }};
        if (stockQuantity < quantity) {
            alert('Stok tidak mencukupi');
            return;
        }
        
        // Show loading state
        const addToCartBtn = event.target; // Get the button that was clicked
        const originalText = addToCartBtn.textContent;
        addToCartBtn.textContent = 'Menambah...';
        addToCartBtn.disabled = true;
        
        const requestData = {
            product_id: productId,
            variation_id: null, // No variation for simple products
            quantity: quantity
        };
        
        console.log('Sending request data:', requestData);
        
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response received:', data);
            if (data.success) {
                // Store success message in session storage
                sessionStorage.setItem('cartSuccessMessage', data.message);
                // Refresh the page
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat semasa menambah ke troli');
        })
        .finally(() => {
            // Reset button state
            addToCartBtn.textContent = originalText;
            addToCartBtn.disabled = false;
        });
    @else
        // User is not logged in, show login modal
        showLoginModal();
    @endauth
}

function showLoginModal() {
    const modal = document.getElementById('login-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideLoginModal() {
    const modal = document.getElementById('login-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function goToLogin() {
    window.location.href = '{{ route("login") }}';
}

function buyNow() {
    // Check if user is logged in
    @auth
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};
        const variationId = selectedVariation ? selectedVariation.id : null;
        
        // Validate stock
        const stockQuantity = selectedVariation ? selectedVariation.stock_quantity : {{ $product->stock_quantity }};
        if (stockQuantity < quantity) {
            alert('Stok tidak mencukupi');
            return;
        }
        
        // Redirect directly to direct checkout page
        window.location.href = '{{ route("direct-checkout.index") }}?product_id={{ $product->id }}&quantity=' + quantity + (variationId ? '&variation_id=' + variationId : '');
    @else
        // User is not logged in, show login modal
        showLoginModal();
    @endauth
}

function buyNowSimple() {
    // Check if user is logged in
    @auth
        const quantity = parseInt(document.getElementById('quantity').value);
        const productId = {{ $product->id }};
        
        // Validate stock
        const stockQuantity = {{ $product->stock_quantity }};
        if (stockQuantity < quantity) {
            alert('Stok tidak mencukupi');
            return;
        }
        
        // Redirect directly to direct checkout page
        window.location.href = '{{ route("direct-checkout.index") }}?product_id={{ $product->id }}&quantity=' + quantity;
    @else
        // User is not logged in, show login modal
        showLoginModal();
    @endauth
}

// Initialize page with pre-selected variant
document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel
    if (totalImages > 1) {
        initCarouselTouch();
        
        // Set up navigation buttons
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', prevImage);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', nextImage);
        }
        
        // Add hover effect to show navigation arrows
        const carouselContainer = document.querySelector('.relative');
        if (carouselContainer) {
            carouselContainer.classList.add('group');
        }
    }
    
    // Auto-select first product thumbnail (legacy support)
    const firstProductThumbnail = document.querySelector('.product-thumbnail');
    if (firstProductThumbnail) {
        firstProductThumbnail.classList.add('ring-2', 'ring-red-500');
    }
    
    // Initialize variant from backend data (takes precedence)
    @if($selectedVariation)
        console.log('Found selected variation from backend:', @json($selectedVariation));
        const backendVariant = @json($selectedVariation);
        const variant = productVariations.find(v => v.id === backendVariant.id);
        if (variant) {
            console.log('Found matching variant in productVariations:', variant);
            
            // Set the selected variation
            selectedVariation = variant;
            
            // Use setTimeout to ensure DOM is fully ready before updating price
            setTimeout(() => {
                console.log('Calling updateProductPrice with delay...');
                updateProductPrice(variant);
                enableAddToCart();
            }, 200); // Increased delay to ensure DOM is ready
            
            // Use setTimeout to ensure DOM is fully ready
            setTimeout(() => {
                // Debug: Log all variation options
                console.log('All variation options:', document.querySelectorAll('.variation-option'));
                document.querySelectorAll('.variation-option').forEach((option, index) => {
                    console.log(`Option ${index}:`, option, 'data-variation-id:', option.getAttribute('data-variation-id'));
                });
                
                // Select the variant box with active styling
                const variantBox = document.querySelector(`.variation-option[data-variation-id="${variant.id}"]`);
                console.log('Looking for variant box with data-variation-id:', variant.id);
                console.log('Found variant box:', variantBox);
                
                if (variantBox) {
                    console.log('Applying styling to variant box');
                    console.log('Before styling - classes:', variantBox.className);
                    console.log('Before styling - border color:', variantBox.style.borderColor);
                    
                    variantBox.classList.remove('border-gray-200');
                    variantBox.classList.add('border-red-500', 'active');
                    variantBox.style.borderColor = '#ef4444'; // red-500 color
                    variantBox.style.borderWidth = '2px';
                    
                    console.log('After styling - classes:', variantBox.className);
                    console.log('After styling - border color:', variantBox.style.borderColor);
                    console.log('After styling - border width:', variantBox.style.borderWidth);
                } else {
                    console.log('Variant box not found!');
                }
            }, 100); // Small delay to ensure DOM is ready
            
            // Find and display the variant image in the carousel
            const variantImage = document.querySelector(`img[data-variation-id="${variant.id}"]`);
            if (variantImage) {
                const imageIndex = parseInt(variantImage.getAttribute('data-index'));
                console.log('Found variant image at index:', imageIndex);
                goToImage(imageIndex);
            }
        } else {
            console.log('Variant not found in productVariations array');
        }
    @else
        // Fallback: Check for variant parameter in URL
        const urlParams = new URLSearchParams(window.location.search);
        const variantParam = urlParams.get('variant');
        
        if (variantParam) {
            const decodedVariantName = decodeURIComponent(variantParam);
            console.log('Found variant parameter in URL:', decodedVariantName);
            console.log('Raw variant parameter:', variantParam);
            console.log('Available product variations:', productVariations.map(v => ({ id: v.id, name: v.name })));
            
            // Find the variant by name
            const variant = productVariations.find(v => v.name === decodedVariantName);
            console.log('Variant search result:', variant);
            console.log('Exact name comparison test:');
            productVariations.forEach(v => {
                console.log(`  "${v.name}" === "${decodedVariantName}" = ${v.name === decodedVariantName}`);
            });
            if (variant) {
                console.log('Found variant from URL:', variant);
                
                // Set the selected variation
                selectedVariation = variant;
                
                // Use setTimeout to ensure DOM is fully ready before updating price
                setTimeout(() => {
                    console.log('Calling updateProductPrice with delay (URL fallback)...');
                    updateProductPrice(variant);
                    enableAddToCart();
                }, 200); // Increased delay to ensure DOM is ready
                
                // Use setTimeout to ensure DOM is fully ready
                setTimeout(() => {
                    // Select the variant box with active styling
                    const variantBox = document.querySelector(`.variation-option[data-variation-id="${variant.id}"]`);
                    if (variantBox) {
                        variantBox.classList.remove('border-gray-200');
                        variantBox.classList.add('border-red-500', 'active');
                        variantBox.style.borderColor = '#ef4444';
                        variantBox.style.borderWidth = '2px';
                    }
                }, 100);
                
                // Find and display the variant image in the carousel
                const variantImage = document.querySelector(`img[data-variation-id="${variant.id}"]`);
                if (variantImage) {
                    const imageIndex = parseInt(variantImage.getAttribute('data-index'));
                    goToImage(imageIndex);
                }
            }
        }
    @endif
    
    // Add click event listeners to update tooltips
    document.querySelectorAll('.variation-option').forEach(option => {
        option.addEventListener('click', function() {
            // Update tooltip after a short delay to reflect the new state
            setTimeout(() => {
                const isSelected = this.classList.contains('border-red-500') || this.classList.contains('active');
                this.title = isSelected ? 'Klik untuk buang pilihan' : 'Klik untuk pilih varian ini';
            }, 100);
        });
    });
    
    // Check for success message on page load
    const successMessage = sessionStorage.getItem('cartSuccessMessage');
    if (successMessage) {
        showSuccessMessage(successMessage);
        sessionStorage.removeItem('cartSuccessMessage');
    }
    
    // Add click outside functionality for login modal
    const loginModal = document.getElementById('login-modal');
    loginModal.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            hideLoginModal();
        }
    });
    
    // Add escape key functionality
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideLoginModal();
        }
    });
});

function showSuccessMessage(message) {
    const successMessage = document.getElementById('success-message');
    const messageText = document.getElementById('success-message-text');
    
    messageText.textContent = message;
    successMessage.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideSuccessMessage();
    }, 5000);
}

function hideSuccessMessage() {
    const successMessage = document.getElementById('success-message');
    successMessage.classList.add('hidden');
}

// Reviews Modal Functions
function openReviewsModal() {
    const modal = document.getElementById('reviews-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReviewsModal() {
    const modal = document.getElementById('reviews-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function filterReviews(rating) {
    const reviewItems = document.querySelectorAll('.review-item');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const noReviewsMessage = document.getElementById('no-reviews-message');
    let visibleCount = 0;

    // Update filter button styles
    filterButtons.forEach(btn => {
        btn.classList.remove('active', 'bg-arsenal', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });

    // Find and activate the clicked button
    const clickedButton = event.target;
    clickedButton.classList.remove('bg-gray-100', 'text-gray-700');
    clickedButton.classList.add('active', 'bg-arsenal', 'text-white');

    // Filter reviews
    reviewItems.forEach(item => {
        if (rating === 'all' || parseInt(item.dataset.rating) === rating) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });

    // Show/hide no reviews message
    if (visibleCount === 0) {
        noReviewsMessage.classList.remove('hidden');
    } else {
        noReviewsMessage.classList.add('hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const reviewsModal = document.getElementById('reviews-modal');
    if (reviewsModal) {
        reviewsModal.addEventListener('click', function(e) {
            if (e.target === reviewsModal) {
                closeReviewsModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('reviews-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeReviewsModal();
            }
        }
    });
});

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const copyLinkModal = document.getElementById('copy-link-modal');
    if (copyLinkModal) {
        copyLinkModal.addEventListener('click', function(e) {
            if (e.target === copyLinkModal) {
                closeCopyLinkModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('copy-link-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeCopyLinkModal();
            }
        }
    });
});
</script>

<!-- Reviews Modal -->
<div id="reviews-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2 sm:p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] sm:max-h-[80vh] overflow-hidden mx-2 my-4 sm:my-8">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Ulasan Pelanggan</h3>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                        <span class="ml-2 text-base sm:text-lg font-bold text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                        <span class="text-xs sm:text-sm text-gray-500">({{ $product->reviews_count }} ulasan)</span>
                    </div>
                </div>
            </div>
            <button onclick="closeReviewsModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-col h-full">
            <!-- Rating Filter - Fixed at top -->
            <div class="p-4 sm:p-6 pb-3 sm:pb-4 border-b border-gray-200 flex-shrink-0">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="text-xs sm:text-sm font-medium text-gray-700 w-full sm:w-auto mb-2 sm:mb-0">Tapis mengikut rating:</span>
                    <button onclick="filterReviews('all')" 
                            class="filter-btn active px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-arsenal text-white">
                        Semua ({{ $product->reviews->count() }})
                    </button>
                    <button onclick="filterReviews(5)" 
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                        5★ ({{ $product->reviews->where('rating', 5)->count() }})
                    </button>
                    <button onclick="filterReviews(4)" 
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                        4★ ({{ $product->reviews->where('rating', 4)->count() }})
                    </button>
                    <button onclick="filterReviews(3)" 
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                        3★ ({{ $product->reviews->where('rating', 3)->count() }})
                    </button>
                    <button onclick="filterReviews(2)" 
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                        2★ ({{ $product->reviews->where('rating', 2)->count() }})
                    </button>
                    <button onclick="filterReviews(1)" 
                            class="filter-btn px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                        1★ ({{ $product->reviews->where('rating', 1)->count() }})
                    </button>
                </div>
            </div>

            <!-- Reviews List - Scrollable -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 pt-3 sm:pt-4" style="max-height: calc(85vh - 200px);">
                <div id="reviews-list" class="space-y-4 sm:space-y-6">
                    @foreach($product->reviews as $review)
                        <div class="review-item border-b border-gray-200 pb-4 sm:pb-6 last:border-b-0 last:pb-0" data-rating="{{ $review->rating }}">
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $review->reviewer_avatar }}" 
                                         alt="{{ $review->reviewer_name }}" 
                                         class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 border-gray-100 shadow-sm object-cover">
                                </div>
                                
                                <!-- Review Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Review Header -->
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2 sm:mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-base sm:text-lg mb-1">{{ $review->reviewer_name }}</h4>
                                            <div class="flex items-center space-x-2 sm:space-x-3 mb-2">
                                                <!-- Rating Stars -->
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                             fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                    @endfor
                                                    <span class="ml-2 text-xs sm:text-sm font-medium text-gray-700">{{ $review->rating }}/5</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Review Date -->
                                        <div class="text-right">
                                            <span class="text-xs sm:text-sm text-gray-500">{{ malayDiffForHumans($review->created_at) }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Review Comment -->
                                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                        <p class="text-gray-800 leading-relaxed text-xs sm:text-sm">{{ $review->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- No Reviews Message -->
                <div id="no-reviews-message" class="hidden text-center py-8 sm:py-12">
                    <div class="mx-auto w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Tiada ulasan dijumpai</h3>
                    <p class="text-gray-600 text-xs sm:text-sm">Tiada ulasan yang sepadan dengan penapis yang dipilih.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush 