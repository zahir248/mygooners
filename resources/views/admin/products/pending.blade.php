@extends('layouts.admin')

@section('title', 'Produk Menunggu Kelulusan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Produk Menunggu Kelulusan</h1>
            <p class="mt-2 text-sm text-gray-700">Senarai produk yang menunggu kelulusan admin</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-2">
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Semua Produk
            </a>
        </div>
    </div>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Menunggu Kelulusan ({{ $products->total() }})
            </h3>
        </div>
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyedia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Tindakan</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            @php
                                $displayProduct = $product->is_update_request && $product->original_product_id ? \App\Models\Product::find($product->original_product_id) : $product;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 h-16 w-24">
                                            @if($displayProduct->images && is_array($displayProduct->images) && count($displayProduct->images) > 0)
                                                <img class="h-16 w-24 rounded-lg object-cover" src="{{ route('product.image', ['filename' => basename($displayProduct->images[0])]) }}" alt="{{ $displayProduct->title }}">
                                            @else
                                                <div class="h-16 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="text-sm font-bold text-gray-900 truncate">{{ $displayProduct->title }}</p>
                                                @if($displayProduct->is_featured)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        Ditampilkan
                                                    </span>
                                                @endif
                                                @if($product->is_update_request)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Kemaskini</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 line-clamp-2">
                                                ID Produk: #{{ $displayProduct->id }} • {{ $displayProduct->category }} • RM{{ number_format($displayProduct->price, 2) }}
                                                @if($product->is_update_request && $product->original_product_id)
                                                    <br><span class="text-blue-600">Kemaskini untuk produk #{{ $product->original_product_id }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $product->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->user->email }}</div>
                                            </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $displayProduct->category }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div class="font-medium">{{ $displayProduct->created_at->format('j M Y') }}</div>
                                        <div class="text-xs">{{ $displayProduct->updated_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <!-- Approve Button -->
                                        <button type="button" 
                                                onclick="openApproveModal({{ $product->id }}, '{{ $product->title }}')"
                                                class="text-green-600 hover:text-green-900" 
                                                title="Lulus Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <!-- Reject Button -->
                                        <button type="button" 
                                                onclick="openRejectModal({{ $product->id }}, '{{ $product->title }}')"
                                                class="text-red-600 hover:text-red-900" 
                                                title="Tolak Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <!-- View Product -->
                                        <button type="button" 
                                                onclick="openProductModal({{ $product->id }})"
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="Lihat Produk">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $products->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        @if($products->onFirstPage())
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                                Previous
                            </button>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        @endif
                        
                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada produk menunggu</h3>
                <p class="mt-1 text-sm text-gray-500">Semua produk telah diluluskan atau ditolak.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Lulus Produk</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu meluluskan "<span id="approveProductTitle"></span>"? Produk ini akan menjadi aktif dan boleh dilihat oleh pengguna.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeApproveModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <form id="approveProductFormModal" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Lulus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Tolak Produk</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">
                    Adakah anda pasti mahu menolak "<span id="rejectProductTitle"></span>"?
                </p>
                <form id="rejectProductFormModal" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Sebab Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            placeholder="Sila berikan sebab penolakan permohonan ini..."
                            required></textarea>
            </div>
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeRejectModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Tolak
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 60;">
    <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Butiran Permohonan</h3>
            <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="productModalContent" class="space-y-6">
            <!-- Loading spinner -->
            <div id="productModalLoading" class="flex justify-center items-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Product content will be loaded here -->
            <div id="productModalData" class="hidden">
                <!-- Changes Summary -->
                <div id="changesSummary" class="mb-6"></div>
                
                <!-- Product Images -->
                <div id="productImages" class="mb-6"></div>
                
                <!-- Product Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Product Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Maklumat Produk</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tajuk Produk</label>
                                <p id="productTitle" class="text-gray-900 font-medium"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Penerangan</label>
                                <p id="productDescription" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Kategori</label>
                                <p id="productCategory" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Harga Asal</label>
                                <p id="productPrice" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Harga Jualan</label>
                                <p id="productSalePrice" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Kuantiti Stok</label>
                                <p id="productStock" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tag</label>
                                <div id="productTags" class="flex flex-wrap gap-2"></div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tarikh Permohonan</label>
                                <p id="productCreatedAt" class="text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Maklumat Pengguna</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama</label>
                                <p id="userName" class="text-gray-900 font-medium"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Email</label>
                                <p id="userEmail" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">No. Telefon</label>
                                <p id="userPhone" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Bio</label>
                                <p id="userBio" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Lokasi</label>
                                <p id="userLocation" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Status Penjual</label>
                                <p id="userSellerStatus" class="text-gray-900"></p>
                            </div>
                            
                            <!-- User Images Section -->
                            <div>
                                <label class="text-sm font-medium text-gray-600">Gambar Pengguna</label>
                                <div id="userImages" class="mt-2 space-y-3">
                                    <!-- Profile Image -->
                                    <div id="userProfileImage" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Gambar Profil</label>
                                        <div class="mt-1">
                                            <img id="profileImageSrc" src="" alt="Profile Image" class="w-24 h-24 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                    
                                    <!-- ID Document -->
                                    <div id="userIdDocument" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Dokumen Pengenalan</label>
                                        <div class="mt-1">
                                            <img id="idDocumentSrc" src="" alt="ID Document" class="w-32 h-20 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                    
                                    <!-- Selfie with ID -->
                                    <div id="userSelfieWithId" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Selfie dengan Dokumen Pengenalan</label>
                                        <div class="mt-1">
                                            <img id="selfieWithIdSrc" src="" alt="Selfie with ID" class="w-32 h-20 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="sellerDetails" class="hidden space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Nama Perniagaan</label>
                                    <p id="userBusinessName" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Jenis Perniagaan</label>
                                    <p id="userBusinessType" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Alamat Perniagaan</label>
                                    <p id="userBusinessAddress" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kawasan Operasi</label>
                                    <p id="userOperatingArea" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Tahun Pengalaman</label>
                                    <p id="userYearsExperience" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kemahiran</label>
                                    <p id="userSkills" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kawasan Perkhidmatan</label>
                                    <p id="userServiceAreas" class="text-gray-900"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Variations Section -->
                <div id="productVariations" class="mt-6 space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Variasi Produk</h4>
                    <div id="variationsContent"></div>
                </div>
            </div>
        </div>
        <!-- Add some bottom padding to ensure content is visible -->
        <div class="h-8"></div>
        
        <!-- Scroll to top button -->
        <button onclick="scrollToTop()" 
                class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors duration-200"
                style="z-index: 70;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>
    </div>
</div>

<script>
    function openApproveModal(productId, productTitle) {
        // Disable background page interaction
        document.body.style.overflow = 'hidden';
        document.body.style.pointerEvents = 'none';
        
        document.getElementById('approveProductTitle').textContent = productTitle;
        const baseUrl = '{{ route("admin.products.approve", ":id") }}';
        document.getElementById('approveProductFormModal').action = baseUrl.replace(':id', productId);
        document.getElementById('approveModal').classList.remove('hidden');
        
        // Re-enable pointer events for modal only
        document.getElementById('approveModal').style.pointerEvents = 'auto';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        
        // Re-enable background page interaction
        document.body.style.overflow = '';
        document.body.style.pointerEvents = '';
    }

    function openRejectModal(productId, productTitle) {
        // Disable background page interaction
        document.body.style.overflow = 'hidden';
        document.body.style.pointerEvents = 'none';
        
        document.getElementById('rejectProductTitle').textContent = productTitle;
        const baseUrl = '{{ route("admin.products.reject", ":id") }}';
        document.getElementById('rejectProductFormModal').action = baseUrl.replace(':id', productId);
        document.getElementById('rejectModal').classList.remove('hidden');
        
        // Re-enable pointer events for modal only
        document.getElementById('rejectModal').style.pointerEvents = 'auto';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        
        // Re-enable background page interaction
        document.body.style.overflow = '';
        document.body.style.pointerEvents = '';
    }

    function openProductModal(productId) {
        // Disable background page interaction
        document.body.style.overflow = 'hidden';
        document.body.style.pointerEvents = 'none';
        
        // Show loading
        document.getElementById('productModalLoading').classList.remove('hidden');
        document.getElementById('productModalData').classList.add('hidden');
        document.getElementById('productModal').classList.remove('hidden');
        
        // Re-enable pointer events for modal only
        document.getElementById('productModal').style.pointerEvents = 'auto';
        
        // Scroll modal to top
        document.getElementById('productModal').scrollTo({ top: 0, behavior: 'instant' });
        
        // Fetch product details
        fetch(`{{ route('admin.products.details', ':id') }}`.replace(':id', productId))
            .then(response => response.json())
            .then(data => {
                // Hide loading and show data
                document.getElementById('productModalLoading').classList.add('hidden');
                document.getElementById('productModalData').classList.remove('hidden');
                
                // Populate changes summary if this is an update request
                this.populateChangesSummary(data.changes);
                
                // Populate product information with change highlighting
                this.populateProductField('productTitle', data.product.title, data.changes?.title);
                this.populateProductField('productDescription', data.product.description, data.changes?.description);
                this.populateProductField('productCategory', data.product.category, data.changes?.category);
                this.populateProductField('productPrice', `RM${parseFloat(data.product.price).toFixed(2)}`, data.changes?.price);
                this.populateProductField('productSalePrice', data.product.sale_price ? `RM${parseFloat(data.product.sale_price).toFixed(2)}` : 'Tiada harga jualan', data.changes?.sale_price);
                this.populateProductField('productStock', data.product.stock_quantity, data.changes?.stock_quantity);
                document.getElementById('productCreatedAt').textContent = data.created_at;
                
                // Populate tags with change highlighting
                const tagsContainer = document.getElementById('productTags');
                if (data.changes?.tags) {
                    // Tags have changed - show with highlighting
                    tagsContainer.innerHTML = `
                        <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-green-800">Tag Baharu</p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        ${data.product.tags && data.product.tags.length > 0 ? 
                                            data.product.tags.map(tag => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${tag}</span>`).join('') : 
                                            '<span class="text-gray-500 text-sm">Tiada tag</span>'
                                        }
                                    </div>
                                    <details class="mt-2">
                                        <summary class="text-xs text-green-600 cursor-pointer hover:text-green-800">Lihat tag asal</summary>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            ${data.changes.tags.old && data.changes.tags.old.length > 0 ? 
                                                data.changes.tags.old.map(tag => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">${tag}</span>`).join('') : 
                                                '<span class="text-gray-500 text-sm">Tiada tag</span>'
                                            }
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Tags unchanged - show normally
                    tagsContainer.innerHTML = '';
                    if (data.product.tags && data.product.tags.length > 0) {
                        data.product.tags.forEach(tag => {
                            const tagElement = document.createElement('span');
                            tagElement.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                            tagElement.textContent = tag;
                            tagsContainer.appendChild(tagElement);
                        });
                    } else {
                        tagsContainer.innerHTML = '<span class="text-gray-500 text-sm">Tiada tag</span>';
                    }
                }
                
                // Populate images
                const imagesContainer = document.getElementById('productImages');
                imagesContainer.innerHTML = '';
                if (data.images && data.images.length > 0) {
                    const label = document.createElement('div');
                    label.className = 'mb-2 font-semibold text-gray-700';
                    label.textContent = 'Gambar Produk';
                    imagesContainer.appendChild(label);

                    const grid = document.createElement('div');
                    grid.className = 'grid grid-cols-2 md:grid-cols-4 gap-4';
                    data.images.forEach(imageUrl => {
                        const imgDiv = document.createElement('div');
                        imgDiv.innerHTML = `<img src="${imageUrl}" alt="Gambar Produk" class="w-full h-32 object-cover rounded-lg">`;
                        grid.appendChild(imgDiv);
                    });
                    imagesContainer.appendChild(grid);
                } else {
                    imagesContainer.innerHTML = '<p class="text-gray-500 text-sm">Tiada gambar</p>';
                }
                
                // Populate variations
                const variationsContainer = document.getElementById('variationsContent');
                variationsContainer.innerHTML = '';
                if (data.variations && data.variations.length > 0) {
                    // Show variation label if exists
                    if (data.product.variation_label) {
                        const labelDiv = document.createElement('div');
                        labelDiv.className = 'mb-4 p-3 bg-gray-50 rounded-lg';
                        labelDiv.innerHTML = `<strong>Label Variasi:</strong> ${data.product.variation_label}`;
                        variationsContainer.appendChild(labelDiv);
                    }
                    
                    // Create variations grid
                    const variationsGrid = document.createElement('div');
                    variationsGrid.className = 'grid grid-cols-1 md:grid-cols-2 gap-4';
                    
                    data.variations.forEach(variation => {
                        const variationCard = document.createElement('div');
                        variationCard.className = 'border rounded-lg p-4 bg-white shadow-sm';
                        
                        const statusBadge = variation.is_active ? 
                            '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>' :
                            '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>';
                        
                        variationCard.innerHTML = `
                            <div class="space-y-2">
                                <div class="flex justify-between items-start">
                                    <h5 class="font-medium text-gray-900">${variation.name}</h5>
                                    ${statusBadge}
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><strong>SKU:</strong> ${variation.sku || 'Tiada SKU'}</p>
                                    <p><strong>Harga:</strong> RM${parseFloat(variation.price || 0).toFixed(2)}</p>
                                    <p><strong>Harga Jualan:</strong> ${variation.sale_price ? `RM${parseFloat(variation.sale_price).toFixed(2)}` : 'Tiada'}</p>
                                    <p><strong>Stok:</strong> ${variation.stock_quantity}</p>
                                </div>
                                ${variation.images && variation.images.length > 0 ? `
                                    <div class="mt-3">
                                        <label class="text-xs font-medium text-gray-500">Gambar Variasi:</label>
                                        <div class="mt-1 flex space-x-2 overflow-x-auto">
                                            ${variation.images.map(img => `<img src="${img}" alt="Variation Image" class="w-16 h-16 object-cover rounded border">`).join('')}
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        
                        variationsGrid.appendChild(variationCard);
                    });
                    
                    variationsContainer.appendChild(variationsGrid);
                } else {
                    variationsContainer.innerHTML = '<p class="text-gray-500 text-sm">Tiada variasi produk</p>';
                }
                
                // Populate user information
                document.getElementById('userName').textContent = data.user.name;
                document.getElementById('userEmail').textContent = data.user.email;
                document.getElementById('userPhone').textContent = data.user.phone || 'Tiada maklumat';
                document.getElementById('userBio').textContent = data.user.bio || 'Tiada maklumat';
                document.getElementById('userLocation').textContent = data.user.location || 'Tiada maklumat';
                document.getElementById('userSellerStatus').textContent = data.user.is_seller ? 'Penjual Disahkan' : 'Pengguna Biasa';
                
                // Populate user images
                const profileImageDiv = document.getElementById('userProfileImage');
                const idDocumentDiv = document.getElementById('userIdDocument');
                const selfieWithIdDiv = document.getElementById('userSelfieWithId');
                
                // Profile Image
                if (data.user.profile_image) {
                    document.getElementById('profileImageSrc').src = '{{ asset("storage/") }}/' + data.user.profile_image;
                    profileImageDiv.classList.remove('hidden');
                } else {
                    profileImageDiv.classList.add('hidden');
                }
                
                // ID Document
                if (data.user.id_document) {
                    document.getElementById('idDocumentSrc').src = '{{ asset("storage/") }}/' + data.user.id_document;
                    idDocumentDiv.classList.remove('hidden');
                } else {
                    idDocumentDiv.classList.add('hidden');
                }
                
                // Selfie with ID
                if (data.user.selfie_with_id) {
                    document.getElementById('selfieWithIdSrc').src = '{{ asset("storage/") }}/' + data.user.selfie_with_id;
                    selfieWithIdDiv.classList.remove('hidden');
                } else {
                    selfieWithIdDiv.classList.add('hidden');
                }
                
                // Show/hide seller details
                const sellerDetails = document.getElementById('sellerDetails');
                if (data.user.is_seller) {
                    sellerDetails.classList.remove('hidden');
                    document.getElementById('userBusinessName').textContent = data.user.business_name || 'Tiada maklumat';
                    document.getElementById('userBusinessType').textContent = data.user.business_type || 'Tiada maklumat';
                    document.getElementById('userBusinessAddress').textContent = data.user.business_address || 'Tiada maklumat';
                    document.getElementById('userOperatingArea').textContent = data.user.operating_area || 'Tiada maklumat';
                    document.getElementById('userYearsExperience').textContent = data.user.years_experience || 'Tiada maklumat';
                    document.getElementById('userSkills').textContent = data.user.skills || 'Tiada maklumat';
                    document.getElementById('userServiceAreas').textContent = data.user.service_areas || 'Tiada maklumat';
                } else {
                    sellerDetails.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching product details:', error);
                document.getElementById('productModalLoading').innerHTML = '<p class="text-red-500">Ralat memuat data produk</p>';
            });
    }

    function closeProductModal() {
        document.getElementById('productModal').classList.add('hidden');
        
        // Re-enable background page interaction
        document.body.style.overflow = '';
        document.body.style.pointerEvents = '';
    }

    function scrollToTop() {
        const modal = document.getElementById('productModal');
        modal.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function populateProductField(elementId, value, change) {
        const element = document.getElementById(elementId);
        if (change) {
            // Field has changed - show with highlighting
            element.innerHTML = `
                <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-green-800">Nilai Baharu</p>
                            <p class="text-sm text-green-700 mt-1">${value}</p>
                            <details class="mt-2">
                                <summary class="text-xs text-green-600 cursor-pointer hover:text-green-800">Lihat nilai asal</summary>
                                <p class="text-xs text-gray-600 mt-1 bg-gray-50 p-2 rounded">${change.old}</p>
                            </details>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Field unchanged - show normally
            element.textContent = value;
        }
    }

    function populateChangesSummary(changes) {
        const summaryContainer = document.getElementById('changesSummary');
        
        if (!changes || Object.keys(changes).length === 0) {
            summaryContainer.innerHTML = '';
            return;
        }
        
        const changedFields = Object.keys(changes);
        const fieldLabels = {
            'title': 'Tajuk Produk',
            'description': 'Penerangan',
            'category': 'Kategori',
            'price': 'Harga Asal',
            'sale_price': 'Harga Jualan',
            'stock_quantity': 'Kuantiti Stok',
            'tags': 'Tag',
            'images': 'Gambar',
            'variation_label': 'Label Variasi',
            'variations': 'Variasi Produk'
        };
        
        const summaryHTML = `
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Ringkasan Perubahan</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p class="mb-2">Bidang yang dikemaskini:</p>
                            <ul class="list-disc list-inside space-y-1">
                                ${changedFields.map(field => `<li>${fieldLabels[field] || field}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        summaryContainer.innerHTML = summaryHTML;
    }

    // Close modals when clicking outside
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApproveModal();
        }
    });

    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });

    document.getElementById('productModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProductModal();
        }
    });
</script>
@endsection 