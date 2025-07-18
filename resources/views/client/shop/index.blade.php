@extends('layouts.app')

@section('title', 'Kedai Arsenal - Barangan Rasmi - MyGooners')
@section('meta_description', 'Beli barangan Arsenal rasmi, jersi, aksesori, dan item eksklusif. Dapatkan peralatan Arsenal terkini dan sokong Gunners dengan gaya.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-red-600 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Kedai Arsenal Rasmi</h1>
            <p class="text-xl text-red-100 max-w-3xl mx-auto">
                Dapatkan barangan Arsenal terkini, jersi, dan item eksklusif. Tunjukkan kebanggaan Gunners anda dengan peralatan rasmi.
            </p>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('shop.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-4 lg:space-y-0">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                    <div class="relative">
                        <input type="text" 
                               id="search"
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari jersi, aksesori..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="lg:w-48">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select id="category" name="category" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ strtolower($cat) }}" {{ strtolower($category) === strtolower($cat) ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="lg:w-48">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Susun Mengikut</label>
                    <select id="sort" name="sort" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Terbaru Dahulu</option>
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Paling Popular</option>
                        <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="w-full lg:w-auto bg-arsenal hover:bg-arsenal text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Gunakan Penapis
                    </button>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $category || $sort !== 'newest')
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600">Penapis aktif:</span>
                    @if($search)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            Carian: "{{ $search }}"
                            <a href="{{ route('shop.index', array_filter(['category' => $category, 'sort' => $sort !== 'newest' ? $sort : null])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if($category)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            {{ ucfirst($category) }}
                            <a href="{{ route('shop.index', array_filter(['search' => $search, 'sort' => $sort !== 'newest' ? $sort : null])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if($sort !== 'newest')
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            Susun: {{ ucfirst(str_replace('_', ' ', $sort)) }}
                            <a href="{{ route('shop.index', array_filter(['search' => $search, 'category' => $category])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ route('shop.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Kosongkan semua</a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($products->count() > 0)
        <!-- Results Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @if($search || $category)
                        Hasil Carian ({{ $products->count() }} dijumpai)
                    @else
                        Semua Produk ({{ $products->count() }})
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">Barangan Arsenal rasmi dan item eksklusif</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Disusun mengikut: {{ ucfirst(str_replace('_', ' ', $sort)) }}</span>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    <!-- Product Image -->
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $product->images[0] }}" 
                             alt="{{ $product->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 space-y-2">
                            @if($product->sale_price)
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                </span>
                            @endif
                            @if($product->is_featured)
                                <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                    UTAMA
                                </span>
                            @endif
                        </div>

                        @if($product->stock_quantity <= 5)
                            <div class="absolute bottom-3 left-3">
                                <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    Hanya {{ $product->stock_quantity }} tinggal
                                </span>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="bg-white hover:bg-gray-100 text-gray-700 p-2 rounded-full shadow-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Product Content -->
                    <div class="p-5">
                        <div class="mb-2">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $product->category }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                            <a href="{{ route('shop.show', $product->slug) }}">
                                {{ $product->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                            {{ Str::limit($product->description, 100) }}
                        </p>
                        
                        <!-- Rating and Reviews -->
                        <div class="flex items-center mb-4 text-sm">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 font-medium text-gray-900">{{ number_format($product->average_rating, 1) }}</span>
                            <span class="ml-1 text-gray-500">({{ $product->reviews_count }})</span>
                            <span class="ml-3 text-gray-400">•</span>
                            <span class="ml-3 text-gray-500">{{ number_format($product->views_count) }} tontonan</span>
                        </div>
                        
                        <!-- Price and Add to Cart -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                @if($product->sale_price)
                                    <span class="text-2xl font-bold text-red-600">£{{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through">£{{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-2xl font-bold text-gray-900">£{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            @if($product->stock_quantity > 0)
                                <a href="{{ route('shop.show', $product->slug) }}" class="bg-arsenal hover:bg-arsenal text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Lihat Butiran
                                </a>
                            @else
                                <span class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium">
                                    Kehabisan Stok
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More / Pagination -->
        <div class="mt-12 text-center">
            <button class="bg-arsenal hover:bg-arsenal text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Muat Lebih Banyak Produk
            </button>
        </div>
    @else
        <!-- No Products Found -->
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Tiada produk dijumpai</h3>
            <p class="text-gray-600 mb-6">
                @if($search || $category)
                    Tiada produk sepadan dengan kriteria carian anda. Cuba laraskan penapis anda.
                @else
                    Tiada produk tersedia pada masa ini.
                @endif
            </p>
            @if($search || $category)
                <a href="{{ route('shop.index') }}" class="text-red-600 hover:text-red-700 font-medium">
                    ← Lihat semua produk
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Features Section -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Mengapa Membeli-belah Dengan Kami?</h2>
            <p class="text-gray-600">Barangan Arsenal rasmi dengan jaminan kualiti dan keaslian</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">100% Rasmi</h3>
                <p class="text-gray-600">Semua produk adalah barangan Arsenal berlesen rasmi dengan jaminan keaslian.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Penghantaran Pantas</h3>
                <p class="text-gray-600">Penghantaran yang cepat dan boleh dipercayai untuk menghantar peralatan Arsenal anda dengan selamat.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414-1.414l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Pemulangan Mudah</h3>
                <p class="text-gray-600">Tidak berpuas hati? Dasar pemulangan 30 hari yang mudah untuk ketenangan fikiran anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush 