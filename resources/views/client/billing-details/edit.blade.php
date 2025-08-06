@extends('layouts.app')

@section('title', 'Edit Alamat Bil - MyGooners')
@section('meta_description', 'Edit alamat bil di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('addresses.index') }}" class="hover:text-red-600 transition-colors">Alamat</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Edit Alamat</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Alamat Bil</h1>
                <p class="text-gray-600 mt-1">Kemas kini maklumat alamat bil anda</p>
            </div>
            
            <a href="{{ route('billing-details.index') }}" 
               class="text-gray-600 hover:text-red-600 font-medium transition-colors">
                ← Kembali ke Alamat Bil
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Maklumat Alamat Bil</h2>
        </div>
        
        <form method="POST" action="{{ route('addresses.billing.update', $billingDetail) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Label -->
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-2">
                        Label Alamat <span class="text-gray-500">(Pilihan)</span>
                    </label>
                    <input type="text" name="label" id="label" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Contoh: Rumah, Pejabat, Alamat Bil"
                           value="{{ old('label', $billingDetail->label) }}">
                    @error('label')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Penuh <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Masukkan nama penuh"
                           value="{{ old('name', $billingDetail->name) }}">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Emel <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Masukkan alamat emel"
                           value="{{ old('email', $billingDetail->email) }}">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombor Telefon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" id="phone" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Contoh: 012-3456789"
                           value="{{ old('phone', $billingDetail->phone) }}">
                    @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" id="address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Masukkan alamat lengkap">{{ old('address', $billingDetail->address) }}</textarea>
                    @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- City, State, Postal Code -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Bandar <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" id="city" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Contoh: Kuala Lumpur"
                               value="{{ old('city', $billingDetail->city) }}">
                        @error('city')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                            Negeri <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="state" id="state" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Contoh: Selangor"
                               value="{{ old('state', $billingDetail->state) }}">
                        @error('state')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Poskod <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="postal_code" id="postal_code" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="Contoh: 50000"
                               value="{{ old('postal_code', $billingDetail->postal_code) }}">
                        @error('postal_code')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                        Negara <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="country" id="country" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Contoh: Malaysia"
                           value="{{ old('country', $billingDetail->country) }}">
                    @error('country')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Set as Default -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                           {{ old('is_default', $billingDetail->is_default) ? 'checked' : '' }}>
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">
                        Tetapkan sebagai alamat lalai
                    </label>
                </div>
            </div>
            
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-8">
                <a href="{{ route('billing-details.index') }}" 
                   class="text-gray-600 hover:text-gray-800 font-medium">
                    Batal
                </a>
                
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    Kemas Kini Alamat Bil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 