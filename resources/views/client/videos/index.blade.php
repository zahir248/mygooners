@extends('layouts.app')

@section('title', 'Galeri Video Arsenal - MyGooners')
@section('meta_description', 'Tonton video Arsenal terkini, podcast, analisis perlawanan, dan kandungan eksklusif dari komuniti MyGooners.')

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-red-600 to-red-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Galeri Video Arsenal</h1>
            <p class="text-xl text-red-100 max-w-3xl mx-auto">
                Tonton kandungan Arsenal eksklusif, analisis perlawanan, podcast, dan video komuniti dari sesama Gooners
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
                <form action="{{ route('videos.index') }}" method="GET" class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Cari video..." 
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
                <a href="{{ route('videos.index') }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !$category ? 'bg-arsenal text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua Kategori
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('videos.index', ['category' => strtolower(str_replace(' ', '-', $cat))]) }}" 
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
    @if($videos->count() > 0)
        <!-- Featured Video (if first video is featured) -->
        @if($videos->first() && $videos->first()->is_featured && !$search && !$category)
            @php $featuredVideo = $videos->first() @endphp
            <div class="mb-12">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="md:flex">
                        <div class="md:w-1/2 relative">
                            <div class="relative w-full" style="padding-bottom: 56.25%;">
                                <iframe 
                                    class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $featuredVideo->youtube_video_id }}"
                                    title="{{ $featuredVideo->title }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <div class="md:w-1/2 p-8">
                            <div class="flex items-center mb-4">
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $featuredVideo->category }}
                                </span>
                                <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold ml-3">
                                    UTAMA
                                </span>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                                <a href="{{ route('videos.show', $featuredVideo->slug) }}" 
                                   class="hover:text-red-600 transition-colors">
                                    {{ $featuredVideo->title }}
                                </a>
                            </h2>
                            <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                                {{ Str::limit($featuredVideo->description, 200) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $featuredVideo->published_at->diffForHumans() }}
                                    <span class="mx-2">•</span>
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ number_format($featuredVideo->views_count) }} tontonan
                                    <span class="mx-2">•</span>
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $featuredVideo->duration }}
                                </div>
                                <a href="{{ route('videos.show', $featuredVideo->slug) }}" 
                                   class="bg-arsenal hover:bg-arsenal text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    Tonton Video Penuh
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Videos Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($videos->skip($videos->first() && $videos->first()->is_featured && !$search && !$category ? 1 : 0) as $video)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="relative">
                        @if($video->thumbnail)
                            <img src="{{ route('video.thumbnail', $video->thumbnail) }}" 
                                 alt="{{ $video->title }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                 onerror="this.onerror=null; this.src='https://img.youtube.com/vi/{{ $video->youtube_video_id }}/maxresdefault.jpg';">
                        @else
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_video_id }}/maxresdefault.jpg" 
                                 alt="{{ $video->title }}" 
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-48 bg-gray-200 flex items-center justify-center group-hover:scale-105 transition-transform duration-300\'><svg class=\'h-12 w-12 text-gray-400\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\'></path></svg></div>';">
                        @endif
                        
                        <!-- Play Button Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center group-hover:bg-opacity-60 transition-all">
                            <a href="{{ route('videos.show', $video->slug) }}" 
                               class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 transform group-hover:scale-110 transition-all">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </div>
                        
                        <!-- Duration Badge -->
                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-sm font-medium">
                            {{ $video->duration }}
                        </div>
                        
                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $video->category }}
                            </span>
                        </div>
                        
                        @if($video->is_featured)
                            <div class="absolute top-4 right-4">
                                <span class="bg-yellow-400 text-gray-900 px-2 py-1 rounded-full text-xs font-bold">
                                    UTAMA
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors">
                            <a href="{{ route('videos.show', $video->slug) }}">
                                {{ $video->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4 leading-relaxed">
                            {{ Str::limit($video->description, 120) }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $video->published_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($video->views_count) }}
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach(array_slice($video->tags, 0, 3) as $tag)
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($videos->hasPages())
            <div class="mt-12">
                {{ $videos->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- No Videos Found -->
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Tiada video dijumpai</h3>
            <p class="text-gray-600 mb-6">
                @if($search)
                    Tiada video sepadan dengan carian anda untuk "{{ $search }}"
                @elseif($category)
                    Tiada video dijumpai dalam kategori "{{ $category }}"
                @else
                    Tiada video telah dimuat naik lagi
                @endif
            </p>
            <a href="{{ route('videos.index') }}" class="text-red-600 hover:text-red-700 font-medium">
                ← Lihat semua video
            </a>
        </div>
    @endif
</div>

<!-- Video Stats Section -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Statistik Galeri Video</h2>
            <p class="text-gray-600">Kandungan Arsenal dipacu komuniti</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $videos->count() }}+</h3>
                <p class="text-gray-600">Jumlah Video</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ number_format($videos->sum('views_count')) }}+</h3>
                <p class="text-gray-600">Jumlah Tontonan</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ count($categories) }}</h3>
                <p class="text-gray-600">Kategori</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $videos->where('is_featured', true)->count() }}</h3>
                <p class="text-gray-600">Video Utama</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush 