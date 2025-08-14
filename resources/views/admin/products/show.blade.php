@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Butiran Produk')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
                <div>
            <h1 class="text-2xl font-bold text-gray-900">Butiran Produk</h1>
            <p class="mt-2 text-sm text-gray-700">{{ $product->title }}</p>
                                </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.products.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                Kembali ke Produk
            </a>
            
                    <a href="{{ route('admin.products.edit', $product->id) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Produk
                    </a>
            
            <button type="button" 
                    onclick="openStatusModal('{{ $product->status === 'active' ? 'deactivate' : 'activate' }}', '{{ $product->title }}')"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $product->status === 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $product->status === 'active' ? 'focus:ring-yellow-500' : 'focus:ring-green-500' }}">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($product->status === 'active')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                @endif
                            </svg>
                            {{ $product->status === 'active' ? 'Nyahaktif' : 'Aktif' }}
                        </button>
            
            <button type="button" 
                    onclick="openFeaturedModal('{{ $product->is_featured ? 'unfeature' : 'feature' }}', '{{ $product->title }}')"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $product->is_featured ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $product->is_featured ? 'focus:ring-purple-500' : 'focus:ring-blue-500' }}">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($product->is_featured)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                @endif
                            </svg>
                            {{ $product->is_featured ? 'Nyahpaparkan' : 'Tampilkan' }}
                        </button>
                </div>
            </div>
        </div>

    <div class="mx-4 grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Information -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Maklumat Produk</h3>
                    </div>
                    <div class="p-6">
                        <!-- Product Images -->
                        @if(!empty($product->images))
                            <div class="mb-6">
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="aspect-w-16 aspect-h-9">
                                        <img src="{{ route('product.image', basename($product->images[0])) }}" 
                                             alt="{{ $product->title }}" 
                                             class="w-full h-64 object-cover rounded-lg">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900">{{ $product->title }}</h4>
                                <div class="mt-2 flex items-center space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $product->category }}
                                    </span>
                                    @if($product->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    @elseif($product->status === 'inactive')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Tidak Aktif
                                        </span>

                                    @elseif($product->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            Ditampilkan
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Harga Asal</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">RM{{ number_format($product->price, 2) }}</dd>
                                </div>
                                @if($product->sale_price)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Harga Jualan</dt>
                                        <dd class="mt-1 text-sm font-semibold text-red-600">RM{{ number_format($product->sale_price, 2) }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stok</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($product->stock_quantity > 10)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $product->stock_quantity }} Dalam Stok
                                            </span>
                                        @elseif($product->stock_quantity > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $product->stock_quantity }} Stok Rendah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Kehabisan Stok
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Penilaian</dt>
                                    <dd class="mt-1 flex items-center">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($product->average_rating))
                                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }} ulasan)</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tontonan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->views_count) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Dicipta</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('j M Y \p\a\d\a g:i A') }}</dd>
                                </div>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <p class="whitespace-pre-line">{{ $product->description }}</p>
                                </dd>
                            </div>

                            @if($product->tags)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tag</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if(is_array($product->tags))
                                            @foreach($product->tags as $tag)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-1">{{ $tag }}</span>
                                            @endforeach
                                        @else
                                            {{ $product->tags }}
                                        @endif
                                    </dd>
                                </div>
                            @endif

                            @if($product->meta_title || $product->meta_description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">SEO</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($product->meta_title)
                                            <div class="mb-1">
                                                <strong>Meta Title:</strong> {{ $product->meta_title }}
                                            </div>
                                        @endif
                                        @if($product->meta_description)
                                            <div>
                                                <strong>Meta Description:</strong> {{ $product->meta_description }}
                                            </div>
                                        @endif
                                    </dd>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Reviews -->
                @if($product->reviews->count() > 0)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Ulasan Produk ({{ $product->reviews->count() }})</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($product->reviews as $review)
                                    <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                            @if($review->user)
                                                @if($review->user->profile_image)
                                                    @if(Str::startsWith($review->user->profile_image, 'http'))
                                                        <img src="{{ $review->user->profile_image }}" 
                                                             alt="{{ $review->user->name }}" 
                                                             class="h-8 w-8 rounded-full object-cover">
                                                    @else
                                                        <img src="{{ asset('storage/' . $review->user->profile_image) }}" 
                                                             alt="{{ $review->user->name }}" 
                                                             class="h-8 w-8 rounded-full object-cover">
                                                    @endif
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                                        <span class="text-xs font-bold text-red-600">{{ substr($review->user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-xs font-bold text-gray-600">A</span>
                                                    </div>
                                            @endif
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $review->user->name }}</div>
                                                    <div class="flex items-center">
                                                        <div class="flex items-center">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $review->rating)
                                                                    <svg class="h-3 w-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @else
                                                                    <svg class="h-3 w-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="ml-1 text-xs text-gray-500">{{ $review->created_at->format('j M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <div class="mt-2 text-sm text-gray-700">
                                                {{ $review->comment }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tindakan Pantas</h3>
                    </div>
                                         <div class="p-6 space-y-4">
                         <a href="{{ route('shop.show', $product->slug) }}" 
                            target="_blank"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                             <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                             </svg>
                             Lihat di Kedai
                         </a>
                         <button type="button" 
                                 onclick="openDeleteModal('{{ $product->title }}')"
                                 class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                             <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                             </svg>
                             Padam Produk
                         </button>
                     </div>
                </div>

                <!-- Product Statistics -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statistik Produk</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Jumlah Tontonan</span>
                            <span class="text-sm text-gray-900">{{ number_format($product->views_count) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Jumlah Ulasan</span>
                            <span class="text-sm text-gray-900">{{ $product->reviews->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Purata Penilaian</span>
                            <span class="text-sm text-gray-900">{{ number_format($product->average_rating, 1) }}/5</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Stok Semasa</span>
                            <span class="text-sm text-gray-900">{{ $product->stock_quantity }} unit</span>
                        </div>
                    </div>
                </div>

                <!-- Product Images Gallery -->
                @if($product->images && count($product->images) > 1)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Galeri Gambar</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(array_slice($product->images, 1) as $image)
                                    <div class="aspect-w-1 aspect-h-1">
                                        <img src="{{ route('product.image', basename($image)) }}" 
                                             alt="{{ $product->title }}" 
                                             class="w-full h-20 object-cover rounded-lg">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
    </div>
</div>

<!-- Status Confirmation Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <!-- Modal Title -->
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="statusModalTitle">Ubah Status Produk</h3>
            
            <!-- Modal Content -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="statusModalMessage">
                    Adakah anda pasti mahu mengubah status produk ini?
                </p>
                <div class="mt-3 bg-gray-50 p-3 rounded-md">
                    <p class="text-xs text-gray-600 font-medium">Produk:</p>
                    <p class="text-sm text-gray-800 mt-1" id="statusProductName"></p>
                </div>
            </div>
            
            <!-- Modal Actions -->
            <div class="flex items-center justify-center gap-3 mt-4">
                <button id="cancelStatus" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                    Batal
                </button>
                <form id="statusForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 text-sm">
                        Ya, Ubah Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Featured Confirmation Modal -->
<div id="featuredModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <!-- Info Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Modal Title -->
            <h3 class="text-lg font-medium text-gray-900 mt-4" id="featuredModalTitle">Ubah Status Ditampilkan</h3>
            
            <!-- Modal Content -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="featuredModalMessage">
                    Adakah anda pasti mahu mengubah status ditampilkan produk ini?
                </p>
                <div class="mt-3 bg-gray-50 p-3 rounded-md">
                    <p class="text-xs text-gray-600 font-medium">Produk:</p>
                    <p class="text-sm text-gray-800 mt-1" id="featuredProductName"></p>
                </div>
            </div>
            
            <!-- Modal Actions -->
            <div class="flex items-center justify-center gap-3 mt-4">
                <button id="cancelFeatured" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                    Batal
                </button>
                <form id="featuredForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                        Ya, Ubah Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <!-- Modal Title -->
            <h3 class="text-lg font-medium text-gray-900 mt-4">Padam Produk</h3>
            
            <!-- Modal Content -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu memadamkan produk ini?
                </p>
                <div class="mt-3 bg-gray-50 p-3 rounded-md">
                    <p class="text-xs text-gray-600 font-medium">Produk:</p>
                    <p class="text-sm text-gray-800 mt-1" id="deleteProductName"></p>
                </div>
                <p class="text-xs text-red-600 mt-2">
                    Tindakan ini tidak boleh dibatalkan.
                </p>
            </div>
            
            <!-- Modal Actions -->
            <div class="flex items-center justify-center gap-3 mt-4">
                <button id="cancelDelete" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                    Batal
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                        Padam
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Modal
        const statusModal = document.getElementById('statusModal');
        const cancelStatusBtn = document.getElementById('cancelStatus');
        const statusForm = document.getElementById('statusForm');
        const statusModalTitle = document.getElementById('statusModalTitle');
        const statusModalMessage = document.getElementById('statusModalMessage');
        const statusProductName = document.getElementById('statusProductName');
        
        // Featured Modal
        const featuredModal = document.getElementById('featuredModal');
        const cancelFeaturedBtn = document.getElementById('cancelFeatured');
        const featuredForm = document.getElementById('featuredForm');
        const featuredModalTitle = document.getElementById('featuredModalTitle');
        const featuredModalMessage = document.getElementById('featuredModalMessage');
        const featuredProductName = document.getElementById('featuredProductName');
        
        // Delete Modal
        const deleteModal = document.getElementById('deleteModal');
        const cancelDeleteBtn = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        const deleteProductName = document.getElementById('deleteProductName');
        
        // Close status modal when clicking cancel
        cancelStatusBtn.addEventListener('click', function() {
            statusModal.classList.add('hidden');
        });
        
        // Close featured modal when clicking cancel
        cancelFeaturedBtn.addEventListener('click', function() {
            featuredModal.classList.add('hidden');
        });

        // Close delete modal when clicking cancel
        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
        });
        
        // Close modals when clicking outside
        statusModal.addEventListener('click', function(e) {
            if (e.target === statusModal) {
                statusModal.classList.add('hidden');
            }
        });
        
        featuredModal.addEventListener('click', function(e) {
            if (e.target === featuredModal) {
                featuredModal.classList.add('hidden');
            }
        });

        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });
        
        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!statusModal.classList.contains('hidden')) {
                    statusModal.classList.add('hidden');
                }
                if (!featuredModal.classList.contains('hidden')) {
                    featuredModal.classList.add('hidden');
                }
                if (!deleteModal.classList.contains('hidden')) {
                    deleteModal.classList.add('hidden');
                }
            }
        });
    });
    
    // Function to open status modal
    function openStatusModal(action, productName) {
        const statusModal = document.getElementById('statusModal');
        const statusForm = document.getElementById('statusForm');
        const statusModalTitle = document.getElementById('statusModalTitle');
        const statusModalMessage = document.getElementById('statusModalMessage');
        const statusProductName = document.getElementById('statusProductName');
        
        // Set the form action
        statusForm.action = '{{ route("admin.products.toggle-status", $product->id) }}';
        
        // Set the modal content based on action
        if (action === 'deactivate') {
            statusModalTitle.textContent = 'Nyahaktif Produk';
            statusModalMessage.textContent = 'Adakah anda pasti mahu menyahaktifkan produk ini?';
        } else {
            statusModalTitle.textContent = 'Aktif Produk';
            statusModalMessage.textContent = 'Adakah anda pasti mahu mengaktifkan produk ini?';
        }
        
        // Set the product name
        statusProductName.textContent = productName;
        
        // Show the modal
        statusModal.classList.remove('hidden');
    }
    
    // Function to open featured modal
    function openFeaturedModal(action, productName) {
        const featuredModal = document.getElementById('featuredModal');
        const featuredForm = document.getElementById('featuredForm');
        const featuredModalTitle = document.getElementById('featuredModalTitle');
        const featuredModalMessage = document.getElementById('featuredModalMessage');
        const featuredProductName = document.getElementById('featuredProductName');
        
        // Set the form action
        featuredForm.action = '{{ route("admin.products.toggle-featured", $product->id) }}';
        
        // Set the modal content based on action
        if (action === 'unfeature') {
            featuredModalTitle.textContent = 'Nyahpaparkan Produk';
            featuredModalMessage.textContent = 'Adakah anda pasti mahu menyahpaparkan produk ini?';
        } else {
            featuredModalTitle.textContent = 'Tampilkan Produk';
            featuredModalMessage.textContent = 'Adakah anda pasti mahu menampilkan produk ini?';
        }
        
        // Set the product name
        featuredProductName.textContent = productName;
        
        // Show the modal
        featuredModal.classList.remove('hidden');
    }

    // Function to open delete modal
    function openDeleteModal(productName) {
        const deleteModal = document.getElementById('deleteModal');
        const deleteProductName = document.getElementById('deleteProductName');
        deleteProductName.textContent = productName;
        deleteModal.classList.remove('hidden');
    }
</script>
@endsection 