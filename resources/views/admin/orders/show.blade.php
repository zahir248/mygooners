@extends('layouts.admin')

@section('title', 'Butiran Pesanan - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Butiran Pesanan</h1>
            <p class="text-gray-600 mt-2">{{ $order->order_number }}</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('admin.orders.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                Kembali ke Senarai
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
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
                                    <p class="text-lg font-semibold text-gray-900">{{ $item->getFormattedSubtotal() }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->getFormattedPrice() }} setiap unit</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

            @if($order->status === 'shipped' && ($order->tracking_number || $order->shipping_courier))
                <!-- Tracking Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Maklumat Penjejakan</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($order->tracking_number)
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-2">Nombor Penjejakan</h3>
                                    <a href="{{ $order->getTrackingUrl() }}" 
                                       target="_blank" 
                                       class="text-lg font-bold text-blue-600 hover:text-blue-800 underline cursor-pointer">
                                        {{ $order->tracking_number }}
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">Klik nombor di atas untuk menjejak penghantaran di tracking.my</p>
                                </div>
                            @endif
                            
                            @if($order->shipping_courier)
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-2">Kurier Penghantaran</h3>
                                    <p class="text-lg font-bold text-gray-900">{{ $order->shipping_courier }}</p>
                                    <p class="text-sm text-gray-500 mt-1">Syarikat penghantaran yang digunakan</p>
                                </div>
                            @endif
                        </div>
                        
                        @if($order->shipped_at)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    <strong>Tarikh Penghantaran:</strong> {{ $order->shipped_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Order Information -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Maklumat Pesanan</h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-gray-600 text-sm">Nombor Pesanan</span>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    
                    <div>
                        <span class="text-gray-600 text-sm">Tarikh Pesanan</span>
                        <p class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <span class="text-gray-600 text-sm">Kaedah Pembayaran</span>
                        <p class="font-medium">{{ $order->getPaymentMethodDisplayName() }}</p>
                    </div>
                    
                    @if($order->stripe_payment_intent_id)
                        <div>
                            <span class="text-gray-600 text-sm">Stripe Payment Intent</span>
                            <p class="font-medium text-sm">{{ $order->stripe_payment_intent_id }}</p>
                        </div>
                    @endif
                    
                    @if($order->toyyibpay_bill_code)
                        <div>
                            <span class="text-gray-600 text-sm">ToyyibPay Bill Code</span>
                            <p class="font-medium text-sm">{{ $order->toyyibpay_bill_code }}</p>
                        </div>
                    @endif
                    
                    @if($order->shipped_at)
                        <div>
                            <span class="text-gray-600 text-sm">Tarikh Penghantaran</span>
                            <p class="font-medium">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                    
                                         @if($order->tracking_number)
                         <div>
                             <span class="text-gray-600 text-sm">Nombor Penjejakan</span>
                             <a href="{{ $order->getTrackingUrl() }}" 
                                target="_blank" 
                                class="font-medium text-blue-600 hover:text-blue-800 underline cursor-pointer">
                                 {{ $order->tracking_number }}
                             </a>
                         </div>
                     @endif
                    
                    @if($order->shipping_courier)
                        <div>
                            <span class="text-gray-600 text-sm">Kurier Penghantaran</span>
                            <p class="font-medium">{{ $order->shipping_courier }}</p>
                        </div>
                    @endif
                    
                    @if($order->delivered_at)
                        <div>
                            <span class="text-gray-600 text-sm">Tarikh Penerimaan</span>
                            <p class="font-medium">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                </div>
                
                <div class="p-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah Harga:</span>
                        <span class="font-medium">{{ $order->getFormattedSubtotal() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Penghantaran:</span>
                        <span class="font-medium text-green-600">{{ $order->getFormattedShippingCost() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cukai:</span>
                        <span class="font-medium">{{ $order->getFormattedTax() }}</span>
                    </div>
                    <div class="border-t border-gray-300 pt-3">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Jumlah Keseluruhan:</span>
                            <span class="text-red-600">{{ $order->getFormattedTotal() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Pengurusan Status</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Order Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                        <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="status" id="orderStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Tertunggak</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Telah Dihantar</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Telah Diterima</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                            
                            <!-- Tracking Information (shown when shipped is selected) -->
                            <div id="trackingFields" class="space-y-3" style="display: {{ $order->status == 'shipped' ? 'block' : 'none' }};">
                                <h4 class="text-sm font-medium text-gray-700">Maklumat Penjejakan</h4>
                                
                                <!-- Tracking Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombor Penjejakan</label>
                                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                                           placeholder="Masukkan nombor penjejakan" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Shipping Courier -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kurier Penghantaran</label>
                                    <select name="shipping_courier" id="shippingCourierSelect" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Kurier</option>
                                        <option value="Pos Malaysia" {{ $order->shipping_courier == 'Pos Malaysia' ? 'selected' : '' }}>Pos Malaysia</option>
                                        <option value="J&T" {{ $order->shipping_courier == 'J&T' ? 'selected' : '' }}>J&T</option>
                                        <option value="DHL" {{ $order->shipping_courier == 'DHL' ? 'selected' : '' }}>DHL</option>
                                        <option value="FedEx" {{ $order->shipping_courier == 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                        <option value="TNT" {{ $order->shipping_courier == 'TNT' ? 'selected' : '' }}>TNT</option>
                                        <option value="Shopee Express" {{ $order->shipping_courier == 'Shopee Express' ? 'selected' : '' }}>Shopee Express</option>
                                        <option value="Lazada Express" {{ $order->shipping_courier == 'Lazada Express' ? 'selected' : '' }}>Lazada Express</option>
                                        <option value="Ninja Van" {{ $order->shipping_courier == 'Ninja Van' ? 'selected' : '' }}>Ninja Van</option>
                                        <option value="GrabExpress" {{ $order->shipping_courier == 'GrabExpress' ? 'selected' : '' }}>GrabExpress</option>
                                        <option value="Gojek" {{ $order->shipping_courier == 'Gojek' ? 'selected' : '' }}>Gojek</option>
                                        <option value="Shopee Food" {{ $order->shipping_courier == 'Shopee Food' ? 'selected' : '' }}>Shopee Food</option>
                                        <option value="Lalamove" {{ $order->shipping_courier == 'Lalamove' ? 'selected' : '' }}>Lalamove</option>
                                        <option value="Uber Eats" {{ $order->shipping_courier == 'Uber Eats' ? 'selected' : '' }}>Uber Eats</option>
                                        <option value="Foodpanda" {{ $order->shipping_courier == 'Foodpanda' ? 'selected' : '' }}>Foodpanda</option>
                                        <option value="Deliveroo" {{ $order->shipping_courier == 'Deliveroo' ? 'selected' : '' }}>Deliveroo</option>
                                        <option value="GrabFood" {{ $order->shipping_courier == 'GrabFood' ? 'selected' : '' }}>GrabFood</option>
                                        <option value="Rider" {{ $order->shipping_courier == 'Rider' ? 'selected' : '' }}>Rider</option>
                                        <option value="Other" {{ $order->shipping_courier == 'Other' ? 'selected' : '' }}>Lain-lain</option>
                                    </select>
                                </div>
                            </div>
                            
                            <textarea name="notes" placeholder="Nota (pilihan)" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $order->notes }}</textarea>
                            
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                Kemas Kini Status
                            </button>
                        </form>
                    </div>

                    <!-- Tracking Information (for shipped orders) -->
                    @if($order->status === 'shipped')
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Kemas Kini Maklumat Penjejakan</h3>
                            <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="shipped">
                                
                                <!-- Tracking Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombor Penjejakan</label>
                                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                                           placeholder="Masukkan nombor penjejakan" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Shipping Courier -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kurier Penghantaran</label>
                                    <input type="text" name="shipping_courier" value="{{ $order->shipping_courier }}" 
                                           placeholder="Contoh: Pos Malaysia, J&T, etc." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                    Kemas Kini Maklumat Penjejakan
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Payment Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                        <form method="POST" action="{{ route('admin.orders.update-payment-status', $order->id) }}">
                            @csrf
                            @method('PATCH')
                            <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Tertunggak</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Telah Dibayar</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Gagal</option>
                                <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                            
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                Kemas Kini Pembayaran
                            </button>
                        </form>
                    </div>

                    <!-- Current Status Display -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Status Semasa:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeClass() }}">
                                @switch($order->status)
                                    @case('pending')
                                        Tertunggak
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
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Pembayaran:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusBadgeClass() }}">
                                @switch($order->payment_status)
                                    @case('pending')
                                        Tertunggak
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
                    </div>

                    <!-- Track Package -->
                    @if($order->tracking_number)
                        <div class="border-t border-gray-200 pt-4">
                            <a href="{{ $order->getTrackingUrl() }}" 
                               target="_blank"
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors block text-center">
                                📦 Jejak Penghantaran
                            </a>
                        </div>
                    @endif

                    <!-- Delete Order -->
                    @if(in_array($order->status, ['pending', 'cancelled']))
                        <div class="border-t border-gray-200 pt-4">
                            <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" 
                                  onsubmit="return confirm('Adakah anda pasti mahu memadamkan pesanan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                    Padam Pesanan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle tracking fields visibility based on status selection
    document.getElementById('orderStatus').addEventListener('change', function() {
        const trackingFields = document.getElementById('trackingFields');
        if (this.value === 'shipped') {
            trackingFields.style.display = 'block';
        } else {
            trackingFields.style.display = 'none';
        }
    });

    // Handle custom courier input visibility
    document.getElementById('shippingCourierSelect').addEventListener('change', function() {
        const customInput = document.getElementById('customCourierInput');
        if (this.value === 'Other') {
            customInput.style.display = 'block';
        } else {
            customInput.style.display = 'none';
        }
    });

    // Initialize custom courier input visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        const courierSelect = document.getElementById('shippingCourierSelect');
        const customInput = document.getElementById('customCourierInput');
        
        if (courierSelect.value === 'Other') {
            customInput.style.display = 'block';
        }
    });
</script>

@endsection 