@extends('layouts.app')

@section('title', 'Kedai Arsenal - Barangan Rasmi - MyGooners')
@section('meta_description', 'Beli barangan Arsenal rasmi, jersi, aksesori, dan item eksklusif. Dapatkan peralatan Arsenal terkini dan sokong Gunners dengan gaya.')

@section('content')
<!-- Success Message Display -->
<div id="success-message" class="fixed top-20 right-4 z-50 hidden">
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" id="success-message-text"></p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="hideSuccessMessage()" class="inline-flex text-green-400 hover:text-green-600 focus:outline-none focus:text-green-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Login Required Modal -->
<div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Log Masuk Diperlukan</h3>
            <p class="text-gray-600 text-center mb-6">
                Anda perlu log masuk terlebih dahulu untuk menambah item ke troli. Sila log masuk untuk meneruskan.
            </p>
            <div class="flex space-x-3">
                <button onclick="hideLoginModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button onclick="goToLogin()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Log Masuk
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Search and Filter Section -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('shop.index') }}" method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-end lg:space-x-4 space-y-4 lg:space-y-0">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                    <div class="relative">
                        <input type="text" 
                               id="search"
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari jersi, aksesori..." 
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

                <!-- Sort Filter -->
                <div class="lg:w-48">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Susun Mengikut</label>
                    <select id="sort" name="sort" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Terbaru Dahulu</option>
                        <option value="popular" {{ $sort === 'popular' ? 'selected' : '' }}>Paling Popular</option>
                        <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="rating" {{ $sort === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="w-full lg:w-auto bg-arsenal hover:bg-arsenal text-white px-8 py-3 rounded-lg font-medium transition-colors">
                        Gunakan Penapis
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if($products->count() > 0)
        <!-- Results Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @if($search || $category)
                        Hasil Carian ({{ $products->count() }} dijumpai)
                    @else
                        Semua Produk ({{ $products->count() }})
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">Barangan Arsenal rasmi dan item eksklusif</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Disusun mengikut: {{ ucfirst(str_replace('_', ' ', $sort)) }}</span>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    <!-- Product Image -->
                    <div class="relative h-64 overflow-hidden">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ route('product.image', basename($product->images[0])) }}" 
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
                        <!-- Quick Actions -->
                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="bg-white hover:bg-gray-100 text-gray-700 p-2 rounded-full shadow-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>
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
                        <div class="flex items-center mb-4 text-sm">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-gray-600">({{ $product->reviews_count }} ulasan)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($product->sale_price)
                                    <span class="text-lg font-bold text-red-600 mr-2">RM{{ number_format($product->sale_price, 2) }}</span>
                                    <span class="text-sm line-through text-gray-400">RM{{ number_format($product->price, 2) }}</span>
                                @else
                                    <span class="text-lg font-bold text-gray-900">RM{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="quickAddToCart({{ $product->id }})" 
                                        class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <svg class="w-4 h-4 text-white mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                    <span class="text-xs">Tambah</span>
                                </button>
                                <a href="{{ route('shop.show', $product->slug) }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links('vendor.pagination.tailwind') }}
        </div>
    @else
        <div class="text-center py-24">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h3m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v4"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Tiada produk dijumpai</h3>
            <p class="mt-1 text-gray-500">Cuba ubah penapis carian atau semak ejaan anda.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function quickAddToCart(productId) {
    // Check if user is logged in
    @auth
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                variation_id: null,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store success message in session storage
                sessionStorage.setItem('cartSuccessMessage', data.message);
                // Refresh the page
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ralat semasa menambah ke troli');
        });
    @else
        // User is not logged in, show login modal
        showLoginModal();
    @endauth
}

function showLoginModal() {
    const modal = document.getElementById('login-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideLoginModal() {
    const modal = document.getElementById('login-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function goToLogin() {
    window.location.href = '{{ route("login") }}';
}



function showSuccessMessage(message) {
    const successMessage = document.getElementById('success-message');
    const messageText = document.getElementById('success-message-text');
    
    messageText.textContent = message;
    successMessage.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        hideSuccessMessage();
    }, 5000);
}

function hideSuccessMessage() {
    const successMessage = document.getElementById('success-message');
    successMessage.classList.add('hidden');
}

// Check for success message on page load
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = sessionStorage.getItem('cartSuccessMessage');
    if (successMessage) {
        showSuccessMessage(successMessage);
        sessionStorage.removeItem('cartSuccessMessage');
    }
    
    // Add click outside functionality for login modal
    const loginModal = document.getElementById('login-modal');
    loginModal.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            hideLoginModal();
        }
    });
    
    // Add escape key functionality
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideLoginModal();
        }
    });
});
</script>
@endpush 