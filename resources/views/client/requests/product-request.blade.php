@extends('layouts.app')

@section('title', 'Mohon Tambah Produk - MyGooners')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mohon Tambah Produk</h1>
        <p class="text-gray-600">Isi maklumat produk anda untuk permohonan kelulusan admin</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('product.request.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Nama Produk *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: T-Shirt Arsenal Home 2024" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                    <select id="category" name="category" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Pilih kategori</option>
                        <option value="Pakaian" {{ old('category') == 'Pakaian' ? 'selected' : '' }}>Pakaian</option>
                        <option value="Kasut" {{ old('category') == 'Kasut' ? 'selected' : '' }}>Kasut</option>
                        <option value="Aksesori" {{ old('category') == 'Aksesori' ? 'selected' : '' }}>Aksesori</option>
                        <option value="Sukan" {{ old('category') == 'Sukan' ? 'selected' : '' }}>Sukan</option>
                        <option value="Elektronik" {{ old('category') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                        <option value="Makanan" {{ old('category') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                        <option value="Kesihatan" {{ old('category') == 'Kesihatan' ? 'selected' : '' }}>Kesihatan</option>
                        <option value="Lain-lain" {{ old('category') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Penerangan Produk *</label>
                <textarea id="description" name="description" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                          placeholder="Terangkan produk anda dengan terperinci..." required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga (RM) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="0.00" required>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan (RM) (Pilihan)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="0.00">
                    @error('sale_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="0" required>
                    @error('stock_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (Pisahkan dengan koma)</label>
                <input type="text" id="tags" name="tags" value="{{ old('tags') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: arsenal, jersey, football, sports">
                @error('tags')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk (Pilihan)</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Anda boleh memuat naik sehingga 5 gambar (JPG, PNG, GIF)</p>
                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title (Pilihan)</label>
                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Tajuk untuk SEO">
                @error('meta_title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description (Pilihan)</label>
                <textarea id="meta_description" name="meta_description" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                          placeholder="Penerangan untuk SEO">{{ old('meta_description') }}</textarea>
                @error('meta_description')
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
                                <li>Produk yang diluluskan akan dipaparkan di platform MyGooners</li>
                                <li>Pastikan harga dan stok yang dimasukkan adalah betul</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
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