@extends('layouts.app')

@section('title', 'Mohon Refund - MyGooners')
@section('meta_description', 'Mohon refund untuk pesanan anda di MyGooners.')

@section('content')
<!-- Breadcrumb -->
<div class="bg-gray-50 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm text-gray-600">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">Utama</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('checkout.orders') }}" class="hover:text-red-600 transition-colors">Pesanan Saya</a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900 font-medium">Mohon Refund</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mohon Refund</h1>
                <p class="text-gray-600 mt-1">Pesanan #{{ $order->order_number }}</p>
            </div>
            
            <a href="{{ route('checkout.orders') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Kembali ke Pesanan
            </a>
        </div>
    </div>
</div>

<!-- Order Summary -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nombor Pesanan</p>
                    <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jumlah Bayaran</p>
                    <p class="font-medium text-gray-900">{{ $order->getFormattedTotal() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tarikh Diterima</p>
                    <p class="font-medium text-gray-900">{{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Hari Berbaki</p>
                    <p class="font-medium text-gray-900">
                        @if($order->getFormattedRefundCountdown() !== null)
                            {{ $order->getFormattedRefundCountdown() }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Form -->
<div class="bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('checkout.refunds.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Refund Type Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start space-x-3">
                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Jenis Refund</h4>
                        <p class="mt-1 text-sm text-blue-800">
                            Semua permohonan refund adalah jenis <strong>Return & Refund</strong>. 
                            Anda perlu menghantar item balik dan akan mendapat wang balik selepas admin menyemak item tersebut. 
                            Untuk mendapatkan item baru, sila buat pesanan baharu.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Refund Reason -->
            <div>
                <label for="refund_reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Sebab Refund <span class="text-red-500">*</span>
                </label>
                <textarea id="refund_reason" name="refund_reason" rows="4" required
                          placeholder="Terangkan sebab anda memohon refund (minimum 10 aksara)"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('refund_reason') }}</textarea>
                @error('refund_reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start space-x-3">
                    <svg class="h-6 w-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Maklumat Tambahan</h4>
                        <p class="mt-1 text-sm text-blue-800">
                            Selepas admin meluluskan permohonan refund anda, anda akan diminta untuk memberikan maklumat bank dan maklumat penghantaran balik item.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Proof Images -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Gambar Bukti <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-4">
                    Sila muat naik tepat 3 gambar sebagai bukti. Format yang diterima: JPEG, PNG, JPG. Saiz maksimum: 2MB setiap gambar.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @for($i = 1; $i <= 3; $i++)
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-red-400 transition-colors">
                            <input type="file" id="image_{{ $i }}" name="images[]" accept="image/*" required
                                   class="hidden" onchange="previewImage(this, {{ $i }})">
                            
                            <!-- Preview area - separate from label -->
                            <div id="preview_{{ $i }}" class="space-y-2">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="text-sm text-gray-600">Gambar {{ $i }}</p>
                                <p class="text-xs text-gray-500">Klik untuk pilih</p>
                            </div>
                            
                            <!-- Label for file input -->
                            <label for="image_{{ $i }}" class="cursor-pointer block mt-2">
                                <span class="text-xs text-blue-600 hover:text-blue-800 underline">Klik untuk pilih fail</span>
                            </label>
                        </div>
                    @endfor
                </div>
                @error('images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Important Notice -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start space-x-3">
                    <svg class="h-6 w-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-red-900">Perhatian Penting</h4>
                                                 <ul class="mt-2 text-sm text-red-800 space-y-1">
                             <li>• Refund hanya boleh dimohon dalam tempoh 3 hari selepas pesanan diterima</li>
                             <li>• Admin akan menyemak permohonan anda dalam masa 24-48 jam</li>
                             <li>• Selepas diluluskan, anda perlu memberikan maklumat bank dan maklumat penghantaran balik</li>
                             <li>• Item mesti dihantar balik ke alamat MyGooners dalam keadaan baik untuk proses refund</li>
                             <li>• Untuk mendapatkan item baru, sila buat pesanan baharu</li>
                             <li>• Semua maklumat yang diberikan mesti tepat dan lengkap</li>
                         </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('checkout.orders') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                    Hantar Permohonan Refund
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, index) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = document.getElementById(`preview_${index}`);
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview" class="mx-auto h-24 w-24 object-cover rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600 truncate max-w-full">${input.files[0].name}</p>
                <p class="text-xs text-blue-500 cursor-pointer hover:text-blue-700" onclick="document.getElementById('image_${index}').click()">Klik untuk tukar</p>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection 