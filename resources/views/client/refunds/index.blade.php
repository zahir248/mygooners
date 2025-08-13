@extends('layouts.app')

@section('title', 'Permohonan Refund Saya - MyGooners')
@section('meta_description', 'Lihat semua permohonan refund anda di MyGooners.')

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
            <span class="text-gray-900 font-medium">Permohonan Refund</span>
        </nav>
    </div>
</div>

<!-- Header -->
<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Permohonan Refund Saya</h1>
                <p class="text-gray-600 mt-1">Lihat dan uruskan semua permohonan refund anda</p>
            </div>
            
            <a href="{{ route('checkout.orders') }}" 
               class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Lihat Pesanan
            </a>
        </div>
    </div>
</div>

<!-- Information Banner -->
<div id="refund-info-banner" class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-3 flex-1">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-blue-900 mb-1">Maklumat Penting Mengenai Refund</h3>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p>• <strong>Tempoh Refund:</strong> Refund hanya boleh dimohon dalam tempoh 3 hari selepas pesanan diterima</p>
                        <p>• <strong>Jenis Refund:</strong> Return & Refund</p>
                        <p>• <strong>Bukti:</strong> Setiap permohonan memerlukan 3 gambar sebagai bukti</p>
                        <p>• <strong>Maklumat Bank:</strong> Pastikan maklumat bank anda tepat untuk pemprosesan refund</p>
                        <p>• <strong>Masa Semakan:</strong> Admin akan menyemak permohonan dalam masa 24-48 jam</p>
                    </div>
                </div>
            </div>
            <button onclick="closeRefundInfoBanner()" class="flex-shrink-0 ml-4 text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Refunds List -->
<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($refunds->count() > 0)
            <div class="space-y-6">
                @foreach($refunds as $refund)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Refund #{{ $refund->id }} - {{ $refund->order->order_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $refund->created_at->format('d/m/Y H:i') }}
                                    </p>
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
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $refund->getStatusBadgeClass() }}">
                                        {{ $refund->getStatusDisplayName() }}
                                    </span>
                                    
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
                                </div>
                            </div>

                            <!-- Refund Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">Jenis Refund</p>
                                    <p class="font-medium text-gray-900">Return & Refund</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Jumlah Refund</p>
                                    <p class="font-medium text-gray-900">{{ $refund->getFormattedRefundAmount() }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Bank</p>
                                    <p class="font-medium text-gray-900">{{ $refund->bank_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Akaun</p>
                                    <p class="font-medium text-gray-900">{{ $refund->bank_account_number }}</p>
                                </div>
                            </div>

                            <!-- Refund Reason -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Sebab Refund:</p>
                                <p class="text-gray-900">{{ Str::limit($refund->refund_reason, 150) }}</p>
                            </div>

                            <!-- Status-specific Information -->
                            @if($refund->status === 'rejected' && $refund->rejection_reason)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-start space-x-3">
                                        <svg class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-red-900">Sebab Penolakan</h4>
                                            <p class="text-sm text-red-800 mt-1">{{ $refund->rejection_reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($refund->status === 'approved' && $refund->admin_notes)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-start space-x-3">
                                        <svg class="h-5 w-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-green-900">Nota Admin</h4>
                                            <p class="text-sm text-green-800 mt-1">{{ $refund->admin_notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="flex items-center space-x-4">
                                    @if($refund->status === 'pending')
                                        <span class="text-sm text-yellow-600">
                                            <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Menunggu semakan admin
                                        </span>
                                    @elseif($refund->status === 'approved')
                                        <div class="flex flex-col space-y-2">
                                            <span class="text-sm text-blue-600">
                                                <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Refund diluluskan
                                            </span>
                                            <span class="text-xs text-gray-600">
                                                Klik "Lihat Detail" untuk berikan maklumat bank dan tracking
                                            </span>
                                        </div>
                                    @elseif($refund->status === 'processing')
                                        <span class="text-sm text-purple-600">
                                            <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Sedang diproses
                                        </span>
                                    @elseif($refund->status === 'completed')
                                        <span class="text-sm text-green-600">
                                            <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Refund selesai
                                        </span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('checkout.refunds.show', $refund->id) }}" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $refunds->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada Permohonan Refund</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Anda belum membuat sebarang permohonan refund. 
                    <a href="{{ route('checkout.orders') }}" class="text-red-600 hover:text-red-500 font-medium">
                        Lihat pesanan anda
                    </a>
                    untuk memohon refund jika diperlukan.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
function closeRefundInfoBanner() {
    const banner = document.getElementById('refund-info-banner');
    if (banner) {
        banner.style.display = 'none';
    }
}
</script>
@endsection 