@extends('layouts.app')

@section('title', 'Maklumat Penjual - MyGooners')

@section('content')
<div x-data="{ showIdModal: false, showSelfieModal: false, showProfileModal: false }" x-effect="document.body.classList.toggle('overflow-hidden', showProfileModal)" class="min-h-screen bg-gray-50 pb-8">
    <div class="max-w-7xl mx-auto py-4 sm:py-6 lg:py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Maklumat Penjual</h1>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed">Maklumat lengkap perniagaan dan dokumen pengesahan anda</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <button @click="showProfileModal = true" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 sm:px-4 sm:py-2 rounded-lg font-medium transition-colors text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Kemaskini Maklumat Peribadi
                    </button>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 sm:px-4 sm:py-2 rounded-lg font-medium transition-colors text-sm sm:text-base">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 mb-4 sm:mb-6">
                @php $profileImg = trim($user->profile_image ?? ''); @endphp
                @if($profileImg !== '')
                    @if(Str::startsWith($profileImg, 'http'))
                        <img src="{{ $profileImg }}" alt="Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full shadow-md object-cover mx-auto sm:mx-0">
                    @else
                        <img src="{{ asset('storage/' . $profileImg) }}" alt="Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full shadow-md object-cover mx-auto sm:mx-0">
                    @endif
                @else
                    <img src="{{ asset('images/profile-image-default.png') }}" alt="Avatar" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full shadow-md object-cover mx-auto sm:mx-0">
                @endif
                <div class="text-center sm:text-left flex-1">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ $user->name }}</h2>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Penjual Disahkan
                        </span>
                        <span class="text-gray-500 text-sm">{{ $user->business_name }}</span>
                    </div>
                    <p class="text-gray-600 text-sm sm:text-base">{{ $user->bio ?: 'Tiada bio dinyatakan' }}</p>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
            <!-- Business Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Maklumat Perniagaan
                </h3>
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perniagaan</label>
                        <p class="text-gray-900 font-medium text-sm sm:text-base break-words">{{ $user->business_name ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Perniagaan</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->business_type ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Pendaftaran Perniagaan</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->business_registration ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Perniagaan</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->business_address ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Operasi</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->operating_area ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Laman Web/Sosial Media</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->website ?: 'Tidak dinyatakan' }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal & Contact Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Maklumat Peribadi & Hubungan
                </h3>
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penuh</label>
                        <p class="text-gray-900 font-medium text-sm sm:text-base break-words">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->phone ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->location ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pengalaman</label>
                        <p class="text-gray-900 text-sm sm:text-base">{{ $user->years_experience ? $user->years_experience . ' tahun' : 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kemahiran</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->skills ?: 'Tidak dinyatakan' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kawasan Perkhidmatan</label>
                        <p class="text-gray-900 text-sm sm:text-base break-words">{{ $user->service_areas ?: 'Tidak dinyatakan' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Documents -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-6 sm:mb-8">
            <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dokumen Pengesahan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                @if($user->id_document)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Dokumen ID</h4>
                            <button @click="showIdModal = true" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Dokumen
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-600">Dokumen ID telah dimuat naik</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($user->selfie_with_id)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base">Selfie Bersama ID</h4>
                            <button @click="showSelfieModal = true" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Selfie
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-blue-900">Status Akaun Penjual</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 text-sm">
                <div class="bg-white rounded-lg p-3 border border-blue-200">
                    <div class="font-semibold text-blue-900">Status</div>
                    <div class="text-blue-700">Disahkan & Aktif</div>
                </div>
                <div class="bg-white rounded-lg p-3 border border-blue-200">
                    <div class="font-semibold text-blue-900">Tarikh Pendaftaran</div>
                    <div class="text-blue-700">{{ $user->created_at->format('d M Y') }}</div>
                </div>
                <div class="bg-white rounded-lg p-3 border border-blue-200 sm:col-span-2 lg:col-span-1">
                    <div class="font-semibold text-blue-900">Kemaskini Terakhir</div>
                    <div class="text-blue-700">{{ $user->updated_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for Document Viewing -->
    <!-- ID Document Modal -->
    <div x-show="showIdModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
            <button @click="showIdModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">Dokumen ID</h3>
            @php $idDoc = $user->id_document; @endphp
            @if($idDoc && Str::endsWith(strtolower($idDoc), ['.jpg', '.jpeg', '.png', '.gif']))
                <img src="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" alt="Dokumen ID" class="w-full max-w-md mx-auto rounded shadow">
            @elseif($idDoc && Str::endsWith(strtolower($idDoc), ['.pdf']))
                <embed src="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" type="application/pdf" class="w-full h-80 rounded shadow" />
            @else
                <p class="text-gray-500">Tidak dapat memaparkan dokumen ini.</p>
            @endif
        </div>
    </div>

    <!-- Selfie Modal -->
    <div x-show="showSelfieModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
            <button @click="showSelfieModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">Selfie Bersama ID</h3>
            @php $selfie = $user->selfie_with_id; @endphp
            @if($selfie && Str::endsWith(strtolower($selfie), ['.jpg', '.jpeg', '.png', '.gif']))
                <img src="{{ route('seller.image', ['filename' => basename($selfie)]) }}" alt="Selfie ID" class="w-full max-w-md mx-auto rounded shadow">
            @else
                <p class="text-gray-500">Tidak dapat memaparkan selfie ini.</p>
            @endif
        </div>
    </div>

    <!-- Profile Update Modal -->
    <div x-show="showProfileModal" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150" style="display: none;" class="fixed inset-0 z-50 flex items-start justify-center bg-black bg-opacity-60 p-2 sm:p-4 overflow-y-auto" x-trap="showProfileModal" @keydown.window.escape="showProfileModal = false" aria-modal="true" role="dialog">
        <div @click.away="showProfileModal = false" class="bg-white rounded-lg shadow-lg w-full max-w-4xl my-4 sm:my-8 flex flex-col max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-4rem)]">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900">Kemaskini Maklumat Peribadi</h3>
                <button @click="showProfileModal = false" class="text-gray-500 hover:text-gray-700 text-2xl" aria-label="Tutup">&times;</button>
            </div>
            
            <!-- Modal Body with Scroll -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 min-h-0">
                <form id="profile-update-form" method="POST" action="{{ route('dashboard.update_profile') }}" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:gap-6">
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Nama</label>
                            <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">No. Telefon</label>
                            <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('phone', $user->phone) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Bio</label>
                            <textarea name="bio" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Lokasi</label>
                            <input type="text" name="location" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('location', $user->location) }}">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Gambar Profil</label>
                            @php $profileImg = trim($user->profile_image ?? ''); @endphp
                            @if($profileImg !== '')
                                @if(Str::startsWith($profileImg, 'http'))
                                    <img src="{{ $profileImg }}" alt="Profile Image" class="w-20 h-20 sm:w-16 sm:h-16 rounded-full mb-3 object-cover border-2 border-gray-200">
                                @else
                                    <img src="{{ asset('storage/' . $profileImg) }}" alt="Profile Image" class="w-20 h-20 sm:w-16 sm:h-16 rounded-full mb-3 object-cover border-2 border-gray-200">
                                @endif
                            @else
                                <img src="{{ asset('images/profile-image-default.png') }}" alt="Profile Image" class="w-20 h-20 sm:w-16 sm:h-16 rounded-full mb-3 object-cover border-2 border-gray-200">
                            @endif
                            <input type="file" name="profile_image" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*">
                        </div>
                        
                        <!-- Business Information Section -->
                        <div class="col-span-2">
                            <hr class="my-6 border-gray-300">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Maklumat Penjual
                            </h4>
                        </div>
                        
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Nama Perniagaan</label>
                            <input type="text" name="business_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('business_name', $user->business_name) }}">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Jenis Perniagaan</label>
                            <input type="text" name="business_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('business_type', $user->business_type) }}">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">No. Pendaftaran Perniagaan</label>
                            <input type="text" name="business_registration" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('business_registration', $user->business_registration) }}">
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Alamat Perniagaan</label>
                            <input type="text" name="business_address" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('business_address', $user->business_address) }}">
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Kawasan Operasi / Wilayah</label>
                            <input type="text" name="operating_area" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('operating_area', $user->operating_area) }}">
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Laman Web / Media Sosial</label>
                            <input type="text" name="website" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('website', $user->website) }}">
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Tahun Pengalaman</label>
                            <input type="number" name="years_experience" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('years_experience', $user->years_experience) }}">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">Kemahiran / Tag / Kepakaran</label>
                            <input type="text" name="skills" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('skills', $user->skills) }}">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-2">Kawasan Perkhidmatan / Liputan</label>
                            <input type="text" name="service_areas" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('service_areas', $user->skills) }}">
                        </div>
                        
                        <!-- Document Upload Section -->
                        <div class="col-span-2">
                            <hr class="my-6 sm:my-8 border-gray-300">
                            <h4 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Dokumen Pengesahan
                            </h4>
                        </div>
                        
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Kad Pengenalan / Sijil / Lesen Perniagaan</label>
                            @php $idDoc = $user->id_document; @endphp
                            @if($idDoc)
                                <div class="mb-3">
                                    @if(Str::endsWith(strtolower($idDoc), ['.jpg', '.jpeg', '.png', '.gif']))
                                        <img src="{{ route('seller.document', ['filename' => basename($idDoc)]) }}" alt="ID Document" class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-lg border-2 border-gray-200">
                                    @elseif(Str::endsWith(strtolower($idDoc), ['.pdf']))
                                        <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-100 rounded-lg border-2 border-gray-200 flex items-center justify-center">
                                            <div class="text-center">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-gray-500">PDF Document</span>
                                            </div>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">Dokumen semasa</p>
                                </div>
                            @endif
                            <input type="file" name="id_document" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*,application/pdf">
                            <p class="text-xs text-gray-500 mt-1">Gantikan jika ingin kemaskini</p>
                        </div>
                        <div>
                            <label class="block text-sm sm:text-base font-medium text-gray-700 mb-2">Selfie Bersama Kad Pengenalan</label>
                            @php $selfie = $user->selfie_with_id; @endphp
                            @if($selfie)
                                <div class="mb-3">
                                    <img src="{{ route('seller.image', ['filename' => basename($selfie)]) }}" alt="Selfie with ID" class="w-24 h-24 sm:w-32 sm:h-32 object-cover rounded-lg border-2 border-gray-200">
                                    <p class="text-xs text-gray-500 mt-1">Selfie semasa</p>
                                </div>
                            @endif
                            <input type="file" name="selfie_with_id" class="w-full border border-gray-300 rounded-lg px-3 py-3 sm:py-2 text-sm sm:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Gantikan jika ingin kemaskini</p>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 p-4 sm:p-6 border-t border-gray-200 flex-shrink-0">
                <button @click="showProfileModal = false" class="px-4 py-3 sm:py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Batal
                </button>
                <button type="submit" form="profile-update-form" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 sm:py-2 rounded-lg font-semibold transition-colors">
                    Simpan Maklumat
                </button>
            </div>
        </div>
        
        <style>
            /* Custom scrollbar for the modal body */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }
            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 3px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            .overflow-y-auto {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }
            
            /* Ensure modal container respects height constraints */
            .max-h-\[calc\(100vh-2rem\)\] {
                max-height: calc(100vh - 2rem);
            }
            .sm\:max-h-\[calc\(100vh-4rem\)\] {
                max-height: calc(100vh - 4rem);
            }
            
            /* Prevent body scroll when modal is open */
            body.modal-open {
                overflow: hidden;
            }
            
            /* Mobile-first responsive improvements */
            @media (max-width: 640px) {
                .modal-mobile-padding {
                    padding: 1rem;
                }
                
                .modal-mobile-text {
                    font-size: 0.875rem;
                }
                
                .modal-mobile-input {
                    padding: 0.75rem;
                }
                
                /* Ensure two-column layout works properly on mobile */
                .grid-cols-2 > div {
                    min-width: 0;
                }
                
                /* Optimize input field sizing on mobile */
                .grid-cols-2 input,
                .grid-cols-2 textarea {
                    font-size: 14px;
                    padding: 8px 12px;
                }
                
                /* Ensure labels don't wrap awkwardly */
                .grid-cols-2 label {
                    font-size: 13px;
                    line-height: 1.2;
                }
            }
        </style>
        
        <script>
            // Handle body scroll when modal is open
            document.addEventListener('alpine:init', () => {
                Alpine.effect(() => {
                    const modal = document.querySelector('[x-show="showProfileModal"]');
                    if (modal && modal.__x && modal.__x.$data.showProfileModal) {
                        document.body.classList.add('modal-open');
                    } else {
                        document.body.classList.remove('modal-open');
                    }
                });
            });
        </script>
    </div>
</div>
@endsection 