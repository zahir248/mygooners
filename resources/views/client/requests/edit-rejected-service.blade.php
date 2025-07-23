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
                <h1 class="text-2xl font-bold text-gray-900">Kemaskini Permohonan Perkhidmatan</h1>
            </div>
            <p class="text-gray-600">Kemaskini maklumat perkhidmatan anda berdasarkan sebab penolakan dan hantar semula untuk kelulusan.</p>
        </div>

        <!-- Rejection Reason Reminder -->
        @if($service->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="bg-red-100 text-red-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800 mb-1">Sebab Penolakan Sebelum Ini:</h3>
                    <p class="text-sm text-red-700">{{ $service->rejection_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">Sila perbaiki isu-isu ini sebelum menghantar semula permohonan anda.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Maklumat Perkhidmatan</h2>
            </div>

            <form action="{{ route('rejected.service.update', $service->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tajuk Perkhidmatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $service->title) }}"
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
                            <option value="Jersi" {{ old('category', $service->category) == 'Jersi' ? 'selected' : '' }}>Jersi</option>
                            <option value="Pakaian Latihan" {{ old('category', $service->category) == 'Pakaian Latihan' ? 'selected' : '' }}>Pakaian Latihan</option>
                            <option value="Aksesori" {{ old('category', $service->category) == 'Aksesori' ? 'selected' : '' }}>Aksesori</option>
                            <option value="Kasut" {{ old('category', $service->category) == 'Kasut' ? 'selected' : '' }}>Kasut</option>
                            <option value="Koleksi" {{ old('category', $service->category) == 'Koleksi' ? 'selected' : '' }}>Koleksi</option>
                            <option value="Lain-lain" {{ old('category', $service->category) == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Lokasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location', $service->location) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pricing -->
                    <div>
                        <label for="pricing" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="pricing" 
                               name="pricing" 
                               value="{{ old('pricing', $service->pricing) }}"
                               placeholder="Contoh: RM 50-100 atau RM 50/jam"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('pricing')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Info -->
                    <div>
                        <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">
                            Maklumat Hubungan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="contact_info" 
                               name="contact_info" 
                               value="{{ old('contact_info', $service->contact_info) }}"
                               placeholder="Contoh: WhatsApp: 012-3456789"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('contact_info')
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
                                  required>{{ old('description', $service->description) }}</textarea>
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
                               value="{{ old('tags', is_array($service->tags) ? implode(', ', $service->tags) : $service->tags) }}"
                               placeholder="Contoh: jersi arsenal, original, berkualiti"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan tag dengan koma</p>
                        @error('tags')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Images -->
                    @if($service->images && count($service->images) > 0)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Semasa
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($service->images as $index => $image)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $image) }}" 
                                     alt="Service Image" 
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
                            <span>Gunakan gambar berkualiti tinggi yang jelas menunjukkan perkhidmatan anda</span>
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
                            <span>Pastikan maklumat hubungan adalah tepat dan boleh dihubungi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeImage(button) {
    const imageContainer = button.parentElement;
    imageContainer.remove();
}
</script>
@endsection 