@extends('layouts.app')

@section('title', 'Pesanan Saya - MyGooners')
@section('meta_description', 'Lihat semua pesanan anda di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Pesanan Saya</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pesanan Saya</h1>
                <p class="text-gray-600 mt-1">Lihat dan uruskan semua pesanan anda</p>
            </div>
            
            <a href="{{ route('shop.index') }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Teruskan Membeli
            </a>
        </div>
    </div>
</div>

<!-- Information Banner -->
<div id="infoBanner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-blue-900 mb-1">Maklumat Penting Mengenai Pesanan</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p>• <strong>Pembatalan:</strong> Anda boleh membatalkan pesanan yang belum dibayar atau pesanan yang telah dibayar dalam tempoh 24 jam</p>
                    <p>• <strong>Auto-Cancel:</strong> Pesanan yang belum dibayar akan dibatalkan secara automatik oleh sistem selepas 24 jam</p>
                    <p>• <strong>Pembayaran Semula:</strong> Jika pembayaran gagal, anda boleh cuba bayar semula untuk pesanan yang sama</p>
                    <p>• <strong>Status Pesanan:</strong> Ikuti perkembangan pesanan anda dari "Menunggu Pembayaran" hingga "Telah Diterima"</p>
                    <p>• <strong>Tandakan Sebagai Diterima:</strong> Apabila anda menerima pesanan yang telah dihantar, anda boleh menandakannya sebagai diterima</p>
                    <p>• <strong>Auto-Delivery:</strong> Pesanan yang telah dihantar akan ditandakan sebagai diterima secara automatik selepas 7 hari jika anda tidak menandakannya sendiri</p>
                    <p>• <strong>Bantuan:</strong> Jika anda memerlukan bantuan, sila hubungi kami melalui emel atau telefon</p>
                </div>
            </div>
            <button onclick="toggleBanner()" class="flex-shrink-0 text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Status Cards -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- All Orders Card -->
            <a href="{{ route('checkout.orders') }}" 
               class="bg-white border-2 {{ !$status ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-gray-900 mb-1">
                    {{ array_sum($orderCounts) }}
                </div>
                <div class="text-sm text-gray-600">Semua Pesanan</div>
            </a>

            <!-- Pending Orders Card -->
            <a href="{{ route('checkout.orders', ['status' => 'pending']) }}" 
               class="bg-white border-2 {{ $status === 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-yellow-600 mb-1">
                    {{ $orderCounts['pending'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Menunggu Pembayaran</div>
            </a>

            <!-- Processing Orders Card -->
            <a href="{{ route('checkout.orders', ['status' => 'processing']) }}" 
               class="bg-white border-2 {{ $status === 'processing' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-blue-600 mb-1">
                    {{ $orderCounts['processing'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Sedang Diproses</div>
            </a>

            <!-- Shipped Orders Card -->
            <a href="{{ route('checkout.orders', ['status' => 'shipped']) }}" 
               class="bg-white border-2 {{ $status === 'shipped' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-purple-600 mb-1">
                    {{ $orderCounts['shipped'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Telah Dihantar</div>
            </a>

            <!-- Delivered Orders Card -->
            <a href="{{ route('checkout.orders', ['status' => 'delivered']) }}" 
               class="bg-white border-2 {{ $status === 'delivered' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-green-600 mb-1">
                    {{ $orderCounts['delivered'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Telah Diterima</div>
            </a>

            <!-- Cancelled/Refunded Orders Card -->
            <a href="{{ route('checkout.orders', ['status' => 'cancelled']) }}" 
               class="bg-white border-2 {{ $status === 'cancelled' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }} rounded-lg p-4 text-center transition-all duration-200 hover:shadow-md">
                <div class="text-2xl font-bold text-red-600 mb-1">
                    {{ ($orderCounts['cancelled'] ?? 0) + ($orderCounts['refunded'] ?? 0) }}
                </div>
                <div class="text-sm text-gray-600">Dibatalkan/Dikembalikan</div>
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if($orders->count() > 0)
        <!-- Status Filter Header -->
        @if($status)
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <h2 class="text-xl font-bold text-gray-900">
                        @switch($status)
                            @case('pending')
                                Pesanan Menunggu Pembayaran
                                @break
                            @case('processing')
                                Pesanan Sedang Diproses
                                @break
                            @case('shipped')
                                Pesanan Telah Dihantar
                                @break
                            @case('delivered')
                                Pesanan Telah Diterima
                                @break
                            @case('cancelled')
                                Pesanan Dibatalkan/Dikembalikan
                                @break
                            @default
                                Pesanan {{ ucfirst($status) }}
                        @endswitch
                    </h2>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">
                        {{ $orders->total() }} pesanan
                    </span>
                </div>
                <a href="{{ route('checkout.orders') }}" 
                   class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors">
                    Lihat Semua Pesanan
                </a>
            </div>
        @endif

        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Pesanan #{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $order->getStatusBadgeClass() }}">
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
                                                @if($order->wasAutoDelivered())
                                                    Telah Diterima (Auto)
                                                @else
                                                    Telah Diterima
                                                @endif
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
                                    
                                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $order->getPaymentStatusBadgeClass() }}">
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
                                
                                <p class="text-xl font-bold text-red-600">{{ $order->getFormattedTotal() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Order Items Preview -->
                        <div class="space-y-3 mb-6">
                            @foreach($order->items->take(3) as $item)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden bg-gray-200">
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
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product_name }}</p>
                                        @if($item->variation_name)
                                            <p class="text-xs text-gray-500">{{ $item->variation_name }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">Kuantiti: {{ $item->quantity }}</p>
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ $item->getFormattedSubtotal() }}</p>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($order->items->count() > 3)
                                <div class="text-center py-2">
                                    <p class="text-sm text-gray-500">Dan {{ $order->items->count() - 3 }} item lagi...</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">Jumlah Item: {{ $order->items->count() }}</span>
                                <span class="text-gray-600">Kaedah Pembayaran: {{ $order->getPaymentMethodDisplayName() }}</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('checkout.show', $order->id) }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Lihat Butiran
                                </a>
                                
                                @if($order->status === 'pending' && $order->payment_status === 'pending')
                                    <a href="{{ route('checkout.show-retry-payment', $order->id) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                         Bayar Sekarang
                                     </a>
                                 @endif
 
                                @if($order->payment_status === 'failed' && !in_array($order->status, ['cancelled', 'refunded']))
                                    <a href="{{ route('checkout.show-retry-payment', $order->id) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                         Cuba Bayar Semula
                                     </a>
                                 @endif
 
                                @if($order->status === 'shipped')
                                    <button type="button" 
                                            onclick="openMarkDeliveredModal('{{ $order->id }}', '{{ $order->order_number }}')"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        ✅ Tandakan Sebagai Diterima
                                    </button>
                                                                    @if($order->isAutoDeliveryCountdownActive())
                                    <div class="text-xs text-orange-600 mt-1">
                                        ⚠️ Pesanan akan ditandakan sebagai diterima secara automatik dalam {{ $order->getFormattedAutoDeliveryCountdown() }}
                                    </div>
                                @elseif($order->isAutoDeliveryOverdue())
                                    <div class="text-xs text-red-600 mt-1">
                                        ⚠️ Pesanan sepatutnya ditandakan sebagai diterima secara automatik
                                    </div>
                                @endif
                                @endif

                                @if(in_array($order->status, ['pending', 'processing']) && 
                                    ($order->payment_status !== 'paid' || 
                                     ($order->payment_status === 'paid' && $order->created_at->diffInHours(now()) <= 24)))
                                    <button type="button" 
                                            onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_number }}')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Batalkan Pesanan
                                    </button>
                                @endif
                            </div>
                            
                            <div class="text-sm text-gray-500">
                                <p>Dihantar ke: {{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                @if($status)
                    @switch($status)
                        @case('pending')
                            Tiada Pesanan Menunggu Pembayaran
                            @break
                        @case('processing')
                            Tiada Pesanan Sedang Diproses
                            @break
                        @case('shipped')
                            Tiada Pesanan Telah Dihantar
                            @break
                        @case('delivered')
                            Tiada Pesanan Telah Diterima
                            @break
                        @case('cancelled')
                            Tiada Pesanan Dibatalkan
                            @break
                        @case('refunded')
                            Tiada Pesanan Dikembalikan
                            @break
                        @default
                            Tiada Pesanan {{ ucfirst($status) }}
                    @endswitch
                @else
                    Anda Belum Ada Pesanan
                @endif
            </h2>
            <p class="text-gray-600 mb-8">
                @if($status)
                    @switch($status)
                        @case('pending')
                            Tiada pesanan dengan status "Menunggu Pembayaran" ditemui.
                            @break
                        @case('processing')
                            Tiada pesanan dengan status "Sedang Diproses" ditemui.
                            @break
                        @case('shipped')
                            Tiada pesanan dengan status "Telah Dihantar" ditemui.
                            @break
                        @case('delivered')
                            Tiada pesanan dengan status "Telah Diterima" ditemui.
                            @break
                        @case('cancelled')
                            Tiada pesanan dengan status "Dibatalkan" ditemui.
                            @break
                        @case('refunded')
                            Tiada pesanan dengan status "Dikembalikan" ditemui.
                            @break
                        @default
                            Tiada pesanan dengan status "{{ ucfirst($status) }}" ditemui.
                    @endswitch
                @else
                    Mulakan membeli-belah untuk melihat pesanan anda di sini.
                @endif
            </p>
            <div class="space-x-4">
                <a href="{{ route('shop.index') }}" 
                   class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition-colors">
                    Mulakan Membeli
                </a>
                @if($status)
                    <a href="{{ route('checkout.orders') }}" 
                       class="inline-block border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-8 py-3 rounded-lg font-bold transition-colors">
                        Lihat Semua Pesanan
                    </a>
                @else
                    <a href="{{ route('home') }}" 
                       class="inline-block border-2 border-gray-300 text-gray-700 hover:bg-gray-50 px-8 py-3 rounded-lg font-bold transition-colors">
                        Kembali ke Utama
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Batalkan Pesanan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Adakah anda pasti mahu membatalkan pesanan <span id="modalOrderNumber" class="font-semibold text-gray-700"></span>?
                </p>
                <p class="text-xs text-gray-400 mb-4">
                    Tindakan ini tidak boleh dibatalkan. Pesanan akan ditandakan sebagai dibatalkan.
                </p>
            </div>
            <div class="flex items-center justify-center space-x-3">
                <button id="cancelModalClose" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <form id="cancelOrderForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            id="cancelSubmitBtn"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                        Ya, Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Delivered Modal -->
<div id="markDeliveredModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tandakan Sebagai Diterima</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Adakah anda pasti mahu menandakan pesanan <span id="markDeliveredOrderNumber" class="font-semibold text-gray-700"></span> sebagai diterima?
                </p>
                <p class="text-xs text-gray-400 mb-4">
                    Pastikan anda telah menerima dan memeriksa semua item dalam pesanan ini.
                </p>
            </div>
            <div class="flex items-center justify-center space-x-3">
                <button id="markDeliveredModalClose" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <form id="markDeliveredForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            id="markDeliveredSubmitBtn"
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                        Ya, Tandakan Sebagai Diterima
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openCancelModal(orderId, orderNumber) {
        document.getElementById('modalOrderNumber').textContent = orderNumber;
        
        // Determine the correct route based on current page
        let cancelRoute;
        if (window.location.pathname.includes('direct-checkout')) {
            cancelRoute = '{{ route("direct-checkout.cancel-order", ":orderId") }}'.replace(':orderId', orderId);
        } else {
            cancelRoute = '{{ route("checkout.cancel-order", ":orderId") }}'.replace(':orderId', orderId);
        }
        
        document.getElementById('cancelOrderForm').action = cancelRoute;
        document.getElementById('cancelOrderModal').classList.remove('hidden');
    }

    function openMarkDeliveredModal(orderId, orderNumber) {
        document.getElementById('markDeliveredOrderNumber').textContent = orderNumber;
        
        // Determine the correct route based on current page
        let markDeliveredRoute;
        if (window.location.pathname.includes('direct-checkout')) {
            markDeliveredRoute = '{{ route("direct-checkout.mark-delivered", ":orderId") }}'.replace(':orderId', orderId);
        } else {
            markDeliveredRoute = '{{ route("checkout.mark-delivered", ":orderId") }}'.replace(':orderId', orderId);
        }
        
        document.getElementById('markDeliveredForm').action = markDeliveredRoute;
        document.getElementById('markDeliveredModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelOrderModal').classList.add('hidden');
    }

    function closeMarkDeliveredModal() {
        document.getElementById('markDeliveredModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('cancelOrderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelModal();
        }
    });

    document.getElementById('markDeliveredModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeMarkDeliveredModal();
        }
    });

    // Close modal when clicking cancel button
    document.getElementById('cancelModalClose').addEventListener('click', closeCancelModal);
    document.getElementById('markDeliveredModalClose').addEventListener('click', closeMarkDeliveredModal);

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCancelModal();
        }
    });

    // Handle form submission with loading state
    document.getElementById('cancelOrderForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('cancelSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
    });

    document.getElementById('markDeliveredForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('markDeliveredSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
    });
 
     // Banner functionality
     function toggleBanner() {
         const banner = document.getElementById('infoBanner');
         banner.style.display = 'none';
     }
 
     // Check if banner should be shown on page load
     document.addEventListener('DOMContentLoaded', function() {
         // Banner will always show on page load
         document.getElementById('infoBanner').style.display = 'block';
     });
 </script>

@endsection 