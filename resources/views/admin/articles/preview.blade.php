<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $article->title ?: 'Untitled Article' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .prose {
            max-width: none;
        }
        
        /* Custom styles for article content headings */
        .prose h1 {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #111827;
        }

        .prose h2 {
            font-size: 1.875rem;
            font-weight: 600;
            line-height: 1.3;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #111827;
        }

        .prose h3 {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.4;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .prose h4 {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.4;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .prose h5 {
            font-size: 1.125rem;
            font-weight: 600;
            line-height: 1.4;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .prose h6 {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
            margin-top: 0.75rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .prose p {
            margin-bottom: 1rem;
            color: #374151;
            line-height: 1.7;
        }

        .prose strong {
            font-weight: 700;
            color: #111827;
        }

        .prose em {
            font-style: italic;
        }

        .prose u {
            text-decoration: underline;
        }

        .prose a {
            color: #dc2626;
            text-decoration: none;
        }

        .prose a:hover {
            text-decoration: underline;
        }

        .prose ul, .prose ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .prose li {
            margin-bottom: 0.25rem;
        }

        .prose blockquote {
            border-left: 4px solid #dc2626;
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Close Button -->
    <div class="fixed top-4 right-4 z-50">
        <button onclick="window.close()" 
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close Preview
        </button>
    </div>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div>
                            <span class="text-gray-400">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                <span class="sr-only">Utama</span>
                            </span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">Berita</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $article->category ?: 'Uncategorized' }}</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-900" aria-current="page">{{ Str::limit($article->title ?: 'Untitled Article', 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Article Header -->
            <div class="relative">
                @if($article->cover_image)
                    <img src="{{ route('article.image', basename($article->cover_image)) }}" 
                     alt="{{ $article->title ?: 'Article Image' }}" 
                     class="w-full h-64 md:h-96 object-cover">
                @else
                    <div class="w-full h-64 md:h-96 bg-gray-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6">
                    <div class="flex items-center mb-4">
                        <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            {{ $article->category ?: 'Uncategorized' }}
                        </span>
                        @if($article->is_featured)
                            <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold ml-3">
                                UTAMA
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                        {{ $article->title ?: 'Untitled Article' }}
                    </h1>
                    <div class="flex items-center text-white text-sm">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $article->formatted_published_date }}
                        <span class="mx-2">•</span>
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{ number_format($article->views_count) }} tontonan
                        <span class="mx-2">•</span>
                        <span>{{ $article->content ? ceil(str_word_count(strip_tags($article->content)) / 200) : 0 }} min baca</span>
                    </div>
                </div>
            </div>

            <!-- Article Body -->
            <div class="p-8 md:p-12">
                <!-- Excerpt -->
                @if($article->excerpt)
                    <div class="text-xl text-gray-600 leading-relaxed mb-8 border-l-4 border-red-600 pl-6 bg-gray-50 p-6 rounded-r-lg">
                        {{ $article->excerpt }}
                    </div>
                @endif

                <!-- Video Embed (if available) -->
                @if($article->youtube_video_id)
                    <div class="mb-8">
                        <div class="relative w-full" style="padding-bottom: 56.25%;">
                            <iframe 
                                class="absolute top-0 left-0 w-full h-full rounded-lg"
                                src="https://www.youtube.com/embed/{{ $article->youtube_video_id }}"
                                title="{{ $article->title ?: 'Video' }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @endif

                <!-- Article Content -->
                <div class="max-w-none article-content">
                    {!! $article->formatted_content ?: '<p class="text-gray-500 italic">No content provided yet.</p>' !!}
                </div>

                <!-- Tags -->
                @if($article->tags && is_array($article->tags) && count($article->tags) > 0)
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($article->tags as $tag)
                                <span class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Preview Notice -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Preview Mode</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This is a preview of how your article will appear to readers. Changes made here are not saved.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>
</body>
</html>
