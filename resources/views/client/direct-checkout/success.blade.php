@extends('layouts.app')

@section('title', 'Pesanan Berjaya - MyGooners')
@section('meta_description', 'Pesanan anda telah berjaya dibuat di MyGooners.')

@section('content')
<!-- Success Banner -->
<div class="bg-green-50 border-b border-green-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-green-900 mb-2">Pesanan Berjaya!</h1>
            <p class="text-green-700">Terima kasih atas pembelian anda. Pesanan anda telah berjaya dibuat.</p>
        </div>
    </div>
</div>

<!-- Information Banner -->
<div id="directSuccessInfoBanner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-blue-900 mb-1">Apa Yang Seterusnya?</h3>
                <div class="text-sm text-blue-800 space-y-1">
                    <p>• <strong>Pengesahan:</strong> Anda akan menerima emel pengesahan pesanan dalam beberapa minit</p>
                    <p>• <strong>Pemprosesan:</strong> Pesanan anda akan diproses dan dihantar dalam 1-2 hari bekerja</p>
                    <p>• <strong>Penjejakan:</strong> Anda akan menerima nombor pengesanan penghantaran melalui emel</p>
                    <p>• <strong>Pembatalan:</strong> Anda boleh membatalkan pesanan ini dalam tempoh 24 jam dari masa pembelian</p>
                </div>
            </div>
            <button onclick="toggleDirectSuccessBanner()" class="flex-shrink-0 text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Order Information -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Maklumat Pesanan</h2>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Order Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Butiran Pesanan</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nombor Pesanan:</span>
                            <span class="font-medium">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tarikh Pesanan:</span>
                            <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeClass() }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kaedah Pembayaran:</span>
                            <span class="font-medium">{{ $order->getPaymentMethodDisplayName() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status Pembayaran:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusBadgeClass() }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jumlah Keseluruhan:</span>
                            <span class="font-bold text-lg text-red-600">{{ $order->getFormattedTotal() }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Alamat Penghantaran</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p class="font-medium text-gray-900">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_email }}</p>
                        <p>{{ $order->shipping_phone }}</p>
                        <p class="mt-2">{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Item Pesanan</h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    @foreach($order->items as $item)
                        <div class="flex items-center p-4 border-b border-gray-200 last:border-b-0">
                            <div class="flex-shrink-0">
                                <img src="{{ $item->variation && $item->variation->images && count($item->variation->images) > 0 ? route('variation.image', basename($item->variation->images[0])) : ($item->product->images && count($item->product->images) > 0 ? route('product.image', basename($item->product->images[0])) : asset('images/profile-image-default.png')) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            </div>
                            
                            <div class="flex-1 ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h4>
                                @if($item->variation_name)
                                    <p class="text-sm text-gray-500">{{ $item->variation_name }}</p>
                                @endif
                                <p class="text-sm text-gray-500">Kuantiti: {{ $item->quantity }}</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $item->getFormattedSubtotal() }}</p>
                                <p class="text-sm text-gray-500">{{ $item->getFormattedPrice() }} setiap unit</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Ringkasan Pesanan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Harga:</span>
                        <span class="font-medium">{{ $order->getFormattedSubtotal() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Penghantaran:</span>
                        <span class="font-medium text-green-600">Percuma</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cukai:</span>
                        <span class="font-medium">RM0.00</span>
                    </div>
                    <div class="border-t border-gray-300 pt-2">
                        <div class="flex justify-between font-bold">
                            <span>Jumlah Keseluruhan:</span>
                            <span class="text-red-600">{{ $order->getFormattedTotal() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($order->notes)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Nota Pesanan</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Next Steps -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Langkah Seterusnya</h3>
        <div class="space-y-3 text-sm text-blue-800">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-blue-800 font-bold text-xs">1</span>
                </div>
                <p>Anda akan menerima emel pengesahan dengan butiran pesanan lengkap.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-blue-800 font-bold text-xs">2</span>
                </div>
                <p>Tim kami akan memproses pesanan anda dalam masa 1-2 hari bekerja.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 bg-blue-200 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <span class="text-blue-800 font-bold text-xs">3</span>
                </div>
                <p>Anda akan diberitahu melalui emel apabila pesanan anda dihantar.</p>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4">
        <a href="{{ route('checkout.show', $order) }}" 
           class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-medium text-center transition-colors">
            Lihat Butiran Pesanan
        </a>
        <a href="{{ route('checkout.orders') }}" 
           class="flex-1 border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white py-3 px-6 rounded-lg font-medium text-center transition-colors">
            Lihat Semua Pesanan
        </a>
        <a href="{{ route('shop.index') }}" 
           class="flex-1 border-2 border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-lg font-medium text-center transition-colors">
            Teruskan Membeli
        </a>
    </div>
</div>

<script>
    // Direct success info banner functionality
    function toggleDirectSuccessBanner() {
        const banner = document.getElementById('directSuccessInfoBanner');
        banner.style.display = 'none';
    }

    // Check if banner should be shown on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Banner will always show on page load
        document.getElementById('directSuccessInfoBanner').style.display = 'block';
    });
</script>

@endsection 