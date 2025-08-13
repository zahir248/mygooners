@extends('layouts.app')

@section('title', 'Kegemaran Saya')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Kegemaran Saya</h1>
            <p class="mt-2 text-gray-600">Produk dan perkhidmatan yang anda simpan</p>
        </div>

        @if($favourites->count() > 0)
            <!-- Favourites Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($favourites as $product)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                        <!-- Product Image -->
                        <div class="relative h-64 overflow-hidden">
                            @if($product->images && count($product->images) > 0)
                                <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                     alt="{{ $product->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 space-y-2">
                                @if($product->sale_price)
                                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                                    </span>
                                @endif
                                @if($product->is_featured)
                                    <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">
                                        UTAMA
                                    </span>
                                @endif
                            </div>
                            @if($product->stock_quantity <= 5)
                                <div class="absolute bottom-3 left-3">
                                    <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        Hanya {{ $product->stock_quantity }} tinggal
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Remove from Favourites Button -->
                            <button onclick="removeFromFavourites({{ $product->id }}, this)" 
                                    class="absolute top-3 right-3 bg-white hover:bg-gray-100 text-red-500 p-2 rounded-full shadow-lg transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Product Content -->
                        <div class="p-5">
                            <div class="mb-2">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $product->category }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                                <a href="{{ route('shop.show', $product->slug) }}">
                                    {{ $product->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                {{ Str::limit($product->description, 100) }}
                            </p>

                            <!-- Rating and Reviews -->
                            @if($product->reviews && $product->reviews->count() > 0)
                                <div class="flex items-center mb-4 text-sm">
                                    @php
                                        $averageRating = $product->reviews->avg('rating');
                                        $ratingCount = $product->reviews->count();
                                    @endphp
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $averageRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-gray-600">({{ $ratingCount }} ulasan)</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <div>
                                    @if($product->sale_price)
                                        <span class="text-lg font-bold text-red-600 mr-2">RM{{ number_format($product->sale_price, 2) }}</span>
                                        <span class="text-sm line-through text-gray-400">RM{{ number_format($product->price, 2) }}</span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex justify-end">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favourites->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada kegemaran</h3>
                <p class="mt-1 text-sm text-gray-500">Mula tambah produk ke kegemaran anda untuk melihatnya di sini.</p>
                <div class="mt-6">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Lihat Produk
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

        <!-- Remove from Favourites Modal -->
        <div id="removeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <!-- Modal Icon -->
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    
                    <!-- Modal Title -->
                    <h3 class="text-lg font-medium text-gray-900 mt-4">Keluarkan dari Kegemaran</h3>
                    
                    <!-- Modal Message -->
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Adakah anda pasti mahu mengeluarkan produk ini dari senarai kegemaran anda?
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            Tindakan ini tidak boleh dibatalkan.
                        </p>
                    </div>
                    
                    <!-- Modal Actions -->
                    <div class="flex justify-center space-x-3 mt-6">
                        <button id="cancelRemove" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                            Batal
                        </button>
                        <button id="confirmRemove" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                            Ya, Keluarkan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        let currentProductId = null;
        let currentRemoveButton = null;

        function removeFromFavourites(productId, button) {
            currentProductId = productId;
            currentRemoveButton = button;
            
            // Show modal
            const modal = document.getElementById('removeModal');
            modal.classList.remove('hidden');
            
            // Add backdrop click to close
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        function closeModal() {
            const modal = document.getElementById('removeModal');
            modal.classList.add('hidden');
            currentProductId = null;
            currentRemoveButton = null;
        }

        function confirmRemove() {
            if (!currentProductId) return;
            
            fetch(`/favourites/remove`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: currentProductId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the product card from the DOM
                    if (currentRemoveButton) {
                        const productCard = currentRemoveButton.closest('.bg-white.rounded-xl');
                        if (productCard) {
                            productCard.style.opacity = '0';
                            productCard.style.transform = 'scale(0.9)';
                            setTimeout(() => {
                                productCard.remove();
                                // Check if no more products
                                const remainingProducts = document.querySelectorAll('.bg-white.rounded-xl');
                                if (remainingProducts.length === 0) {
                                    location.reload(); // Reload to show empty state
                                }
                            }, 300);
                        }
                    }
                    closeModal();
                    
                    // Show success message
                    showSuccessMessage('Produk berjaya dikeluarkan dari kegemaran');
                } else {
                    showErrorMessage(data.message || 'Ralat mengeluarkan produk dari kegemaran');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Ralat mengeluarkan produk dari kegemaran');
            });
        }

        function showSuccessMessage(message) {
            // Create success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 z-50 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm';
            notification.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        function showErrorMessage(message) {
            // Create error notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-20 right-4 z-50 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-sm';
            notification.innerHTML = `
                <div class="flex items-start">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        function addToCart(productId) {
            fetch(`/cart/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Produk berjaya ditambah ke troli!');
                } else {
                    showErrorMessage(data.message || 'Ralat menambah produk ke troli');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Ralat menambah produk ke troli');
            });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Cancel button
            document.getElementById('cancelRemove').addEventListener('click', closeModal);
            
            // Confirm button
            document.getElementById('confirmRemove').addEventListener('click', confirmRemove);
            
            // ESC key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });
        });
        </script>
@endsection 