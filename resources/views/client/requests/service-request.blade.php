@extends('layouts.app')

@section('title', 'Mohon Tambah Perkhidmatan - MyGooners')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mohon Tambah Perkhidmatan</h1>
        <p class="text-gray-600">Isi maklumat perkhidmatan anda untuk permohonan kelulusan admin</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('service.request.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Nama Perkhidmatan *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Servis Baiki Komputer" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="category" name="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih kategori</option>
                        <option value="Teknologi" {{ old('category') == 'Teknologi' ? 'selected' : '' }}>Teknologi</option>
                        <option value="Kesihatan" {{ old('category') == 'Kesihatan' ? 'selected' : '' }}>Kesihatan</option>
                        <option value="Pendidikan" {{ old('category') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                        <option value="Kecantikan" {{ old('category') == 'Kecantikan' ? 'selected' : '' }}>Kecantikan</option>
                        <option value="Sukan" {{ old('category') == 'Sukan' ? 'selected' : '' }}>Sukan</option>
                        <option value="Makanan" {{ old('category') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="Pengangkutan" {{ old('category') == 'Pengangkutan' ? 'selected' : '' }}>Pengangkutan</option>
                        <option value="Lain-lain" {{ old('category') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
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
                          placeholder="Terangkan perkhidmatan anda dengan terperinci..." required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Perkhidmatan *</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: Kuala Lumpur, Selangor" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pricing" class="block text-sm font-medium text-gray-700 mb-2">Harga Perkhidmatan *</label>
                    <input type="text" id="pricing" name="pricing" value="{{ old('pricing') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: RM50-100, RM100/sejam" required>
                    @error('pricing')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">Maklumat Hubungan *</label>
                <input type="text" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: WhatsApp: 012-3456789, Email: service@example.com" required>
                @error('contact_info')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (Pisahkan dengan koma)</label>
                <input type="text" id="tags" name="tags" value="{{ old('tags') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: baiki komputer, IT support, troubleshooting">
                @error('tags')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Gambar Perkhidmatan (Pilihan)</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Anda boleh memuat naik sehingga 5 gambar (JPG, PNG, GIF)</p>
                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Maklumat Penting</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Permohonan anda akan disemak oleh admin dalam masa 1-3 hari bekerja</li>
                                <li>Pastikan semua maklumat yang diberikan adalah tepat dan lengkap</li>
                                <li>Anda akan dimaklumkan melalui email setelah permohonan diluluskan atau ditolak</li>
                                <li>Perkhidmatan yang diluluskan akan dipaparkan di platform MyGooners</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    Hantar Permohonan
                </button>
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 