@extends('layouts.app')

@section('title', 'MyGooners - Komuniti Peminat Arsenal')
@section('meta_description', 'Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif. Sertai ribuan Gooners di seluruh dunia.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-red-600 to-red-700 text-white relative overflow-hidden" style="background-image: url('{{ asset('images/hero-section.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 70vh; margin-top: -64px; padding-top: 64px;">
    <!-- Overlay to ensure text readability -->
    <div class="absolute inset-0 bg-black bg-opacity-75"></div>
    <div class="relative z-10 h-full flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Selamat Datang ke <span class="text-yellow-300">MyGooners</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-red-100 max-w-3xl mx-auto">
                Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video eksklusif, perkhidmatan dipercayai, dan barangan tulen.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('blog.index') }}" class="bg-white text-red-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                    Berita Terkini
                </a>
                <a href="{{ route('register') }}" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                    Sertai Komuniti
                </a>
            </div>
        </div>
    </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Featured Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        <!-- Main Featured Article -->
        <div class="lg:col-span-2">
            @if($featuredArticles->count() > 0)
                @php $article = $featuredArticles->first() @endphp
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="relative">
                        @if($article->cover_image)
                            <img src="{{ route('article.image', basename($article->cover_image)) }}" alt="{{ $article->title }}" class="w-full h-64 md:h-80 object-cover">
                        @else
                            <div class="w-full h-64 md:h-80 bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $article->category ?: 'Berita' }}
                            </span>
                        </div>
                        @if($article->is_featured)
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                    UTAMA
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                            <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-gray-600 mb-4 text-lg leading-relaxed">
                            {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 200) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}
                                <span class="mx-2">•</span>
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($article->views_count ?? 0) }} tontonan
                            </div>
                            <a href="{{ route('blog.show', $article->slug) }}" class="text-red-600 hover:text-red-700 font-medium">
                                Baca Lagi →
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Fallback when no articles -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tiada Artikel Terkini</h3>
                        <p class="text-gray-600">Artikel akan muncul di sini tidak lama lagi.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Content -->
        <div class="space-y-6">
            <!-- Featured Video -->
            @if($featuredVideo)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Video Utama</h3>
                    </div>
                    <div class="relative">
                        <img src="{{ $featuredVideo->thumbnail_url }}" alt="{{ $featuredVideo->title }}" class="w-full h-48 object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                            <a href="{{ route('videos.show', $featuredVideo->slug) }}" class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 transition-colors">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        @if($featuredVideo->duration)
                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                                {{ $featuredVideo->duration }}
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">
                            <a href="{{ route('videos.show', $featuredVideo->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $featuredVideo->title }}
                            </a>
                        </h4>
                        <p class="text-gray-600 text-sm mb-3">{{ Str::limit($featuredVideo->description, 100) }}</p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ $featuredVideo->published_at ? $featuredVideo->published_at->diffForHumans() : $featuredVideo->created_at->diffForHumans() }}</span>
                            <span>{{ number_format($featuredVideo->views_count ?? 0) }} tontonan</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Fallback when no video -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Tiada Video</h3>
                        <p class="text-gray-600 text-sm">Video akan muncul di sini tidak lama lagi.</p>
                    </div>
                </div>
            @endif

            <!-- Recent Articles List -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Artikel Terkini</h3>
                </div>
                @if($featuredArticles->count() > 1)
                    <div class="divide-y divide-gray-200">
                        @foreach($featuredArticles->skip(1)->take(3) as $article)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex space-x-3">
                                    @if($article->cover_image)
                                        <img src="{{ route('article.image', basename($article->cover_image)) }}" alt="{{ $article->title }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                            <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-red-600 transition-colors">
                                                {{ Str::limit($article->title, 60) }}
                                            </a>
                                        </h4>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $article->category ?: 'Berita' }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $article->published_at ? $article->published_at->diffForHumans() : $article->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center">
                        <p class="text-gray-600 text-sm">Tiada artikel tambahan untuk dipaparkan.</p>
                    </div>
                @endif
                <div class="p-4 bg-gray-50">
                    <a href="{{ route('blog.index') }}" class="text-red-600 hover:text-red-700 font-medium text-sm">
                        Lihat Semua Artikel →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Perkhidmatan Dipercayai</h2>
                <p class="text-gray-600">Berhubung dengan peminat Arsenal yang disahkan menawarkan perkhidmatan berkualiti</p>
            </div>
            <a href="{{ route('services.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Lihat Semua Perkhidmatan
            </a>
        </div>
        
        @if($newServices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($newServices as $service)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('services.show', $service->slug) }}" class="hover:text-red-600 transition-colors">
                                            {{ $service->title }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $service->category ?: 'Perkhidmatan' }}</span>
                                        @if($service->is_verified)
                                            <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Disahkan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-red-600">{{ $service->pricing ?: 'Harga Rundingan' }}</div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        {{ number_format($service->trust_score ?? 0, 1) }}
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 mb-4 text-sm">{{ Str::limit($service->description, 120) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $service->location ?: 'Lokasi Tidak Dinyatakan' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    @if($service->user)
                                        @if($service->user->profile_image)
                                            @if(Str::startsWith($service->user->profile_image, 'http'))
                                                <img src="{{ $service->user->profile_image }}" alt="{{ $service->user->name }}" class="w-6 h-6 rounded-full mr-2 object-cover">
                                            @else
                                                <img src="{{ route('profile.image', basename($service->user->profile_image)) }}" alt="{{ $service->user->name }}" class="w-6 h-6 rounded-full mr-2 object-cover">
                                            @endif
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($service->user->name) }}&size=24&background=dc2626&color=fff" alt="{{ $service->user->name }}" class="w-6 h-6 rounded-full mr-2">
                                        @endif
                                        <span>{{ $service->user->name }}</span>
                                    @else
                                        <span>Pengguna Tidak Dikenali</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback when no services -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tiada Perkhidmatan</h3>
                <p class="text-gray-600">Perkhidmatan akan muncul di sini tidak lama lagi.</p>
            </div>
        @endif
    </section>

    <!-- Shop Section -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Produk Popular</h2>
                <p class="text-gray-600">Dapatkan barangan Arsenal terkini dan item eksklusif</p>
            </div>
            <a href="{{ route('shop.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                Lihat Semua Produk
            </a>
        </div>
        
        @if($popularProducts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($popularProducts as $product)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                        <div class="relative">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ route('product.image', basename($product->images[0])) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            @if($product->sale_price)
                                <div class="absolute top-2 left-2">
                                    <span class="bg-red-600 text-white px-2 py-1 rounded-full text-xs font-bold">
                                        JUALAN
                                    </span>
                                </div>
                            @endif
                            @if($product->is_featured)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-yellow-400 text-gray-900 px-2 py-1 rounded-full text-xs font-bold">
                                        UTAMA
                                    </span>
                                </div>
                            @endif
                            @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                <div class="absolute bottom-2 left-2">
                                    <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Hanya {{ $product->stock_quantity }} lagi
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="mb-2">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $product->category ?: 'Produk' }}</span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('shop.show', $product->slug) }}" class="hover:text-red-600 transition-colors">
                                    {{ $product->title }}
                                </a>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-4">{{ Str::limit($product->description, 80) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($product->sale_price)
                                        <span class="text-xl font-bold text-red-600">RM{{ number_format($product->sale_price, 2) }}</span>
                                        <span class="text-sm text-gray-500 line-through">RM{{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="text-xl font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('shop.show', $product->slug) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Lihat Item
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Fallback when no products -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tiada Produk</h3>
                <p class="text-gray-600">Produk akan muncul di sini tidak lama lagi.</p>
            </div>
        @endif
    </section>

</div>
@endsection 