@extends('layouts.app')

@section('title', 'Pasaran Perkhidmatan Arsenal - MyGooners')
@section('meta_description', 'Berhubung dengan peminat Arsenal dipercayai yang menawarkan perkhidmatan berkualiti. Cari latihan, pengangkutan, fotografi, dan banyak lagi dari ahli komuniti yang disahkan.')

@section('content')


<!-- Search and Filter Section -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('services.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-4 lg:space-y-0">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Perkhidmatan</label>
                    <div class="relative">
                        <input type="text" 
                               id="search"
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari perkhidmatan..." 
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

                <!-- Location Filter -->
                <div class="lg:w-48">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <select id="location" name="location" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ strtolower($loc) }}" {{ strtolower($location) === strtolower($loc) ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="w-full lg:w-auto bg-arsenal hover:bg-arsenal text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Cari
                    </button>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $category || $location)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-600">Penapis aktif:</span>
                    @if($search)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            Carian: "{{ $search }}"
                            <a href="{{ route('services.index', array_filter(['category' => $category, 'location' => $location])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if($category)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            {{ ucfirst($category) }}
                            <a href="{{ route('services.index', array_filter(['search' => $search, 'location' => $location])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    @if($location)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm flex items-center">
                            {{ ucfirst($location) }}
                            <a href="{{ route('services.index', array_filter(['search' => $search, 'category' => $category])) }}" class="ml-2 text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </a>
                        </span>
                    @endif
                    <a href="{{ route('services.index') }}" class="text-sm text-red-600 hover:text-red-800 font-medium">Kosongkan semua</a>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($services->count() > 0)
        <!-- Results Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @if($search || $category || $location)
                        Keputusan Carian ({{ $services->count() }} dijumpai)
                    @else
                        Semua Perkhidmatan ({{ $services->count() }})
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">Perkhidmatan dipercayai dari peminat Arsenal yang disahkan</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Disusun mengikut: Terkini</span>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    <!-- Service Image -->
                    <div class="relative h-48 overflow-hidden">
                        @if($service->images && is_array($service->images) && count($service->images) > 0)
                            <img src="{{ route('service.image', ['filename' => basename($service->images[0])]) }}" 
                                 alt="{{ $service->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586.58622 0 012.8280L166m-2-2l1.586.58622 0 012.828 0L2014m-6-6h0.01M6 20h12a2 20 002-2V6a2 20 00-2-2H6a2 2 00-22220                   </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                {{ $service->category }}
                            </span>
                        </div>
                        @if($service->is_verified)
                            <div class="absolute top-4 right-4">
                                <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-bold flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    DISAHKAN
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Service Content -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-xl font-bold text-gray-900 group-hover:text-red-600 transition-colors">
                                <a href="{{ route('services.show', $service->slug) }}">
                                    {{ $service->title }}
                                </a>
                            </h3>
                            <div class="text-right">
                                <div class="text-lg font-bold text-red-600">{{ $service->pricing }}</div>
                            </div>
                        </div>

                        <p class="text-gray-600 mb-4 leading-relaxed">
                            {{ Str::limit($service->description, 120) }}
                        </p>

                        <!-- Trust Score and Location -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $service->location }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ number_format($service->trust_score, 1) }}</span>
                            </div>
                        </div>

                        <!-- Service Provider -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                                         <div class="flex items-center space-x-3">
                                 @if($service->user->profile_image)
                                     @if(Str::startsWith($service->user->profile_image, 'http'))
                                         <img src="{{ $service->user->profile_image }}" 
                                              alt="{{ $service->user->name }}" 
                                              class="w-8 h-8 rounded-full object-cover">
                                     @else
                                         <img src="{{ route('profile.image', basename($service->user->profile_image)) }}" 
                                              alt="{{ $service->user->name }}" 
                                              class="w-8 h-8 rounded-full object-cover">
                                     @endif
                                 @else
                                     <img src="https://ui-avatars.com/api/?name={{ urlencode($service->user->name) }}&size=32&background=dc2626&color=fff" 
                                          alt="{{ $service->user->name }}" 
                                          class="w-8 h-8 rounded-full">
                                 @endif
                                 <div>
                                     <p class="text-sm font-medium text-gray-900">{{ $service->user->name }}</p>
                                     <p class="text-xs text-gray-500">{{ $service->created_at->diffForHumans() }}</p>
                                 </div>
                             </div>
                            <div class="flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ number_format($service->views_count) }} tontonan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More / Pagination -->
        <div class="mt-12 text-center">
            <button class="bg-arsenal hover:bg-arsenal text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Muatkan Lebih Banyak Perkhidmatan
            </button>
        </div>
    @else
        <!-- No Services Found -->
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">Tiada perkhidmatan dijumpai</h3>
            <p class="text-gray-600 mb-6">
                @if($search || $category || $location)
                    Tiada perkhidmatan yang sepadan dengan kriteria carian anda. Cuba laraskan penapis anda.
                @else
                    Tiada perkhidmatan telah disenaraikan lagi.
                @endif
            </p>
            <div class="space-x-4">
                @if($search || $category || $location)
                    <a href="{{ route('services.index') }}" class="text-red-600 hover:text-red-700 font-medium">
                        ← Lihat semua perkhidmatan
                    </a>
                @endif
                <button class="bg-arsenal hover:bg-arsenal text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Senaraikan Perkhidmatan Anda
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Trust & Safety Section -->
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Kepercayaan & Keselamatan</h2>
            <p class="text-gray-600">Bagaimana kami memastikan komuniti MyGooners selamat</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Penyedia Disahkan</h3>
                <p class="text-gray-600">Semua penyedia perkhidmatan adalah peminat Arsenal yang disahkan dengan kelayakan terbukti dan skor kepercayaan komuniti.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Ulasan Berkualiti</h3>
                <p class="text-gray-600">Sistem ulasan yang dipacu komuniti membantu anda membuat keputusan berdasarkan maklumat tentang penyedia perkhidmatan.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-red-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Komunikasi Selamat</h3>
                <p class="text-gray-600">Semua komunikasi dipermudahkan melalui platform selamat kami untuk melindungi privasi anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush 