@extends('layouts.admin')

@section('title', 'Detail Ulasan Perkhidmatan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Ulasan Perkhidmatan</h1>
                <p class="mt-2 text-sm text-gray-700">Lihat maklumat lengkap ulasan perkhidmatan</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.service-reviews.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Review Details -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Ulasan #{{ $review->id }}
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Maklumat lengkap ulasan perkhidmatan
            </p>
        </div>
        
        <div class="border-t border-gray-200">
            <dl>
                <!-- User Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Pengguna</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center space-x-3">
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
                            <div>
                                <div class="font-medium">{{ $review->user ? $review->user->name : 'Anonim' }}</div>
                                <div class="text-sm text-gray-500">{{ $review->user ? $review->user->email : 'Pengguna tidak berdaftar' }}</div>
                            </div>
                        </div>
                    </dd>
                </div>

                <!-- Service Information -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Perkhidmatan</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 h-20 w-32">
                                @if($review->service->images && count($review->service->images) > 0)
                                    <img class="h-20 w-32 rounded-lg object-cover" 
                                         src="{{ route('service.image', basename($review->service->images[0])) }}" 
                                         alt="{{ $review->service->title }}">
                                @else
                                    <div class="h-20 w-32 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="font-medium">{{ $review->service->title }}</div>
                                <div class="text-sm text-gray-500">
                                    ID: #{{ $review->service->id }} • {{ $review->service->category }} • {{ $review->service->location }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $review->service->pricing }}</div>
                            </div>
                        </div>
                    </dd>
                </div>

                <!-- Rating -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Rating</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="h-6 w-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endif
                            @endfor
                            <span class="ml-3 text-lg font-medium text-gray-900">{{ $review->rating }}/5</span>
                        </div>
                    </dd>
                </div>

                <!-- Comment -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Komen</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="whitespace-pre-wrap">{{ $review->comment }}</p>
                        </div>
                    </dd>
                </div>

                <!-- Status -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        @if($review->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Diluluskan
                            </span>
                        @elseif($review->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Menunggu
                            </span>
                        @endif
                    </dd>
                </div>

                <!-- Dates -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Tarikh</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <div><strong>Dicipta:</strong> {{ $review->created_at->format('j F Y, H:i') }}</div>
                            <div><strong>Dikemas kini:</strong> {{ $review->updated_at->format('j F Y, H:i') }}</div>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Tindakan</h3>
            <div class="flex flex-wrap gap-3">
                @if($review->status === 'pending')
                    <form action="{{ route('admin.service-reviews.approve', $review) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Luluskan Ulasan
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.service-reviews.reject', $review) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak Ulasan
                        </button>
                    </form>
                @endif

                <button type="button" 
                        onclick="openDeleteModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Padam Ulasan
                </button>
            </div>
        </div>
    </div>
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
                    <p class="text-sm text-gray-800 mt-1" id="reviewComment">{{ Str::limit($review->comment, 100) }}</p>
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
                <form action="{{ route('admin.service-reviews.destroy', $review) }}" method="POST" class="inline">
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
});

// Function to open delete modal
function openDeleteModal() {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.classList.remove('hidden');
}
</script>
@endpush
