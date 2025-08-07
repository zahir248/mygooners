@extends('layouts.app')

@section('title', 'Butiran Pesanan - MyGooners')
@section('meta_description', 'Lihat butiran lengkap pesanan anda di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('checkout.orders') }}" class="hover:text-red-600 transition-colors">Pesanan Saya</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Pesanan #{{ $order->order_number }}</span>
        </nav>
    </div>
</div>

<!-- Order Status Banner -->
<div id="orderStatusBanner" class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                @if($order->status === 'pending')
                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @elseif($order->status === 'processing')
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                @elseif($order->status === 'shipped')
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                @elseif($order->status === 'delivered')
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @else
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-green-900 mb-1">
                    Status Pesanan: 
                    <span class="font-bold">
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
                            @default
                                {{ ucfirst($order->status) }}
                        @endswitch
                    </span>
                </h3>
                <div class="text-sm text-green-800 space-y-1">
                    @if($order->status === 'pending')
                        <p>• <strong>Pembayaran:</strong> Sila selesaikan pembayaran anda untuk meneruskan proses pesanan</p>
                        <p>• <strong>Pembatalan:</strong> Anda boleh membatalkan pesanan ini dalam tempoh 24 jam dari masa pembelian</p>
                    @elseif($order->status === 'processing')
                        <p>• <strong>Pesanan anda sedang diproses</strong> dan akan dihantar secepat mungkin</p>
                        <p>• <strong>Pembatalan:</strong> Anda masih boleh membatalkan pesanan ini dalam tempoh 24 jam dari masa pembelian</p>
                    @elseif($order->status === 'shipped')
                        <p>• <strong>Pesanan anda telah dihantar!</strong> Anda akan menerima nombor pengesanan secepat mungkin</p>
                        <p>• <strong>Penjejakan:</strong> Ikuti perkembangan penghantaran anda melalui nombor pengesanan</p>
                        <p>• <strong>Tandakan Sebagai Diterima:</strong> Apabila anda menerima pesanan, sila tandakan sebagai diterima menggunakan butang di bawah</p>
                                                    @if($order->isAutoDeliveryCountdownActive())
                                <p class="text-orange-600">• <strong>Auto-Delivery:</strong> Pesanan akan ditandakan sebagai diterima secara automatik dalam {{ $order->getFormattedAutoDeliveryCountdown() }}</p>
                            @elseif($order->isAutoDeliveryOverdue())
                                <p class="text-red-600">• <strong>Auto-Delivery:</strong> Pesanan sepatutnya ditandakan sebagai diterima secara automatik</p>
                            @endif
                    @elseif($order->status === 'delivered')
                        <p>• <strong>Pesanan anda telah diterima!</strong> Terima kasih kerana membeli-belah dengan kami</p>
                        @if($order->wasAutoDelivered())
                            <p class="text-blue-600">• <strong>Auto-Delivery:</strong> Pesanan ini telah ditandakan sebagai diterima secara automatik oleh sistem</p>
                        @endif
                        <p>• <strong>Ulasan:</strong> Kongsikan pengalaman anda dengan memberikan ulasan untuk produk yang dibeli</p>
                    @elseif($order->status === 'cancelled')
                        <p>• <strong>Pesanan ini telah dibatalkan</strong> pada {{ $order->updated_at->format('d/m/Y H:i') }}</p>
                        <p>• <strong>Refund:</strong> Jika pembayaran telah dibuat, refund akan diproses dalam 3-5 hari bekerja</p>
                    @endif
                    
                    @if($order->payment_status === 'failed')
                        <p class="mt-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                            ❌ <strong>Pembayaran Gagal:</strong> Anda boleh cuba bayar semula menggunakan butang "Cuba Bayar Semula" di bawah
                        </p>
                    @elseif(in_array($order->status, ['pending', 'processing']) && 
                        ($order->payment_status !== 'paid' || 
                         ($order->payment_status === 'paid' && $order->created_at->diffInHours(now()) <= 24)))
                        <p class="mt-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                            💡 <strong>Tip:</strong> Anda boleh membatalkan pesanan ini menggunakan butang "Batalkan Pesanan" di bawah
                        </p>
                    @endif
                </div>
            </div>
            <button onclick="toggleOrderBanner()" class="flex-shrink-0 text-green-600 hover:text-green-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Order Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pesanan #{{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-1">Dibuat pada {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div class="text-right">
                <div class="flex items-center space-x-3">
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
                
                <p class="text-2xl font-bold text-red-600 mt-2">{{ $order->getFormattedTotal() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Item Pesanan</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-200">
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
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 00-2-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('shop.show', $item->product->slug) }}" class="hover:text-red-600 transition-colors">
                                            {{ $item->product_name }}
                                        </a>
                                    </h3>
                                    @if($item->variation_name)
                                        <p class="text-sm text-gray-500">{{ $item->variation_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">Kuantiti: {{ $item->quantity }}</p>
                                    <p class="text-sm text-gray-500">Harga seunit: {{ $item->getFormattedPrice() }}</p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ $item->getFormattedSubtotal() }}</p>
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
            
            <!-- Shipping Information -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Maklumat Penghantaran</h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Alamat Penghantaran</h3>
                            <div class="text-gray-600 space-y-1">
                                <p class="font-medium">{{ $order->shipping_name }}</p>
                                <p>{{ $order->shipping_email }}</p>
                                <p>{{ $order->shipping_phone }}</p>
                                <p>{{ $order->shipping_address }}</p>
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                                <p>{{ $order->shipping_country }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Alamat Bil</h3>
                            <div class="text-gray-600 space-y-1">
                                <p class="font-medium">{{ $order->billing_name }}</p>
                                <p>{{ $order->billing_email }}</p>
                                <p>{{ $order->billing_phone }}</p>
                                <p>{{ $order->billing_address }}</p>
                                <p>{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}</p>
                                <p>{{ $order->billing_country }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($order->notes)
                <!-- Order Notes -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Nota Pesanan</h2>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Order Information -->
        <div class="lg:col-span-1">
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
                                @default
                                    {{ ucfirst($order->payment_status) }}
                            @endswitch
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kaedah Pembayaran:</span>
                        <span class="font-medium">{{ $order->getPaymentMethodDisplayName() }}</span>
                    </div>
                    
                    @if($order->shipped_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tarikh Penghantaran:</span>
                            <span class="font-medium">{{ $order->shipped_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    
                    @if($order->delivered_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tarikh Penerimaan:</span>
                            <span class="font-medium">{{ $order->delivered_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Information -->
            @if($order->tracking_number || $order->shipping_courier)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Maklumat Penghantaran</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                                                 @if($order->tracking_number)
                             <div class="flex justify-between">
                                 <span class="text-gray-600">Nombor Penjejakan:</span>
                                 <a href="{{ $order->getTrackingUrl() }}" 
                                    target="_blank" 
                                    class="font-medium text-blue-600 hover:text-blue-800 underline cursor-pointer">
                                     {{ $order->tracking_number }}
                                 </a>
                             </div>
                         @endif
                         
                         @if($order->shipping_courier)
                             <div class="flex justify-between">
                                 <span class="text-gray-600">Kurier Penghantaran:</span>
                                 <span class="font-medium">{{ $order->shipping_courier }}</span>
                             </div>
                         @endif
                         
                         @if($order->tracking_number)
                             <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                 <p class="text-sm text-blue-800">
                                     <strong>Petunjuk:</strong> Klik pada nombor penjejakan di atas untuk menjejak penghantaran anda secara langsung di tracking.my
                                 </p>
                             </div>
                         @endif
                    </div>
                </div>
            @endif
            
            @if($order->fpl_manager_name && $order->fpl_team_name)
                <!-- Fantasy Premier League Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
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
                        
                        @if($order->payment_status === 'paid')
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kod Liga:</span>
                                <span class="font-medium">k7l1d7</span>
                            </div>
                            
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-900">Sertai Liga FPL MyGooners</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Gunakan kod liga <strong>k7l1d7</strong> untuk menyertai liga Fantasy Premier League MyGooners. 
                                            Bersaing dengan pemain lain dan menangi hadiah menarik!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kod Liga:</span>
                                <span class="font-medium text-gray-400">Sila selesaikan pembayaran untuk melihat kod liga</span>
                            </div>
                            
                            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-gray-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700">Kod Liga FPL</h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Kod liga akan dipaparkan selepas pembayaran diselesaikan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Tindakan</h2>
                </div>
                
                <div class="p-6 space-y-3">
                    <!-- Invoice Actions -->
                    @if(!(($order->status === 'pending' && $order->payment_status === 'pending') || $order->payment_status === 'failed') && $order->status !== 'cancelled')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <h3 class="text-sm font-bold text-blue-900 mb-2">📄 Invois Pesanan</h3>
                            <p class="text-xs text-blue-700 mb-3">Muat turun atau lihat invois untuk pesanan ini</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('checkout.invoice.view', $order->id) }}" 
                                   target="_blank"
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
                                    Lihat Invois
                                </a>
                                <a href="{{ route('checkout.invoice.download', $order->id) }}" 
                                   class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm font-medium text-center transition-colors">
                                    Muat Turun
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                            <h3 class="text-sm font-bold text-gray-700 mb-2">📄 Invois Pesanan</h3>
                            <p class="text-xs text-gray-600 mb-3">
                                @if($order->status === 'pending' && $order->payment_status === 'pending')
                                    Invois akan tersedia selepas pembayaran berjaya.
                                @elseif($order->payment_status === 'failed')
                                    Invois tidak tersedia untuk pesanan dengan pembayaran gagal.
                                @elseif($order->status === 'cancelled')
                                    Invois tidak tersedia untuk pesanan yang telah dibatalkan.
                                @endif
                            </p>
                        </div>
                    @endif
                    
                    <a href="{{ route('checkout.orders') }}" 
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                         Kembali ke Pesanan
                     </a>
                     
                     @if($order->tracking_number)
                         <a href="{{ $order->getTrackingUrl() }}" 
                            target="_blank"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                             📦 Jejak Penghantaran
                         </a>
                     @endif
                     
                     @if($order->payment_status === 'failed' && !in_array($order->status, ['cancelled', 'refunded']))
                        <a href="{{ route('checkout.show-retry-payment', $order->id) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                            Cuba Bayar Semula
                        </a>
                    @endif

                    @if($order->status === 'shipped')
                        <button type="button" 
                                onclick="openMarkDeliveredModal('{{ $order->id }}', '{{ $order->order_number }}')"
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                            ✅ Tandakan Sebagai Diterima
                        </button>
                    @endif

                    @if(in_array($order->status, ['pending', 'processing']) && 
                        ($order->payment_status !== 'paid' || 
                         ($order->payment_status === 'paid' && $order->created_at->diffInHours(now()) <= 24)))
                        <button type="button" 
                                onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_number }}')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                            Batalkan Pesanan
                        </button>
                    @endif
                    
                    <a href="{{ route('shop.index') }}" 
                       class="w-full border-2 border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-lg font-bold text-center transition-colors block">
                        Teruskan Membeli
                    </a>
                </div>
            </div>
        </div>
    </div>
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
         submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
     });
 
     // Order status banner functionality
     function toggleOrderBanner() {
         const banner = document.getElementById('orderStatusBanner');
         banner.style.display = 'none';
     }
 
     // Check if banner should be shown on page load
     document.addEventListener('DOMContentLoaded', function() {
         // Banner will always show on page load
         document.getElementById('orderStatusBanner').style.display = 'block';
     });
 </script>

@endsection 