@extends('layouts.admin')

@section('title', 'Cipta Produk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
                            <div>
            <h1 class="text-2xl font-bold text-gray-900">Cipta Produk Baharu</h1>
            <p class="mt-2 text-sm text-gray-700">Isi maklumat untuk produk baharu</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Produk
                                </a>
                            </div>
                            </div>
            </div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 px-4 sm:px-6 lg:px-8" onsubmit="return validateForm()">
                @csrf
    <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Maklumat Produk</h3>
                    </div>
        <div class="px-6 py-4 space-y-6">
                        <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tajuk Produk <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category" id="category" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('category') border-red-500 @enderror">
                                <option value="">Pilih kategori</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga Asal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                                <input type="number" 
                                       step="0.01"
                                       min="0"
                               name="price" 
                               id="price" 
                               value="{{ old('price') }}" 
                                       required
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('price') border-red-500 @enderror">
                    </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                                <input type="number" 
                                       step="0.01"
                                       min="0"
                               name="sale_price" 
                               id="sale_price" 
                               value="{{ old('sale_price') }}" 
                               class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('sale_price') border-red-500 @enderror"
                               oninput="validateSalePrice(this)">
                    </div>
                    <p id="sale_price_help" class="mt-1 text-sm text-gray-500">Kosongkan jika tiada jualan</p>
                                @error('sale_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tag (pisahkan dengan koma)</label>
                <input type="text" name="tags" id="tags" value="{{ old('tags') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror" placeholder="arsenal, jersey, home, 2024/25">
                @error('tags')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_title') border-red-500 @enderror">
                    @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                </div>
                        <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_description') border-red-500 @enderror">{{ old('meta_description') }}</textarea>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
                        <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">Tandakan sebagai produk ditampilkan</label>
                    </div>
                        <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
                @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                @error('images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

        <!-- Product Variations Section -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Varian Produk</h3>
                <button type="button" onclick="toggleVariations()" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    <span id="variations-toggle-text">Tambah Varian</span>
                        </button>
            </div>
            <p class="mt-1 text-sm text-gray-600">Tambah varian secara manual untuk produk dengan pilihan saiz, warna, atau ciri lain</p>
        </div>
        
        <div id="variations-section" class="px-6 py-4 space-y-6" style="display: none;">
            <!-- Variation Label Field -->
            <div>
                <label for="variation_label" class="block text-sm font-medium text-gray-700 mb-2">Label Varian</label>
                <input type="text" name="variation_label" id="variation_label" value="{{ old('variation_label', 'Pilihan Varian') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('variation_label') border-red-500 @enderror" placeholder="Contoh: Saiz, Warna, Jenis, dll">
                <p class="mt-1 text-sm text-gray-500">Label yang akan dipaparkan untuk bahagian pilihan varian (contoh: "Saiz", "Warna", "Jenis")</p>
                @error('variation_label')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Add Variation Button -->
            <div class="flex justify-between items-center">
                <h4 class="text-md font-medium text-gray-900">Senarai Varian</h4>
                <button type="button" onclick="addVariation()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Varian
                        </button>
                    </div>

            <!-- Variations Container -->
            <div id="variations-container" class="space-y-4">
                <!-- Individual variations will be added here -->
            </div>

            <!-- No Variations Message -->
            <div id="no-variations-message" class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="text-lg font-medium">Tiada varian ditambah</p>
                <p class="text-sm">Klik "Tambah Varian" untuk mula menambah varian produk</p>
                </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">Cipta Produk</button>
    </div>
</form>

@push('scripts')
    <script>
// Global variables
let variationCounter = 0;
let attributeValues = {};

function validateForm() {
    console.log('Form submission attempted');
    
    // Check required fields
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const category = document.getElementById('category').value;
    const price = document.getElementById('price').value;
    const stockQuantity = document.getElementById('stock_quantity').value;
    const status = document.getElementById('status').value;
    
    if (!title || !description || !category || !price || !stockQuantity || !status) {
        alert('Sila isi semua medan yang diperlukan');
        return false;
    }
    
    // Check if variations are enabled and have required fields
    const variationsSection = document.getElementById('variations-section');
    if (variationsSection.style.display !== 'none') {
        const variations = document.querySelectorAll('[name^="variations"][name$="[name]"]');
        let hasValidVariation = false;
        
        variations.forEach(variation => {
            if (variation.value.trim()) {
                hasValidVariation = true;
            }
        });
        
        if (variations.length > 0 && !hasValidVariation) {
            alert('Sila isi nama varian untuk sekurang-kurangnya satu varian');
            return false;
        }
    }
    
    console.log('Form validation passed');
    return true;
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
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">Nama yang akan dipaparkan kepada pelanggan</p>
            </div>
            
            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                <input type="text" 
                       name="variations[${variationCounter}][sku]" 
                       placeholder="Contoh: ARS-HOME-RED-L" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">Kod stok unik (optional)</p>
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
                           class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
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
                           class="w-full pl-8 border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500"
                           oninput="validateVariationSalePrice(this, ${variationCounter})">
                </div>
                <p class="mt-1 text-sm text-gray-500">Kosongkan jika tiada jualan</p>
            </div>
            
            <!-- Stock Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                <input type="number" 
                       min="0" 
                       name="variations[${variationCounter}][stock_quantity]" 
                       value="0" 
                       required 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
            </div>
        </div>
        

        
        <!-- Status -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="variations[${variationCounter}][is_active]" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
        
        <!-- Images Upload -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Varian</label>
            <input type="file" 
                   name="variations[${variationCounter}][images][]" 
                   multiple 
                   accept="image/*" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
            <p class="mt-1 text-sm text-gray-500">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
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

function validateSalePrice(input) {
    const salePrice = parseFloat(input.value);
    const regularPrice = parseFloat(document.getElementById('price').value);
    const helpText = document.getElementById('sale_price_help');
    
    if (isNaN(salePrice)) {
        helpText.textContent = 'Sila masukkan nombor yang sah';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
    } else if (salePrice < 0) {
        helpText.textContent = 'Harga jualan tidak boleh negatif';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
        input.value = 0;
    } else if (salePrice >= regularPrice) {
        helpText.textContent = 'Harga jualan mesti lebih rendah daripada harga asal';
        helpText.className = 'mt-1 text-sm text-red-600';
        input.classList.add('border-red-500');
        input.classList.remove('border-green-500');
    } else {
        helpText.textContent = 'Harga jualan sah';
        helpText.className = 'mt-1 text-sm text-green-600';
        input.classList.remove('border-red-500');
        input.classList.add('border-green-500');
    }
}

// Update sale price validation when regular price changes
document.getElementById('price').addEventListener('input', function() {
    const salePriceInput = document.getElementById('sale_price');
    if (salePriceInput.value) {
        validateSalePrice(salePriceInput);
    }
});
    </script>
@endpush

@endsection 