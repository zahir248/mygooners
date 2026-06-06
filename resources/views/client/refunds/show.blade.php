@extends('layouts.app')

@section('title', __('Detail Refund - MyGooners'))
@section('meta_description', __('Lihat detail permohonan refund anda di MyGooners.'))

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">{{ __('Utama') }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('checkout.orders') }}" class="hover:text-red-600 transition-colors">{{ __('Pesanan Saya') }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('checkout.refunds') }}" class="hover:text-red-600 transition-colors">{{ __('Permohonan Refund') }}</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ __('Detail Refund') }}</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Detail Refund') }}</h1>
                <p class="text-gray-600 mt-1">{{ trans('account_page.refund_number', ['id' => $refund->id, 'order_number' => $refund->order->order_number]) }}</p>
            </div>
            
                                                  <div class="flex items-center space-x-3">
                  @if($refund->status === 'approved' && (!$refund->bank_name || !$refund->bank_account_number || !$refund->bank_account_holder || !$refund->tracking_number || !$refund->shipping_courier))
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                           <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                           </svg>
                           {{ __('Maklumat Belum Lengkap') }}
                       </span>
                  @elseif($refund->status === 'processing')
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                           <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                           </svg>
                           {{ __('Sedang Diproses') }}
                       </span>
                                     @endif
                   
                   <!-- Return Shipping Status -->
                   <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $refund->tracking_number && $refund->shipping_courier ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                       @if($refund->tracking_number && $refund->shipping_courier)
                           <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                           </svg>
                           {{ __('Sudah Dihantar') }}
                       @else
                           <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                           </svg>
                           {{ __('Belum Dihantar') }}
                       @endif
                   </span>
                   
                  <a href="{{ route('checkout.refunds') }}" 
                     class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">{{ __('Kembali ke Refund') }}</a>
             </div>
        </div>
    </div>
</div>



