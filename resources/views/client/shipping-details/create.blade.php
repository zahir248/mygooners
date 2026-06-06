@extends('layouts.app')

@section('title', __('Tambah Alamat Penghantaran - MyGooners'))
@section('meta_description', __('Tambah alamat penghantaran baru di MyGooners.'))

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">{{ __('Utama') }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('addresses.index') }}" class="hover:text-red-600 transition-colors">{{ __('Alamat') }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ __('Tambah Alamat Baru') }}</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Tambah Alamat Penghantaran') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Tambah alamat penghantaran baru untuk kemudahan semasa checkout') }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">{{ __('Maklumat Alamat') }}</h2>
        </div>
        
        <form action="{{ route('addresses.shipping.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nama Penuh *') }}</label>
                    <input type="text" id="name" name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                           required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Emel *') }}</label>
                    <input type="email" id="email" name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                           required>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nombor Telefon *') }}</label>
                <input type="tel" id="phone" name="phone" 
                       value="{{ old('phone') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                       required>
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Alamat Penghantaran *') }}</label>
                <textarea id="address" name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                          required>{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Bandar *') }}</label>
                    <input type="text" id="city" name="city" 
                           value="{{ old('city') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                           required>
                    @error('city')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Negeri *') }}</label>
                    <select id="state" name="state" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                            required>
                        <option value="">{{ __('Pilih Negeri') }}</option>
                        <option value="Johor" {{ old('state') == 'Johor' ? 'selected' : '' }}>{{ __('Johor') }}</option>
                        <option value="Kedah" {{ old('state') == 'Kedah' ? 'selected' : '' }}>{{ __('Kedah') }}</option>
                        <option value="Kelantan" {{ old('state') == 'Kelantan' ? 'selected' : '' }}>{{ __('Kelantan') }}</option>
                        <option value="Melaka" {{ old('state') == 'Melaka' ? 'selected' : '' }}>{{ __('Melaka') }}</option>
                        <option value="Negeri Sembilan" {{ old('state') == 'Negeri Sembilan' ? 'selected' : '' }}>{{ __('Negeri Sembilan') }}</option>
                        <option value="Pahang" {{ old('state') == 'Pahang' ? 'selected' : '' }}>{{ __('Pahang') }}</option>
                        <option value="Perak" {{ old('state') == 'Perak' ? 'selected' : '' }}>{{ __('Perak') }}</option>
                        <option value="Perlis" {{ old('state') == 'Perlis' ? 'selected' : '' }}>{{ __('Perlis') }}</option>
                        <option value="Pulau Pinang" {{ old('state') == 'Pulau Pinang' ? 'selected' : '' }}>{{ __('Pulau Pinang') }}</option>
                        <option value="Sabah" {{ old('state') == 'Sabah' ? 'selected' : '' }}>{{ __('Sabah') }}</option>
                        <option value="Sarawak" {{ old('state') == 'Sarawak' ? 'selected' : '' }}>{{ __('Sarawak') }}</option>
                        <option value="Selangor" {{ old('state') == 'Selangor' ? 'selected' : '' }}>{{ __('Selangor') }}</option>
                        <option value="Terengganu" {{ old('state') == 'Terengganu' ? 'selected' : '' }}>{{ __('Terengganu') }}</option>
                        <option value="Kuala Lumpur" {{ old('state') == 'Kuala Lumpur' ? 'selected' : '' }}>{{ __('Kuala Lumpur') }}</option>
                        <option value="Labuan" {{ old('state') == 'Labuan' ? 'selected' : '' }}>{{ __('Labuan') }}</option>
                        <option value="Putrajaya" {{ old('state') == 'Putrajaya' ? 'selected' : '' }}>{{ __('Putrajaya') }}</option>
                    </select>
                    @error('state')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Poskod *') }}</label>
                    <input type="text" id="postal_code" name="postal_code" 
                           value="{{ old('postal_code') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                           required>
                    @error('postal_code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Negara *') }}</label>
                <input type="text" id="country" name="country" 
                       value="{{ old('country', 'Malaysia') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                       required>
                @error('country')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="label" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Label (Pilihan)') }}</label>
                    <input type="text" id="label" name="label" 
                           value="{{ old('label') }}"
                           placeholder="{{ __('Contoh: Rumah, Pejabat, Dorm') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    @error('label')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center pt-6">
                    <input type="checkbox" name="is_default" id="is_default" value="1"
                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                           {{ old('is_default') ? 'checked' : '' }}>
                    <label for="is_default" class="ml-2 block text-sm text-gray-700">{{ __('Tetapkan sebagai alamat lalai') }}</label>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('addresses.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">{{ __('Batal') }}</a>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">{{ __('Simpan Alamat') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection 