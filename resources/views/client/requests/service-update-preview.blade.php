@extends('layouts.app')

@section('title', 'Pratonton Kemaskini Perkhidmatan - MyGooners')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pratonton Kemaskini Perkhidmatan</h1>
        <p class="text-gray-600">Lihat perubahan yang anda telah buat untuk perkhidmatan ini</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <!-- Status Banner -->
        <div class="mb-6">
            @if($updateRequest->status === 'pending')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m-4-4h8"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Permohonan Menunggu</h3>
                            <p class="text-sm text-yellow-700 mt-1">Permohonan kemaskini anda sedang disemak oleh admin.</p>
                        </div>
                    </div>
                </div>
            @elseif($updateRequest->status === 'rejected')
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Permohonan Ditolak</h3>
                            <p class="text-sm text-red-700 mt-1">
                                @if($updateRequest->rejection_reason)
                                    Sebab: {{ $updateRequest->rejection_reason }}
                                @else
                                    Permohonan kemaskini anda telah ditolak oleh admin.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Service Information -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Maklumat Perkhidmatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Perkhidmatan Asal</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tajuk</label>
                            <p class="text-gray-900">{{ $originalService->title }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Kategori</label>
                            <p class="text-gray-900">{{ $originalService->category }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Lokasi</label>
                            <p class="text-gray-900">{{ $originalService->location }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Harga</label>
                            <p class="text-gray-900">{{ $originalService->pricing }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Maklumat Hubungan</label>
                            <p class="text-gray-900">{{ $originalService->contact_info }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Penerangan</label>
                            <p class="text-gray-900">{{ $originalService->description }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tag</label>
                            <p class="text-gray-900">
                                @if(is_array($originalService->tags) && count($originalService->tags) > 0)
                                    {{ implode(', ', $originalService->tags) }}
                                @else
                                    Tiada tag
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Perubahan yang Diminta</h3>
                    <div class="space-y-3">
                        @foreach($changes as $field => $change)
                            @if($field === 'images')
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <label class="text-sm font-medium text-green-800">Gambar</label>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 mb-1">Bilangan gambar asal: <span class="font-semibold">{{ $change['old_count'] }}</span></p>
                                        <p class="text-sm text-gray-600 mb-1">Bilangan gambar baharu: <span class="font-semibold">{{ $change['new_count'] }}</span></p>
                                        @if(!empty($change['added']))
                                            <p class="text-sm text-green-700 mb-1 mt-2">Gambar ditambah:</p>
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                @foreach($change['added'] as $img)
                                                    <img src="{{ Storage::url($img) }}" alt="Gambar Baharu" class="w-16 h-16 object-cover rounded-lg border-2 border-green-400">
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($change['removed']))
                                            <p class="text-sm text-red-700 mb-1 mt-2">Gambar dibuang:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($change['removed'] as $img)
                                                    <img src="{{ Storage::url($img) }}" alt="Gambar Lama" class="w-16 h-16 object-cover rounded-lg border-2 border-red-400">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <label class="text-sm font-medium text-green-800">
                                        @switch($field)
                                            @case('title') Tajuk Perkhidmatan @break
                                            @case('description') Penerangan @break
                                            @case('location') Lokasi @break
                                            @case('pricing') Harga @break
                                            @case('contact_info') Maklumat Hubungan @break
                                            @case('category') Kategori @break
                                            @case('tags') Tag @break
                                            @default {{ ucfirst($field) }}
                                        @endswitch
                                    </label>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 mb-1">Dari:</p>
                                        <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ $change['old'] }}</p>
                                        <p class="text-sm text-gray-600 mb-1 mt-2">Kepada:</p>
                                        <p class="text-sm text-green-900 bg-green-50 p-2 rounded">{{ $change['new'] }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @if(empty($changes))
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-sm text-gray-600">Tiada perubahan dikesan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Section -->
        @if(isset($changes['images']))
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Perubahan Gambar</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Gambar Semasa</h3>
                    @if(is_array($originalService->images) && count($originalService->images) > 0)
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($originalService->images as $image)
                                <img src="{{ Storage::url($image) }}" alt="Original Image" class="w-full h-24 object-cover rounded-lg">
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Tiada gambar</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Gambar Baharu</h3>
                    @if(is_array($updateRequest->images) && count($updateRequest->images) > 0)
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($updateRequest->images as $image)
                                @php
                                    $isNew = in_array($image, $changes['images']['added'] ?? []);
                                @endphp
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" alt="Updated Image" 
                                         class="w-full h-24 object-cover rounded-lg {{ $isNew ? 'ring-2 ring-green-400' : '' }}">
                                    @if($isNew)
                                        <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1 py-0.5 rounded">Baharu</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Tiada gambar</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-4">
            <a href="{{ route('dashboard') }}" 
               class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
                Kembali ke Dashboard
            </a>
            <div class="flex-1">
                @include('client.partials.cancel-modal', [
                    'action' => route('service.update.cancel', $updateRequest->id),
                    'message' => 'Adakah anda pasti mahu membatalkan permohonan kemaskini perkhidmatan ini? Tindakan ini tidak boleh diundur.',
                    'buttonClass' => 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center'
                ])
            </div>
            @if($updateRequest->status === 'rejected')
                <a href="{{ route('service.edit.request.create', $originalService->id) }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
                    Hantar Semula
                </a>
            @endif
        </div>
    </div>
</div>
@endsection 