@extends('layouts.app')

@section('title', 'Kemaskini Perkhidmatan - MyGooners')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kemaskini Perkhidmatan</h1>
        <p class="text-gray-600">Kemaskini maklumat perkhidmatan anda untuk permohonan kelulusan admin</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('service.edit.request.store', $service->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Nama Perkhidmatan *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $service->title) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Servis Baiki Komputer" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="category" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih kategori</option>
                        <option value="Coaching" {{ old('category', $service->category) == 'Coaching' ? 'selected' : '' }}>Coaching</option>
                        <option value="Transport" {{ old('category', $service->category) == 'Transport' ? 'selected' : '' }}>Pengangkutan</option>
                        <option value="Authentication" {{ old('category', $service->category) == 'Authentication' ? 'selected' : '' }}>Pengesahan</option>
                        <option value="Photography" {{ old('category', $service->category) == 'Photography' ? 'selected' : '' }}>Rafi</option>
                        <option value="Entertainment" {{ old('category', $service->category) == 'Entertainment' ? 'selected' : '' }}>Hiburan</option>
                        <option value="Catering" {{ old('category', $service->category) == 'Catering' ? 'selected' : '' }}>Katering</option>
                        <option value="Security" {{ old('category', $service->category) == 'Security' ? 'selected' : '' }}>Keselamatan</option>
                        <option value="Other" {{ old('category', $service->category) == 'Other' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Penerangan Perkhidmatan *</label>
                <textarea id="description" name="description" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                          placeholder="Terangkan perkhidmatan anda dengan terperinci..." required>{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Perkhidmatan *</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $service->location) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Kuala Lumpur, Selangor" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pricing" class="block text-sm font-medium text-gray-700 mb-2">Harga Perkhidmatan *</label>
                    <input type="text" id="pricing" name="pricing" value="{{ old('pricing', $service->pricing) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: RM50-100, RM100/sejam" required>
                    @error('pricing')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">Maklumat Hubungan *</label>
                <input type="text" id="contact_info" name="contact_info" value="{{ old('contact_info', $service->contact_info) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: WhatsApp: 012-3456789, Email: service@example.com" required>
                @error('contact_info')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (Pisahkan dengan koma)</label>
                <input type="text" id="tags" name="tags" value="{{ old('tags', is_array($service->tags) ? implode(', ', $service->tags) : '') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: baiki komputer, IT support, troubleshooting">
                @error('tags')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Images Section -->
            @if($service->images && count($service->images) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Semasa</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($service->images as $index => $image)
                    <div class="relative">
                        <img src="{{ route('service.image', basename($image)) }}" alt="Service Image" class="w-full h-24 object-cover rounded-lg">
                        <label class="absolute top-2 left-2">
                            <input type="checkbox" name="current_images[]" value="{{ $image }}" checked 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-1 text-xs text-white bg-black bg-opacity-50 px-1 rounded">Simpan</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <p class="text-sm text-gray-500 mt-2">Tandakan gambar yang ingin dikekalkan</p>
            </div>
            @endif

            <!-- New Images Section -->
            <div>
                <label for="new_images" class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Baharu</label>
                <input type="file" id="new_images" name="new_images[]" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Pilih gambar baharu untuk ditambah (maksimum 5MB setiap gambar)</p>
                @error('new_images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Maklumat Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Perubahan akan dihantar untuk kelulusan admin</li>
                                <li>Perkhidmatan anda akan kekal aktif sehingga kelulusan</li>
                                <li>Admin akan menyemak permohonan dalam masa 1-3 hari bekerja</li>
                                <li>Anda akan dimaklumkan melalui email selepas kelulusan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    Hantar Permohonan Kemaskini
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-lg font-semibold transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 