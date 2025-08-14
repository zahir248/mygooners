@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Butiran Ulasan')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Butiran Ulasan</h1>
            <p class="mt-2 text-sm text-gray-700">Lihat maklumat lengkap ulasan produk</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.reviews.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Ulasan
            </a>
            
            <button type="button" 
                    onclick="openDeleteModal({{ $review->id }}, '{{ Str::limit($review->comment, 50) }}')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Padam Ulasan
            </button>
        </div>
    </div>
</div>

    <!-- Review Details -->
    <div class="mx-4 grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Review Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Maklumat Ulasan</h2>
                </div>
                <div class="px-6 py-4">
                    <!-- Rating -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                            <span class="ml-3 text-lg font-semibold text-gray-900">{{ $review->rating }}/5</span>
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Komen Ulasan</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dicipta Pada</label>
                            <p class="text-gray-900">{{ $review->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kemas Kini Terakhir</label>
                            <p class="text-gray-900">{{ $review->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- User Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Maklumat Pengguna</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center mb-6">
                        @if($review->user)
                            @if($review->user->profile_image)
                                @if(Str::startsWith($review->user->profile_image, 'http'))
                                    <img src="{{ $review->user->profile_image }}" 
                                         alt="{{ $review->user->name }}" 
                                         class="w-20 h-20 rounded-full mr-4 object-cover">
                                @else
                                    <img src="{{ asset('storage/' . $review->user->profile_image) }}" 
                                         alt="{{ $review->user->name }}" 
                                         class="w-20 h-20 rounded-full mr-4 object-cover">
                                @endif
                            @else
                                <div class="w-20 h-20 rounded-full mr-4 bg-red-100 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-red-600">{{ substr($review->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                <p class="text-gray-600">{{ $review->user->email }}</p>
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-full mr-4 bg-gray-100 flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-600">A</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Anonim</h4>
                                <p class="text-gray-600">Pengguna tidak berdaftar</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @if($review->user)
                            <div>
                                <span class="text-sm font-medium text-gray-700">ID Pengguna:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $review->user->id }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Menyertai:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $review->user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Status:</span>
                                <span class="text-sm text-gray-900 ml-2">{{ ucfirst($review->user->status ?? 'aktif') }}</span>
                            </div>
                        @else
                            <div>
                                <span class="text-sm font-medium text-gray-700">Jenis Pengguna:</span>
                                <span class="text-sm text-gray-900 ml-2">Pengunjung</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Status:</span>
                                <span class="text-sm text-gray-900 ml-2">Tidak berdaftar</span>
                            </div>
                        @endif
                    </div>

                    @if($review->user)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.users.show', $review->user->id) }}" 
                           class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Lihat Profil Pengguna →
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Information -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Maklumat Produk</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="mb-6">
                        @if($review->product->images && is_array($review->product->images) && count($review->product->images) > 0)
                            <img src="{{ route('product.image', basename($review->product->images[0])) }}" 
                                 alt="{{ $review->product->title }}" 
                                 class="w-full h-32 object-cover rounded-lg">
                        @else
                            <div class="w-full h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-700">ID Produk:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $review->product->id }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Kategori:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $review->product->category }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Harga:</span>
                            <span class="text-sm text-gray-900 ml-2">RM{{ number_format($review->product->price, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-700">Status:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ ucfirst($review->product->status) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.products.show', $review->product->id) }}" 
                           class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Lihat Butiran Produk →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review History (if any) -->
    @if($review->updated_at != $review->created_at)
        <div class="mx-4 mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sejarah Ulasan</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">
                            Ulasan ini dikemas kini terakhir pada {{ $review->updated_at->format('F d, Y \a\t g:i A') }}.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
            deleteForm.action = `/admin/reviews/${reviewId}`;
            
            // Set the comment text
            reviewCommentSpan.textContent = comment;
            
            // Show the modal
            deleteModal.classList.remove('hidden');
        }
    </script>
</div>
@endsection
