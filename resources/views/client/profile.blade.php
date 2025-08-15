@extends('layouts.app')

@section('title', 'Maklumat Profil - MyGooners')

@section('content')
<div x-data="{ showIdModal: false, showSelfieModal: false, showProfileModal: false }" x-effect="document.body.classList.toggle('overflow-hidden', showProfileModal)" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Maklumat Profil</h1>
                <p class="text-gray-600">Maklumat lengkap profil dan akaun anda</p>
            </div>
            <div class="flex gap-3">
                <button @click="showProfileModal = true" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Kemaskini Maklumat Peribadi
                </button>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <div class="flex items-center gap-6 mb-6">
            @php $profileImg = trim($user->profile_image ?? ''); @endphp
            @if($profileImg !== '')
                @if(Str::startsWith($profileImg, 'http'))
                    <img src="{{ $profileImg }}" alt="Avatar" class="w-24 h-24 rounded-full shadow-md object-cover">
                @else
                    <img src="{{ asset('storage/' . $profileImg) }}" alt="Avatar" class="w-24 h-24 rounded-full shadow-md object-cover">
                @endif
            @else
                <img src="{{ asset('images/profile-image-default.png') }}" alt="Avatar" class="w-24 h-24 rounded-full shadow-md object-cover">
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mb-2">
                    @if($user->is_seller)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Penjual Disahkan
                        </span>
                        @if($user->business_name)
                            <span class="text-gray-500 text-sm">{{ $user->business_name }}</span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-sm font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Akaun Biasa
                        </span>
                    @endif
                </div>
                <p class="text-gray-600">{{ $user->bio ?: 'Tiada bio dinyatakan' }}</p>
            </div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Personal & Contact Information -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Maklumat Peribadi
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penuh</label>
                    <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <p class="text-gray-900">{{ $user->phone ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <p class="text-gray-900">{{ $user->location ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <p class="text-gray-900">{{ $user->bio ?: 'Tiada maklumat' }}</p>
                </div>
                @if($user->is_seller)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengalaman</label>
                        <p class="text-gray-900">{{ $user->years_experience ? $user->years_experience . ' tahun' : 'Tiada maklumat' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kemahiran</label>
                        <p class="text-gray-900">{{ $user->skills ?: 'Tiada maklumat' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Perkhidmatan</label>
                        <p class="text-gray-900">{{ $user->service_areas ?: 'Tiada maklumat' }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($user->is_seller)
        <!-- Business Information -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Maklumat Perniagaan
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perniagaan</label>
                    <p class="text-gray-900 font-medium">{{ $user->business_name ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Perniagaan</label>
                    <p class="text-gray-900">{{ $user->business_type ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Pendaftaran Perniagaan</label>
                    <p class="text-gray-900">{{ $user->business_registration ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perniagaan</label>
                    <p class="text-gray-900">{{ $user->business_address ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Operasi</label>
                    <p class="text-gray-900">{{ $user->operating_area ?: 'Tiada maklumat' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laman Web/Sosial Media</label>
                    <p class="text-gray-900">{{ $user->website ?: 'Tiada maklumat' }}</p>
                </div>
            </div>
        </div>
        @else
        <!-- Account Information for Non-Sellers -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Maklumat Akaun
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Akaun</label>
                    <p class="text-gray-900 font-medium">Akaun Biasa</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarikh Pendaftaran</label>
                    <p class="text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kemaskini Terakhir</label>
                    <p class="text-gray-900">{{ $user->updated_at->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Log Masuk Terakhir</label>
                    <p class="text-gray-900">{{ $user->last_login ? $user->last_login->format('d M Y H:i') : 'Tiada maklumat' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($user->is_seller)
    <!-- Verification Documents Section -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Dokumen Pengesahan
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($user->id_document)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Kad Pengenalan / Sijil / Lesen Perniagaan</h4>
                        <button @click="showIdModal = true" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Dokumen
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-600">Dokumen telah dimuat naik</span>
                        </div>
                    </div>
                </div>
            @endif

            @if($user->selfie_with_id)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Selfie Bersama ID</h4>
                        <button @click="showSelfieModal = true" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Lihat Selfie
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-600">Selfie ID telah dimuat naik</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Account Status -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-blue-900">Status Akaun Penjual</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg p-3 border border-blue-200">
                <div class="font-semibold text-blue-900">Status</div>
                <div class="text-blue-700">Disahkan & Aktif</div>
            </div>
            <div class="bg-white rounded-lg p-3 border border-blue-200">
                <div class="font-semibold text-blue-900">Tarikh Pendaftaran</div>
                <div class="text-blue-700">{{ $user->created_at->format('d M Y') }}</div>
            </div>
            <div class="bg-white rounded-lg p-3 border border-blue-200">
                <div class="font-semibold text-blue-900">Kemaskini Terakhir</div>
                <div class="text-blue-700">{{ $user->updated_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>
    @else
    <!-- Become Seller CTA for Non-Sellers -->
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-yellow-800">Jana Pendapatan dengan Menjadi Penjual</h3>
        </div>
        <p class="text-yellow-700 mb-4">Daftar sebagai penjual untuk mula menjual produk dan perkhidmatan kepada komuniti Gooners.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Daftar Sebagai Penjual
        </a>
    </div>
    @endif

    <!-- Document View Modals -->
    @if($user->is_seller)
    <!-- ID Document Modal -->
    <div x-show="showIdModal" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" x-trap="showIdModal" @keydown.window.escape="showIdModal = false" aria-modal="true" role="dialog">
        <div @click.away="showIdModal = false" class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-6 relative">
            <button @click="showIdModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl" aria-label="Tutup">&times;</button>
            <h3 class="text-xl font-bold mb-4">Kad Pengenalan / Sijil / Lesen Perniagaan</h3>
            <div class="flex justify-center">
                @php $idDoc = $user->id_document; @endphp
                @if($idDoc)
                    @if(Str::endsWith(strtolower($idDoc), ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" alt="ID Document" class="max-w-full max-h-96 object-contain rounded">
                    @elseif(Str::endsWith(strtolower($idDoc), ['.pdf']))
                        <div class="w-full h-96 bg-gray-100 rounded flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-gray-600">PDF Document</p>
                                <a href="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" target="_blank" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Buka PDF</a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Selfie Modal -->
    <div x-show="showSelfieModal" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" x-trap="showSelfieModal" @keydown.window.escape="showSelfieModal = false" aria-modal="true" role="dialog">
        <div @click.away="showSelfieModal = false" class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-6 relative">
            <button @click="showSelfieModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl" aria-label="Tutup">&times;</button>
            <h3 class="text-xl font-bold mb-4">Selfie Bersama ID</h3>
            <div class="flex justify-center">
                @php $selfie = $user->selfie_with_id; @endphp
                @if($selfie)
                    <img src="{{ route('seller.image', ['filename' => basename($selfie)]) }}" alt="Selfie with ID" class="max-w-full max-h-96 object-contain rounded">
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Profile Update Modal -->
    <div x-show="showProfileModal" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" x-trap="showProfileModal" @keydown.window.escape="showProfileModal = false" aria-modal="true" role="dialog">
        <div @click.away="showProfileModal = false" class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative overflow-y-auto max-h-[90vh] custom-scrollbar focus:outline-none" tabindex="0">
            <button @click="showProfileModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl" aria-label="Tutup">&times;</button>
            <h3 class="text-xl font-bold mb-4">Kemaskini Maklumat Peribadi</h3>
            <form method="POST" action="{{ route('dashboard.update_profile') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium">Nama</label>
                        <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">Email</label>
                        <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">No. Telefon</label>
                        <input type="text" name="phone" class="w-full border rounded p-2" value="{{ old('phone', $user->phone) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">Bio</label>
                        <textarea name="bio" class="w-full border rounded p-2">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    <div>
                        <label class="block font-medium">Lokasi</label>
                        <input type="text" name="location" class="w-full border rounded p-2" value="{{ old('location', $user->location) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Gambar Profil</label>
                        @php $profileImg = trim($user->profile_image ?? ''); @endphp
                        @if($profileImg !== '')
                            @if(Str::startsWith($profileImg, 'http'))
                                <img src="{{ $profileImg }}" alt="Profile Image" class="w-16 h-16 rounded-full mb-2 object-cover">
                            @else
                                <img src="{{ asset('storage/' . $profileImg) }}" alt="Profile Image" class="w-16 h-16 rounded-full mb-2 object-cover">
                            @endif
                        @else
                            <img src="{{ asset('images/profile-image-default.png') }}" alt="Profile Image" class="w-16 h-16 rounded-full mb-2 object-cover">
                        @endif
                        <input type="file" name="profile_image" class="w-full border rounded p-2" accept="image/*">
                    </div>
                    @if($user->is_seller)
                        <div class="col-span-2">
                            <hr class="my-4">
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">Maklumat Penjual</h4>
                        </div>
                        <div>
                            <label class="block font-medium">Nama Perniagaan</label>
                            <input type="text" name="business_name" class="w-full border rounded p-2" value="{{ old('business_name', $user->business_name) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Jenis Perniagaan</label>
                            <input type="text" name="business_type" class="w-full border rounded p-2" value="{{ old('business_type', $user->business_type) }}">
                        </div>
                        <div>
                            <label class="block font-medium">No. Pendaftaran Perniagaan</label>
                            <input type="text" name="business_registration" class="w-full border rounded p-2" value="{{ old('business_registration', $user->business_registration) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Alamat Perniagaan</label>
                            <input type="text" name="business_address" class="w-full border rounded p-2" value="{{ old('business_address', $user->business_address) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Kawasan Operasi / Wilayah</label>
                            <input type="text" name="operating_area" class="w-full border rounded p-2" value="{{ old('operating_area', $user->operating_area) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Laman Web / Media Sosial</label>
                            <input type="text" name="website" class="w-full border rounded p-2" value="{{ old('website', $user->website) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Tahun Pengalaman</label>
                            <input type="number" name="years_experience" class="w-full border rounded p-2" value="{{ old('years_experience', $user->years_experience) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Kemahiran / Tag / Kepakaran</label>
                            <input type="text" name="skills" class="w-full border rounded p-2" value="{{ old('skills', $user->skills) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Kawasan Perkhidmatan / Liputan</label>
                            <input type="text" name="service_areas" class="w-full border rounded p-2" value="{{ old('service_areas', $user->service_areas) }}">
                        </div>
                        <div>
                            <label class="block font-medium">Kad Pengenalan / Sijil / Lesen Perniagaan</label>
                            @php $idDoc = $user->id_document; @endphp
                                                    @if($idDoc)
                            <div class="mb-2">
                                @if(Str::endsWith(strtolower($idDoc), ['.jpg', '.jpeg', '.png', '.gif']))
                                    <img src="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" alt="ID Document" class="w-32 h-32 object-cover rounded border">
                                @elseif(Str::endsWith(strtolower($idDoc), ['.pdf']))
                                    <div class="w-32 h-32 bg-gray-100 rounded border flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-gray-500">PDF Document</span>
                                        </div>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Dokumen semasa</p>
                            </div>
                        @endif
                            <input type="file" name="id_document" class="w-full border rounded p-2" accept="image/*,application/pdf">
                            <p class="text-xs text-gray-500 mt-1">Gantikan jika ingin kemaskini</p>
                        </div>
                        <div>
                            <label class="block font-medium">Selfie Bersama Kad Pengenalan</label>
                            @php $selfie = $user->selfie_with_id; @endphp
                            @if($selfie)
                                <div class="mb-2">
                                    <img src="{{ route('seller.image', ['filename' => basename($selfie)]) }}" alt="Selfie with ID" class="w-32 h-32 object-cover rounded border">
                                    <p class="text-xs text-gray-500 mt-1">Selfie semasa</p>
                                </div>
                            @endif
                            <input type="file" name="selfie_with_id" class="w-full border rounded p-2" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Gantikan jika ingin kemaskini</p>
                        </div>
                    @endif
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">Simpan Maklumat</button>
            </form>
        </div>
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
                background: #f3f4f6;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #e5e7eb;
                border-radius: 8px;
            }
            .custom-scrollbar {
                scrollbar-width: thin;
                scrollbar-color: #e5e7eb #f3f4f6;
            }
        </style>
    </div>
</div>
@endsection 