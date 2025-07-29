@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Butiran Permohonan Penjual</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.seller-requests.pending') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Senarai
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Seller Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Maklumat Asas</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 h-16 w-16">
                            @if($seller->profile_image)
                                @if(Str::startsWith($seller->profile_image, 'http'))
                                    <img class="h-16 w-16 rounded-full object-cover" src="{{ $seller->profile_image }}" alt="{{ $seller->name }}">
                                @else
                                    <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $seller->profile_image) }}" alt="{{ $seller->name }}">
                                @endif
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-xl font-medium text-gray-700">{{ substr($seller->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="ml-6">
                            <h4 class="text-xl font-medium text-gray-900">{{ $seller->name }}</h4>
                            <p class="text-gray-500">{{ $seller->email }}</p>
                            <p class="text-gray-500">{{ $seller->phone ?? 'N/A' }}</p>
                            <div class="mt-2">
                                @if($seller->seller_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($seller->seller_status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Diluluskan
                                    </span>
                                @elseif($seller->seller_status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Bio</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->bio ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tarikh Daftar</label>
                            <p class="mt-1 text-sm text-gray-900">
                            @if($seller->seller_application_date)
                                {{ $seller->seller_application_date->format('d/m/Y H:i') }}
                            @else
                                {{ $seller->created_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status Pengesahan</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($seller->is_verified)
                                    <span class="text-green-600">Disahkan</span>
                                @else
                                    <span class="text-gray-600">Belum Disahkan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Maklumat Perniagaan</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Perniagaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->business_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Perniagaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->business_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pendaftaran Perniagaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->business_registration ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Perniagaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->business_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kawasan Operasi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->operating_area ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Laman Web</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->website ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Pengalaman</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->years_experience ?? 'N/A' }} tahun</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kemahiran</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->skills ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Kawasan Perkhidmatan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $seller->service_areas ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Dokumen</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dokumen Pengenalan</label>
                            @if($seller->id_document)
                                <a href="{{ asset('storage/' . $seller->id_document) }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Lihat Dokumen
                                </a>
                            @else
                                <p class="text-sm text-gray-500">Tiada dokumen</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selfie dengan ID</label>
                            @if($seller->selfie_with_id)
                                <a href="{{ asset('storage/' . $seller->selfie_with_id) }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Lihat Gambar
                                </a>
                            @else
                                <p class="text-sm text-gray-500">Tiada gambar</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($seller->seller_status === 'rejected' && $seller->seller_rejection_reason)
            <!-- Rejection Reason -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sebab Penolakan</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-900">{{ $seller->seller_rejection_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status Permohonan</h3>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        @if($seller->seller_status === 'pending')
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="mt-4 text-lg font-medium text-gray-900">Menunggu</h4>
                            <p class="mt-2 text-sm text-gray-500">Permohonan sedang menunggu kelulusan</p>
                        @elseif($seller->seller_status === 'approved')
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="mt-4 text-lg font-medium text-gray-900">Diluluskan</h4>
                            <p class="mt-2 text-sm text-gray-500">Permohonan telah diluluskan</p>
                        @elseif($seller->seller_status === 'rejected')
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h4 class="mt-4 text-lg font-medium text-gray-900">Ditolak</h4>
                            <p class="mt-2 text-sm text-gray-500">Permohonan telah ditolak</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($seller->seller_status === 'pending')
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tindakan</h3>
                </div>
                <div class="p-6 space-y-3">
                    <button type="button" 
                            onclick="openApproveModal()"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Luluskan Permohonan
                    </button>
                    
                    <button type="button" 
                            onclick="openRejectModal()"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tolak Permohonan
                    </button>
                </div>
            </div>
            @endif

            <!-- Delete Action -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Tindakan Berbahaya</h3>
                </div>
                <div class="p-6">
                    <button type="button" 
                            onclick="openDeleteModal()"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Padam Permohonan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Tolak Permohonan Penjual</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Adakah anda pasti mahu menolak permohonan penjual untuk <span class="font-medium">{{ $seller->name }}</span>?
                </p>
                <form method="POST" action="{{ route('admin.seller-requests.reject', $seller->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="seller_rejection_reason" class="block text-sm font-medium text-gray-700 text-left">Sebab Penolakan</label>
                        <textarea name="seller_rejection_reason" id="seller_rejection_reason" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Sila berikan sebab penolakan..."></textarea>
                    </div>
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Tolak
                    </button>
                </form>
                <button onclick="closeRejectModal()" 
                        class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Lulus Permohonan Penjual</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu meluluskan permohonan penjual untuk <span class="font-medium">{{ $seller->name }}</span>?
                </p>
            </div>
            <div class="items-center px-4 py-3 flex justify-center space-x-2">
                <form method="POST" action="{{ route('admin.seller-requests.approve', $seller->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 mr-2">
                        Luluskan
                    </button>
                </form>
                <button onclick="closeApproveModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Padam Permohonan Penjual</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu memadamkan permohonan penjual untuk <span class="font-medium">{{ $seller->name }}</span>? Tindakan ini tidak boleh diundur dan akan memadam semua data permohonan mereka.
                </p>
            </div>
            <div class="items-center px-4 py-3 flex justify-center space-x-2">
                <form method="POST" action="{{ route('admin.seller-requests.destroy', $seller->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mr-2">
                        Padam
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('approveModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApproveModal();
        }
    });
    
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
    
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
});
</script>
@endsection 