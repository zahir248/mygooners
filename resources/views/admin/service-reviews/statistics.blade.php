@extends('layouts.admin')

@section('title', 'Statistik Ulasan Perkhidmatan')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Statistik Ulasan Perkhidmatan</h1>
            <p class="mt-2 text-sm text-gray-700">Analisis mendalam prestasi perkhidmatan berdasarkan ulasan pelanggan</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.service-reviews.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Ulasan
            </a>
        </div>
    </div>
</div>

    <!-- Overview Cards -->
    <div class="mx-4 bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Ringkasan Statistik</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Perkhidmatan Direview</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['services_with_reviews'] ?? 0) }}</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Purata Rating</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}/5</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Perkhidmatan Terbaik</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['top_rated_services'] ?? 0) }}</p>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Jumlah Ulasan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_reviews']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analysis -->
    <div class="mx-4 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Rating Distribution Chart -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Agihan Rating</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    @foreach($stats['reviews_by_rating'] as $rating => $count)
                        @php
                            $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                            $colorClass = $rating >= 4 ? 'bg-green-500' : ($rating >= 3 ? 'bg-yellow-500' : 'bg-red-500');
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-700">{{ $rating }} Bintang</span>
                                    <span class="ml-2 text-sm text-gray-500">({{ $count }})</span>
                                </div>
                                <span class="text-sm text-gray-900">{{ number_format($percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Service Analysis -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-100 text-blue-600 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Analisis Perkhidmatan</h3>
                </div>
            </div>
            
            <div class="px-6 py-4">
                @if($stats['total_reviews'] > 0)
                    <div class="space-y-4">
                        <!-- Service Coverage -->
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Liputan Perkhidmatan</span>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">{{ number_format($stats['services_with_reviews'] ?? 0) }} Perkhidmatan</span>
                        </div>
                        
                        <!-- Customer Satisfaction -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Kepuasan Pelanggan</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600">{{ number_format($stats['average_rating'], 1) }}/5.0</span>
                        </div>
                        
                        <!-- Quality Performance -->
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Perkhidmatan Berkualiti Tinggi</span>
                            </div>
                            <span class="text-sm font-semibold text-yellow-600">{{ number_format($stats['top_rated_services'] ?? 0) }} Perkhidmatan</span>
                        </div>
                        
                        <!-- Excellence Indicator -->
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-700">Indikator Kecemerlangan</span>
                            </div>
                            <span class="text-sm font-semibold text-purple-600">{{ number_format($stats['reviews_by_rating'][5] ?? 0) }} Ulasan 5★</span>
                        </div>
                    </div>
                    
                    <!-- Summary Statement -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <strong>Ringkasan:</strong> Portfolio perkhidmatan menunjukkan prestasi yang memberangsangkan dengan 
                            {{ number_format($stats['average_rating'], 1) }} bintang purata. 
                            {{ number_format($stats['top_rated_services'] ?? 0) }} perkhidmatan mendapat pengiktirafan tinggi, 
                            mencerminkan komitmen terhadap kualiti dan kepuasan pelanggan.
                        </p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Tiada Data Ulasan</h4>
                        <p class="text-sm text-gray-500">Belum ada perkhidmatan yang telah direview oleh pelanggan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mx-4 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tindakan Pantas</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.service-reviews.index') }}" 
                   class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-3 rounded-lg text-center transition-colors">
                    <div class="text-lg font-semibold">{{ $stats['total_reviews'] }}</div>
                    <div class="text-sm">Semua Ulasan</div>
                </a>
                
                <a href="{{ route('admin.services.index') }}" 
                   class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-3 rounded-lg text-center transition-colors">
                    <div class="text-lg font-semibold">{{ $stats['total_services'] ?? 0 }}</div>
                    <div class="text-sm">Semua Perkhidmatan</div>
                </a>
                
                <a href="{{ route('admin.service-reviews.index', ['rating' => '5']) }}" 
                   class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-center transition-colors">
                    <div class="text-lg font-semibold">{{ $stats['reviews_by_rating'][5] ?? 0 }}</div>
                    <div class="text-sm">Ulasan 5 Bintang</div>
                </a>
                
                <a href="{{ route('admin.service-reviews.index', ['rating' => '1']) }}" 
                   class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-3 rounded-lg text-center transition-colors">
                    <div class="text-lg font-semibold">{{ $stats['reviews_by_rating'][1] ?? 0 }}</div>
                    <div class="text-sm">Ulasan 1 Bintang</div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
