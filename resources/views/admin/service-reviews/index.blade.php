@extends('layouts.admin')

@section('title', 'Pengurusan Ulasan Perkhidmatan')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengurusan Ulasan Perkhidmatan</h1>
            <p class="mt-2 text-sm text-gray-700">Urus semua ulasan perkhidmatan Arsenal yang diterima</p>
        </div>
        <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.service-reviews.statistics') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            Lihat Statistik
        </a>
    </div>
    </div>
            </div>
            
<!-- Filters and Search -->
<div class="mx-4 bg-white shadow rounded-lg mb-6">
    <form method="GET" action="{{ route('admin.service-reviews.index') }}" class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Tapis Ulasan</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari ulasan..."
                           value="{{ request('search') }}"
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Service Filter -->
                <select name="service_id" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Perkhidmatan</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ Str::limit($service->title, 30) }}
                        </option>
                    @endforeach
                </select>
                <!-- Rating Filter -->
                <select name="rating" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Rating</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
                </select>
                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                    Tapis
                </button>
                    <a href="{{ route('admin.service-reviews.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                        Reset
                </a>
            </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Reviews Table -->
<div class="mx-4 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Ulasan ({{ $reviews->total() }})
            </h3>
            @if(request('search') || request('service_id') || request('rating'))
                <div class="mt-2 sm:mt-0">
                    <p class="text-sm text-gray-600">
                        Tapisan aktif:
                        @if(request('search'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                Cari: "{{ request('search') }}"
                            </span>
                        @endif
                        @if(request('service_id'))
                            @php
                                $selectedService = $services->firstWhere('id', request('service_id'));
                            @endphp
                            @if($selectedService)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1">
                                    Perkhidmatan: {{ Str::limit($selectedService->title, 20) }}
                                </span>
                            @endif
                        @endif
                        @if(request('rating'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-1">
                                Rating: {{ request('rating') }} Bintang
                            </span>
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
    
    @if($reviews->total() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Perkhidmatan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rating
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Komen
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarikh
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Tindakan</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($review->user && $review->user->profile_image)
                                            @if(Str::startsWith($review->user->profile_image, 'http'))
                                                <img src="{{ $review->user->profile_image }}" 
                                                     alt="{{ $review->user->name }}" 
                                                     class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <img src="{{ asset('storage/' . $review->user->profile_image) }}" 
                                             alt="{{ $review->user->name }}" 
                                                     class="h-10 w-10 rounded-full object-cover">
                                            @endif
                                        @elseif($review->user)
                                            <div class="h-10 w-10 rounded-full bg-red-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ substr($review->user->name, 0, 1) }}</span>
                                        </div>
                                    @else
                                            <div class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">A</span>
                                        </div>
                                    @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="text-sm font-bold text-gray-900 truncate">
                                                {{ $review->user ? $review->user->name : 'Anonim' }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500 line-clamp-2">
                                            {{ $review->user ? $review->user->email : 'Pengguna tidak berdaftar' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 h-16 w-24">
                                        @if($review->service->images && count($review->service->images) > 0)
                                            <img class="h-16 w-24 rounded-lg object-cover" src="{{ route('service.image', basename($review->service->images[0])) }}" alt="{{ $review->service->title }}">
                                        @else
                                            <div class="h-16 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <p class="text-sm font-bold text-gray-900 truncate">
                                                {{ $review->service->title }}
                                            </p>
                                        </div>
                                        <p class="text-sm text-gray-500 line-clamp-2">
                                            ID Perkhidmatan: #{{ $review->service->id }} • {{ $review->service->category }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                            @endif
                                    @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $review->rating }}/5</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    {{ Str::limit($review->comment, 100) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>
                                    <div class="font-medium">{{ $review->created_at->format('j M Y') }}</div>
                                    <div class="text-xs">{{ $review->created_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <!-- View Review -->
                                    <a href="{{ route('admin.service-reviews.show', $review) }}" 
                                       class="text-blue-600 hover:text-blue-900"
                                       title="Lihat Ulasan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <!-- Approve/Reject Actions -->
                                    @if($review->status === 'pending')
                                        <form action="{{ route('admin.service-reviews.approve', $review) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Luluskan Ulasan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.service-reviews.reject', $review) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900"
                                                    title="Tolak Ulasan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Delete Review -->
                                    <button type="button" 
                                            onclick="openDeleteModal({{ $review->id }}, '{{ Str::limit($review->comment, 50) }}')"
                                            class="text-red-600 hover:text-red-900"
                                            title="Padam Ulasan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
    </div>

    <!-- Pagination -->
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $reviews->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $reviews->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $reviews->total() }}</span> results
                </div>
                <div class="flex space-x-2">
                    @if($reviews->onFirstPage())
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Previous
                        </button>
                    @else
                        <a href="{{ $reviews->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($reviews->hasMorePages())
                        <a href="{{ $reviews->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Next
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada ulasan</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request('service_id') || request('rating') || request('search'))
                    Tiada ulasan dijumpai yang sepadan dengan kriteria tapisan anda.
                    @if(request('service_id'))
                        <br><span class="text-red-600">Perkhidmatan ID: {{ request('service_id') }}</span>
                    @endif
                    @if(request('rating'))
                        <br><span class="text-red-600">Rating: {{ request('rating') }} Bintang</span>
                    @endif
                    @if(request('search'))
                        <br><span class="text-red-600">Carian: "{{ request('search') }}"</span>
                    @endif
                @else
                    Tiada ulasan dijumpai dalam sistem.
                @endif
            </p>
            @if(request('service_id') || request('rating') || request('search'))
                <div class="mt-4">
                    <a href="{{ route('admin.service-reviews.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reset Semua Tapisan
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <!-- Modal Title -->
            <h3 class="text-lg font-medium text-gray-900 mt-4">Padam Ulasan</h3>
            
            <!-- Modal Content -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu memadamkan ulasan ini?
                </p>
                <div class="mt-3 bg-gray-50 p-3 rounded-md">
                    <p class="text-xs text-gray-600 font-medium">Komen:</p>
                    <p class="text-sm text-gray-800 mt-1" id="reviewComment"></p>
                </div>
                <p class="text-xs text-red-600 mt-2">
                    Tindakan ini tidak boleh dibatalkan.
                </p>
            </div>
            
            <!-- Modal Actions -->
            <div class="flex items-center justify-center gap-3 mt-4">
                <button id="cancelDelete" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                        Padam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const deleteForm = document.getElementById('deleteForm');
    const reviewCommentSpan = document.getElementById('reviewComment');
    
    // Close modal when clicking cancel
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
        }
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            deleteModal.classList.add('hidden');
        }
    });
    
    // Handle form submission
    deleteForm.addEventListener('submit', function(e) {
        // Form will submit normally to the delete route
        // The modal will be hidden by the page refresh/redirect
        });
    });

// Function to open delete modal
function openDeleteModal(reviewId, comment) {
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const reviewCommentSpan = document.getElementById('reviewComment');
    
    // Set the form action
    deleteForm.action = `/admin/service-reviews/${reviewId}`;
    
    // Set the comment text
    reviewCommentSpan.textContent = comment;
    
    // Show the modal
    deleteModal.classList.remove('hidden');
}
</script>
@endpush
