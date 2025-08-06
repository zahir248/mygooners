@extends('layouts.app')

@section('title', 'Alamat - MyGooners')
@section('meta_description', 'Uruskan alamat bil dan penghantaran anda di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Alamat</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Alamat</h1>
                <p class="text-gray-600 mt-1">Uruskan alamat bil dan penghantaran anda</p>
            </div>
            
            <div class="flex space-x-3">
                <a href="{{ route('addresses.billing.create') }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                    Tambah Alamat Bil
                </a>
                <a href="{{ route('addresses.shipping.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm">
                    Tambah Alamat Penghantaran
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8">
            <button id="billing-tab" class="tab-button active py-4 px-1 border-b-2 border-red-500 text-red-600 font-medium text-sm">
                Alamat Bil ({{ $billingDetails->count() }})
            </button>
            <button id="shipping-tab" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                Alamat Penghantaran ({{ $shippingDetails->count() }})
            </button>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Billing Details Tab Content -->
    <div id="billing-content" class="tab-content active">
        @if($billingDetails->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($billingDetails as $billingDetail)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border {{ $billingDetail->is_default ? 'border-red-500' : 'border-gray-200' }}">
                    @if($billingDetail->is_default)
                        <div class="bg-red-500 text-white px-4 py-2 text-center text-sm font-medium">
                            Alamat Lalai
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $billingDetail->display_label }}</h3>
                                @if($billingDetail->label)
                                    <p class="text-sm text-gray-500">{{ $billingDetail->label }}</p>
                                @endif
                            </div>
                            
                            @if($billingDetail->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Lalai
                                </span>
                            @endif
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <p class="font-medium text-gray-900">{{ $billingDetail->name }}</p>
                            <p>{{ $billingDetail->email }}</p>
                            <p>{{ $billingDetail->phone }}</p>
                            <p class="mt-3">{{ $billingDetail->address }}</p>
                            <p>{{ $billingDetail->city }}, {{ $billingDetail->state }} {{ $billingDetail->postal_code }}</p>
                            <p>{{ $billingDetail->country }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2">
                                @if(!$billingDetail->is_default)
                                    <form method="POST" action="{{ route('addresses.billing.set-default', $billingDetail) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Tetapkan sebagai Lalai
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('addresses.billing.edit', $billingDetail) }}" 
                                   class="text-gray-600 hover:text-red-600 text-sm font-medium">
                                    Edit
                                </a>
                                
                                @if(!$billingDetail->is_default)
                                    <form method="POST" action="{{ route('addresses.billing.destroy', $billingDetail) }}" class="inline" 
                                          onsubmit="return confirm('Adakah anda pasti mahu memadamkan alamat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Padam
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Anda Belum Ada Alamat Bil</h2>
            <p class="text-gray-600 mb-8">Tambah alamat bil pertama anda untuk memudahkan proses pembelian seterusnya.</p>
            <a href="{{ route('addresses.billing.create') }}" 
               class="inline-block bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition-colors">
                Tambah Alamat Bil Pertama
            </a>
        </div>
    @endif
    </div>

    <!-- Shipping Details Tab Content -->
    <div id="shipping-content" class="tab-content hidden">
        @if($shippingDetails->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($shippingDetails as $shippingDetail)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border {{ $shippingDetail->is_default ? 'border-blue-500' : 'border-gray-200' }}">
                        @if($shippingDetail->is_default)
                            <div class="bg-blue-500 text-white px-4 py-2 text-center text-sm font-medium">
                                Alamat Lalai
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $shippingDetail->display_label }}</h3>
                                    @if($shippingDetail->label)
                                        <p class="text-sm text-gray-500">{{ $shippingDetail->label }}</p>
                                    @endif
                                </div>
                                
                                @if($shippingDetail->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Lalai
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <p class="font-medium text-gray-900">{{ $shippingDetail->name }}</p>
                                <p>{{ $shippingDetail->email }}</p>
                                <p>{{ $shippingDetail->phone }}</p>
                                <p class="mt-3">{{ $shippingDetail->address }}</p>
                                <p>{{ $shippingDetail->city }}, {{ $shippingDetail->state }} {{ $shippingDetail->postal_code }}</p>
                                <p>{{ $shippingDetail->country }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                                <div class="flex items-center space-x-2">
                                    @if(!$shippingDetail->is_default)
                                        <form method="POST" action="{{ route('addresses.shipping.set-default', $shippingDetail) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Tetapkan sebagai Lalai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('addresses.shipping.edit', $shippingDetail) }}" 
                                       class="text-gray-600 hover:text-blue-600 text-sm font-medium">
                                        Edit
                                    </a>
                                    
                                    @if(!$shippingDetail->is_default)
                                        <form method="POST" action="{{ route('addresses.shipping.destroy', $shippingDetail) }}" class="inline" 
                                              onsubmit="return confirm('Adakah anda pasti mahu memadamkan alamat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Padam
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Anda Belum Ada Alamat Penghantaran</h2>
                <p class="text-gray-600 mb-8">Tambah alamat penghantaran pertama anda untuk memudahkan proses checkout.</p>
                <a href="{{ route('addresses.shipping.create') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition-colors">
                    Tambah Alamat Penghantaran Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const billingTab = document.getElementById('billing-tab');
    const shippingTab = document.getElementById('shipping-tab');
    const billingContent = document.getElementById('billing-content');
    const shippingContent = document.getElementById('shipping-content');

    function switchTab(activeTab, activeContent, inactiveTab, inactiveContent) {
        // Update tab buttons
        activeTab.classList.add('active', 'border-red-500', 'text-red-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        
        inactiveTab.classList.remove('active', 'border-red-500', 'text-red-600');
        inactiveTab.classList.add('border-transparent', 'text-gray-500');
        
        // Update content
        activeContent.classList.remove('hidden');
        activeContent.classList.add('active');
        
        inactiveContent.classList.add('hidden');
        inactiveContent.classList.remove('active');
    }

    billingTab.addEventListener('click', function() {
        switchTab(billingTab, billingContent, shippingTab, shippingContent);
    });

    shippingTab.addEventListener('click', function() {
        switchTab(shippingTab, shippingContent, billingTab, billingContent);
    });
});
</script>
@endpush 