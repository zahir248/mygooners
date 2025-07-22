@extends('layouts.app')

@section('title', 'Panel Kawalan - MyGooners')

@section('content')
<div x-data="{ showIdModal: false, showSelfieModal: false, showProfileModal: false }" x-effect="document.body.classList.toggle('overflow-hidden', showProfileModal)" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
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
            <button @click="showProfileModal = true" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Kemaskini Maklumat Peribadi
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-green-100 text-green-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ $services->count() ?? 0 }}</div>
            <div class="text-gray-500 text-sm">Perkhidmatan Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-purple-100 text-purple-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ $products->count() ?? 0 }}</div>
            <div class="text-gray-500 text-sm">Produk Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-blue-100 text-blue-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z"/></svg>
            </div>
            <div class="text-2xl font-bold">-</div>
            <div class="text-gray-500 text-sm">Jualan (akan datang)</div>
        </div>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="text-2xl font-bold">{{ auth()->user()->is_seller ? 'Penjual' : 'Pengguna' }}</div>
            <div class="text-gray-500 text-sm">Status Akaun</div>
        </div>
    </div>

    <!-- Seller Info Card -->
    @if(auth()->user()->is_seller)
    <div class="bg-white rounded-xl shadow p-6 mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Maklumat Penjual</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="font-semibold text-gray-700">Nama Perniagaan:</span> {{ auth()->user()->business_name }}</div>
                    <div><span class="font-semibold text-gray-700">Jenis Perniagaan:</span> {{ auth()->user()->business_type }}</div>
                    <div><span class="font-semibold text-gray-700">No. Pendaftaran:</span> {{ auth()->user()->business_registration ?: '-' }}</div>
                    <div><span class="font-semibold text-gray-700">Alamat Perniagaan:</span> {{ auth()->user()->business_address }}</div>
                    <div><span class="font-semibold text-gray-700">Kawasan Operasi:</span> {{ auth()->user()->operating_area }}</div>
                    <div><span class="font-semibold text-gray-700">Laman Web/Sosial:</span> {{ auth()->user()->website ?: '-' }}</div>
                    <div><span class="font-semibold text-gray-700">Tahun Pengalaman:</span> {{ auth()->user()->years_experience }}</div>
                    <div><span class="font-semibold text-gray-700">Kemahiran:</span> {{ auth()->user()->skills }}</div>
                    <div><span class="font-semibold text-gray-700">Kawasan Perkhidmatan:</span> {{ auth()->user()->service_areas }}</div>
                    <div><span class="font-semibold text-gray-700">Telefon:</span> {{ auth()->user()->phone }}</div>
                    <div><span class="font-semibold text-gray-700">Bio:</span> {{ auth()->user()->bio }}</div>
                </div>
            </div>
            <div class="flex flex-col gap-2 items-center">
                @if(auth()->user()->id_document)
                    <button @click="showIdModal = true" type="button" class="text-blue-600 hover:underline text-sm">Lihat Dokumen ID</button>
                @endif
                @if(auth()->user()->selfie_with_id)
                    <button @click="showSelfieModal = true" type="button" class="text-blue-600 hover:underline text-sm">Lihat Selfie ID</button>
                @endif
            </div>
        </div>
    </div>
    <!-- Modals for ID and Selfie -->
    <div>
        <!-- ID Document Modal -->
        <div x-show="showIdModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
                <button @click="showIdModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h3 class="text-lg font-bold mb-4">Dokumen ID</h3>
                @php $idDoc = auth()->user()->id_document; @endphp
                @if($idDoc && Str::endsWith(strtolower($idDoc), ['.jpg', '.jpeg', '.png', '.gif']))
                    <img src="{{ asset('storage/' . $idDoc) }}" alt="Dokumen ID" class="w-full rounded shadow">
                @elseif($idDoc && Str::endsWith(strtolower($idDoc), ['.pdf']))
                    <embed src="{{ asset('storage/' . $idDoc) }}" type="application/pdf" class="w-full h-96 rounded shadow" />
                @else
                    <p class="text-gray-500">Tidak dapat memaparkan dokumen ini.</p>
                @endif
            </div>
        </div>
        <!-- Selfie Modal -->
        <div x-show="showSelfieModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
                <button @click="showSelfieModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                <h3 class="text-lg font-bold mb-4">Selfie Bersama ID</h3>
                @php $selfie = auth()->user()->selfie_with_id; @endphp
                @if($selfie && Str::endsWith(strtolower($selfie), ['.jpg', '.jpeg', '.png', '.gif']))
                    <img src="{{ asset('storage/' . $selfie) }}" alt="Selfie ID" class="w-full rounded shadow">
                @else
                    <p class="text-gray-500">Tidak dapat memaparkan selfie ini.</p>
                @endif
            </div>
        </div>
    </div>
    @endif

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

    <!-- My Services Section -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Perkhidmatan Saya</h3>
            @if(auth()->user()->is_seller)
                <a href="#" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Mohon Tambah Perkhidmatan
                </a>
            @endif
        </div>
        @if($services->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 rounded-full text-xs {{ $service->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} font-semibold">{{ ucfirst($service->status) }}</span>
                                <span class="text-xs text-gray-400">{{ $service->created_at->format('d M Y') }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $service->title }}</h4>
                            <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('services.show', $service->slug) }}" class="text-blue-600 hover:underline text-sm font-medium">Lihat</a>
                            <a href="{{ route('services.edit', $service->id) }}" class="text-yellow-600 hover:underline text-sm font-medium">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 4h6"/></svg>
                <p class="mb-2">Anda belum menyiarkan sebarang perkhidmatan.</p>
                @if(auth()->user()->is_seller)
                    <a href="#" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors mt-2">
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
                <a href="#" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Mohon Tambah Produk
                </a>
            @endif
        </div>
        @if($products->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 rounded-full text-xs {{ $product->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} font-semibold">{{ ucfirst($product->status) }}</span>
                                <span class="text-xs text-gray-400">{{ $product->created_at->format('d M Y') }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $product->title }}</h4>
                            <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <a href="{{ route('shop.show', $product->slug) }}" class="text-blue-600 hover:underline text-sm font-medium">Lihat</a>
                            <a href="{{ route('shop.edit', $product->id) }}" class="text-yellow-600 hover:underline text-sm font-medium">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 4h6"/></svg>
                <p class="mb-2">Anda belum menyiarkan sebarang produk.</p>
                @if(auth()->user()->is_seller)
                    <a href="#" class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Mohon Tambah Produk
                    </a>
                @endif
            </div>
        @endif
    </div>

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
                        <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', auth()->user()->name) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">Email</label>
                        <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email', auth()->user()->email) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">No. Telefon</label>
                        <input type="text" name="phone" class="w-full border rounded p-2" value="{{ old('phone', auth()->user()->phone) }}" required>
                    </div>
                    <div>
                        <label class="block font-medium">Bio</label>
                        <textarea name="bio" class="w-full border rounded p-2">{{ old('bio', auth()->user()->bio) }}</textarea>
                    </div>
                    <div>
                        <label class="block font-medium">Lokasi</label>
                        <input type="text" name="location" class="w-full border rounded p-2" value="{{ old('location', auth()->user()->location) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Gambar Profil</label>
                        @php $profileImg = trim(auth()->user()->profile_image ?? ''); @endphp
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
                @if(auth()->user()->is_seller)
                    <div class="col-span-2">
                        <hr class="my-4">
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Maklumat Penjual</h4>
                    </div>
                    <div>
                        <label class="block font-medium">Nama Perniagaan</label>
                        <input type="text" name="business_name" class="w-full border rounded p-2" value="{{ old('business_name', auth()->user()->business_name) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Jenis Perniagaan</label>
                        <input type="text" name="business_type" class="w-full border rounded p-2" value="{{ old('business_type', auth()->user()->business_type) }}">
                    </div>
                    <div>
                        <label class="block font-medium">No. Pendaftaran Perniagaan</label>
                        <input type="text" name="business_registration" class="w-full border rounded p-2" value="{{ old('business_registration', auth()->user()->business_registration) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Alamat Perniagaan</label>
                        <input type="text" name="business_address" class="w-full border rounded p-2" value="{{ old('business_address', auth()->user()->business_address) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Kawasan Operasi / Wilayah</label>
                        <input type="text" name="operating_area" class="w-full border rounded p-2" value="{{ old('operating_area', auth()->user()->operating_area) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Laman Web / Media Sosial</label>
                        <input type="text" name="website" class="w-full border rounded p-2" value="{{ old('website', auth()->user()->website) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Tahun Pengalaman</label>
                        <input type="number" name="years_experience" class="w-full border rounded p-2" value="{{ old('years_experience', auth()->user()->years_experience) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Kemahiran / Tag / Kepakaran</label>
                        <input type="text" name="skills" class="w-full border rounded p-2" value="{{ old('skills', auth()->user()->skills) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Kawasan Perkhidmatan / Liputan</label>
                        <input type="text" name="service_areas" class="w-full border rounded p-2" value="{{ old('service_areas', auth()->user()->service_areas) }}">
                    </div>
                    <div>
                        <label class="block font-medium">Muat Naik Kad Pengenalan / Sijil / Lesen Perniagaan (Gantikan jika ingin kemaskini)</label>
                        <input type="file" name="id_document" class="w-full border rounded p-2" accept="image/*,application/pdf">
                    </div>
                    <div>
                        <label class="block font-medium">Selfie Bersama Kad Pengenalan (Gantikan jika ingin kemaskini)</label>
                        <input type="file" name="selfie_with_id" class="w-full border rounded p-2" accept="image/*">
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
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.effect(() => {
                    if (Alpine.store('showProfileModal') || document.querySelector('[x-show=showProfileModal]')?.__x?.$data.showProfileModal) {
                        document.body.classList.add('overflow-hidden');
                    } else {
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });
        </script>
    </div>
</div>
@endsection 