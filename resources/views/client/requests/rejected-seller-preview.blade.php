@extends('layouts.app')

@section('title', 'Permohonan Penjual Ditolak - MyGooners')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Permohonan Penjual Ditolak</h1>
                <p class="text-gray-600 mt-2">Semak maklumat permohonan penjual anda yang ditolak</p>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                Ditolak
            </span>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Personal Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Maklumat Peribadi</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama</label>
                        <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Emel</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Telefon</label>
                        <p class="text-gray-900">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Lokasi</label>
                        <p class="text-gray-900">{{ $user->location ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Maklumat Perniagaan</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nama Perniagaan</label>
                        <p class="text-gray-900 font-medium">{{ $user->business_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Jenis Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Pendaftaran Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_registration ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Alamat Perniagaan</label>
                        <p class="text-gray-900">{{ $user->business_address ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Kawasan Operasi</label>
                        <p class="text-gray-900">{{ $user->operating_area ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Laman Web</label>
                        <p class="text-gray-900">{{ $user->website ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tahun Pengalaman</label>
                        <p class="text-gray-900">{{ $user->years_experience ?? 'N/A' }} tahun</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Kemahiran</label>
                        <p class="text-gray-900">{{ $user->skills ?? 'N/A' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-600">Kawasan Perkhidmatan</label>
                        <p class="text-gray-900">{{ $user->service_areas ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Dokumen</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Dokumen Pengenalan</label>
                        @if($user->id_document)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $user->id_document) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Dokumen
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tiada dokumen</p>
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Selfie dengan ID</label>
                        @if($user->selfie_with_id)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $user->selfie_with_id) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Gambar
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Tiada gambar</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rejection Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Maklumat Penolakan</h3>
            </div>
            <div class="px-6 py-4">
                @if($user->seller_rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-red-800 mb-2">Sebab Penolakan:</p>
                        <p class="text-red-700">{{ $user->seller_rejection_reason }}</p>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Tiada sebab penolakan dinyatakan</p>
                @endif
            </div>

            <!-- Application Details -->
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Butiran Permohonan</h3>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tarikh Permohonan</label>
                        <p class="text-gray-900">
                            @if($user->seller_application_date)
                                {{ $user->seller_application_date->format('d/m/Y H:i') }}
                            @else
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-red-600 font-medium">Ditolak</p>
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
            <a href="{{ route('rejected.seller.edit') }}"
               class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors text-center">
               Hantar Semula
            </a>
        </div>
    </div>
</div>
@endsection 