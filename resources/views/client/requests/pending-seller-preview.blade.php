@extends('layouts.app')

@section('title', 'Permohonan Penjual - MyGooners')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Permohonan Penjual</h1>
                <p class="text-gray-600 mt-2">Semak maklumat permohonan penjual anda</p>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                Menunggu Kelulusan Admin
            </span>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Personal Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Maklumat Peribadi</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telefon</label>
                        <p class="text-gray-900">{{ $user->phone ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <p class="text-gray-900">{{ $user->location ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <p class="text-gray-900">{{ $user->bio ?? 'Tidak dinyatakan' }}</p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Maklumat Perniagaan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_name ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Perniagaan</label>
                        <p class="text-gray-900">
                            @switch($user->business_type)
                                @case('individual')
                                    Individu
                                    @break
                                @case('company')
                                    Syarikat
                                    @break
                                @case('freelance')
                                    Freelance
                                    @break
                                @case('other')
                                    Lain-lain
                                    @break
                                @default
                                    Tidak dinyatakan
                            @endswitch
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Pendaftaran Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_registration ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengalaman</label>
                        <p class="text-gray-900">{{ $user->years_experience ?? '0' }} tahun</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_address ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Operasi / Wilayah</label>
                        <p class="text-gray-900">{{ $user->operating_area ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Laman Web / Media Sosial</label>
                        <p class="text-gray-900">{{ $user->website ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Perkhidmatan / Liputan</label>
                        <p class="text-gray-900">{{ $user->service_areas ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kemahiran / Tag / Kepakaran</label>
                        <p class="text-gray-900">{{ $user->skills ?? 'Tidak dinyatakan' }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Dokumen</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kad Pengenalan / Sijil / Lesen Perniagaan</label>
                        @if($user->id_document)
                            <a href="{{ Storage::url($user->id_document) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                Lihat Dokumen
                            </a>
                        @else
                            <p class="text-gray-500">Tidak dimuat naik</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selfie Bersama Kad Pengenalan</label>
                        @if($user->selfie_with_id)
                            <a href="{{ Storage::url($user->selfie_with_id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">
                                Lihat Gambar
                            </a>
                        @else
                            <p class="text-gray-500">Tidak dimuat naik</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Maklumat Permohonan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tarikh Permohonan</label>
                        <p class="text-gray-900">
                            @if($user->seller_application_date)
                                {{ $user->seller_application_date->format('d/m/Y H:i') }}
                            @else
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p class="text-gray-900">Menunggu kelulusan admin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-4">
            <a href="{{ route('dashboard') }}" 
               class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
                Kembali ke Dashboard
            </a>
            <div class="flex-1">
                @include('client.partials.cancel-modal', [
                    'action' => route('seller.request.cancel'),
                    'message' => 'Adakah anda pasti mahu membatalkan permohonan penjual ini? Tindakan ini tidak boleh diundur.',
                    'buttonClass' => 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center'
                ])
            </div>
        </div>
    </div>
</div>
@endsection 