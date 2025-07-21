@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
            <p class="mt-2 text-sm text-gray-700">Kemaskini produk: {{ $product->title }}</p>
                    </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
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

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8 px-4 sm:px-6 lg:px-8" id="edit-product-form">
                @csrf
                @method('PUT')
    <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Maklumat Produk</h3>
                    </div>
        <div class="px-6 py-4 space-y-6">
                        <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tajuk Produk <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $product->title) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
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
                            <option value="{{ $key }}" {{ old('category', $product->category) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('stock_quantity') border-red-500 @enderror">
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
                               value="{{ old('price', $product->price) }}" 
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
                               value="{{ old('sale_price', $product->sale_price) }}" 
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
                <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($product->tags) ? implode(',', $product->tags) : $product->tags) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('tags') border-red-500 @enderror" placeholder="arsenal, jersey, home, 2024/25">
                @error('tags')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_title') border-red-500 @enderror">
                    @error('meta_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('status') border-red-500 @enderror">

                                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" id="meta_description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('meta_description') border-red-500 @enderror">{{ old('meta_description', $product->meta_description) }}</textarea>
                @error('meta_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_featured" class="ml-2 block text-sm text-gray-900">Tandakan sebagai produk ditampilkan</label>
            </div>
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
                
                <!-- Current Images Display -->
                @if($product->images && is_array($product->images) && count($product->images))
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 border-b border-gray-300 mb-3">Gambar semasa (seret untuk menyusun semula, klik X untuk padam):</p>
                        <div id="current-images" class="flex flex-wrap gap-4">
                            @foreach($product->images as $index => $img)
                                <div class="relative group cursor-move" data-image-index="{{ $index }}">
                                    <img src="{{ Storage::url($img) }}" 
                                         alt="Gambar Produk" 
                                         class="h-24 w-36 object-cover rounded-lg border border-gray-300">
                                    <button type="button" 
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                            data-image-index="{{ $index }}"
                                            onclick="removeImage({{ $index }})">
                                        ×
                                    </button>
                                    <input type="hidden" name="current_images[]" value="{{ $img }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- New Images Upload -->
                <div>
                    <label for="new_images" class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Baru</label>
                    <input type="file" name="new_images[]" id="new_images" multiple accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                    <p class="mt-1 text-sm text-gray-500">Boleh muat naik lebih dari satu gambar. PNG, JPG, GIF sehingga 10MB setiap satu.</p>
                </div>

                @error('new_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                @error('new_images.*')
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
                    <span id="variations-toggle-text">{{ ($product->variations && count($product->variations) > 0) ? 'Sembunyikan Varian' : 'Tambah Varian' }}</span>
                </button>
            </div>
            <p class="mt-1 text-sm text-gray-600">Tambah varian secara manual untuk produk dengan pilihan saiz, warna, atau ciri lain</p>
        </div>
        
        <div id="variations-section" class="px-6 py-4 space-y-6" style="display: {{ ($product->variations && count($product->variations) > 0) ? 'block' : 'none' }};">
            <!-- Variation Label Field -->
            <div>
                <label for="variation_label" class="block text-sm font-medium text-gray-700 mb-2">Label Varian</label>
                <input type="text" name="variation_label" id="variation_label" value="{{ old('variation_label', $product->variation_label) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('variation_label') border-red-500 @enderror" placeholder="Contoh: Saiz, Warna, Jenis, dll">
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

            <!-- Existing Variations -->
            @if($product->variations && count($product->variations) > 0)
                <div id="existing-variations" class="space-y-4">
                    @foreach($product->variations as $index => $variation)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6" id="existing-variation-{{ $variation->id }}">
                            <div class="flex items-center justify-between mb-4">
                                <h5 class="text-lg font-medium text-gray-900">Varian: {{ $variation->name }}</h5>
                                <div class="flex space-x-2">
                                    <button type="button" onclick="editVariation({{ $variation->id }})" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" onclick="deleteVariation({{ $variation->id }})" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div><strong>SKU:</strong> {{ $variation->sku ?: 'Tiada' }}</div>
                                <div><strong>Harga:</strong> RM{{ number_format($variation->price, 2) }}</div>
                                @if($variation->sale_price)
                                    <div><strong>Harga Jualan:</strong> RM{{ number_format($variation->sale_price, 2) }}</div>
                                @endif
                                <div><strong>Stok:</strong> {{ $variation->stock_quantity }}</div>
                                <div><strong>Status:</strong> {{ $variation->is_active ? 'Aktif' : 'Tidak Aktif' }}</div>
                            </div>
                            
                            @if($variation->images && count($variation->images) > 0)
                                <div class="mt-4">
                                    <h6 class="text-sm font-medium text-gray-700 mb-2">Gambar Varian:</h6>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($variation->images as $image)
                                            <img src="{{ Storage::url($image) }}" 
                                                 alt="Gambar Varian" 
                                                 class="h-16 w-20 object-cover rounded border border-gray-300">
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Variations Container for New Variations -->
            <div id="variations-container" class="space-y-4">
                <!-- New variations will be added here -->
            </div>

            <!-- No Variations Message -->
            <div id="no-variations-message" class="text-center py-8 text-gray-500" style="display: {{ ($product->variations && count($product->variations) > 0) ? 'none' : 'block' }};">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="text-lg font-medium">Tiada varian ditambah</p>
                <p class="text-sm">Klik "Tambah Varian" untuk mula menambah varian produk</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-3">
        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">Kemaskini Produk</button>
    </div>
</form>

@push('scripts')
<script>
// Global variables for variations
let variationCounter = 0;
let attributeValues = {};



// Sale Price Validation
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

    // Function to remove an image
    function removeImage(index) {
        const currentImagesDiv = document.getElementById('current-images');
        const images = Array.from(currentImagesDiv.children);
        const imageToRemove = images.find(img => img.dataset.imageIndex === index.toString());
        
        if (imageToRemove) {
            imageToRemove.remove();
            // Update data-image-index for remaining images
            const remainingImages = Array.from(currentImagesDiv.children);
            remainingImages.forEach((img, newIndex) => {
                img.dataset.imageIndex = newIndex.toString();
                const removeBtn = img.querySelector('button');
                if (removeBtn) {
                    removeBtn.dataset.imageIndex = newIndex.toString();
                    removeBtn.onclick = () => removeImage(newIndex);
                }
            });
        }
    }

    // Drag and Drop functionality
    let draggedImage = null;

    // Initialize sale price validation and image management on page load
    document.addEventListener('DOMContentLoaded', function() {
        const salePriceInput = document.getElementById('sale_price');
        if (salePriceInput && salePriceInput.value) {
            validateSalePrice(salePriceInput);
        }

        const currentImages = document.getElementById('current-images');
        const newImagesInput = document.getElementById('new_images');

        if (currentImages) {
            // Make existing images draggable
            const existingImages = currentImages.querySelectorAll('.relative.group');
            existingImages.forEach((img, index) => {
                img.draggable = true;
                img.dataset.imageIndex = index.toString();
                
                // Update remove button
                const removeBtn = img.querySelector('button');
                if (removeBtn) {
                    removeBtn.dataset.imageIndex = index.toString();
                    removeBtn.onclick = () => removeImage(index);
                }
            });

            // Handle drag and drop for current images
            currentImages.addEventListener('dragstart', function(event) {
                if (event.target.closest('.relative.group')) {
                    draggedImage = event.target.closest('.relative.group');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/html', draggedImage.outerHTML);
                }
            });

            currentImages.addEventListener('dragover', function(event) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                const target = event.target.closest('.relative.group');
                if (target && target !== draggedImage) {
                    target.classList.add('bg-gray-100');
                }
            });

            currentImages.addEventListener('dragleave', function(event) {
                const target = event.target.closest('.relative.group');
                if (target) {
                    target.classList.remove('bg-gray-100');
                }
            });

            currentImages.addEventListener('drop', function(event) {
                event.preventDefault();
                const target = event.target.closest('.relative.group');
                if (target && target !== draggedImage) {
                    target.classList.remove('bg-gray-100');
                    
                    const images = Array.from(currentImages.children);
                    const draggedIndex = parseInt(draggedImage.dataset.imageIndex);
                    const targetIndex = parseInt(target.dataset.imageIndex);

                    if (draggedIndex < targetIndex) {
                        currentImages.insertBefore(draggedImage, target.nextSibling);
                    } else {
                        currentImages.insertBefore(draggedImage, target);
                    }

                    // Update data-image-index for all images
                    const updatedImages = Array.from(currentImages.children);
                    updatedImages.forEach((img, newIndex) => {
                        img.dataset.imageIndex = newIndex.toString();
                        const removeBtn = img.querySelector('button');
                        if (removeBtn) {
                            removeBtn.dataset.imageIndex = newIndex.toString();
                            removeBtn.onclick = () => removeImage(newIndex);
                        }
                    });
                }
            });
        }

        // Handle file input change for new images
        if (newImagesInput) {
            newImagesInput.addEventListener('change', function(event) {
                const files = event.target.files;
                const currentImages = document.getElementById('current-images');
                const startIndex = currentImages ? currentImages.children.length : 0;
                
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgContainer = document.createElement('div');
                            imgContainer.classList.add('relative', 'group', 'cursor-move');
                            imgContainer.draggable = true;
                            const imageIndex = startIndex + i;
                            imgContainer.dataset.imageIndex = imageIndex.toString();
                            
                            imgContainer.innerHTML = `
                                <img src="${e.target.result}" alt="Gambar Produk" class="h-24 w-36 object-cover rounded-lg border border-gray-300">
                                <button type="button" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                        data-image-index="${imageIndex}"
                                        onclick="removeImage(${imageIndex})">
                                    ×
                                </button>
                                <input type="hidden" name="new_images[]" value="${file.name}">
                            `;
                            
                            if (currentImages) {
                                currentImages.appendChild(imgContainer);
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        }
    });

    // Variation Management Functions
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
                <h5 class="text-lg font-medium text-gray-900">Varian Baru #${variationCounter}</h5>
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
        const existingVariations = document.getElementById('existing-variations');
        
        if (container.children.length === 0 && (!existingVariations || existingVariations.children.length === 0)) {
            noVariationsMessage.style.display = 'block';
        }
    }

    function editVariation(variationId) {
        // Fetch variation data and populate the edit modal
        fetch(`/admin/products/variations/${variationId}/edit`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditVariationModal(data.variation);
                document.getElementById('editVariationModal').classList.remove('hidden');
            } else {
                showNotification('Error loading variation data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading variation data', 'error');
        });
    }

    function populateEditVariationModal(variation) {
        // Set the variation ID
        document.getElementById('edit_variation_id').value = variation.id;
        
        // Populate form fields
        document.getElementById('edit_variation_name').value = variation.name;
        document.getElementById('edit_variation_sku').value = variation.sku || '';
        document.getElementById('edit_variation_price').value = variation.price || '';
        document.getElementById('edit_variation_sale_price').value = variation.sale_price || '';
        document.getElementById('edit_variation_stock').value = variation.stock_quantity;
        document.getElementById('edit_variation_active').checked = variation.is_active;
        
        // Handle current images
        const currentImagesContainer = document.getElementById('edit_variation_current_images');
        currentImagesContainer.innerHTML = '';
        
        if (variation.images && variation.images.length > 0) {
            variation.images.forEach((image, index) => {
                const imageDiv = document.createElement('div');
                imageDiv.className = 'relative group';
                imageDiv.dataset.imageIndex = index.toString();
                imageDiv.innerHTML = `
                    <img src="/storage/${image}" alt="Variation Image" class="h-20 w-20 object-cover rounded border border-gray-300">
                    <button type="button" onclick="removeEditVariationImage(${index})" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                    <input type="hidden" name="current_images[]" value="${image}">
                `;
                currentImagesContainer.appendChild(imageDiv);
            });
        }
    }

    function removeEditVariationImage(index) {
        const container = document.getElementById('edit_variation_current_images');
        const imageToRemove = container.querySelector(`[data-image-index="${index}"]`);
        if (imageToRemove) {
            imageToRemove.remove();
            // Update data-image-index for remaining images
            const remainingImages = Array.from(container.children);
            remainingImages.forEach((img, newIndex) => {
                img.dataset.imageIndex = newIndex.toString();
                const removeBtn = img.querySelector('button');
                if (removeBtn) {
                    removeBtn.onclick = () => removeEditVariationImage(newIndex);
                }
            });
        }
    }

    function closeEditVariationModal() {
        document.getElementById('editVariationModal').classList.add('hidden');
        // Reset form
        document.getElementById('editVariationForm').reset();
        document.getElementById('edit_variation_current_images').innerHTML = '';
    }

    function saveVariationChanges() {
        const form = document.getElementById('editVariationForm');
        const formData = new FormData(form);
        const variationId = document.getElementById('edit_variation_id').value;
        
        // Validate fields if provided
        const name = formData.get('name');
        const stockQuantity = formData.get('stock_quantity');
        
        if (name && !name.trim()) {
            showNotification('Nama varian tidak boleh kosong', 'error');
            return;
        }
        
        if (stockQuantity && (isNaN(parseInt(stockQuantity)) || parseInt(stockQuantity) < 0)) {
            showNotification('Kuantiti stok mesti nombor yang sah dan tidak boleh negatif', 'error');
            return;
        }
        
        // Validate sale price
        const salePrice = parseFloat(formData.get('sale_price'));
        const price = parseFloat(formData.get('price'));
        
        if (salePrice && price && salePrice >= price) {
            showNotification('Harga jualan mesti lebih rendah daripada harga asal', 'error');
            return;
        }
        
        // Clear any hidden inputs that might have been added incorrectly
        const hiddenNewImagesInputs = form.querySelectorAll('input[name="new_images[]"]');
        hiddenNewImagesInputs.forEach(input => {
            if (input.type === 'hidden') {
                input.remove();
            }
        });
        
        // Remove empty file inputs to prevent validation errors
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            if (input.files.length === 0) {
                input.remove();
            }
        });
        
        // Set the form action
        form.action = `/admin/products/variations/${variationId}`;
        
        // Add method override for PUT request
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
        
        // Debug: Log form data before submission
        const debugFormData = new FormData(form);
        console.log('Form data being sent:');
        for (let [key, value] of debugFormData.entries()) {
            console.log(key, value);
        }
        
        // Submit the form
        form.submit();
    }

    function validateEditVariationSalePrice(input) {
        const salePrice = parseFloat(input.value);
        const priceInput = document.getElementById('edit_variation_price');
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

    function deleteVariation(variationId) {
        // Get variation name for the modal
        const variationDiv = document.getElementById(`existing-variation-${variationId}`);
        const variationName = variationDiv ? variationDiv.querySelector('h5').textContent.replace('Varian: ', '') : 'Varian ini';
        
        // Show confirmation modal
        confirmDeleteVariation(variationId, variationName);
    }

    function markVariationForDeletion(variationId) {
            // Check if this variation is already marked for deletion
            const form = document.querySelector('form');
            const existingDeleteInputs = form.querySelectorAll('input[name="delete_variations[]"]');
            const alreadyMarked = Array.from(existingDeleteInputs).some(input => input.value === variationId.toString());
            
            if (!alreadyMarked) {
                // Add hidden input to mark variation for deletion
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_variations[]';
                deleteInput.value = variationId;
                form.appendChild(deleteInput);
                
                console.log(`Variation ${variationId} marked for deletion`);
            }
            
            // Remove from display
            const variationDiv = document.getElementById(`existing-variation-${variationId}`);
            if (variationDiv) {
                variationDiv.remove();
                console.log(`Variation ${variationId} removed from display`);
            }
            
            // Check if no variations left
            const existingVariations = document.getElementById('existing-variations');
            const container = document.getElementById('variations-container');
            const noVariationsMessage = document.getElementById('no-variations-message');
            
            if ((!existingVariations || existingVariations.children.length === 0) && 
                (!container || container.children.length === 0)) {
                noVariationsMessage.style.display = 'block';
            }
    }

    function showNotification(message, type = 'info') {
        // Create notification element matching admin layout style exactly
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full`;
        
        if (type === 'success') {
            // Exact same design as admin layout success message
            notification.innerHTML = `
                <div class="bg-green-50 border-l-4 border-green-400 p-4 m-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                        class="inline-flex text-green-700 rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'error') {
            // Error message design - same as admin layout error message
            notification.innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                        class="inline-flex text-red-700 rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Info message design
            notification.innerHTML = `
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 m-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">${message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                        class="inline-flex text-blue-700 rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Add to page
        document.body.appendChild(notification);
        
        // Remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    function confirmDeleteVariation(variationId, variationName) {
        document.getElementById('deleteVariationTitle').textContent = variationName;
        document.getElementById('deleteVariationId').value = variationId;
        document.getElementById('deleteVariationModal').classList.remove('hidden');
            }

    function closeDeleteVariationModal() {
        document.getElementById('deleteVariationModal').classList.add('hidden');
    }

    function performVariationDeletion() {
        const variationId = document.getElementById('deleteVariationId').value;
        
        // Create a form for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/variations/${variationId}`;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Add to page and submit
        document.body.appendChild(form);
        form.submit();
    }

    // Add form submission debugging for variations
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Log variations marked for deletion before submission
                const formData = new FormData(form);
                const deleteVariations = [];
                
                for (let [key, value] of formData.entries()) {
                    if (key === 'delete_variations[]') {
                        deleteVariations.push(value);
                    }
                }
                
                if (deleteVariations.length > 0) {
                    console.log('Variations marked for deletion during form submission:', deleteVariations);
                }
            });
        }
    });

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
</script>
@endpush

