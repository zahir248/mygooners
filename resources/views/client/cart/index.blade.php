@extends('layouts.app')

@section('title', 'Troli Beli-Belah - MyGooners')
@section('meta_description', 'Troli beli-belah anda di MyGooners. Semak dan kemas kini item anda sebelum membuat pembelian.')

@push('styles')
<style>
    .scale-110 {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }
</style>
@endpush

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

<!-- Remove Item Modal -->
<div id="remove-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Keluarkan Item</h3>
            <p class="text-gray-600 text-center mb-6" id="remove-modal-text">
                Adakah anda pasti mahu mengeluarkan item ini dari troli?
            </p>
            <div class="flex space-x-3">
                <button onclick="hideRemoveModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button onclick="confirmRemoveItem()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Keluarkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cart Modal -->
<div id="clear-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Kosongkan Troli</h3>
            <p class="text-gray-600 text-center mb-6">
                Adakah anda pasti mahu mengosongkan semua item dalam troli? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex space-x-3">
                <button onclick="hideClearModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button onclick="confirmClearCart()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                    Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.index') }}" class="hover:text-red-600 transition-colors">Kedai</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Troli Beli-Belah</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if($cart->items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h1 class="text-2xl font-bold text-gray-900">Troli Beli-Belah</h1>
                        <p class="text-gray-600 mt-1">{{ $cart->item_count }} item dalam troli</p>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($cart->items as $item)
                            <div class="p-6" data-item-id="{{ $item->id }}">
                                <div class="flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-200">
                                            @if($item->variation && $item->variation->images && count($item->variation->images) > 0)
                                                <!-- Show variation image if available -->
                                                @php
                                                    \Log::info('Variation image found:', [
                                                        'variation_id' => $item->variation->id,
                                                        'variation_name' => $item->variation->name,
                                                        'images' => $item->variation->images
                                                    ]);
                                                @endphp
                                                <img src="{{ route('variation.image', basename($item->variation->images[0])) }}" 
                                                     alt="{{ $item->variation->name }}" 
                                                     class="w-full h-full object-cover">
                                            @elseif($item->product->images && count($item->product->images) > 0)
                                                @php
                                                    \Log::info('Using product image as fallback:', [
                                                        'variation_id' => $item->variation ? $item->variation->id : 'null',
                                                        'variation_name' => $item->variation ? $item->variation->name : 'null',
                                                        'product_images' => $item->product->images
                                                    ]);
                                                @endphp
                                                <!-- Fallback to product image -->
                                                <img src="{{ route('product.image', basename($item->product->images[0])) }}" 
                                                     alt="{{ $item->product->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                @php
                                                    \Log::info('No images found for cart item:', [
                                                        'item_id' => $item->id,
                                                        'variation_id' => $item->variation ? $item->variation->id : 'null',
                                                        'variation_name' => $item->variation ? $item->variation->name : 'null',
                                                        'variation_images' => $item->variation ? $item->variation->images : 'null',
                                                        'product_images' => $item->product->images
                                                    ]);
                                                @endphp
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    <a href="{{ route('shop.show', $item->product->slug) }}" class="hover:text-red-600 transition-colors">
                                                        {{ $item->display_name }}
                                                    </a>
                                                </h3>
                                                <p class="text-sm text-gray-500 mt-1">
                                                    {{ $item->product->category }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-gray-900">RM{{ number_format($item->price, 2) }}</p>
                                                <p class="text-sm text-gray-500">seunit</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Quantity Controls -->
                                        <div class="flex items-center justify-between mt-4">
                                            <div class="flex items-center space-x-3">
                                                <label class="text-sm font-medium text-gray-700">Kuantiti:</label>
                                                <div class="flex items-center border border-gray-300 rounded-lg">
                                                    <button onclick="updateQuantity({{ $item->id }}, -1)" 
                                                            class="px-3 py-1 text-gray-600 hover:text-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <span class="px-3 py-1 text-sm font-medium quantity-display">{{ $item->quantity }}</span>
                                                    <button onclick="updateQuantity({{ $item->id }}, 1)" 
                                                            class="px-3 py-1 text-gray-600 hover:text-red-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-red-600 item-subtotal">RM{{ number_format($item->subtotal, 2) }}</p>
                                                    <p class="text-sm text-gray-500">Jumlah</p>
                                                </div>
                                                <button onclick="removeItem({{ $item->id }})" 
                                                        class="text-red-600 hover:text-red-800 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Cart Actions -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button onclick="clearCart()" 
                                        class="text-red-600 hover:text-red-800 font-medium transition-colors">
                                    Kosongkan Troli
                                </button>
                                @auth
                                    <a href="{{ route('checkout.orders') }}" 
                                       class="text-gray-600 hover:text-red-600 font-medium transition-colors">
                                        Pesanan Saya
                                    </a>
                                @endauth
                            </div>
                            <a href="{{ route('shop.index') }}" 
                               class="text-gray-600 hover:text-red-600 font-medium transition-colors">
                                Teruskan Membeli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-24">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Item:</span>
                            <span class="font-medium" id="order-summary-item-count">{{ $cart->item_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Harga:</span>
                            <span class="font-medium" id="order-summary-subtotal">RM{{ number_format($cart->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Penghantaran:</span>
                            <span class="font-medium text-green-600">Percuma</span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Jumlah Keseluruhan:</span>
                                <span class="text-red-600" id="order-summary-total">RM{{ number_format($cart->total, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="space-y-3 pt-4">
                            <a href="{{ route('checkout.index') }}" 
                               class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-bold transition-colors inline-block text-center">
                                Teruskan ke Pembayaran
                            </a>
                        </div>
                        
                        <!-- Security Badge -->
                        <div class="mt-6 p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-green-700 font-medium">Pembayaran Selamat</span>
                            </div>
                            <p class="text-xs text-green-600 mt-1">Data anda dilindungi dengan enkripsi SSL</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Troli Anda Kosong</h2>
            <p class="text-gray-600 mb-8">Nampaknya anda belum menambah sebarang item ke troli anda.</p>
            <div class="space-x-4">
                <a href="{{ route('shop.index') }}" 
                   class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition-colors">
                    Mulakan Membeli
                </a>
                <a href="{{ route('home') }}" 
                   class="inline-block border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-8 py-3 rounded-lg font-bold transition-colors">
                    Kembali ke Utama
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function updateQuantity(itemId, change) {
    const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
    const quantityDisplay = itemElement.querySelector('.quantity-display');
    const currentQuantity = parseInt(quantityDisplay.textContent);
    const newQuantity = currentQuantity + change;
    
    if (newQuantity < 1 || newQuantity > 10) {
        return;
    }
    
    // Disable quantity buttons during update
    const quantityButtons = itemElement.querySelectorAll('button[onclick*="updateQuantity"]');
    quantityButtons.forEach(button => button.disabled = true);
    
    fetch(`{{ url('cart/update') }}/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            quantityDisplay.textContent = newQuantity;
            itemElement.querySelector('.item-subtotal').textContent = 'RM' + data.item_subtotal;
            updateCartCount(data.cart_count);
            updateCartTotal(data.cart_total);
            updateOrderSummary(data.cart_count, data.cart_total);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ralat semasa mengemas kini kuantiti');
    })
    .finally(() => {
        // Re-enable quantity buttons
        quantityButtons.forEach(button => button.disabled = false);
    });
}

let currentRemoveItemId = null;

function removeItem(itemId) {
    currentRemoveItemId = itemId;
    showRemoveModal();
}

function showRemoveModal() {
    const modal = document.getElementById('remove-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideRemoveModal() {
    const modal = document.getElementById('remove-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentRemoveItemId = null;
}

function confirmRemoveItem() {
    if (!currentRemoveItemId) return;
    
    fetch(`{{ url('cart/remove') }}/${currentRemoveItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item element from the DOM
            const itemElement = document.querySelector(`[data-item-id="${currentRemoveItemId}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            
            // Update cart count and total
            updateCartCount(data.cart_count);
            updateCartTotal(data.cart_total);
            updateOrderSummary(data.cart_count, data.cart_total);
            
            // Check if cart is empty
            const remainingItems = document.querySelectorAll('[data-item-id]');
            if (remainingItems.length === 0) {
                // Reload page to show empty cart message
                window.location.reload();
            } else {
                // Show success message
                showSuccessMessage(data.message);
            }
            
            hideRemoveModal();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ralat semasa mengeluarkan item');
    });
}

function clearCart() {
    showClearModal();
}

function showClearModal() {
    const modal = document.getElementById('clear-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideClearModal() {
    const modal = document.getElementById('clear-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmClearCart() {
    fetch('{{ route("cart.clear") }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count and total
            updateCartCount(data.cart_count);
            updateCartTotal(data.cart_total);
            updateOrderSummary(data.cart_count, data.cart_total);
            
            // Reload page to show empty cart message
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ralat semasa mengosongkan troli');
    });
}

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count;
        
        // Show/hide the badge based on count
        if (count > 0) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    });
}

function updateCartTotal(total) {
    const cartTotalElements = document.querySelectorAll('.cart-total');
    cartTotalElements.forEach(element => {
        element.textContent = 'RM' + total;
    });
}

function updateOrderSummary(itemCount, total) {
    // Update item count
    const itemCountElement = document.getElementById('order-summary-item-count');
    if (itemCountElement) {
        itemCountElement.textContent = itemCount;
        // Add subtle animation
        itemCountElement.classList.add('scale-110');
        setTimeout(() => itemCountElement.classList.remove('scale-110'), 200);
    }
    
    // Update subtotal
    const subtotalElement = document.getElementById('order-summary-subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = 'RM' + total;
        // Add subtle animation
        subtotalElement.classList.add('scale-110');
        setTimeout(() => subtotalElement.classList.remove('scale-110'), 200);
    }
    
    // Update total
    const totalElement = document.getElementById('order-summary-total');
    if (totalElement) {
        totalElement.textContent = 'RM' + total;
        // Add subtle animation
        totalElement.classList.add('scale-110');
        setTimeout(() => totalElement.classList.remove('scale-110'), 200);
    }
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
    const removeMessage = sessionStorage.getItem('cartRemoveMessage');
    if (removeMessage) {
        showSuccessMessage(removeMessage);
        sessionStorage.removeItem('cartRemoveMessage');
    }
    
    // Add click outside functionality for modals
    const removeModal = document.getElementById('remove-modal');
    const clearModal = document.getElementById('clear-modal');
    
    removeModal.addEventListener('click', function(e) {
        if (e.target === removeModal) {
            hideRemoveModal();
        }
    });
    
    clearModal.addEventListener('click', function(e) {
        if (e.target === clearModal) {
            hideClearModal();
        }
    });
    
    // Add escape key functionality
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideRemoveModal();
            hideClearModal();
        }
    });
});
</script>
@endpush 