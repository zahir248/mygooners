@extends('layouts.app')

@section('title', $service->title . ' - Arsenal Services Marketplace - MyGooners')
@section('meta_description', $service->description)

@section('meta_tags')
<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $service->title }} - Arsenal Services Marketplace">
<meta property="og:description" content="{{ $service->description }}">
<meta property="og:image" content="{{ $service->images[0] }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->url() }}">
<meta property="og:site_name" content="MyGooners">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $service->title }} - Arsenal Services Marketplace">
<meta name="twitter:description" content="{{ $service->description }}">
<meta name="twitter:image" content="{{ $service->images[0] }}">

<!-- Additional Meta Tags -->
<meta name="keywords" content="Arsenal, {{ implode(', ', $service->tags) }}, services, community">
<meta name="author" content="{{ $service->user->name }}">
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
            <a href="{{ route('services.index') }}" class="hover:text-red-600 transition-colors">Perkhidmatan</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ $service->title }}</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Service Images -->
            <div class="mb-8">
                <div class="relative h-96 rounded-xl overflow-hidden bg-gray-200 mb-4">
                    @if($service->images && is_array($service->images) && count($service->images) > 0)
                        <img src="{{ route('service.image', ['filename' => basename($service->images[0])]) }}" 
                             alt="{{ $service->title }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586.58622 0 012.8280L166m-2-2l1.586.58622 0 012.828 0L2014m-6-6h0.01M6 20h12a2 20 002-2V6a2 20 00-2-2H6a2 2 00-22220                   </svg>
                        </div>
                    @endif
                    @if($service->is_verified)
                        <div class="absolute top-4 right-4">
                            <span class="bg-green-100 text-green-600 px-3 py-2 rounded-full text-sm font-bold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                PENYEDIA DISAHKAN
                            </span>
                        </div>
                    @endif
                </div>
                
                @if(count($service->images) > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(array_slice($service->images, 1) as $image)
                            <div class="aspect-square rounded-lg overflow-hidden bg-gray-200">
                                <img src="{{ route('service.image', ['filename' => basename($image)]) }}" 
                                     alt="{{ $service->title }}" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Service Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $service->category }}
                    </span>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{ number_format($service->views_count) }} tontonan
                    </div>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $service->title }}</h1>
                <div class="flex items-center space-x-4 text-gray-600">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $service->location }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <span class="font-medium text-gray-900">{{ number_format($service->trust_score, 1) }}</span>
                        <span class="text-gray-500 ml-1">({{ $reviews->count() }} ulasan)</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Disenarai {{ $service->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            <!-- Service Description -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Mengenai Perkhidmatan Ini</h2>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {{ $service->description }}
                </div>
                
                @if($service->tags)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($service->tags as $tag)
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Reviews Section -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Ulasan Pelanggan</h2>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $service->trust_score ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <span class="text-lg font-semibold text-gray-900">{{ number_format($service->trust_score, 1) }}</span>
                        <span class="text-gray-500">({{ $reviews->count() }} ulasan)</span>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex items-start space-x-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&size=40&background=dc2626&color=fff" 
                                     alt="{{ $review->user->name }}" 
                                     class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <h4 class="font-medium text-gray-900">{{ $review->user->name }}</h4>
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

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <button class="text-red-600 hover:text-red-700 font-medium">
                        Lihat Semua Ulasan →
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Contact Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="text-3xl font-bold text-red-600 mb-2">RM {{ $service->pricing }}</div>
                    <p class="text-gray-600">Harga permulaan</p>
                </div>

                <!-- Provider Info -->
                <div class="flex items-center space-x-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($service->user->name) }}&size=60&background=dc2626&color=fff" 
                         alt="{{ $service->user->name }}" 
                         class="w-15 h-15 rounded-full">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">{{ $service->user->name }}</h3>
                        @if($service->user->is_verified)
                            <div class="flex items-center text-sm text-green-600 mb-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Penyedia Disahkan
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span>{{ number_format($service->user->trust_score, 1) }} rating</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ $service->user->location }}</p>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <button class="w-full bg-arsenal hover:bg-arsenal text-white py-3 px-4 rounded-lg font-medium transition-colors">
                        Hubungi Penyedia
                    </button>
                    <button class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Hantar Mesej
                    </button>
                </div>

                <!-- Provider Bio -->
                @if($service->user->bio)
                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-2">Mengenai Penyedia</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $service->user->bio }}</p>
                    </div>
                @endif
            </div>

            <!-- Share Options -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Kongsi Perkhidmatan Ini</h3>
                <div class="flex space-x-3">
                    <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                        Facebook
                    </button>
                    <button onclick="shareOnTwitter()" class="flex-1 bg-sky-500 hover:bg-sky-600 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                        Twitter
                    </button>
                    <button onclick="shareOnLinkedIn()" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                        LinkedIn
                    </button>
                </div>
                <button onclick="copyToClipboard()" class="w-full mt-3 border border-gray-300 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                    Salin Pautan
                </button>
            </div>

            <!-- Report/Safety -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="font-bold text-gray-900 mb-4">Keselamatan Dahulu</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <p>• Sentiasa bertemu di tempat awam</p>
                    <p>• Sahkan kelayakan penyedia</p>
                    <p>• Gunakan sistem mesej platform dahulu</p>
                    <p>• Laporkan aktiviti yang mencurigakan</p>
                </div>
                <button class="w-full mt-4 text-red-600 hover:text-red-700 text-sm font-medium">
                    Laporkan Penyenaraian Ini
                </button>
            </div>
        </div>
    </div>

    <!-- Related Services -->
    <div class="mt-16">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Perkhidmatan Serupa</h2>
            <p class="text-gray-600">Perkhidmatan lain yang mungkin anda minati</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($relatedServices as $relatedService)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow overflow-hidden">
                    <div class="relative h-48">
                        @if($relatedService->images && is_array($relatedService->images) && count($relatedService->images) > 0)
                            <img src="{{ route('service.image', ['filename' => basename($relatedService->images[0])]) }}" 
                                 alt="{{ $relatedService->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586.58622 0 012.8280L166m-2-2l1.586.58622 0 012.828 0L2014m-6-6h01M6 20h12a220 002-2V6a22002                   </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $relatedService->category }}
                            </span>
                        </div>
                        @if($relatedService->is_verified)
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-bold">
                                    DISAHKAN
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">
                            <a href="{{ route('services.show', $relatedService->slug) }}" class="hover:text-red-600 transition-colors">
                                {{ $relatedService->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-4">{{ Str::limit($relatedService->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-bold text-red-600">RM {{ $relatedService->pricing }}</div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span>{{ number_format($relatedService->trust_score, 1) }}</span>
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
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $service->title }} - Arsenal Services Marketplace');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out this service from a fellow Arsenal fan: {{ $service->title }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
}

function shareOnLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $service->title }} - Arsenal Services Marketplace');
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank', 'width=600,height=400');
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Link copied to clipboard!');
    });
}
</script>
@endpush 