@extends('layouts.app')

@section('title', 'Bayar Pesanan - MyGooners')
@section('meta_description', 'Pilih kaedah pembayaran untuk pesanan anda.')

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
            <span class="text-gray-900 font-medium">
                @if($order->payment_status === 'failed')
                    Cuba Bayar Semula
                @else
                    Bayar Pesanan
                @endif
            </span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">
                @if($order->payment_status === 'failed')
                    Cuba Bayar Semula
                @else
                    Bayar Pesanan
                @endif
            </h1>
            <p class="text-gray-600 mt-1">Pilih kaedah pembayaran untuk pesanan #{{ $order->order_number }}</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Order Summary -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-600">Nombor Pesanan</p>
                    <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Jumlah Bayaran</p>
                    <p class="text-xl font-bold text-red-600">{{ $order->getFormattedTotal() }}</p>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="space-y-3 mb-4">
                @foreach($order->items as $item)
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
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Kaedah Pembayaran Sebelumnya:</span>
                    <span class="font-medium text-gray-900">{{ $order->getPaymentMethodDisplayName() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Selection -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Pilih Kaedah Pembayaran</h2>
            <p class="text-sm text-gray-600 mt-1">Pilih kaedah pembayaran yang anda mahu gunakan</p>
        </div>
        
        <form method="POST" action="{{ route('checkout.retry-payment-with-method', $order->id) }}" class="p-6">
            @csrf
            
            <!-- Payment Methods -->
            <div class="space-y-4 mb-6">
                <!-- ToyyibPay -->
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                    <input type="radio" name="payment_method" value="toyyibpay" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" {{ old('payment_method', $order->payment_method) === 'toyyibpay' ? 'checked' : '' }}>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">ToyyibPay</p>
                                <p class="text-xs text-gray-500">Pembayaran selamat melalui ToyyibPay</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a2 3 0 003 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </label>

                @if(setting('stripe_payment_enabled', false))
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                        <input type="radio" name="payment_method" value="stripe" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300" {{ old('payment_method', $order->payment_method) === 'stripe' ? 'checked' : '' }}>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Stripe</p>
                                    <p class="text-xs text-gray-500">Bayar menggunakan kad kredit/debit</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a2 3 0 003 3z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </label>
                @else
                    <!-- Hidden Stripe option - kept for form functionality but not visible to users -->
                    <input type="radio" name="payment_method" value="stripe" class="hidden" {{ old('payment_method', $order->payment_method) === 'stripe' ? 'checked' : '' }}>
                @endif
            </div>

            @error('payment_method')
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('checkout.orders') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                    Kembali ke Pesanan
                </a>
                
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold transition-colors">
                    @if($order->payment_status === 'failed')
                        Cuba Bayar Semula
                    @else
                        Bayar Sekarang
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 