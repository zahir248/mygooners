@extends('layouts.app')

@section('title', $product->title . ' - Arsenal Shop - MyGooners')
@section('meta_description', $product->description)

@section('meta_tags')
<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $product->title }} - Arsenal Shop">
<meta property="og:description" content="{{ $product->description }}">
<meta property="og:image" content="{{ $product->images[0] }}">
<meta property="og:type" content="product">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:site_name" content="MyGooners">
<meta property="product:price:amount" content="{{ $product->sale_price ?? $product->price }}">
<meta property="product:price:currency" content="GBP">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->title }} - Arsenal Shop">
<meta name="twitter:description" content="{{ $product->description }}">
<meta name="twitter:image" content="{{ $product->images[0] }}">

<!-- Additional Meta Tags -->
<meta name="keywords" content="Arsenal, {{ implode(', ', $product->tags) }}, merchandise, shop">
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.index') }}" class="hover:text-red-600 transition-colors">Kedai</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.index', ['category' => strtolower($product->category)]) }}" class="hover:text-red-600 transition-colors">{{ $product->category }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ $product->title }}</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <!-- Product Images -->
        <div>
            <!-- Main Image -->
            <div class="relative mb-4">
                <div class="aspect-square rounded-xl overflow-hidden bg-gray-200 mb-4">
                    <img id="mainImage" 
                         src="{{ $product->images[0] }}" 
                         alt="{{ $product->title }}" 
                         class="w-full h-full object-cover">
                </div>
                
                <!-- Badges -->
                <div class="absolute top-4 left-4 space-y-2">
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
                </div>

                @if($product->stock_quantity <= 5)
                    <div class="absolute bottom-4 left-4">
                        <span class="bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-medium">
                            Hanya {{ $product->stock_quantity }} tinggal dalam stok
                        </span>
                    </div>
                @endif
            </div>
            
            <!-- Thumbnail Images -->
            @if(count($product->images) > 1)
                <div class="grid grid-cols-4 gap-2">
                    @foreach($product->images as $index => $image)
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-200 cursor-pointer hover:ring-2 hover:ring-red-500 transition-all"
                             onclick="changeMainImage('{{ $image }}', this)">
                            <img src="{{ $image }}" 
                                 alt="{{ $product->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
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
                
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $product->title }}</h1>
                
                <!-- Rating and Reviews -->
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-lg font-semibold text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                    <span class="text-gray-500">({{ $product->reviews_count }} ulasan)</span>
                    <a href="#reviews" class="text-red-600 hover:text-red-700 font-medium">Baca Ulasan</a>
                </div>

                <!-- Price -->
                <div class="flex items-center space-x-4 mb-6">
                    @if($product->sale_price)
                        <span class="text-4xl font-bold text-red-600">£{{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-2xl text-gray-500 line-through">£{{ number_format($product->price, 2) }}</span>
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                            Jimat £{{ number_format($product->price - $product->sale_price, 2) }}
                        </span>
                    @else
                        <span class="text-4xl font-bold text-gray-900">£{{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
            </div>

            <!-- Product Description -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Penerangan</h3>
                <div class="prose text-gray-700 leading-relaxed">
                    {{ $product->description }}
                </div>
            </div>

            <!-- Size Selection (if applicable) -->
            @if(isset($product->sizes) && count($product->sizes) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Saiz</h3>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach($product->sizes as $size)
                            <button class="border border-gray-300 hover:border-red-500 hover:text-red-600 py-2 px-4 rounded-lg text-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quantity and Add to Cart -->
            <div class="mb-8">
                <div class="flex items-center space-x-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti</label>
                        <select id="quantity" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            @for($i = 1; $i <= min($product->stock_quantity, 10); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        @if($product->stock_quantity > 0)
                            <span class="text-green-600 font-medium">✓ Dalam Stok ({{ $product->stock_quantity }} tersedia)</span>
                        @else
                            <span class="text-red-600 font-medium">✗ Kehabisan Stok</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    @if($product->stock_quantity > 0)
                        <button class="w-full bg-arsenal hover:bg-arsenal text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors">
                            Tambah ke Troli
                        </button>
                        <button class="w-full border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-4 px-6 rounded-lg font-bold text-lg transition-colors">
                            Beli Sekarang
                        </button>
                    @else
                        <button class="w-full bg-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg cursor-not-allowed">
                            Kehabisan Stok
                        </button>
                        <button class="w-full border-2 border-gray-300 text-gray-600 py-4 px-6 rounded-lg font-bold text-lg">
                            Notify When Available
                        </button>
                    @endif
                </div>
            </div>

            <!-- Product Features -->
            <div class="border-t border-gray-200 pt-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Official Arsenal Product
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                        </svg>
                        Free UK Delivery
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414-1.414l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        30-Day Returns
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        Customer Support
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($product->tags)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->tags as $tag)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Share Options -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-4">Share This Product</h3>
                <div class="flex space-x-3">
                    <button onclick="shareOnFacebook()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                        Facebook
                    </button>
                    <button onclick="shareOnTwitter()" class="bg-sky-500 hover:bg-sky-600 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                        Twitter
                    </button>
                    <button onclick="copyToClipboard()" class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div id="reviews" class="mb-16">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Customer Reviews</h2>
                <div class="flex items-center space-x-2">
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                    <span class="text-gray-500">({{ $product->reviews_count }} ulasan)</span>
                </div>
            </div>

            <div class="space-y-8">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-200 pb-8 last:border-b-0 last:pb-0">
                        <div class="flex items-start space-x-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&size=50&background=dc2626&color=fff" 
                                 alt="{{ $review->user->name }}" 
                                 class="w-12 h-12 rounded-full">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <button class="bg-arsenal hover:bg-arsenal text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Write a Review
                </button>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div>
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">You May Also Like</h2>
            <p class="text-gray-600">More Arsenal merchandise you might be interested in</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                    <div class="relative h-64">
                        <img src="{{ $relatedProduct->images[0] }}" 
                             alt="{{ $relatedProduct->title }}" 
                             class="w-full h-full object-cover">
                        @if($relatedProduct->sale_price)
                            <div class="absolute top-3 left-3">
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    SALE
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium mb-2 inline-block">
                            {{ $relatedProduct->category }}
                        </span>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <a href="{{ route('shop.show', $relatedProduct->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $relatedProduct->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($relatedProduct->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($relatedProduct->sale_price)
                                    <span class="text-xl font-bold text-red-600">£{{ number_format($relatedProduct->sale_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through">£{{ number_format($relatedProduct->price, 2) }}</span>
                                @else
                                    <span class="text-xl font-bold text-gray-900">£{{ number_format($relatedProduct->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
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
@endsection

@push('scripts')
<script>
function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('mainImage').src = imageSrc;
    
    // Remove active state from all thumbnails
    document.querySelectorAll('.aspect-square').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-red-500');
    });
    
    // Add active state to clicked thumbnail
    thumbnail.classList.add('ring-2', 'ring-red-500');
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
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Link copied to clipboard!');
    });
}

// Auto-select first thumbnail
document.addEventListener('DOMContentLoaded', function() {
    const firstThumbnail = document.querySelector('.aspect-square');
    if (firstThumbnail) {
        firstThumbnail.classList.add('ring-2', 'ring-red-500');
    }
});
</script>
@endpush 