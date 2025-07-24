@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Kemaskini Permohonan Produk</h1>
            </div>
            <p class="text-gray-600">Kemaskini maklumat produk anda berdasarkan sebab penolakan dan hantar semula untuk kelulusan.</p>
        </div>

        <!-- Rejection Reason Reminder -->
        @if($product->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="bg-red-100 text-red-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800 mb-1">Sebab Penolakan Sebelum Ini:</h3>
                    <p class="text-sm text-red-700">{{ $product->rejection_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">Sila perbaiki isu-isu ini sebelum menghantar semula permohonan anda.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Maklumat Produk</h2>
            </div>

            <form action="{{ route('rejected.product.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tajuk Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $product->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category" 
                                name="category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="Jerseys" {{ old('category', $product->category) == 'Jerseys' ? 'selected' : '' }}>Jersi</option>
                            <option value="Training Wear" {{ old('category', $product->category) == 'Training Wear' ? 'selected' : '' }}>Pakaian Latihan</option>
                            <option value="Accessories" {{ old('category', $product->category) == 'Accessories' ? 'selected' : '' }}>Aksesori</option>
                            <option value="Footwear" {{ old('category', $product->category) == 'Footwear' ? 'selected' : '' }}>Kasut</option>
                            <option value="Collectibles" {{ old('category', $product->category) == 'Collectibles' ? 'selected' : '' }}>Koleksi</option>
                            <option value="Other" {{ old('category', $product->category) == 'Other' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Asal (RM) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="{{ old('price', $product->price) }}"
                               step="0.01"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sale Price -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Jualan (RM)
                        </label>
                        <input type="number" 
                               id="sale_price" 
                               name="sale_price" 
                               value="{{ old('sale_price', $product->sale_price) }}"
                               step="0.01"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tiada harga jualan</p>
                        @error('sale_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Quantity -->
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Kuantiti Stok <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="stock_quantity" 
                               name="stock_quantity" 
                               value="{{ old('stock_quantity', $product->stock_quantity) }}"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('stock_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Penerangan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="md:col-span-2">
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                            Tag
                        </label>
                        <input type="text" 
                               id="tags" 
                               name="tags" 
                               value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}"
                               placeholder="Contoh: jersi arsenal, original, berkualiti"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan tag dengan koma</p>
                        @error('tags')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Current Images -->
                    @if($product->images && count($product->images) > 0)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Semasa
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($product->images as $index => $image)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="Product Image" 
                                     class="w-full h-24 object-cover rounded-lg border">
                                <input type="hidden" name="current_images[]" value="{{ $image }}">
                                <button type="button" 
                                        onclick="removeImage(this)" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- New Images -->
                    <div class="md:col-span-2">
                        <label for="new_images" class="block text-sm font-medium text-gray-700 mb-2">
                            Tambah Gambar Baharu
                        </label>
                        <input type="file" 
                               id="new_images" 
                               name="new_images[]" 
                               multiple
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Anda boleh memilih beberapa gambar. Format yang diterima: JPG, PNG, GIF. Saiz maksimum: 10MB setiap gambar.</p>
                        @error('new_images.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Product Variations Section -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Varian Produk</h3>
                        <button type="button" onclick="toggleVariations()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <span id="variations-toggle-text">Tambah Varian</span>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Tambah varian secara manual untuk produk dengan pilihan saiz, warna, atau ciri lain</p>
                    
                    <div id="variations-section" class="space-y-6" style="display: none;">
                        <!-- Variation Label Field -->
                        <div>
                            <label for="variation_label" class="block text-sm font-medium text-gray-700 mb-2">Label Varian</label>
                            <input type="text" name="variation_label" id="variation_label" 
                                   value="{{ old('variation_label', $product->variation_label ?? 'Pilihan Varian') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Contoh: Saiz, Warna, Jenis, dll">
                            <p class="text-xs text-gray-500 mt-1">Label yang akan dipaparkan untuk bahagian pilihan varian (contoh: "Saiz", "Warna", "Jenis")</p>
                        </div>
                        
                        <!-- Add Variation Button -->
                        <div class="flex justify-between items-center">
                            <h4 class="text-md font-medium text-gray-900">Senarai Varian</h4>
                            <button type="button" onclick="addVariation()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Varian
                            </button>
                        </div>

                        <!-- Variations Container -->
                        <div id="variations-container" class="space-y-4">
                            @if($product->variations && $product->variations->count() > 0)
                                @foreach($product->variations as $index => $variation)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6" id="variation-{{ $index + 1 }}">
                                        <div class="flex items-center justify-between mb-4">
                                            <h5 class="text-lg font-medium text-gray-900">Varian #{{ $index + 1 }}</h5>
                                            <button type="button" onclick="removeVariation({{ $index + 1 }})" class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <input type="hidden" name="variations[{{ $index + 1 }}][id]" value="{{ $variation->id }}">
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Variation Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Varian <span class="text-red-500">*</span></label>
                                                <input type="text" 
                                                       name="variations[{{ $index + 1 }}][name]" 
                                                       value="{{ $variation->name }}"
                                                       placeholder="Contoh: Merah Saiz L, Putih Saiz M" 
                                                       required 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <p class="text-xs text-gray-500 mt-1">Nama yang akan dipaparkan kepada pelanggan</p>
                                            </div>
                                            
                                            <!-- SKU -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                                <input type="text" 
                                                       name="variations[{{ $index + 1 }}][sku]" 
                                                       value="{{ $variation->sku }}"
                                                       placeholder="Contoh: ARS-HOME-RED-L" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <p class="text-xs text-gray-500 mt-1">Kod stok unik (optional)</p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                            <!-- Price -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga <span class="text-red-500">*</span></label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">RM</span>
                                                    </div>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           name="variations[{{ $index + 1 }}][price]" 
                                                           value="{{ $variation->price }}"
                                                           required 
                                                           class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                </div>
                                            </div>
                                            
                                            <!-- Sale Price -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">RM</span>
                                                    </div>
                                                    <input type="number" 
                                                           step="0.01" 
                                                           min="0" 
                                                           name="variations[{{ $index + 1 }}][sale_price]" 
                                                           value="{{ $variation->sale_price }}"
                                                           class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                           oninput="validateVariationSalePrice(this, {{ $index + 1 }})">
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tiada jualan</p>
                                            </div>
                                            
                                            <!-- Stock Quantity -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                                                <input type="number" 
                                                       min="0" 
                                                       name="variations[{{ $index + 1 }}][stock_quantity]" 
                                                       value="{{ $variation->stock_quantity }}" 
                                                       required 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>
                                        
                                        <!-- Status -->
                                        <div class="mt-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                            <select name="variations[{{ $index + 1 }}][is_active]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <option value="1" {{ $variation->is_active ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ !$variation->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Current Variation Images -->
                                        @if($variation->images && count($variation->images) > 0)
                                        <div class="mt-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Varian Semasa</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                                @foreach($variation->images as $imgIndex => $image)
                                                <div class="relative">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                         alt="Variation Image" 
                                                         class="w-full h-24 object-cover rounded-lg border">
                                                    <input type="hidden" name="variations[{{ $index + 1 }}][current_images][]" value="{{ $image }}">
                                                    <button type="button" 
                                                            onclick="removeVariationImage(this)" 
                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                                        ×
                                                    </button>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                        
                                        <!-- New Variation Images -->
                                        <div class="mt-6">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Varian Baharu</label>
                                            <input type="file" 
                                                   name="variations[{{ $index + 1 }}][new_images][]" 
                                                   multiple 
                                                   accept="image/*" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <p class="text-xs text-gray-500 mt-1">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- No Variations Message -->
                        <div id="no-variations-message" class="text-center py-8 text-gray-500" style="display: {{ $product->variations && $product->variations->count() > 0 ? 'none' : 'block' }};">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-lg font-medium">Tiada varian ditambah</p>
                            <p class="text-sm">Klik "Tambah Varian" untuk mula menambah varian produk</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Hantar Semula Permohonan
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start gap-3">
                <div class="bg-blue-100 text-blue-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">Petua untuk Permohonan yang Berjaya</h3>
                    <ul class="text-blue-800 space-y-2">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Pastikan semua maklumat yang diperlukan diisi dengan lengkap</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Gunakan gambar berkualiti tinggi yang jelas menunjukkan produk anda</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Berikan penerangan yang terperinci dan menarik</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Pastikan harga dan stok adalah tepat dan terkini</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let variationCounter = {{ $product->variations ? $product->variations->count() : 0 }};

function removeImage(button) {
    const imageContainer = button.parentElement;
    imageContainer.remove();
}

function removeVariationImage(button) {
    const imageContainer = button.parentElement;
    imageContainer.remove();
}

function toggleVariations() {
    const section = document.getElementById('variations-section');
    const toggleText = document.getElementById('variations-toggle-text');
    
    if (section.style.display === 'none') {
        section.style.display = 'block';
        toggleText.textContent = 'Sembunyikan Varian';
    } else {
        section.style.display = 'none';
        toggleText.textContent = 'Tambah Varian';
    }
}

function addVariation() {
    variationCounter++;
    const container = document.getElementById('variations-container');
    const noVariationsMessage = document.getElementById('no-variations-message');
    
    // Hide no variations message
    noVariationsMessage.style.display = 'none';
    
    const variationDiv = document.createElement('div');
    variationDiv.className = 'bg-gray-50 border border-gray-200 rounded-lg p-6';
    variationDiv.id = `variation-${variationCounter}`;
    
    variationDiv.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <h5 class="text-lg font-medium text-gray-900">Varian #${variationCounter}</h5>
            <button type="button" onclick="removeVariation(${variationCounter})" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Variation Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Varian <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="variations[${variationCounter}][name]" 
                       placeholder="Contoh: Merah Saiz L, Putih Saiz M" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Nama yang akan dipaparkan kepada pelanggan</p>
            </div>
            
            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                <input type="text" 
                       name="variations[${variationCounter}][sku]" 
                       placeholder="Contoh: ARS-HOME-RED-L" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Kod stok unik (optional)</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">RM</span>
                    </div>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="variations[${variationCounter}][price]" 
                           required 
                           class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <!-- Sale Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">RM</span>
                    </div>
                    <input type="number" 
                           step="0.01" 
                           min="0" 
                           name="variations[${variationCounter}][sale_price]" 
                           class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           oninput="validateVariationSalePrice(this, ${variationCounter})">
                </div>
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tiada jualan</p>
            </div>
            
            <!-- Stock Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                <input type="number" 
                       min="0" 
                       name="variations[${variationCounter}][stock_quantity]" 
                       value="0" 
                       required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <!-- Status -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="variations[${variationCounter}][is_active]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
        
        <!-- New Variation Images -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Varian Baharu</label>
            <input type="file" 
                   name="variations[${variationCounter}][new_images][]" 
                   multiple 
                   accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
        </div>
    `;
    
    container.appendChild(variationDiv);
}

function removeVariation(variationId) {
    const variationDiv = document.getElementById(`variation-${variationId}`);
    variationDiv.remove();
    
    // Check if no variations left
    const container = document.getElementById('variations-container');
    const noVariationsMessage = document.getElementById('no-variations-message');
    
    if (container.children.length === 0) {
        noVariationsMessage.style.display = 'block';
    }
}

function validateVariationSalePrice(input, variationId) {
    const salePrice = parseFloat(input.value);
    const priceInput = input.closest('.grid').querySelector(`input[name="variations[${variationId}][price]"]`);
    const regularPrice = parseFloat(priceInput.value);
            
    if (isNaN(salePrice)) {
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
    } else if (salePrice < 0) {
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
        input.value = 0;
    } else if (salePrice >= regularPrice) {
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
    } else {
        input.classList.remove('border-red-500');
        input.classList.add('border-green-500');
    }
}

// Show variations section if there are existing variations
document.addEventListener('DOMContentLoaded', function() {
    const existingVariations = {{ $product->variations ? $product->variations->count() : 0 }};
    if (existingVariations > 0) {
        const section = document.getElementById('variations-section');
        const toggleText = document.getElementById('variations-toggle-text');
        section.style.display = 'block';
        toggleText.textContent = 'Sembunyikan Varian';
    }
});
</script>
@endsection 