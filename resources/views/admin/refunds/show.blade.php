@extends('layouts.admin')

@section('title', 'Detail Refund - Admin Panel')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Refund</h1>
                <p class="text-gray-600 mt-1">Refund #{{ $refund->id }} - {{ $refund->order->order_number }}</p>
            </div>
            
                         <div class="flex items-center space-x-3">
                                  @if($refund->status === 'approved' && (!$refund->bank_name || !$refund->bank_account_number || !$refund->bank_account_holder || !$refund->tracking_number || !$refund->shipping_courier))
                     <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                         <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                         </svg>
                         Maklumat Belum Lengkap
                     </span>
                 @elseif($refund->status === 'processing')
                     <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                         <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                         </svg>
                         Sedang Diproses
                     </span>
                                   @endif
                  
                  <!-- Return Shipping Status -->
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium {{ $refund->tracking_number && $refund->shipping_courier ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200' }}">
                      @if($refund->tracking_number && $refund->shipping_courier)
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                          Sudah Dihantar
                      @else
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                          Belum Dihantar
                      @endif
                  </span>
                  
                  <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $refund->getStatusBadgeClass() }}">
                      {{ $refund->getStatusDisplayName() }}
                  </span>
                 <a href="{{ route('admin.refunds.index') }}" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                     Kembali ke Senarai
                 </a>
             </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Maklumat Pesanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nombor Pesanan</p>
                        <p class="font-medium text-gray-900">{{ $refund->order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pesanan</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $refund->order->getStatusBadgeClass() }}">
                            {{ $refund->order->getOrderStatusDisplayName() }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tarikh Pesanan</p>
                        <p class="font-medium text-gray-900">{{ $refund->order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tarikh Diterima</p>
                        <p class="font-medium text-gray-900">{{ $refund->order->delivered_at ? $refund->order->delivered_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jumlah Bayaran</p>
                        <p class="font-medium text-gray-900">{{ $refund->order->getFormattedTotal() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kaedah Pembayaran</p>
                        <p class="font-medium text-gray-900">{{ $refund->order->getPaymentMethodDisplayName() }}</p>
                    </div>
                </div>
            </div>

            <!-- Refund Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Maklumat Refund</h3>
                <div class="space-y-4">
                                    <div>
                    <p class="text-sm text-gray-600">Jenis Refund</p>
                    <p class="font-medium text-gray-900">Return & Refund</p>
                </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Sebab Refund</p>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $refund->refund_reason }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Jumlah Refund</p>
                        <p class="font-medium text-gray-900 text-lg">{{ $refund->getFormattedRefundAmount() }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Tarikh Permohonan</p>
                        <p class="font-medium text-gray-900">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Bank Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Maklumat Bank untuk Refund</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-blue-700">Nama Bank</p>
                        <p class="font-medium text-blue-900">{{ $refund->bank_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-blue-700">Nombor Akaun</p>
                        <p class="font-medium text-blue-900">{{ $refund->bank_account_number }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-blue-700">Nama Pemegang Akaun</p>
                        <p class="font-medium text-blue-900">{{ $refund->bank_account_holder }}</p>
                    </div>
                </div>
            </div>

                            <!-- Return Shipping Information -->
                @if($refund->tracking_number || $refund->shipping_courier)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-4">Maklumat Penghantaran Balik</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                     @if($refund->tracking_number)
                                 <div>
                                     <p class="text-sm text-yellow-700">Nombor Tracking</p>
                                     @if($refund->getTrackingUrl())
                                         <a href="{{ $refund->getTrackingUrl() }}" 
                                            target="_blank"
                                            class="font-medium text-blue-600 hover:text-blue-800 underline">
                                             {{ $refund->tracking_number }}
                                         </a>
                                         <p class="text-xs text-gray-500 mt-1">Klik nombor di atas untuk menjejak penghantaran di tracking.my</p>
                                     @else
                                         <p class="font-medium text-yellow-900">{{ $refund->tracking_number }}</p>
                                     @endif
                                 </div>
                             @endif
                        @if($refund->shipping_courier)
                            <div>
                                <p class="text-sm text-yellow-700">Syarikat Penghantaran</p>
                                <p class="font-medium text-yellow-900">{{ $refund->shipping_courier }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

                         <!-- Proof Images -->
             <div class="bg-white rounded-lg shadow p-6">
                 <h3 class="text-lg font-semibold text-gray-900 mb-4">Gambar Bukti</h3>
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     @foreach($refund->images as $image)
                         <div class="text-center">
                             <img src="{{ $image->image_url }}" 
                                  alt="Bukti {{ $loop->iteration }}" 
                                  class="w-full h-32 object-cover rounded-lg border border-gray-200">
                             <p class="text-sm text-gray-600 mt-2">{{ $image->image_name }}</p>
                         </div>
                     @endforeach
                 </div>
             </div>
             
             <!-- Refund Receipt Image -->
             @if($refund->receipt_image)
                 <div class="bg-white rounded-lg shadow p-6">
                     <h3 class="text-lg font-semibold text-gray-900 mb-4">Resit Transaksi Refund</h3>
                     <div class="text-center">
                         <img src="{{ $refund->receipt_image_url }}" 
                              alt="Resit Transaksi Refund" 
                              class="max-w-full h-auto max-h-96 object-contain rounded-lg border border-gray-200 mx-auto">
                         <p class="text-sm text-gray-600 mt-2">Resit transaksi refund telah diproses</p>
                     </div>
                 </div>
             @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Update Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kemas Kini Status</h3>
                                 <form action="{{ route('admin.refunds.update-status', $refund->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Baru <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="pending" {{ $refund->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ $refund->status === 'approved' ? 'selected' : '' }}>Diluluskan</option>
                            <option value="rejected" {{ $refund->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="processing" {{ $refund->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ $refund->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    
                                         <div>
                         <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">
                             Nota Admin
                         </label>
                         <textarea id="admin_notes" name="admin_notes" rows="3"
                                   placeholder="Nota untuk pengguna (opsional)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ $refund->admin_notes }}</textarea>
                     </div>
                     
                     <div id="receipt_image_field" class="hidden">
                         <label for="receipt_image" class="block text-sm font-medium text-gray-700 mb-2">
                             Resit Transaksi Refund <span class="text-red-500">*</span>
                         </label>
                         <input type="file" id="receipt_image" name="receipt_image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                         <p class="mt-1 text-sm text-gray-500">Format: JPEG, PNG, JPG. Maksimum: 2MB</p>
                         @error('receipt_image')
                             <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                         @enderror
                     </div>
                    
                    <div id="rejection_reason_field" class="hidden">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Sebab Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3"
                                  placeholder="Terangkan sebab penolakan"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ $refund->rejection_reason }}</textarea>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Kemas Kini Status
                    </button>
                </form>
            </div>

            <!-- User Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Maklumat Pengguna</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-medium text-gray-900">{{ $refund->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium text-gray-900">{{ $refund->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Telefon</p>
                        <p class="font-medium text-gray-900">{{ $refund->user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tarikh Daftar</p>
                        <p class="font-medium text-gray-900">{{ $refund->user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tindakan Pantas</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.orders.show', $refund->order->id) }}" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">
                        Lihat Pesanan
                    </a>
                    <a href="{{ route('admin.users.show', $refund->user->id) }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center block">
                        Lihat Pengguna
                    </a>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sejarah Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Permohonan Dihantar</p>
                            <p class="text-xs text-gray-500">{{ $refund->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($refund->status !== 'pending')
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    @switch($refund->status)
                                        @case('approved')
                                            Diluluskan
                                            @break
                                        @case('rejected')
                                            Ditolak
                                            @break
                                        @case('processing')
                                            Sedang Diproses
                                            @break
                                        @case('completed')
                                            Selesai
                                            @break
                                        @default
                                            Status Diubah
                                    @endswitch
                                </p>
                                <p class="text-xs text-gray-500">{{ $refund->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const rejectionReasonField = document.getElementById('rejection_reason_field');
    const rejectionReasonInput = document.getElementById('rejection_reason');
    const receiptImageField = document.getElementById('receipt_image_field');
    const receiptImageInput = document.getElementById('receipt_image');
    
    function toggleFields() {
        // Toggle rejection reason field
        if (statusSelect.value === 'rejected') {
            rejectionReasonField.classList.remove('hidden');
            rejectionReasonInput.required = true;
        } else {
            rejectionReasonField.classList.add('hidden');
            rejectionReasonInput.required = false;
        }
        
        // Toggle receipt image field
        if (statusSelect.value === 'completed') {
            receiptImageField.classList.remove('hidden');
            receiptImageInput.required = true;
        } else {
            receiptImageField.classList.add('hidden');
            receiptImageInput.required = false;
        }
    }
    
    statusSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initial state
});
</script>
@endsection 