<!-- Refund Details -->
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Maklumat Pesanan') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Nombor Pesanan') }}</p>
                            <p class="font-medium text-gray-900">{{ $refund->order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tarikh Pesanan') }}</p>
                            <p class="font-medium text-gray-900">{{ $refund->order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tarikh Diterima') }}</p>
                            <p class="font-medium text-gray-900">{{ $refund->order->delivered_at ? $refund->order->delivered_at->format('d/m/Y H:i') : __('N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Jumlah Bayaran') }}</p>
                            <p class="font-medium text-gray-900">{{ $refund->order->getFormattedTotal() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Refund Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Maklumat Refund') }}</h3>
                    <div class="space-y-4">
                                        <div>
                    <p class="text-sm text-gray-600">{{ __('Jenis Refund') }}</p>
                    <p class="font-medium text-gray-900">{{ __('Return & Refund') }}</p>
                </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Sebab Refund') }}</p>
                            <p class="text-gray-900">{{ $refund->refund_reason }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Jumlah Refund') }}</p>
                            <p class="font-medium text-gray-900 text-lg">{{ $refund->getFormattedRefundAmount() }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Tarikh Permohonan') }}</p>
                            <p class="font-medium text-gray-900">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('Maklumat Bank untuk Refund') }}</h3>
                    @if($refund->bank_name && $refund->bank_account_number && $refund->bank_account_holder)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-blue-700">{{ __('Nama Bank') }}</p>
                                <p class="font-medium text-blue-900">{{ $refund->bank_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-700">{{ __('Nombor Akaun') }}</p>
                                <p class="font-medium text-blue-900">{{ $refund->bank_account_number }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-blue-700">{{ __('Nama Pemegang Akaun') }}</p>
                                <p class="font-medium text-blue-900">{{ $refund->bank_account_holder }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-blue-800">{{ __('Maklumat bank belum diisi.') }}</p>
                    @endif
                </div>

                                 <!-- Update Refund Details Form (Only for approved refunds that need completion) -->
                 @if($refund->status === 'approved' && (!$refund->bank_name || !$refund->bank_account_number || !$refund->bank_account_holder || !$refund->tracking_number || !$refund->shipping_courier))
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Kemas Kini Maklumat Refund') }}</h3>
                        
                        <!-- Return Address Information -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-yellow-900 mb-2">{{ __('Alamat Penghantaran Balik:') }}</h4>
                            <div class="text-sm text-yellow-800">
                                <p><span class="font-medium">{{ __('Nama Penerima:') }}</span> MyGooners</p>
                                <p><span class="font-medium">{{ __('Alamat:') }}</span> B-10-02, Second Floor, Garden Shoppe One City</p>
                                <p><span class="font-medium">{{ __('Jalan:') }}</span> Jalan USJ 25/1A, One City</p>
                                <p><span class="font-medium">{{ __('Poskod & Negeri:') }}</span> 47650 Subang Jaya, Selangor</p>
                            </div>
                        </div>

                        <!-- Important Reminder -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start space-x-3">
                                <svg class="h-5 w-5 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-orange-900 mb-2">{{ __('Perhatian Penting!') }}</h4>
                                    <div class="text-sm text-orange-800 space-y-2">
                                        <p>{{ __('•') }}<strong>{{ __('Maklumat Bank:') }}</strong> {{ __('Sila berikan maklumat bank anda untuk pemprosesan refund') }}</p>
                                        <p>{{ __('•') }}<strong>{{ __('Maklumat Tracking:') }}</strong> {{ __('Sila berikan nombor tracking dan syarikat penghantaran') }}</p>
                                        <p>{{ __('•') }}<strong>{{ __('Masa Pengisian:') }}</strong> {{ __('Maklumat ini boleh diisi') }} <strong>{{ __('SELEPAS') }}</strong> {{ __('anda menghantar item balik') }}</p>
                                        <p>{{ __('•') }}<strong>{{ __('Proses Refund:') }}</strong> {{ __('Refund akan diproses') }} <strong>{{ __('SELEPAS') }}</strong> {{ __('item tiba dengan selamat di pihak kami') }}</p>
                                        <p>{{ __('•') }}<strong>{{ __('Masa Pemprosesan:') }}</strong> {{ __('Refund akan diproses dalam masa 3-5 hari bekerja selepas item diterima') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('checkout.refunds.update', $refund->id) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PATCH')
                            
                            <!-- Bank Details -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-blue-900 mb-4">{{ __('Maklumat Bank untuk Refund') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="bank_name" class="block text-sm font-medium text-blue-700 mb-2">
                                            Nama Bank <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="bank_name" name="bank_name" required
                                               value="{{ old('bank_name', $refund->bank_name) }}"
                                               placeholder="{{ __('Contoh: Maybank, CIMB, Public Bank') }}"
                                               class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('bank_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="bank_account_number" class="block text-sm font-medium text-blue-700 mb-2">
                                            Nombor Akaun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="bank_account_number" name="bank_account_number" required
                                               value="{{ old('bank_account_number', $refund->bank_account_number) }}"
                                               placeholder="{{ __('Contoh: 1234-5678-9012') }}"
                                               class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('bank_account_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="bank_account_holder" class="block text-sm font-medium text-blue-700 mb-2">
                                            Nama Pemegang Akaun <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="bank_account_holder" name="bank_account_holder" required
                                               value="{{ old('bank_account_holder', $refund->bank_account_holder) }}"
                                               placeholder="{{ __('Nama penuh seperti dalam kad bank') }}"
                                               class="w-full px-3 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        @error('bank_account_holder')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Return Shipping Details -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-yellow-900 mb-4">{{ __('Maklumat Penghantaran Balik') }}</h4>
                                <p class="text-sm text-yellow-800 mb-4">{{ __('Sila berikan maklumat penghantaran balik item untuk proses refund.') }}</p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                                                                 <label for="tracking_number" class="block text-sm font-medium text-yellow-700 mb-2">
                                             Nombor Tracking <span class="text-red-500">*</span>
                                         </label>
                                        <input type="text" id="tracking_number" name="tracking_number"
                                               value="{{ old('tracking_number', $refund->tracking_number) }}"
                                               placeholder="{{ __('Nombor tracking penghantaran balik') }}"
                                               class="w-full px-3 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        @error('tracking_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                                                                 <label for="shipping_courier" class="block text-sm font-medium text-yellow-700 mb-2">
                                             Syarikat Penghantaran <span class="text-red-500">*</span>
                                         </label>
                                        <select id="shipping_courier" name="shipping_courier"
                                                class="w-full px-3 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                            <option value="">{{ __('Pilih Syarikat Penghantaran') }}</option>
                                            <option value="Pos Malaysia" {{ old('shipping_courier', $refund->shipping_courier) == 'Pos Malaysia' ? 'selected' : '' }}>Pos Malaysia</option>
                                            <option value="J&T" {{ old('shipping_courier', $refund->shipping_courier) == 'J&T' ? 'selected' : '' }}>J&T</option>
                                            <option value="DHL" {{ old('shipping_courier', $refund->shipping_courier) == 'DHL' ? 'selected' : '' }}>DHL</option>
                                            <option value="FedEx" {{ old('shipping_courier', $refund->shipping_courier) == 'FedEx' ? 'selected' : '' }}>FedEx</option>
                                            <option value="TNT" {{ old('shipping_courier', $refund->shipping_courier) == 'TNT' ? 'selected' : '' }}>TNT</option>
                                            <option value="Shopee Express" {{ old('shipping_courier', $refund->shipping_courier) == 'Shopee Express' ? 'selected' : '' }}>Shopee Express</option>
                                            <option value="Lazada Express" {{ old('shipping_courier', $refund->shipping_courier) == 'Lazada Express' ? 'selected' : '' }}>Lazada Express</option>
                                            <option value="Ninja Van" {{ old('shipping_courier', $refund->shipping_courier) == 'Ninja Van' ? 'selected' : '' }}>Ninja Van</option>
                                            <option value="GrabExpress" {{ old('shipping_courier', $refund->shipping_courier) == 'GrabExpress' ? 'selected' : '' }}>GrabExpress</option>
                                            <option value="Gojek" {{ old('shipping_courier', $refund->shipping_courier) == 'Gojek' ? 'selected' : '' }}>Gojek</option>
                                            <option value="Shopee Food" {{ old('shipping_courier', $refund->shipping_courier) == 'Shopee Food' ? 'selected' : '' }}>Shopee Food</option>
                                            <option value="Lalamove" {{ old('shipping_courier', $refund->shipping_courier) == 'Lalamove' ? 'selected' : '' }}>Lalamove</option>
                                            <option value="Uber Eats" {{ old('shipping_courier', $refund->shipping_courier) == 'Uber Eats' ? 'selected' : '' }}>Uber Eats</option>
                                            <option value="Foodpanda" {{ old('shipping_courier', $refund->shipping_courier) == 'Foodpanda' ? 'selected' : '' }}>Foodpanda</option>
                                            <option value="Deliveroo" {{ old('shipping_courier', $refund->shipping_courier) == 'Deliveroo' ? 'selected' : '' }}>Deliveroo</option>
                                            <option value="GrabFood" {{ old('shipping_courier', $refund->shipping_courier) == 'GrabFood' ? 'selected' : '' }}>GrabFood</option>
                                            <option value="Rider" {{ old('shipping_courier', $refund->shipping_courier) == 'Rider' ? 'selected' : '' }}>Rider</option>
                                            <option value="Other" {{ old('shipping_courier', $refund->shipping_courier) == 'Other' ? 'selected' : '' }}>Lain-lain</option>
                                        </select>
                                        @error('shipping_courier')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">{{ __('Kemas Kini Maklumat') }}</button>
                            </div>
                        </form>
                                         </div>
                 @endif

                 <!-- Processing Status Information -->
                 @if($refund->status === 'processing')
                     <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                         <div class="flex items-start space-x-3">
                             <svg class="h-6 w-6 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                             </svg>
                             <div>
                                 <h4 class="text-lg font-medium text-purple-900 mb-2">{{ __('Refund Sedang Diproses') }}</h4>
                                 <div class="text-purple-800 space-y-2">
                                     <p>{{ __('✅') }}<strong>{{ __('Maklumat Lengkap:') }}</strong> {{ __('Semua maklumat bank dan tracking telah diterima') }}</p>
                                     <p>{{ __('⏳') }}<strong>{{ __('Status Semasa:') }}</strong> {{ __('Refund sedang diproses oleh pihak kami') }}</p>
                                     <p>{{ __('⏰') }}<strong>{{ __('Masa Pemprosesan:') }}</strong> {{ __('Refund akan diproses dalam masa 3-5 hari bekerja') }}</p>
                                     <p>{{ __('📧') }}<strong>{{ __('Notifikasi:') }}</strong> {{ __('Anda akan dimaklumkan melalui email apabila refund selesai diproses') }}</p>
                                 </div>
                             </div>
                         </div>
                     </div>
                 @endif
 
                                  <!-- Return Shipping Information -->
                 @if($refund->tracking_number || $refund->shipping_courier)
                     <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                         <h3 class="text-lg font-semibold text-yellow-900 mb-4">{{ __('Maklumat Penghantaran Balik') }}</h3>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             @if($refund->tracking_number)
                                 <div>
                                     <p class="text-sm text-yellow-700">{{ __('Nombor Tracking') }}</p>
                                     @if($refund->getTrackingUrl())
                                         <a href="{{ $refund->getTrackingUrl() }}" 
                                            target="_blank" rel="noopener noreferrer"
                                            class="font-medium text-blue-600 hover:text-blue-800 underline">
                                             {{ $refund->tracking_number }}
                                         </a>
                                         <p class="text-xs text-gray-500 mt-1">{{ __('Klik nombor di atas untuk menjejak penghantaran di tracking.my') }}</p>
                                     @else
                                         <p class="font-medium text-yellow-900">{{ $refund->tracking_number }}</p>
                                     @endif
                                 </div>
                             @endif
                             @if($refund->shipping_courier)
                                 <div>
                                     <p class="text-sm text-yellow-700">{{ __('Syarikat Penghantaran') }}</p>
                                     <p class="font-medium text-yellow-900">{{ $refund->shipping_courier }}</p>
                                 </div>
                             @endif
                         </div>
                     </div>
                 @endif

                <!-- Admin Notes -->
                @if($refund->admin_notes)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('Nota Admin') }}</h3>
                        <p class="text-green-800">{{ $refund->admin_notes }}</p>
                    </div>
                @endif

                <!-- Rejection Reason -->
                @if($refund->status === 'rejected' && $refund->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-red-900 mb-4">{{ __('Sebab Penolakan') }}</h3>
                        <p class="text-red-800">{{ $refund->rejection_reason }}</p>
                    </div>
                @endif

                                 <!-- Proof Images -->
                 <div class="bg-white border border-gray-200 rounded-lg p-6">
                     <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Gambar Bukti') }}</h3>
                     <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                         @foreach($refund->images as $image)
                             <div class="text-center">
                                 <a href="{{ $image->image_url }}" target="_blank" class="block group">
                                     <img src="{{ $image->image_url }}" 
                                          alt="Bukti {{ $loop->iteration }}" 
                                          class="w-full h-32 object-cover rounded-lg border border-gray-200 group-hover:opacity-80 transition-opacity">
                                     <p class="text-sm text-gray-600 mt-2 group-hover:text-blue-600 transition-colors">{{ $image->image_name }}</p>
                                 </a>
                             </div>
                         @endforeach
                     </div>
                 </div>
                 
                 <!-- Refund Receipt Image -->
                 @if($refund->receipt_image)
                     <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                         <h3 class="text-lg font-semibold text-green-900 mb-4">{{ __('✅ Resit Transaksi Refund') }}</h3>
                         <div class="text-center">
                             <a href="{{ $refund->receipt_image_url }}" target="_blank" class="block group">
                                 <img src="{{ $refund->receipt_image_url }}" 
                                      alt="{{ __('Resit Transaksi Refund') }}" 
                                      class="max-w-full h-auto max-h-96 object-contain rounded-lg border border-green-200 mx-auto group-hover:opacity-80 transition-opacity">
                                 <p class="text-sm text-green-700 mt-2 group-hover:text-blue-600 transition-colors">{{ __('Refund anda telah berjaya diproses! Resit transaksi telah disediakan oleh admin.') }}</p>
                             </a>
                         </div>
                     </div>
                 @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Timeline -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Status Refund') }}</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('Permohonan Dihantar') }}</p>
                                <p class="text-xs text-gray-500">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($refund->status === 'pending')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Menunggu Semakan') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Sedang disemak oleh admin') }}</p>
                                </div>
                            </div>
                        @elseif($refund->status === 'approved')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Diluluskan') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Refund diluluskan oleh admin') }}</p>
                                </div>
                            </div>
                        @elseif($refund->status === 'processing')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Sedang Diproses') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Refund sedang diproses') }}</p>
                                </div>
                            </div>
                        @elseif($refund->status === 'completed')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Selesai') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Refund telah selesai') }}</p>
                                </div>
                            </div>
                        @elseif($refund->status === 'rejected')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('Ditolak') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('Permohonan refund ditolak') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Tindakan Pantas') }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('checkout.show', $refund->order->id) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">
                            {{ __('Lihat Pesanan') }}
                        </a>
                        <a href="{{ route('checkout.refunds') }}" 
                           class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">{{ __('Semua Refund') }}</a>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">{{ __('Perlukan Bantuan?') }}</h3>
                    <p class="text-sm text-blue-800 mb-3">{{ __('Jika anda mempunyai sebarang soalan mengenai refund anda, sila hubungi kami:') }}</p>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p>{{ __('📧 Email: support@mygooners.com') }}</p>
                        <p>{{ __('📞 Telefon: +60 12-345 6789') }}</p>
                        <p>{{ __('⏰ Masa: Isnin - Jumaat, 9:00 AM - 6:00 PM') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 