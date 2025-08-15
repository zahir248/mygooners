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
                <h1 class="text-2xl font-bold text-gray-900">Kemaskini Permohonan Penjual</h1>
            </div>
            <p class="text-gray-600">Kemaskini maklumat penjual anda berdasarkan sebab penolakan dan hantar semula untuk kelulusan.</p>
        </div>

        <!-- Rejection Reason Reminder -->
        @if($user->seller_rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="bg-red-100 text-red-600 rounded-full p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800 mb-1">Sebab Penolakan Sebelum Ini:</h3>
                    <p class="text-sm text-red-700">{{ $user->seller_rejection_reason }}</p>
                    <p class="text-xs text-red-600 mt-2">Sila perbaiki isu-isu ini sebelum menghantar semula permohonan anda.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Maklumat Penjual</h2>
            </div>

            <form action="{{ route('rejected.seller.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Business Name -->
                    <div class="md:col-span-2">
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Perniagaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="business_name" 
                               name="business_name" 
                               value="{{ old('business_name', $user->business_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('business_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Perniagaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="business_type" 
                               name="business_type" 
                               value="{{ old('business_type', $user->business_type) }}"
                               placeholder="Contoh: Perusahaan Kecil dan Sederhana"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('business_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Registration -->
                    <div>
                        <label for="business_registration" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombor Pendaftaran Perniagaan
                        </label>
                        <input type="text" 
                               id="business_registration" 
                               name="business_registration" 
                               value="{{ old('business_registration', $user->business_registration) }}"
                               placeholder="Contoh: 12345678-X"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('business_registration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                            Laman Web
                        </label>
                        <input type="url" 
                               id="website" 
                               name="website" 
                               value="{{ old('website', $user->website) }}"
                               placeholder="Contoh: https://www.example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('website')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Operating Area -->
                    <div>
                        <label for="operating_area" class="block text-sm font-medium text-gray-700 mb-2">
                            Kawasan Operasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="operating_area" 
                               name="operating_area" 
                               value="{{ old('operating_area', $user->operating_area) }}"
                               placeholder="Contoh: Selangor, Kuala Lumpur"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('operating_area')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Years Experience -->
                    <div>
                        <label for="years_experience" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Pengalaman <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="years_experience" 
                               name="years_experience" 
                               value="{{ old('years_experience', $user->years_experience) }}"
                               min="0" max="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('years_experience')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Address -->
                    <div class="md:col-span-2">
                        <label for="business_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Alamat Perniagaan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" 
                                  name="business_address" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('business_address', $user->business_address) }}</textarea>
                        @error('business_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Skills -->
                    <div class="md:col-span-2">
                        <label for="skills" class="block text-sm font-medium text-gray-700 mb-2">
                            Kemahiran <span class="text-red-500">*</span>
                        </label>
                        <textarea id="skills" 
                                  name="skills" 
                                  rows="3"
                                  placeholder="Senaraikan kemahiran dan kelebihan anda..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('skills', $user->skills) }}</textarea>
                        @error('skills')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Areas -->
                    <div class="md:col-span-2">
                        <label for="service_areas" class="block text-sm font-medium text-gray-700 mb-2">
                            Kawasan Perkhidmatan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="service_areas" 
                                  name="service_areas" 
                                  rows="3"
                                  placeholder="Senaraikan kawasan yang anda boleh berkhidmat..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  required>{{ old('service_areas', $user->service_areas) }}</textarea>
                        @error('service_areas')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Documents -->
                    @if($user->id_document || $user->selfie_with_id)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dokumen Semasa
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            @if($user->id_document)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-1">Dokumen Pengenalan</p>
                                <a href="{{ route('seller.document', ['filename' => basename($user->id_document)]) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Lihat Dokumen
                                </a>
                            </div>
                            @endif
                            @if($user->selfie_with_id)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-1">Selfie dengan ID</p>
                                <a href="{{ route('seller.image', ['filename' => basename($user->selfie_with_id)]) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Lihat Gambar
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- New Documents -->
                    <div class="md:col-span-2">
                        <label for="id_document" class="block text-sm font-medium text-gray-700 mb-2">
                            Dokumen Pengenalan Baharu
                        </label>
                        <input type="file" 
                               id="id_document" 
                               name="id_document" 
                               accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format yang diterima: JPG, PNG, PDF. Saiz maksimum: 2MB.</p>
                        @error('id_document')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="selfie_with_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Selfie dengan ID Baharu
                        </label>
                        <input type="file" 
                               id="selfie_with_id" 
                               name="selfie_with_id" 
                               accept=".jpg,.jpeg,.png"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format yang diterima: JPG, PNG. Saiz maksimum: 2MB.</p>
                        @error('selfie_with_id')
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
                            <span>Pastikan semua maklumat perniagaan diisi dengan lengkap dan tepat</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Berikan penerangan kemahiran yang terperinci dan relevan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Pastikan dokumen yang dihantar adalah jelas dan sah</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                            </svg>
                            <span>Nyatakan kawasan perkhidmatan yang jelas dan terperinci</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 