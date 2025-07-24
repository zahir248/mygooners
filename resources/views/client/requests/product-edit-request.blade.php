@extends('layouts.app')

@section('title', 'Kemaskini Produk - MyGooners')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Kemaskini Produk</h1>
        <p class="text-gray-600">Kemaskini maklumat produk anda untuk permohonan kelulusan admin</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="POST" action="{{ route('product.edit.request.store', $product->id) }}" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateForm()">
            @csrf
            
            <!-- Product Information Section -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Maklumat Produk</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tajuk Produk *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $product->title) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Contoh: Jersi Arsenal Home 2024" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori *</label>
                        <select id="category" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih kategori</option>
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
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Produk *</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              placeholder="Terangkan produk anda dengan terperinci..." required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga Asal (RM) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="0.00" step="0.01" min="0" required>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan (RM)</label>
                        <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="0.00" step="0.01" min="0">
                        @error('sale_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok *</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="0" min="0" required>
                        @error('stock_quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (Pisahkan dengan koma)</label>
                    <input type="text" id="tags" name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Contoh: jersi, arsenal, home, 2024">
                    @error('tags')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Images Section -->
            <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gambar Produk</h3>
                
                <!-- Current Images -->
                @if($product->images && count($product->images) > 0)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Gambar Sedia Ada</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($product->images as $index => $image)
                        <div class="relative">
                            <img src="{{ route('product.image', basename($image)) }}" alt="Product Image" class="w-full h-32 object-cover rounded-lg">
                            <div class="absolute top-2 left-2">
                                <input type="checkbox" name="current_images[]" value="{{ $image }}" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                       {{ in_array($image, old('current_images', [])) ? 'checked' : 'checked' }}>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- New Images -->
                <div>
                    <label for="new_images" class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Baharu</label>
                    <input type="file" id="new_images" name="new_images[]" multiple 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           accept="image/*">
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Saiz maksimum: 10MB setiap gambar</p>
                    @error('new_images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Product Variations Section -->
            <div class="bg-gray-50 rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Variasi Produk</h3>
                    <button type="button" onclick="toggleVariations()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span id="toggleVariationsText">Tambah Variasi</span>
                    </button>
                </div>

                <div id="variationsSection" class="hidden">
                    <div class="mb-4">
                        <label for="variation_label" class="block text-sm font-medium text-gray-700 mb-2">Label Variasi</label>
                        <input type="text" id="variation_label" name="variation_label" value="{{ old('variation_label', $product->variation_label) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Contoh: Saiz, Warna, Model">
                    </div>

                    <div id="variationsContainer">
                        @if($product->variations && $product->variations->count() > 0)
                            @foreach($product->variations as $index => $variation)
                            <div class="variation-item border rounded-lg p-4 mb-4 bg-white">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium text-gray-900">Variasi {{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeVariation(this)" 
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Variasi *</label>
                                        <input type="text" name="variations[{{ $index }}][name]" value="{{ $variation->name }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Contoh: Saiz L, Warna Merah" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                        <input type="text" name="variations[{{ $index }}][sku]" value="{{ $variation->sku }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Contoh: ARS-HOME-L">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (RM)</label>
                                        <input type="number" name="variations[{{ $index }}][price]" value="{{ $variation->price }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jualan (RM)</label>
                                        <input type="number" name="variations[{{ $index }}][sale_price]" value="{{ $variation->sale_price }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                                        <input type="number" name="variations[{{ $index }}][stock_quantity]" value="{{ $variation->stock_quantity }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="0" min="0">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select name="variations[{{ $index }}][is_active]" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="1" {{ $variation->is_active ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ !$variation->is_active ? 'selected' : '' }}>Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Variation Images -->
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Variasi</label>
                                    
                                    <!-- Current Variation Images -->
                                    @if($variation->images && count($variation->images) > 0)
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-500 mb-2">Gambar Sedia Ada:</label>
                                        <div class="flex space-x-2 overflow-x-auto">
                                            @foreach($variation->images as $imgIndex => $image)
                                            <div class="relative flex-shrink-0">
                                                <img src="{{ route('variation.image', basename($image)) }}" alt="Variation Image" class="w-16 h-16 object-cover rounded border">
                                                <div class="absolute top-1 left-1">
                                                    <input type="checkbox" name="variations[{{ $index }}][current_images][]" value="{{ $image }}" 
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                                           {{ in_array($image, old("variations.{$index}.current_images", [])) ? 'checked' : 'checked' }}>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    <!-- New Variation Images -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Tambah Gambar Baharu:</label>
                                        <input type="file" name="variations[{{ $index }}][new_images][]" multiple 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               accept="image/*">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <button type="button" onclick="addVariation()" 
                            class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-700 transition-colors">
                        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Variasi Baharu
                    </button>
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

<script>
let variationCounter = {{ $product->variations ? $product->variations->count() : 0 }};

function toggleVariations() {
    const section = document.getElementById('variationsSection');
    const button = document.getElementById('toggleVariationsText');
    
    if (section.classList.contains('hidden')) {
        section.classList.remove('hidden');
        button.textContent = 'Sembunyikan Variasi';
    } else {
        section.classList.add('hidden');
        button.textContent = 'Tambah Variasi';
    }
}

function addVariation() {
    const container = document.getElementById('variationsContainer');
    const variationDiv = document.createElement('div');
    variationDiv.className = 'variation-item border rounded-lg p-4 mb-4 bg-white';
    
    variationDiv.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-medium text-gray-900">Variasi ${variationCounter + 1}</h4>
            <button type="button" onclick="removeVariation(this)" 
                    class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Variasi *</label>
                <input type="text" name="variations[${variationCounter}][name]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: Saiz L, Warna Merah" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                <input type="text" name="variations[${variationCounter}][sku]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="Contoh: ARS-HOME-L">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (RM)</label>
                <input type="number" name="variations[${variationCounter}][price]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="0.00" step="0.01" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jualan (RM)</label>
                <input type="number" name="variations[${variationCounter}][sale_price]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="0.00" step="0.01" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                <input type="number" name="variations[${variationCounter}][stock_quantity]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="0" min="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="variations[${variationCounter}][is_active]" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="1" selected>Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Variasi</label>
            <input type="file" name="variations[${variationCounter}][new_images][]" multiple 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   accept="image/*">
        </div>
    `;
    
    container.appendChild(variationDiv);
    variationCounter++;
}

function removeVariation(button) {
    button.closest('.variation-item').remove();
}

function validateForm() {
    const price = parseFloat(document.getElementById('price').value);
    const salePrice = parseFloat(document.getElementById('sale_price').value);
    
    if (salePrice > 0 && salePrice >= price) {
        alert('Harga jualan mestilah kurang daripada harga asal.');
        return false;
    }
    
    return true;
}

// Initialize variations section if there are existing variations
@if($product->variations && $product->variations->count() > 0)
    document.addEventListener('DOMContentLoaded', function() {
        toggleVariations();
    });
@endif
</script>
@endsection 