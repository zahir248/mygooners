@extends('layouts.app')

@section('title', 'Berita & Blog Arsenal - MyGooners')
@section('meta_description', 'Kekal terkini dengan berita Arsenal terkini, laporan perlawanan, kemas kini pemindahan, dan analisis mendalam dari komuniti MyGooners.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-red-600 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Berita & Analisis Arsenal</h1>
            <p class="text-xl text-red-100 max-w-3xl mx-auto">
                Dapatkan berita Arsenal terkini, laporan perlawanan, kemas kini pemindahan, dan analisis mendalam dari sesama Gooners
            </p>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <form action="{{ route('blog.index') }}" method="GET" class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari artikel..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    @if($category)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                </form>
            </div>

            <!-- Category Filter -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blog.index') }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !$category ? 'bg-arsenal text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua Kategori
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', strtolower(str_replace(' ', '-', $cat))) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ strtolower($category) === strtolower(str_replace(' ', '-', $cat)) ? 'bg-arsenal text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($articles->count() > 0)
        <!-- Featured Article (if first article is featured) -->
        @if($articles->first() && $articles->first()->is_featured && !$search && !$category)
            @php $featuredArticle = $articles->first() @endphp
            <div class="mb-12">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="md:flex">
                        <div class="md:w-1/2">
                                                    @if($featuredArticle->cover_image)
                            <img src="{{ route('article.image', basename($featuredArticle->cover_image)) }}" 
                                 alt="{{ $featuredArticle->title }}" 
                                 class="w-full h-64 md:h-full object-cover">
                        @else
                            <div class="w-full h-64 md:h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        </div>
                        <div class="md:w-1/2 p-8">
                            <div class="flex items-center mb-4">
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $featuredArticle->category }}
                                </span>
                                <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold ml-3">
                                    UTAMA
                                </span>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                                <a href="{{ route('blog.show', $featuredArticle->slug) }}" 
                                   class="hover:text-red-600 transition-colors">
                                    {{ $featuredArticle->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                                {{ $featuredArticle->excerpt }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $featuredArticle->published_at->diffForHumans() }}
                                    <span class="mx-2">•</span>
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ number_format($featuredArticle->views_count) }} tontonan
                                </div>
                                <a href="{{ route('blog.show', $featuredArticle->slug) }}" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    Baca Artikel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($articles->skip($articles->first() && $articles->first()->is_featured && !$search && !$category ? 1 : 0) as $article)
                <article class="bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="relative">
                        @if($article->cover_image)
                            <img src="{{ route('article.image', basename($article->cover_image)) }}" 
                             alt="{{ $article->title }}" 
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $article->category }}
                            </span>
                        </div>
                        @if($article->is_featured)
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-400 text-gray-900 px-2 py-1 rounded-full text-xs font-bold">
                                    UTAMA
                                </span>
                            </div>
                        @endif
                        @if($article->youtube_video_id)
                            <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors">
                            <a href="{{ route('blog.show', $article->slug) }}">
                                {{ $article->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            {{ Str::limit($article->excerpt, 120) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $article->published_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($article->views_count) }}
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach(array_slice($article->tags, 0, 3) as $tag)
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="mt-12">
                {{ $articles->appends(request()->query())->links() }}
        </div>
        @endif
    @else
        <!-- No Articles Found -->
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Tiada artikel dijumpai</h3>
            <p class="text-gray-600 mb-6">
                @if($search)
                    Tiada artikel sepadan dengan carian anda untuk "{{ $search }}"
                @elseif($category)
                    Tiada artikel dijumpai dalam kategori "{{ $category }}"
                @else
                    Tiada artikel telah diterbitkan lagi
                @endif
            </p>
            <a href="{{ route('blog.index') }}" class="text-red-600 hover:text-red-700 font-medium">
                ← Lihat semua artikel
            </a>
        </div>
    @endif
</div>


@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush 