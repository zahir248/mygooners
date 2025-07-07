@extends('layouts.app')

@section('title', $video->title . ' - Arsenal Videos - MyGooners')
@section('meta_description', $video->description)

@push('head')
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="video">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $video->title }}">
    <meta property="og:description" content="{{ $video->description }}">
    <meta property="og:image" content="{{ $video->thumbnail }}">
    <meta property="og:video" content="https://www.youtube.com/watch?v={{ $video->youtube_video_id }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="player">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $video->title }}">
    <meta property="twitter:description" content="{{ $video->description }}">
    <meta property="twitter:image" content="{{ $video->thumbnail }}">
    <meta property="twitter:player" content="https://www.youtube.com/embed/{{ $video->youtube_video_id }}">
    <meta property="twitter:player:width" content="560">
    <meta property="twitter:player:height" content="315">

    <!-- Video specific -->
    <meta property="video:duration" content="{{ $video->duration }}">
    <meta property="video:release_date" content="{{ $video->published_at->toISOString() }}">
    @foreach($video->tags as $tag)
        <meta property="video:tag" content="{{ $tag }}">
    @endforeach
@endpush

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <div>
                        <a href="{{ route('home') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            <span class="sr-only">Home</span>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('videos.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Videos</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-4 text-sm font-medium text-gray-900" aria-current="page">{{ Str::limit($video->title, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Video Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Main Video Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Video Player -->
                <div class="relative w-full bg-black" style="padding-bottom: 56.25%;">
                    <iframe 
                        class="absolute top-0 left-0 w-full h-full"
                        src="https://www.youtube.com/embed/{{ $video->youtube_video_id }}?autoplay=0&rel=0&modestbranding=1"
                        title="{{ $video->title }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>

                <!-- Video Info -->
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $video->category }}
                        </span>
                        @if($video->is_featured)
                            <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold ml-3">
                                FEATURED
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $video->title }}</h1>

                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $video->published_at->format('F j, Y') }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($video->views_count) }} views
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $video->duration }}
                            </div>
                        </div>

                        <!-- Video Actions -->
                        <div class="flex items-center space-x-2">
                            <button class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                Like
                            </button>
                            <button onclick="copyToClipboard()" class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Share
                            </button>
                        </div>
                    </div>

                    <!-- Video Description -->
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $video->description }}</p>
                    </div>

                    <!-- Tags -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($video->tags as $tag)
                                <span class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Social Sharing -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Share this video</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                               target="_blank"
                               class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($video->title) }}" 
                               target="_blank"
                               class="flex items-center justify-center w-10 h-10 bg-blue-400 text-white rounded-full hover:bg-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" 
                               target="_blank"
                               class="flex items-center justify-center w-10 h-10 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                            <a href="https://www.youtube.com/watch?v={{ $video->youtube_video_id }}" 
                               target="_blank"
                               class="flex items-center justify-center w-10 h-10 bg-red-600 text-white rounded-full hover:bg-red-700 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 mt-8 lg:mt-0">
            <!-- Related Videos -->
            @if($relatedVideos->count() > 0)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Related Videos</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($relatedVideos as $relatedVideo)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <a href="{{ route('videos.show', $relatedVideo->slug) }}" class="block">
                                    <div class="flex space-x-3">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $relatedVideo->thumbnail }}" 
                                                 alt="{{ $relatedVideo->title }}" 
                                                 class="w-20 h-14 object-cover rounded">
                                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center rounded">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="absolute bottom-1 right-1 bg-black bg-opacity-75 text-white px-1 py-0.5 rounded text-xs">
                                                {{ $relatedVideo->duration }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-1 hover:text-red-600 transition-colors line-clamp-2">
                                                {{ $relatedVideo->title }}
                                            </h4>
                                            <div class="flex items-center text-xs text-gray-500 space-x-2">
                                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $relatedVideo->category }}</span>
                                            </div>
                                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                                <span>{{ number_format($relatedVideo->views_count) }} views</span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $relatedVideo->published_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 bg-gray-50">
                        <a href="{{ route('videos.index') }}" class="text-red-600 hover:text-red-700 font-medium text-sm">
                            View All Videos →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Video Categories -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Video Categories</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        <a href="{{ route('videos.index') }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            All Videos
                        </a>
                        <a href="{{ route('videos.index', ['category' => 'weekly-podcast']) }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            Weekly Podcast
                        </a>
                        <a href="{{ route('videos.index', ['category' => 'match-analysis']) }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            Match Analysis
                        </a>
                        <a href="{{ route('videos.index', ['category' => 'legends']) }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            Arsenal Legends
                        </a>
                        <a href="{{ route('videos.index', ['category' => 'youth-academy']) }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            Youth Academy
                        </a>
                        <a href="{{ route('videos.index', ['category' => 'womens-team']) }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600 rounded transition-colors">
                            Women's Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Video link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endpush 