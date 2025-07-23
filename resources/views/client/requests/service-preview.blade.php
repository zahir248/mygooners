@extends('layouts.app')

@section('title', 'Pratonton Perkhidmatan - MyGooners')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Pratonton Perkhidmatan</h1>
        <span class="px-3 py-1 rounded-full text-xs font-semibold
            @if($service->status == 'active') bg-green-100 text-green-700
            @elseif($service->status == 'inactive') bg-red-100 text-red-700
            @elseif($service->status == 'pending') bg-yellow-100 text-yellow-700
            @elseif($service->status == 'rejected') bg-red-100 text-red-700
            @else bg-gray-100 text-gray-500
            @endif">
            {{ ucfirst($service->status) }}
        </span>
    </div>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($service->images && is_array($service->images) && count($service->images) > 0)
        <div class="mb-8">
            <div class="relative h-96 rounded-xl overflow-hidden bg-gray-200 mb-4">
                <img src="{{ asset('storage/' . $service->images[0]) }}" alt="{{ $service->title }}" class="w-full h-full object-cover">
            </div>
            @if(count($service->images) > 1)
            <div class="grid grid-cols-4 gap-2">
                @foreach(array_slice($service->images, 1) as $img)
                <div class="aspect-square rounded-lg overflow-hidden bg-gray-200">
                    <img src="{{ asset('storage/' . $img) }}" alt="{{ $service->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer">
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @else
        <div class="h-64 bg-gray-200 flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $service->title }}</h2>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $service->category }}
                        </span>
                        <span class="text-gray-500 text-sm">{{ $service->location }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $service->pricing }}</div>
                    <div class="text-sm text-gray-500">Harga Perkhidmatan</div>
                </div>
            </div>
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Penerangan Perkhidmatan</h3>
                <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Maklumat Hubungan</h4>
                    <p class="text-gray-700">{{ $service->contact_info }}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-2">Lokasi</h4>
                    <p class="text-gray-700">{{ $service->location }}</p>
                </div>
            </div>
            @if($service->tags && is_array($service->tags) && count($service->tags) > 0)
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 mb-2">Tag</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($service->tags as $tag)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $tag }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="border-t border-gray-200 pt-4">
                <div class="text-sm text-gray-500">
                    <p><strong>Tarikh Permohonan:</strong> {{ $service->created_at->format('d F Y, H:i') }}</p>
                    <p><strong>ID Perkhidmatan:</strong> #{{ $service->id }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6">
        <a href="{{ route('dashboard') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection 