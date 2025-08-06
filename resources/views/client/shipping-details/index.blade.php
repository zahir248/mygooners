@extends('layouts.app')

@section('title', 'Alamat Penghantaran - MyGooners')
@section('meta_description', 'Uruskan alamat penghantaran anda di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Alamat Penghantaran</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Alamat Penghantaran</h1>
                <p class="text-gray-600 mt-2">Uruskan alamat penghantaran anda untuk kemudahan semasa checkout</p>
            </div>
            <a href="{{ route('addresses.shipping.create') }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Alamat Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($shippingDetails->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($shippingDetails as $shippingDetail)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:border-red-300 transition-colors">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $shippingDetail->display_label }}</h3>
                                @if($shippingDetail->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                                        Alamat Lalai
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('addresses.shipping.edit', $shippingDetail) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('addresses.shipping.destroy', $shippingDetail) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Adakah anda pasti mahu memadamkan alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Padam">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong>Nama:</strong> {{ $shippingDetail->name }}</p>
                            <p><strong>Emel:</strong> {{ $shippingDetail->email }}</p>
                            <p><strong>Telefon:</strong> {{ $shippingDetail->phone }}</p>
                            <p><strong>Alamat:</strong></p>
                            <p class="pl-4">{{ $shippingDetail->full_address }}</p>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            @if(!$shippingDetail->is_default)
                                <form action="{{ route('addresses.shipping.set-default', $shippingDetail) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Tetapkan sebagai Lalai
                                    </button>
                                </form>
                            @else
                                <span class="text-green-600 text-sm font-medium">Alamat Lalai</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tiada alamat penghantaran</h3>
            <p class="text-gray-600 mb-6">Anda belum menambah sebarang alamat penghantaran. Tambah alamat pertama anda untuk memudahkan proses checkout.</p>
            <a href="{{ route('addresses.shipping.create') }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Alamat Pertama
            </a>
        </div>
    @endif
</div>
@endsection 