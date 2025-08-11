@extends('layouts.app')

@section('title', 'Pesanan Berjaya - MyGooners')
@section('meta_description', 'Pesanan anda telah berjaya dibuat. Terima kasih kerana membeli-belah di MyGooners.')

@section('content')
<!-- Success Message -->
<div class="bg-green-50 border-b border-green-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berjaya!</h1>
            <p class="text-lg text-gray-600">Terima kasih kerana membeli-belah di MyGooners</p>
            @if($order->payment_status === 'paid')
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-blue-800 font-medium">Emel pengesahan telah dihantar ke {{ $order->shipping_email }}</p>
                        <p class="text-blue-700 text-sm mt-1">Nota: Invois PDF mungkin tidak dilampirkan kerana isu teknikal. Sila hubungi kami jika anda memerlukan invois.</p>
                    </div>
                    @if($order->billing_email !== $order->shipping_email)
                        <p class="text-blue-700 text-sm mt-1">Dan juga ke {{ $order->billing_email }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Next Steps Banner -->
<div id="nextStepsBanner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-blue-900 mb-4">Langkah Seterusnya</h3>
                <div class="space-y-3">
                    @if($order->payment_status === 'paid')
                        <!-- For successful payments -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                1
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Pemprosesan Pesanan</p>
                                <p class="text-sm text-blue-700">Pesanan anda sedang diproses dan disediakan untuk penghantaran.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                2
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Penyediaan Penghantaran</p>
                                <p class="text-sm text-blue-700">Item anda akan disediakan dan dihantar dalam masa 1-2 hari bekerja.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                3
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Penghantaran</p>
                                <p class="text-sm text-blue-700">Item anda akan dihantar dalam masa 3-5 hari bekerja selepas pemprosesan.</p>
                            </div>
                        </div>
                    @else
                        <!-- For pending payments -->
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                1
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Pembayaran</p>
                                <p class="text-sm text-blue-700">Sila selesaikan pembayaran anda dalam masa 24 jam untuk mengelakkan pembatalan automatik.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                2
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Pengesahan</p>
                                <p class="text-sm text-blue-700">Kami akan mengesahkan pembayaran anda dan memproses pesanan dalam masa 1-2 hari bekerja.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                3
                            </div>
                            <div>
                                <p class="font-medium text-blue-900">Penghantaran</p>
                                <p class="text-sm text-blue-700">Item anda akan dihantar dalam masa 3-5 hari bekerja selepas pengesahan pembayaran.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <button onclick="toggleNextStepsBanner()" class="flex-shrink-0 text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Order Details -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Information -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Maklumat Pesanan</h2>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nombor Pesanan:</span>
                    <span class="font-medium">{{ $order->order_number }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Tarikh Pesanan:</span>
                    <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Status Pesanan:</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->getStatusBadgeClass() }}">
                        @switch($order->status)
                            @case('pending')
                                Menunggu Pembayaran
                                @break
                            @case('processing')
                                Sedang Diproses
                                @break
                            @case('shipped')
                                Telah Dihantar
                                @break
                            @case('delivered')
                                Telah Diterima
                                @break
                            @case('cancelled')
                                Dibatalkan
                                @break
                            @case('refunded')
                                Dikembalikan
                                @break
                            @default
                                {{ ucfirst($order->status) }}
                        @endswitch
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Status Pembayaran:</span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $order->getPaymentStatusBadgeClass() }}">
                        @switch($order->payment_status)
                            @case('pending')
                                Menunggu Pembayaran
                                @break
                            @case('paid')
                                Telah Dibayar
                                @break
                            @case('failed')
                                Gagal
                                @break
                            @case('refunded')
                                Dikembalikan
                                @break
                            @default
                                {{ ucfirst($order->payment_status) }}
                        @endswitch
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kaedah Pembayaran:</span>
                    <span class="font-medium">{{ $order->getPaymentMethodDisplayName() }}</span>
                </div>
                
                <div class="flex justify-between text-lg font-bold">
                    <span>Jumlah Keseluruhan:</span>
                    <span class="text-red-600">{{ $order->getFormattedTotal() }}</span>
                </div>
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Maklumat Penghantaran</h2>
            </div>
            
            <div class="p-6 space-y-3">
                <div>
                    <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                    <p class="text-gray-600">{{ $order->shipping_email }}</p>
                    <p class="text-gray-600">{{ $order->shipping_phone }}</p>
                </div>
                
                <div class="text-gray-600">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                    <p>{{ $order->shipping_country }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if($order->fpl_manager_name && $order->fpl_team_name)
        <!-- Fantasy Premier League Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Fantasy Premier League</h2>
                <p class="text-sm text-gray-600 mt-1">Maklumat untuk pengesahan pesanan</p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nama Manager:</span>
                    <span class="font-medium">{{ $order->fpl_manager_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Nama Pasukan:</span>
                    <span class="font-medium">{{ $order->fpl_team_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Kod Liga:</span>
                    <span class="font-medium">8nx2p4</span>
                </div>
                
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Sertai Liga FPL MyGooners</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Gunakan kod liga <strong>8nx2p4</strong> untuk menyertai liga Fantasy Premier League MyGooners. 
                                Bersaing dengan pemain lain dan menangi hadiah menarik!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Item Pesanan</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($order->items as $item)
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200">
                                @if($item->variation && $item->variation->images && count($item->variation->images) > 0)
                                    <img src="{{ route('variation.image', basename($item->variation->images[0])) }}" 
                                         alt="{{ $item->variation->name }}" 
                                         class="w-full h-full object-cover">
                                @elseif($item->product->images && count($item->product->images) > 0)
                                    <img src="{{ route('product.image', basename($item->product->images[0])) }}" 
                                         alt="{{ $item->product->title }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $item->product_name }}</h3>
                            @if($item->variation_name)
                                <p class="text-sm text-gray-500">{{ $item->variation_name }}</p>
                            @endif
                            <p class="text-sm text-gray-500">Kuantiti: {{ $item->quantity }}</p>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">{{ $item->getFormattedSubtotal() }}</p>
                            <p class="text-sm text-gray-500">{{ $item->getFormattedPrice() }} seunit</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Order Summary -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Jumlah Item:</span>
                    <span class="font-medium">{{ $order->items->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Jumlah Harga:</span>
                    <span class="font-medium">{{ $order->getFormattedSubtotal() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Penghantaran:</span>
                    <span class="font-medium text-green-600">Percuma</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Cukai:</span>
                    <span class="font-medium">{{ $order->getFormattedTax() }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Jumlah Keseluruhan:</span>
                        <span class="text-red-600">{{ $order->getFormattedTotal() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 mt-8">
        <a href="{{ route('checkout.show', $order->id) }}" 
           class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors">
            Lihat Butiran Pesanan
        </a>
        
        <a href="{{ route('checkout.orders') }}" 
           class="flex-1 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-lg font-bold text-center transition-colors">
            Lihat Semua Pesanan
        </a>
        
        <a href="{{ route('shop.index') }}" 
           class="flex-1 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-lg font-bold text-center transition-colors">
            Teruskan Membeli
        </a>
    </div>
</div>

<script>
    // Next steps banner functionality
    function toggleNextStepsBanner() {
        const banner = document.getElementById('nextStepsBanner');
        banner.style.display = 'none';
    }

    // Check if banner should be shown on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Banner will always show on page load
        document.getElementById('nextStepsBanner').style.display = 'block';
    });
</script>

@endsection 