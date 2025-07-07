@extends('layouts.app')

@section('title', 'MyGooners - Arsenal Fan Community')
@section('meta_description', 'The ultimate Arsenal fan community featuring latest news, videos, services marketplace, and exclusive merchandise. Join thousands of Gooners worldwide.')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-red-600 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Welcome to <span class="text-yellow-300">MyGooners</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-red-100 max-w-3xl mx-auto">
                The ultimate Arsenal fan community featuring the latest news, exclusive videos, trusted services, and authentic merchandise.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('blog.index') }}" class="bg-white text-red-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                    Latest News
                </a>
                <a href="{{ route('register') }}" class="border-2 border-white text-white hover:bg-white hover:text-red-600 px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                    Join Community
                </a>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Featured Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
        <!-- Main Featured Article -->
        <div class="lg:col-span-2">
            @if($featuredArticles->first())
                @php $article = $featuredArticles->first() @endphp
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="w-full h-64 md:h-80 object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $article->category }}
                            </span>
                        </div>
                        @if($article->is_featured)
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                    FEATURED
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
                            {{ $article->excerpt }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $article->published_at->diffForHumans() }}
                                <span class="mx-2">•</span>
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($article->views_count) }} views
                            </div>
                            <a href="{{ route('blog.show', $article->slug) }}" class="text-red-600 hover:text-red-700 font-medium">
                                Read More →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Content -->
        <div class="space-y-6">
            <!-- Featured Video -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Featured Video</h3>
                </div>
                <div class="relative">
                    <img src="{{ $featuredVideo->thumbnail }}" alt="{{ $featuredVideo->title }}" class="w-full h-48 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <a href="{{ route('videos.show', $featuredVideo->slug) }}" class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 transition-colors">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-sm">
                        {{ $featuredVideo->duration }}
                    </div>
                </div>
                <div class="p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">
                        <a href="{{ route('videos.show', $featuredVideo->slug) }}" class="hover:text-red-600 transition-colors">
                            {{ $featuredVideo->title }}
                        </a>
                    </h4>
                    <p class="text-gray-600 text-sm mb-3">{{ Str::limit($featuredVideo->description, 100) }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ $featuredVideo->published_at->diffForHumans() }}</span>
                        <span>{{ number_format($featuredVideo->views_count) }} views</span>
                    </div>
                </div>
            </div>

            <!-- Recent Articles List -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Recent Articles</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($featuredArticles->skip(1)->take(3) as $article)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex space-x-3">
                                <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-1">
                                        <a href="{{ route('blog.show', $article->slug) }}" class="hover:text-red-600 transition-colors">
                                            {{ Str::limit($article->title, 60) }}
                                        </a>
                                    </h4>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $article->category }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $article->published_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 bg-gray-50">
                    <a href="{{ route('blog.index') }}" class="text-red-600 hover:text-red-700 font-medium text-sm">
                        View All Articles →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Trusted Services</h2>
                <p class="text-gray-600">Connect with verified Arsenal fans offering quality services</p>
            </div>
            <a href="{{ route('services.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                View All Services
            </a>
        </div>
        
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
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $service->category }}</span>
                                    @if($service->is_verified)
                                        <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-red-600">{{ $service->pricing }}</div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    {{ number_format($service->trust_score, 1) }}
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-gray-600 mb-4 text-sm">{{ Str::limit($service->description, 120) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $service->location }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($service->user->name) }}&size=24&background=dc2626&color=fff" alt="{{ $service->user->name }}" class="w-6 h-6 rounded-full mr-2">
                                <span>{{ $service->user->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Shop Section -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Popular Products</h2>
                <p class="text-gray-600">Get the latest Arsenal merchandise and exclusive items</p>
            </div>
            <a href="{{ route('shop.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                View All Products
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($popularProducts as $product)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                    <div class="relative">
                        <img src="{{ $product->images[0] }}" alt="{{ $product->title }}" class="w-full h-48 object-cover">
                        @if($product->sale_price)
                            <div class="absolute top-2 left-2">
                                <span class="bg-red-600 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    SALE
                                </span>
                            </div>
                        @endif
                        @if($product->is_featured)
                            <div class="absolute top-2 right-2">
                                <span class="bg-yellow-400 text-gray-900 px-2 py-1 rounded-full text-xs font-bold">
                                    FEATURED
                                </span>
                            </div>
                        @endif
                        @if($product->stock_quantity <= 5)
                            <div class="absolute bottom-2 left-2">
                                <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                    Only {{ $product->stock_quantity }} left
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="mb-2">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">{{ $product->category }}</span>
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
                                    <span class="text-xl font-bold text-red-600">£{{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-sm text-gray-500 line-through">£{{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-xl font-bold text-gray-900">£{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('shop.show', $product->slug) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                View Item
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-8 md:p-12 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Stay Updated</h2>
        <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
            Get the latest Arsenal news, transfer updates, and exclusive content delivered straight to your inbox.
        </p>
        <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
            <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Subscribe
            </button>
        </form>
    </section>
</div>
@endsection 