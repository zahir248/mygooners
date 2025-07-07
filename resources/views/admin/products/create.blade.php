@extends('layouts.admin')

@section('title', 'Cipta Produk')

@section('content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-500">
                                    Produk
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z"></path>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">Cipta Produk</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Cipta Produk Baru</h1>
                <p class="mt-1 text-sm text-gray-600">Tambah produk barangan Arsenal baru ke kedai anda</p>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Maklumat Produk</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Tajuk Produk *
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="Masukkan tajuk produk">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Penerangan Produk *
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4" 
                                      required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                      placeholder="Masukkan penerangan produk yang terperinci">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori *
                            </label>
                            <select id="category" 
                                    name="category" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="">Pilih kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pricing -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Biasa (£) *
                                </label>
                                <input type="number" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price') }}"
                                       step="0.01"
                                       min="0"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                       placeholder="0.00">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Harga Jualan (£)
                                    <span class="text-gray-500 text-xs">(pilihan)</span>
                                </label>
                                <input type="number" 
                                       id="sale_price" 
                                       name="sale_price" 
                                       value="{{ old('sale_price') }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                       placeholder="0.00">
                                @error('sale_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Kuantiti Stok *
                            </label>
                            <input type="number" 
                                   id="stock_quantity" 
                                   name="stock_quantity" 
                                   value="{{ old('stock_quantity') }}"
                                   min="0"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="0">
                            @error('stock_quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">
                                URL Imej Produk
                                <span class="text-gray-500 text-xs">(pilihan)</span>
                            </label>
                            <input type="url" 
                                   id="image_url" 
                                   name="image_url" 
                                   value="{{ old('image_url') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="https://example.com/image.jpg">
                            @error('image_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                Tags
                                <span class="text-gray-500 text-xs">(dipisahkan koma)</span>
                            </label>
                            <input type="text" 
                                   id="tags" 
                                   name="tags" 
                                   value="{{ old('tags') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="arsenal, jersi, rasmi, 2024">
                            @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Product Settings -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tetapan Produk</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status *
                            </label>
                            <select id="status" 
                                    name="status" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draf</option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Featured Product -->
                        <div class="flex items-center">
                            <input id="is_featured" 
                                   name="is_featured" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                Produk Utama
                                <span class="text-gray-500 text-xs block">Paparkan produk ini di halaman utama</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tetapan SEO</h3>
                        <p class="text-sm text-gray-600">Tetapan pilihan untuk meningkatkan keterlihatan enjin carian</p>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Tajuk Meta
                                <span id="meta_title_count" class="text-gray-500 text-xs">(0/60 aksara)</span>
                            </label>
                            <input type="text" 
                                   id="meta_title" 
                                   name="meta_title" 
                                   value="{{ old('meta_title') }}"
                                   maxlength="60"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                   placeholder="Tajuk mesra SEO untuk enjin carian">
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Penerangan Meta
                                <span id="meta_description_count" class="text-gray-500 text-xs">(0/160 aksara)</span>
                            </label>
                            <textarea id="meta_description" 
                                      name="meta_description" 
                                      rows="3" 
                                      maxlength="160"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                      placeholder="Penerangan ringkas untuk hasil enjin carian">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('admin.products.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors">
                        Batal
                    </a>
                    <div class="flex space-x-3">
                        <button type="submit" 
                                name="action" 
                                value="draft"
                                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                            Simpan sebagai Draf
                        </button>
                        <button type="submit" 
                                name="action" 
                                value="publish"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-md transition-colors">
                            Cipta Produk
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-populate SEO fields
        document.getElementById('title').addEventListener('input', function(e) {
            const title = e.target.value;
            const metaTitleField = document.getElementById('meta_title');
            if (!metaTitleField.value) {
                metaTitleField.value = title.substring(0, 60);
                updateCharacterCount('meta_title');
            }
        });

        document.getElementById('description').addEventListener('input', function(e) {
            const description = e.target.value;
            const metaDescField = document.getElementById('meta_description');
            if (!metaDescField.value) {
                metaDescField.value = description.substring(0, 160);
                updateCharacterCount('meta_description');
            }
        });

        // Character counters
        function updateCharacterCount(fieldId) {
            const field = document.getElementById(fieldId);
            const countElement = document.getElementById(fieldId + '_count');
            const maxLength = fieldId === 'meta_title' ? 60 : 160;
            const currentLength = field.value.length;
            countElement.textContent = `(${currentLength}/${maxLength} characters)`;
            
            if (currentLength > maxLength * 0.9) {
                countElement.classList.add('text-yellow-600');
                countElement.classList.remove('text-gray-500');
            } else {
                countElement.classList.add('text-gray-500');
                countElement.classList.remove('text-yellow-600');
            }
        }

        document.getElementById('meta_title').addEventListener('input', () => updateCharacterCount('meta_title'));
        document.getElementById('meta_description').addEventListener('input', () => updateCharacterCount('meta_description'));

        // Initialize character counts
        updateCharacterCount('meta_title');
        updateCharacterCount('meta_description');
    </script>
@endsection 