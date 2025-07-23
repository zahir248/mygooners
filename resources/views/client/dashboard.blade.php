@extends('layouts.app')

@section('title', 'Panel Kawalan - MyGooners')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <!-- Profile & Welcome -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-6">
        <div class="flex items-center gap-4">
            @php $profileImg = trim(auth()->user()->profile_image ?? ''); @endphp
            @if($profileImg !== '')
                @if(Str::startsWith($profileImg, 'http'))
                    <img src="{{ $profileImg }}" alt="Avatar" class="w-20 h-20 rounded-full shadow-md object-cover">
                @else
                    <img src="{{ asset('storage/' . $profileImg) }}" alt="Avatar" class="w-20 h-20 rounded-full shadow-md object-cover">
                @endif
            @else
                <img src="{{ asset('images/profile-image-default.png') }}" alt="Avatar" class="w-20 h-20 rounded-full shadow-md object-cover">
            @endif
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">Selamat kembali, {{ auth()->user()->name }}!</h1>
                @if(auth()->user()->is_seller)
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Penjual Disahkan</span>
                        @if(auth()->user()->business_name)
                            <span class="text-gray-600 text-sm font-medium">{{ auth()->user()->business_name }}</span>
                        @endif
                    </div>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">Akaun Biasa</span>
                @endif
                <p class="text-gray-500 text-sm">Panel kawalan akaun & perniagaan anda di MyGooners</p>
            </div>
        </div>
        <div>
            <a href="{{ route('seller.info') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold shadow transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat Maklumat Penjual
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-green-100 text-green-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ $services->where('status', 'active')->count() ?? 0 }}</div>
            <div class="text-gray-500 text-sm">Perkhidmatan Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-purple-100 text-purple-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ $products->where('status', 'active')->count() ?? 0 }}</div>
            <div class="text-gray-500 text-sm">Produk Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ $pendingServices->count() + $pendingProducts->count() }}</div>
            <div class="text-gray-500 text-sm">Permohonan Menunggu</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ auth()->user()->is_seller ? 'Penjual' : 'Pengguna' }}</div>
            <div class="text-gray-500 text-sm">Status Akaun</div>
        </div>
    </div>



    <!-- Become Seller CTA -->
    @if(!auth()->user()->is_seller)
        <div class="bg-gradient-to-r from-yellow-100 to-yellow-200 border-l-4 border-yellow-500 rounded-xl shadow p-6 mb-10">
            <h2 class="text-xl font-bold text-yellow-800 mb-2">Jana pendapatan dengan menjadi penjual di MyGooners!</h2>
            <p class="text-yellow-700 mb-4">Isi maklumat perniagaan anda untuk mula menjual produk dan perkhidmatan kepada komuniti Gooners.</p>
            @if(session('show_seller_form'))
                @include('client.partials.seller-form')
            @else
                <form method="POST" action="{{ route('dashboard.show_seller_form') }}">
                    @csrf
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">Saya ingin menjadi penjual</button>
                </form>
            @endif
        </div>
    @endif

    <!-- Pending Requests Section -->
    @php
        $pendingUpdateRequests = $serviceUpdateRequests->where('status', 'pending');
        $totalPending = $pendingServices->count() + $pendingProducts->count() + $pendingUpdateRequests->count();
    @endphp
    @if($totalPending > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-yellow-800">Permohonan Menunggu Kelulusan</h3>
        </div>
        
        <!-- Pending Services -->
        @if($pendingServices->count() > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Perkhidmatan Menunggu ({{ $pendingServices->count() }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingServices as $service)
                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-start justify-between mb-2">
                        <h5 class="font-medium text-gray-900 line-clamp-2">{{ $service->title }}</h5>
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">
                            Menunggu
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $service->created_at->format('d M Y') }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pending.service.preview', $service->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat
                            </a>
                            @include('client.partials.cancel-modal', [
                                'action' => route('service.cancel', $service->id),
                                'message' => 'Adakah anda pasti mahu membatalkan permohonan perkhidmatan ini? Tindakan ini tidak boleh diundur.'
                            ])
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pending Products -->
        @if($pendingProducts->count() > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Produk Menunggu ({{ $pendingProducts->count() }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingProducts as $product)
                <div class="bg-white rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-start justify-between mb-2">
                        <h5 class="font-medium text-gray-900 line-clamp-2">{{ $product->title }}</h5>
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-medium">
                            Menunggu
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-900">RM {{ number_format($product->price, 2) }}</span>
                        <span class="text-xs text-gray-500">Stok: {{ $product->stock_quantity }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $product->created_at->format('d M Y') }}</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pending.product.preview', $product->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat
                            </a>
                            @include('client.partials.cancel-modal', [
                                'action' => route('product.cancel', $product->id),
                                'message' => 'Adakah anda pasti mahu membatalkan permohonan produk ini? Tindakan ini tidak boleh diundur.'
                            ])
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Pending Update Requests -->
        @if($pendingUpdateRequests->count() > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Kemaskini Perkhidmatan Menunggu ({{ $pendingUpdateRequests->count() }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($pendingUpdateRequests as $updateRequest)
                    @php
                        $originalService = $services->firstWhere('id', $updateRequest->original_service_id);
                    @endphp
                    @if($originalService)
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-start justify-between mb-2">
                            <h5 class="font-medium text-gray-900 line-clamp-2">{{ $originalService->title }}</h5>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                Kemaskini Menunggu
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">Permohonan kemaskini anda sedang disemak oleh admin.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">{{ $updateRequest->created_at->format('d M Y') }}</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('service.update.preview', $updateRequest->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat
                                </a>
                                @include('client.partials.cancel-modal', [
                                    'action' => route('service.update.cancel', $updateRequest->id),
                                    'message' => 'Adakah anda pasti mahu membatalkan permohonan kemaskini perkhidmatan ini? Tindakan ini tidak boleh diundur.'
                                ])
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja. Anda akan dimaklumkan melalui email.
            </p>
            <p class="text-sm text-yellow-800 mt-2">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <strong>Nota:</strong> Item yang menunggu kelulusan tidak dapat dilihat oleh pengguna lain sehingga diluluskan oleh admin.
            </p>
        </div>
    </div>
    @endif

    <!-- Rejected Requests Section -->
    @if($rejectedServices->count() > 0 || $rejectedProducts->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-red-100 text-red-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-red-800">Permohonan Ditolak</h3>
        </div>
        
        <!-- Rejected Services -->
        @if($rejectedServices->count() > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Perkhidmatan Ditolak ({{ $rejectedServices->count() }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rejectedServices as $service)
                <div class="bg-white rounded-lg p-4 border border-red-200">
                    <div class="flex items-start justify-between mb-2">
                        <h5 class="font-medium text-gray-900 line-clamp-2">{{ $service->title }}</h5>
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                            Ditolak
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
                    @if($service->rejection_reason)
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs font-medium text-red-800 mb-1">Sebab Penolakan:</p>
                        <p class="text-xs text-red-700">{{ $service->rejection_reason }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $service->created_at->format('d M Y') }}</span>
                                                        <div class="flex gap-2">
                                    <a href="{{ route('rejected.service.preview', $service->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat
                                    </a>
                                    <a href="{{ route('rejected.service.edit', $service->id) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Hantar Semula
                                    </a>
                                </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Rejected Products -->
        @if($rejectedProducts->count() > 0)
        <div class="mb-6">
            <h4 class="font-semibold text-gray-900 mb-3">Produk Ditolak ({{ $rejectedProducts->count() }})</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($rejectedProducts as $product)
                <div class="bg-white rounded-lg p-4 border border-red-200">
                    <div class="flex items-start justify-between mb-2">
                        <h5 class="font-medium text-gray-900 line-clamp-2">{{ $product->title }}</h5>
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                            Ditolak
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-900">RM {{ number_format($product->price, 2) }}</span>
                        <span class="text-xs text-gray-500">Stok: {{ $product->stock_quantity }}</span>
                    </div>
                    @if($product->rejection_reason)
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs font-medium text-red-800 mb-1">Sebab Penolakan:</p>
                        <p class="text-xs text-red-700">{{ $product->rejection_reason }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $product->created_at->format('d M Y') }}</span>
                                                        <div class="flex gap-2">
                                    <a href="{{ route('rejected.product.preview', $product->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Lihat
                                    </a>
                                    <a href="{{ route('rejected.product.edit', $product->id) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Hantar Semula
                                    </a>
                                </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-red-100 border border-red-300 rounded-lg p-4">
            <p class="text-sm text-red-800">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Permohonan anda telah ditolak oleh admin. Sila semak sebab penolakan dan buat permohonan baharu jika perlu.
            </p>
        </div>
        </div>
    @endif



    <!-- Rejected Update Requests Section -->
    @php
        $rejectedUpdateRequests = $serviceUpdateRequests->where('status', 'rejected');
    @endphp
    @if($rejectedUpdateRequests->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-red-100 text-red-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-red-800">Permohonan Kemaskini Ditolak</h3>
        </div>
        
        <div class="space-y-4">
            @foreach($rejectedUpdateRequests as $updateRequest)
                @php
                    $originalService = $services->firstWhere('id', $updateRequest->original_service_id);
                @endphp
                @if($originalService)
                <div class="bg-white rounded-lg p-4 border border-red-200">
                    <div class="flex items-start justify-between mb-2">
                        <h5 class="font-medium text-gray-900">{{ $originalService->title }}</h5>
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                            Kemaskini Ditolak
                        </span>
                    </div>
                    @if($updateRequest->rejection_reason)
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs font-medium text-red-800 mb-1">Sebab Penolakan:</p>
                        <p class="text-xs text-red-700">{{ $updateRequest->rejection_reason }}</p>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ $updateRequest->created_at->format('d M Y') }}</span>
                        <a href="{{ route('service.edit.request.create', $originalService->id) }}" 
                           class="text-green-600 hover:text-green-800 text-sm font-medium">
                            Hantar Semula
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        </div>
    @endif

    <!-- My Services Section -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Perkhidmatan Saya</h3>
            @if(auth()->user()->is_seller)
                <a href="{{ route('service.request.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Mohon Tambah Perkhidmatan
                </a>
            @endif
        </div>
        @if($services->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 flex flex-col justify-between relative">
                        @if($service->status == 'active' || $service->status == 'inactive')
                            <div class="absolute top-4 right-4" x-data="{ showError: false }">
                                @php $updateRequest = $serviceUpdateRequests->get($service->id); @endphp
                                @if($updateRequest && $updateRequest->status == 'pending')
                                    <button type="button"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 cursor-not-allowed opacity-60"
                                        @click="showError = true"
                                        title="Tidak boleh tukar status semasa permohonan kemaskini sedang menunggu kelulusan">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $service->status == 'active' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                    <div x-show="showError" x-transition class="absolute right-0 mt-2 w-64 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg shadow p-3 z-50">
                                        Tidak boleh tukar status semasa permohonan kemaskini sedang menunggu kelulusan.
                                        <button @click="showError = false" class="absolute top-1 right-2 text-red-400 hover:text-red-600">&times;</button>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('service.status.update', $service->id) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $service->status == 'active' ? 'inactive' : 'active' }}">
                                        <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $service->status == 'active' ? 'bg-green-600' : 'bg-gray-200' }}">
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $service->status == 'active' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($service->status == 'active') bg-green-100 text-green-700
                                    @elseif($service->status == 'inactive') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-500
                                    @endif font-semibold">{{ ucfirst($service->status) }}</span>
                                <span class="text-xs text-gray-400">{{ $service->created_at->format('d M Y') }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $service->title }}</h4>
                            <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('service.preview', $service->id) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline text-sm font-medium">Lihat</a>
                            @if($service->status == 'active' || $service->status == 'inactive')
                                @php
                                    $updateRequest = $serviceUpdateRequests->get($service->id);
                                @endphp
                                @if($updateRequest)
                                    @if($updateRequest->status == 'pending')
                                        <span class="text-yellow-600 text-sm font-medium">Kemaskini Menunggu</span>
                                    @elseif($updateRequest->status == 'rejected')
                                        <a href="{{ route('service.edit.request.create', $service->id) }}" class="text-red-600 hover:underline text-sm font-medium">Kemaskini Ditolak</a>
                                    @endif
                                @else
                                    <a href="{{ route('service.edit.request.create', $service->id) }}" class="text-green-600 hover:underline text-sm font-medium">Kemaskini</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 4h6"/></svg>
                <p class="mb-2">Anda belum menyiarkan sebarang perkhidmatan.</p>
                @if(auth()->user()->is_seller)
                    <a href="{{ route('service.request.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Mohon Tambah Perkhidmatan
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- My Products Section -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Produk Saya</h3>
            @if(auth()->user()->is_seller)
                <a href="{{ route('product.request.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Mohon Tambah Produk
                </a>
            @endif
        </div>
        @if($products->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 flex flex-col justify-between relative">
                        @if($product->status == 'active' || $product->status == 'inactive')
                            <div class="absolute top-4 right-4">
                                <form method="POST" action="{{ route('product.status.update', $product->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="{{ $product->status == 'active' ? 'inactive' : 'active' }}">
                                    <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $product->status == 'active' ? 'bg-green-600' : 'bg-gray-200' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $product->status == 'active' ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>
                                </form>
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($product->status == 'active') bg-green-100 text-green-700
                                    @elseif($product->status == 'inactive') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-500
                                    @endif font-semibold">{{ ucfirst($product->status) }}</span>
                                <span class="text-xs text-gray-400">{{ $product->created_at->format('d M Y') }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $product->title }}</h4>
                            <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('shop.show', $product->slug) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline text-sm font-medium">Lihat</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 4h6"/></svg>
                <p class="mb-2">Anda belum menyiarkan sebarang produk.</p>
                @if(auth()->user()->is_seller)
                    <a href="{{ route('product.request.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Mohon Tambah Produk
                    </a>
                @endif
            </div>
        @endif
    </div>


</div>
@endsection 