<!-- Delete Variation Confirmation Modal -->
<div id="deleteVariationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Padam Varian</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu memadamkan "<span id="deleteVariationTitle"></span>"? Tindakan ini tidak boleh diundur.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeDeleteVariationModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <button onclick="performVariationDeletion()" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Padam
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input for variation ID -->
<input type="hidden" id="deleteVariationId" value="">

<!-- Edit Variation Modal -->
<div id="editVariationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Edit Varian</h3>
            <button onclick="closeEditVariationModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="editVariationForm" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" id="edit_variation_id" name="variation_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Variation Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Varian <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="edit_variation_name"
                           name="name" 
                           placeholder="Contoh: Merah Saiz L, Putih Saiz M" 
                           required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                    <p class="mt-1 text-sm text-gray-500">Nama yang akan dipaparkan kepada pelanggan</p>
                </div>
                
                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                    <input type="text" 
                           id="edit_variation_sku"
                           name="sku" 
                           placeholder="Contoh: ARS-HOME-RED-L" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                    <p class="mt-1 text-sm text-gray-500">Kod stok unik (optional)</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                        <input type="number" 
                               id="edit_variation_price"
                               name="price" 
                               step="0.01" 
                               min="0" 
                               placeholder="0.00" 
                               class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Biarkan kosong untuk menggunakan harga produk utama</p>
                </div>
                
                <!-- Sale Price -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jualan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">RM</span>
                        </div>
                        <input type="number" 
                               id="edit_variation_sale_price"
                               name="sale_price" 
                               step="0.01" 
                               min="0" 
                               placeholder="0.00" 
                               onchange="validateEditVariationSalePrice(this)"
                               class="w-full border border-gray-300 rounded-md pl-10 pr-3 py-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Harga jualan mesti lebih rendah daripada harga asal</p>
                </div>
                
                <!-- Stock Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kuantiti Stok <span class="text-red-500">*</span></label>
                    <input type="number" 
                           id="edit_variation_stock"
                           name="stock_quantity" 
                           min="0" 
                           required 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                    <p class="mt-1 text-sm text-gray-500">Bilangan stok yang tersedia</p>
                </div>
            </div>
            
            <!-- Active Status -->
            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" 
                       id="edit_variation_active"
                       name="is_active" 
                       value="1"
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="edit_variation_active" class="ml-2 block text-sm text-gray-900">
                    Aktifkan varian ini
                </label>
            </div>
            
            <!-- Current Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Semasa</label>
                <div id="edit_variation_current_images" class="flex flex-wrap gap-2">
                    <!-- Current images will be populated here -->
                </div>
            </div>
            
            <!-- New Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tambah Gambar Baru</label>
                <input type="file" 
                       name="new_images[]" 
                       multiple 
                       accept="image/*" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">Pilih gambar untuk varian ini (optional)</p>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" 
                        onclick="closeEditVariationModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <button type="button" 
                        onclick="saveVariationChanges()" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection 