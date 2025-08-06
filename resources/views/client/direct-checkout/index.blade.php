@extends('layouts.app')

@section('title', 'Checkout Langsung - MyGooners')
@section('meta_description', 'Selesaikan pembelian anda secara langsung di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('shop.show', $product) }}" class="hover:text-red-600 transition-colors">{{ $product->title }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Checkout Langsung</span>
        </nav>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('direct-checkout.store') }}" method="POST" id="checkout-form">
                @csrf
                
                <!-- Shipping Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Maklumat Penghantaran</h2>
                    </div>
                    
                    <div class="p-6 space-y-6" id="shipping-fields">
                        <!-- Shipping Details Selection (Hidden by default, shown via JavaScript) -->
                        @if($shippingDetails->count() > 0)
                            <div id="shipping-details-selection" class="border border-gray-200 rounded-lg p-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Pilih Alamat Penghantaran</h3>
                                    <button type="button" id="show-new-shipping-address" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        + Alamat Baru
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    @foreach($shippingDetails as $shippingDetail)
                                        <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:border-red-300 cursor-pointer">
                                            <input type="radio" name="shipping_detail_id" value="{{ $shippingDetail->id }}" 
                                                   class="mt-1 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                                   {{ $shippingDetail->is_default ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium text-gray-900">{{ $shippingDetail->display_label }}</span>
                                                    @if($shippingDetail->is_default)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Lalai
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">{{ $shippingDetail->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $shippingDetail->email }}</p>
                                                <p class="text-sm text-gray-600">{{ $shippingDetail->phone }}</p>
                                                <p class="text-sm text-gray-600 mt-1">{{ $shippingDetail->full_address }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Quick Shipping Details Toggle -->
                        @if($shippingDetails->count() > 0)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Ada {{ $shippingDetails->count() }} alamat penghantaran tersimpan</span>
                                </div>
                                <button type="button" id="toggle-shipping-details" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Pilih Alamat Tersimpan
                                </button>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Penuh *</label>
                                <input type="text" id="shipping_name" name="shipping_name" 
                                       value="{{ old('shipping_name', $user->name ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('shipping_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">Emel *</label>
                                <input type="email" id="shipping_email" name="shipping_email" 
                                       value="{{ old('shipping_email', $user->email ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('shipping_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">Nombor Telefon *</label>
                            <input type="tel" id="shipping_phone" name="shipping_phone" 
                                   value="{{ old('shipping_phone', $user->phone ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   required>
                            @error('shipping_phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Penghantaran *</label>
                            <textarea id="shipping_address" name="shipping_address" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                      required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-2">Bandar *</label>
                                <input type="text" id="shipping_city" name="shipping_city" 
                                       value="{{ old('shipping_city') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('shipping_city')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700 mb-2">Negeri *</label>
                                <select id="shipping_state" name="shipping_state" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                        required>
                                    <option value="">Pilih Negeri</option>
                                    <option value="Johor" {{ old('shipping_state') == 'Johor' ? 'selected' : '' }}>Johor</option>
                                    <option value="Kedah" {{ old('shipping_state') == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                                    <option value="Kelantan" {{ old('shipping_state') == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                    <option value="Melaka" {{ old('shipping_state') == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                    <option value="Negeri Sembilan" {{ old('shipping_state') == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                                    <option value="Pahang" {{ old('shipping_state') == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                    <option value="Perak" {{ old('shipping_state') == 'Perak' ? 'selected' : '' }}>Perak</option>
                                    <option value="Perlis" {{ old('shipping_state') == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                                    <option value="Pulau Pinang" {{ old('shipping_state') == 'Pulau Pinang' ? 'selected' : '' }}>Pulau Pinang</option>
                                    <option value="Sabah" {{ old('shipping_state') == 'Sabah' ? 'selected' : '' }}>Sabah</option>
                                    <option value="Sarawak" {{ old('shipping_state') == 'Sarawak' ? 'selected' : '' }}>Sarawak</option>
                                    <option value="Selangor" {{ old('shipping_state') == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                    <option value="Terengganu" {{ old('shipping_state') == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                                    <option value="Kuala Lumpur" {{ old('shipping_state') == 'Kuala Lumpur' ? 'selected' : '' }}>Kuala Lumpur</option>
                                    <option value="Labuan" {{ old('shipping_state') == 'Labuan' ? 'selected' : '' }}>Labuan</option>
                                    <option value="Putrajaya" {{ old('shipping_state') == 'Putrajaya' ? 'selected' : '' }}>Putrajaya</option>
                                </select>
                                @error('shipping_state')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Poskod *</label>
                                <input type="text" id="shipping_postal_code" name="shipping_postal_code" 
                                       value="{{ old('shipping_postal_code') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('shipping_postal_code')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-2">Negara *</label>
                            <input type="text" id="shipping_country" name="shipping_country" 
                                   value="{{ old('shipping_country', 'Malaysia') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   required>
                            @error('shipping_country')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Save as Shipping Detail -->
                        <div class="flex items-center pt-4 border-t border-gray-200">
                            <input type="checkbox" name="save_shipping_detail" id="save_shipping_detail" value="1"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                   {{ old('save_shipping_detail') ? 'checked' : '' }}>
                            <label for="save_shipping_detail" class="ml-2 block text-sm text-gray-700">
                                Simpan alamat ini sebagai alamat penghantaran untuk kegunaan seterusnya
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-900">Maklumat Bil</h2>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" id="same_as_shipping" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm text-gray-600">Sama seperti alamat penghantaran</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6" id="billing-fields">
                        <!-- Billing Details Selection (Hidden by default, shown via JavaScript) -->
                        @if($billingDetails->count() > 0)
                            <div id="billing-details-selection" class="border border-gray-200 rounded-lg p-4 hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Pilih Alamat Bil</h3>
                                    <button type="button" id="show-new-address" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        + Alamat Baru
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    @foreach($billingDetails as $billingDetail)
                                        <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:border-red-300 cursor-pointer">
                                            <input type="radio" name="billing_detail_id" value="{{ $billingDetail->id }}" 
                                                   class="mt-1 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                                                   {{ $billingDetail->is_default ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-medium text-gray-900">{{ $billingDetail->display_label }}</span>
                                                    @if($billingDetail->is_default)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Lalai
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">{{ $billingDetail->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $billingDetail->email }}</p>
                                                <p class="text-sm text-gray-600">{{ $billingDetail->phone }}</p>
                                                <p class="text-sm text-gray-600 mt-1">{{ $billingDetail->full_address }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Quick Billing Details Toggle -->
                        @if($billingDetails->count() > 0)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Ada {{ $billingDetails->count() }} alamat bil tersimpan</span>
                                </div>
                                <button type="button" id="toggle-billing-details" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Pilih Alamat Tersimpan
                                </button>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Penuh *</label>
                                <input type="text" id="billing_name" name="billing_name" 
                                       value="{{ old('billing_name', $user->name ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('billing_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">Emel *</label>
                                <input type="email" id="billing_email" name="billing_email" 
                                       value="{{ old('billing_email', $user->email ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('billing_email')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">Nombor Telefon *</label>
                            <input type="tel" id="billing_phone" name="billing_phone" 
                                   value="{{ old('billing_phone', $user->phone ?? '') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   required>
                            @error('billing_phone')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Bil *</label>
                            <textarea id="billing_address" name="billing_address" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                      required>{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">Bandar *</label>
                                <input type="text" id="billing_city" name="billing_city" 
                                       value="{{ old('billing_city') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('billing_city')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="billing_state" class="block text-sm font-medium text-gray-700 mb-2">Negeri *</label>
                                <select id="billing_state" name="billing_state" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                        required>
                                    <option value="">Pilih Negeri</option>
                                    <option value="Johor" {{ old('billing_state') == 'Johor' ? 'selected' : '' }}>Johor</option>
                                    <option value="Kedah" {{ old('billing_state') == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                                    <option value="Kelantan" {{ old('billing_state') == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                                    <option value="Melaka" {{ old('billing_state') == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                                    <option value="Negeri Sembilan" {{ old('billing_state') == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                                    <option value="Pahang" {{ old('billing_state') == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                                    <option value="Perak" {{ old('billing_state') == 'Perak' ? 'selected' : '' }}>Perak</option>
                                    <option value="Perlis" {{ old('billing_state') == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                                    <option value="Pulau Pinang" {{ old('billing_state') == 'Pulau Pinang' ? 'selected' : '' }}>Pulau Pinang</option>
                                    <option value="Sabah" {{ old('billing_state') == 'Sabah' ? 'selected' : '' }}>Sabah</option>
                                    <option value="Sarawak" {{ old('shipping_state') == 'Sarawak' ? 'selected' : '' }}>Sarawak</option>
                                    <option value="Selangor" {{ old('billing_state') == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                                    <option value="Terengganu" {{ old('billing_state') == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                                    <option value="Kuala Lumpur" {{ old('billing_state') == 'Kuala Lumpur' ? 'selected' : '' }}>Kuala Lumpur</option>
                                    <option value="Labuan" {{ old('billing_state') == 'Labuan' ? 'selected' : '' }}>Labuan</option>
                                    <option value="Putrajaya" {{ old('billing_state') == 'Putrajaya' ? 'selected' : '' }}>Putrajaya</option>
                                </select>
                                @error('billing_state')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Poskod *</label>
                                <input type="text" id="billing_postal_code" name="billing_postal_code" 
                                       value="{{ old('billing_postal_code') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                       required>
                                @error('billing_postal_code')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-2">Negara *</label>
                            <input type="text" id="billing_country" name="billing_country" 
                                   value="{{ old('billing_country', 'Malaysia') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                   required>
                            @error('billing_country')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Save as Billing Detail -->
                        <div class="flex items-center pt-4 border-t border-gray-200">
                            <input type="checkbox" name="save_billing_detail" id="save_billing_detail" value="1"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                   {{ old('save_billing_detail') ? 'checked' : '' }}>
                            <label for="save_billing_detail" class="ml-2 block text-sm text-gray-700">
                                Simpan alamat ini sebagai alamat bil untuk kegunaan seterusnya
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Kaedah Pembayaran</h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                                <input type="radio" name="payment_method" value="toyyibpay" 
                                       {{ old('payment_method', 'toyyibpay') == 'toyyibpay' ? 'checked' : '' }}
                                       class="text-red-600 focus:ring-red-500" checked>
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <span class="font-medium">ToyyibPay</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Pembayaran selamat melalui ToyyibPay</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                                <input type="radio" name="payment_method" value="stripe" 
                                       {{ old('payment_method') == 'stripe' ? 'checked' : '' }}
                                       class="text-blue-600 focus:ring-blue-500">
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 003 3z"></path>
                                        </svg>
                                        <span class="font-medium">Stripe</span>
                                    </div>
                                    <p class="text-sm text-gray-600">Pembayaran selamat melalui kad kredit/debit</p>
                                </div>
                            </label>
                        </div>
                        
                        @error('payment_method')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Order Notes -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">Nota Pesanan (Pilihan)</h2>
                    </div>
                    
                    <div class="p-6">
                        <textarea name="notes" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                  placeholder="Tambah nota khas untuk pesanan anda...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                </div>
                
                <div class="p-6">
                    <!-- Product Item -->
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="flex-shrink-0">
                            <img src="{{ $variation && $variation->images && count($variation->images) > 0 ? route('variation.image', basename($variation->images[0])) : ($product->images && count($product->images) > 0 ? route('product.image', basename($product->images[0])) : asset('images/profile-image-default.png')) }}" 
                                 alt="{{ $product->title }}" 
                                 class="w-16 h-16 object-cover rounded-lg">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $product->title }}</p>
                            @if($variation)
                                <p class="text-sm text-gray-500">{{ $variation->name }}</p>
                            @endif
                            <p class="text-sm text-gray-500">Kuantiti: {{ $quantity }}</p>
                        </div>
                        
                        <div class="text-right">
                            @php
                                $originalPrice = $variation ? $variation->price : $product->price;
                                $finalPrice = $variation ? ($variation->sale_price ?: $variation->price) : ($product->sale_price ?: $product->price);
                                $hasSale = $finalPrice < $originalPrice;
                            @endphp
                            
                            @if($hasSale)
                                <p class="text-sm font-medium text-red-600">RM{{ number_format($finalPrice * $quantity, 2) }}</p>
                                <p class="text-xs text-gray-500 line-through">RM{{ number_format($originalPrice * $quantity, 2) }}</p>
                                <p class="text-xs text-green-600">Jimat RM{{ number_format(($originalPrice - $finalPrice) * $quantity, 2) }}</p>
                            @else
                                <p class="text-sm font-medium text-gray-900">RM{{ number_format($subtotal, 2) }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Item:</span>
                            <span class="font-medium">{{ $quantity }}</span>
                        </div>
                        @php
                            $originalPrice = $variation ? $variation->price : $product->price;
                            $finalPrice = $variation ? ($variation->sale_price ?: $variation->price) : ($product->sale_price ?: $product->price);
                            $hasSale = $finalPrice < $originalPrice;
                        @endphp
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jumlah Harga:</span>
                            <span class="font-medium {{ $hasSale ? 'text-red-600' : '' }}">RM{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($hasSale)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Harga Asal:</span>
                                <span class="font-medium text-gray-500 line-through">RM{{ number_format($originalPrice * $quantity, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Penjimatan:</span>
                                <span class="font-medium text-green-600">-RM{{ number_format(($originalPrice - $finalPrice) * $quantity, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Penghantaran:</span>
                            <span class="font-medium text-green-600">Percuma</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Cukai:</span>
                            <span class="font-medium">RM0.00</span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Jumlah Keseluruhan:</span>
                                <span class="text-red-600">RM{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 pt-4">
                        <button type="submit" form="checkout-form" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg font-bold transition-colors">
                            Selesaikan Pesanan
                        </button>
                    </div>
                    
                    <!-- Security Badge -->
                    <div class="mt-6 p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-green-700 font-medium">Pembayaran Selamat</span>
                        </div>
                        <p class="text-xs text-green-600 mt-1">Data anda dilindungi dengan enkripsi SSL</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Billing details data for auto-population
const billingDetailsData = @json($billingDetails->keyBy('id'));
// Shipping details data for auto-population
const shippingDetailsData = @json($shippingDetails->keyBy('id'));

document.addEventListener('DOMContentLoaded', function() {
    const sameAsShippingCheckbox = document.getElementById('same_as_shipping');
    const billingFields = document.getElementById('billing-fields');
    const billingDetailRadios = document.querySelectorAll('input[name="billing_detail_id"]');
    const saveBillingDetailCheckbox = document.getElementById('save_billing_detail');
    const billingDetailsSelection = document.getElementById('billing-details-selection');
    const toggleBillingDetailsBtn = document.getElementById('toggle-billing-details');
    const showNewAddressBtn = document.getElementById('show-new-address');
    
    // Shipping address selection elements
    const shippingFields = document.getElementById('shipping-fields');
    const shippingDetailRadios = document.querySelectorAll('input[name="shipping_detail_id"]');
    const saveShippingDetailCheckbox = document.getElementById('save_shipping_detail');
    const shippingDetailsSelection = document.getElementById('shipping-details-selection');
    const toggleShippingDetailsBtn = document.getElementById('toggle-shipping-details');
    const showNewShippingAddressBtn = document.getElementById('show-new-shipping-address');
    
    // Handle toggle billing details button
    if (toggleBillingDetailsBtn) {
        toggleBillingDetailsBtn.addEventListener('click', function() {
            if (billingDetailsSelection) {
                if (billingDetailsSelection.classList.contains('hidden')) {
                    // Show the selection
                    billingDetailsSelection.classList.remove('hidden');
                    this.textContent = 'Sembunyikan';
                    this.classList.add('text-gray-600');
                    this.classList.remove('text-red-600');
                } else {
                    // Hide the selection
                    billingDetailsSelection.classList.add('hidden');
                    this.textContent = 'Pilih Alamat Tersimpan';
                    this.classList.remove('text-gray-600');
                    this.classList.add('text-red-600');
                }
            }
        });
    }
    
    // Handle toggle shipping details button
    if (toggleShippingDetailsBtn) {
        toggleShippingDetailsBtn.addEventListener('click', function() {
            if (shippingDetailsSelection) {
                if (shippingDetailsSelection.classList.contains('hidden')) {
                    // Show the selection
                    shippingDetailsSelection.classList.remove('hidden');
                    this.textContent = 'Sembunyikan';
                    this.classList.add('text-gray-600');
                    this.classList.remove('text-red-600');
                } else {
                    // Hide the selection
                    shippingDetailsSelection.classList.add('hidden');
                    this.textContent = 'Pilih Alamat Tersimpan';
                    this.classList.remove('text-gray-600');
                    this.classList.add('text-red-600');
                }
            }
        });
    }
    
    // Handle show new address button
    if (showNewAddressBtn) {
        showNewAddressBtn.addEventListener('click', function() {
            if (billingDetailsSelection) {
                billingDetailsSelection.classList.add('hidden');
                if (toggleBillingDetailsBtn) {
                    toggleBillingDetailsBtn.textContent = 'Pilih Alamat Tersimpan';
                    toggleBillingDetailsBtn.classList.remove('text-gray-600');
                    toggleBillingDetailsBtn.classList.add('text-red-600');
                }
            }
            // Uncheck all billing detail radios
            billingDetailRadios.forEach(radio => radio.checked = false);
            // Show billing fields
            enableBillingFields();
        });
    }
    
    // Handle show new shipping address button
    if (showNewShippingAddressBtn) {
        showNewShippingAddressBtn.addEventListener('click', function() {
            if (shippingDetailsSelection) {
                shippingDetailsSelection.classList.add('hidden');
                if (toggleShippingDetailsBtn) {
                    toggleShippingDetailsBtn.textContent = 'Pilih Alamat Tersimpan';
                    toggleShippingDetailsBtn.classList.remove('text-gray-600');
                    toggleShippingDetailsBtn.classList.add('text-red-600');
                }
            }
            // Uncheck all shipping detail radios
            shippingDetailRadios.forEach(radio => radio.checked = false);
            // Show shipping fields
            enableShippingFields();
        });
    }
    
    // Handle billing detail selection
    billingDetailRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '') {
                // Show billing fields for new address
                enableBillingFields();
                clearBillingFields();
            } else {
                // Populate billing fields with selected address data
                populateBillingFields(this.value);
                disableBillingFields();
            }
        });
    });
    
    // Handle shipping detail selection
    shippingDetailRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '') {
                // Show shipping fields for new address
                enableShippingFields();
                clearShippingFields();
            } else {
                // Populate shipping fields with selected address data
                populateShippingFields(this.value);
                disableShippingFields();
            }
        });
    });
    
    // Handle same as shipping checkbox
    sameAsShippingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Copy shipping information to billing
            document.getElementById('billing_name').value = document.getElementById('shipping_name').value;
            document.getElementById('billing_email').value = document.getElementById('shipping_email').value;
            document.getElementById('billing_phone').value = document.getElementById('shipping_phone').value;
            document.getElementById('billing_address').value = document.getElementById('shipping_address').value;
            document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
            document.getElementById('billing_state').value = document.getElementById('shipping_state').value;
            document.getElementById('billing_postal_code').value = document.getElementById('shipping_postal_code').value;
            document.getElementById('billing_country').value = document.getElementById('shipping_country').value;
            
            // Uncheck all billing detail radios
            billingDetailRadios.forEach(radio => radio.checked = false);
            
            // Hide billing details selection
            if (billingDetailsSelection) {
                billingDetailsSelection.classList.add('hidden');
            }
            if (toggleBillingDetailsBtn) {
                toggleBillingDetailsBtn.textContent = 'Pilih Alamat Tersimpan';
                toggleBillingDetailsBtn.classList.remove('text-gray-600');
                toggleBillingDetailsBtn.classList.add('text-red-600');
            }
            
            // Make billing fields read-only instead of disabled (so they're still submitted)
            makeBillingFieldsReadOnly();
        } else {
            // Enable billing fields and clear them
            enableBillingFields();
            clearBillingFields();
        }
    });
    
    function enableBillingFields() {
        const billingInputs = billingFields.querySelectorAll('input:not([name="billing_detail_id"]), select, textarea');
        billingInputs.forEach(input => {
            input.disabled = false;
            input.readOnly = false;
            input.classList.remove('bg-gray-100');
        });
    }
    
    function disableBillingFields() {
        const billingInputs = billingFields.querySelectorAll('input:not([name="billing_detail_id"]), select, textarea');
        billingInputs.forEach(input => {
            input.disabled = true;
            input.classList.add('bg-gray-100');
        });
    }
    
    function makeBillingFieldsReadOnly() {
        const billingInputs = billingFields.querySelectorAll('input:not([name="billing_detail_id"]), select, textarea');
        billingInputs.forEach(input => {
            input.disabled = false; // Keep enabled so values are submitted
            input.readOnly = true; // Make read-only for user interaction
            input.classList.add('bg-gray-100');
        });
    }
    
    function populateBillingFields(billingDetailId) {
        const billingDetail = billingDetailsData[billingDetailId];
        if (billingDetail) {
            document.getElementById('billing_name').value = billingDetail.name;
            document.getElementById('billing_email').value = billingDetail.email;
            document.getElementById('billing_phone').value = billingDetail.phone;
            document.getElementById('billing_address').value = billingDetail.address;
            document.getElementById('billing_city').value = billingDetail.city;
            document.getElementById('billing_state').value = billingDetail.state;
            document.getElementById('billing_postal_code').value = billingDetail.postal_code;
            document.getElementById('billing_country').value = billingDetail.country;
        }
    }
    
    function clearBillingFields() {
        document.getElementById('billing_name').value = '';
        document.getElementById('billing_email').value = '';
        document.getElementById('billing_phone').value = '';
        document.getElementById('billing_address').value = '';
        document.getElementById('billing_city').value = '';
        document.getElementById('billing_state').value = '';
        document.getElementById('billing_postal_code').value = '';
        document.getElementById('billing_country').value = 'Malaysia';
    }
    
    function populateShippingFields(shippingDetailId) {
        const shippingDetail = shippingDetailsData[shippingDetailId];
        if (shippingDetail) {
            document.getElementById('shipping_name').value = shippingDetail.name;
            document.getElementById('shipping_email').value = shippingDetail.email;
            document.getElementById('shipping_phone').value = shippingDetail.phone;
            document.getElementById('shipping_address').value = shippingDetail.address;
            document.getElementById('shipping_city').value = shippingDetail.city;
            document.getElementById('shipping_state').value = shippingDetail.state;
            document.getElementById('shipping_postal_code').value = shippingDetail.postal_code;
            document.getElementById('shipping_country').value = shippingDetail.country;
        }
    }
    
    function clearShippingFields() {
        document.getElementById('shipping_name').value = '';
        document.getElementById('shipping_email').value = '';
        document.getElementById('shipping_phone').value = '';
        document.getElementById('shipping_address').value = '';
        document.getElementById('shipping_city').value = '';
        document.getElementById('shipping_state').value = '';
        document.getElementById('shipping_postal_code').value = '';
        document.getElementById('shipping_country').value = 'Malaysia';
    }
    
    function enableShippingFields() {
        const shippingInputs = shippingFields.querySelectorAll('input:not([name="shipping_detail_id"]), select, textarea');
        shippingInputs.forEach(input => {
            input.disabled = false;
            input.classList.remove('bg-gray-100');
        });
    }
    
    function disableShippingFields() {
        const shippingInputs = shippingFields.querySelectorAll('input:not([name="shipping_detail_id"]), select, textarea');
        shippingInputs.forEach(input => {
            input.disabled = true;
            input.classList.add('bg-gray-100');
        });
    }
    
    // Handle save billing detail checkbox visibility
    if (saveBillingDetailCheckbox) {
        billingDetailRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === '') {
                    saveBillingDetailCheckbox.parentElement.classList.remove('hidden');
                } else {
                    saveBillingDetailCheckbox.parentElement.classList.add('hidden');
                }
            });
        });
        
        // Initial state
        const selectedRadio = document.querySelector('input[name="billing_detail_id"]:checked');
        if (selectedRadio && selectedRadio.value !== '') {
            saveBillingDetailCheckbox.parentElement.classList.add('hidden');
            // Populate fields with default selected billing detail
            populateBillingFields(selectedRadio.value);
            disableBillingFields();
        }
    }
    
    // Handle save shipping detail checkbox visibility
    if (saveShippingDetailCheckbox) {
        shippingDetailRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === '') {
                    saveShippingDetailCheckbox.parentElement.classList.remove('hidden');
                } else {
                    saveShippingDetailCheckbox.parentElement.classList.add('hidden');
                }
            });
        });
        
        // Initial state
        const selectedShippingRadio = document.querySelector('input[name="shipping_detail_id"]:checked');
        if (selectedShippingRadio && selectedShippingRadio.value !== '') {
            saveShippingDetailCheckbox.parentElement.classList.add('hidden');
            // Populate fields with default selected shipping detail
            populateShippingFields(selectedShippingRadio.value);
            disableShippingFields();
        }
    }
});
</script>
@endpush 