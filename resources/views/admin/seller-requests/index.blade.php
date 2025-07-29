@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Senarai Penjual</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.seller-requests.pending') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Menunggu ({{ \App\Models\User::where('seller_status', 'pending')->count() }})
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mx-4 bg-white shadow rounded-lg mb-6">
        <form method="GET" action="{{ route('admin.seller-requests.index') }}" class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Tapis Senarai Penjual</h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Cari penjual..."
                               value="{{ request('search') }}"
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Status Filter -->
                    <select name="seller_status" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="approved" {{ request('seller_status') == 'approved' ? 'selected' : '' }}>Diluluskan</option>
                        <option value="rejected" {{ request('seller_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <!-- Filter Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                            Tapis
                        </button>
                        <a href="{{ route('admin.seller-requests.index') }}"
                           class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Sellers Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Senarai Penjual</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perniagaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor Kepercayaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perkhidmatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Masuk Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Permohonan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sellers as $seller)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($seller->profile_image)
                                        @if(Str::startsWith($seller->profile_image, 'http'))
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $seller->profile_image }}" alt="{{ $seller->name }}">
                                        @else
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $seller->profile_image) }}" alt="{{ $seller->name }}">
                                        @endif
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($seller->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $seller->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $seller->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $seller->business_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $seller->business_type ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($seller->trust_score))
                                            <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-1 text-sm text-gray-600">{{ $seller->trust_score }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                            {{ $seller->services_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $seller->last_login ? $seller->last_login->diffForHumans() : 'Tidak pernah' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($seller->seller_application_date)
                                {{ $seller->seller_application_date->format('d/m/Y H:i') }}
                            @else
                                {{ $seller->created_at->format('d/m/Y H:i') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('admin.seller-requests.show', $seller->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Lihat Butiran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($seller->services_count > 0)
                                    <button type="button" 
                                            onclick="openServicesModal({{ $seller->id }}, '{{ $seller->name }}')"
                                            class="text-blue-600 hover:text-blue-900" title="Lihat Perkhidmatan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </button>
                                @endif
                                @if($seller->seller_status === 'pending')
                                    <button type="button" 
                                            onclick="openApproveModal({{ $seller->id }}, '{{ $seller->name }}')"
                                            class="text-green-600 hover:text-green-900" title="Lulus Permohonan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button type="button" 
                                            onclick="openRejectModal({{ $seller->id }}, '{{ $seller->name }}')"
                                            class="text-red-600 hover:text-red-900" title="Tolak Permohonan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                                <button type="button" onclick="openDeleteModal({{ $seller->id }}, '{{ $seller->name }}')" class="text-red-600 hover:text-red-900" title="Padam">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tiada permohonan penjual dijumpai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sellers->links('vendor.pagination.tailwind') }}
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
                    Adakah anda pasti mahu meluluskan permohonan penjual untuk <span id="approveSellerName" class="font-medium"></span>?
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="approveSellerFormModal" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Luluskan
                    </button>
                </form>
                <button onclick="closeApproveModal()" 
                        class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </button>
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
                    Adakah anda pasti mahu menolak permohonan penjual untuk <span id="rejectSellerName" class="font-medium"></span>?
                </p>
                <form id="rejectSellerFormModal" method="POST">
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

<!-- Services Modal -->
<div id="servicesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Perkhidmatan oleh <span id="servicesSellerName" class="font-medium"></span></h3>
                <button onclick="closeServicesModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="servicesModalContent" class="max-h-96 overflow-y-auto">
                <!-- Services will be loaded here -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-sm text-gray-500">Memuatkan perkhidmatan...</p>
                </div>
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
                    Adakah anda pasti mahu memadamkan permohonan penjual untuk <span id="deleteSellerName" class="font-medium"></span>? Tindakan ini tidak boleh diundur dan akan memadam semua data permohonan mereka.
                </p>
            </div>
            <div class="items-center px-4 py-3 flex justify-center space-x-2">
                <form id="deleteSellerFormModal" method="POST" class="inline">
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
function openApproveModal(sellerId, sellerName) {
    document.getElementById('approveSellerName').textContent = sellerName;
    var modalForm = document.getElementById('approveSellerFormModal');
    const baseUrl = '{{ route("admin.seller-requests.approve", ":id") }}';
    modalForm.action = baseUrl.replace(':id', sellerId);
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal(sellerId, sellerName) {
    document.getElementById('rejectSellerName').textContent = sellerName;
    var modalForm = document.getElementById('rejectSellerFormModal');
    const baseUrl = '{{ route("admin.seller-requests.reject", ":id") }}';
    modalForm.action = baseUrl.replace(':id', sellerId);
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openDeleteModal(sellerId, sellerName) {
    document.getElementById('deleteSellerName').textContent = sellerName;
    var modalForm = document.getElementById('deleteSellerFormModal');
    const baseUrl = '{{ route("admin.seller-requests.destroy", ":id") }}';
    modalForm.action = baseUrl.replace(':id', sellerId);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function openServicesModal(sellerId, sellerName) {
    document.getElementById('servicesSellerName').textContent = sellerName;
    document.getElementById('servicesModal').classList.remove('hidden');
    
    // Load services via AJAX
    fetch(`/admin/seller-requests/${sellerId}/services`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('servicesModalContent');
            if (data.services && data.services.length > 0) {
                let html = `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perkhidmatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh Dibuat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                data.services.forEach(service => {
                    const statusClass = service.status === 'active' ? 'bg-green-100 text-green-800' : 
                                      service.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                      'bg-red-100 text-red-800';
                    const statusText = service.status === 'active' ? 'Aktif' : 
                                     service.status === 'pending' ? 'Menunggu' : 'Ditolak';
                    
                    // Format the date
                    const createdDate = new Date(service.created_at).toLocaleDateString('ms-MY', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Truncate description if too long
                    const description = service.description ? 
                        (service.description.length > 100 ? service.description.substring(0, 100) + '...' : service.description) 
                        : 'Tiada penerangan';
                    
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${service.title}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">${description}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">RM ${service.pricing}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                    ${statusText}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${createdDate}
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                `;
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Tiada perkhidmatan dijumpai</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading services:', error);
            document.getElementById('servicesModalContent').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-red-500">Ralat memuatkan perkhidmatan</p>
                </div>
            `;
        });
}

function closeServicesModal() {
    document.getElementById('servicesModal').classList.add('hidden');
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
    
    document.getElementById('servicesModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeServicesModal();
        }
    });
});
</script>
@endsection 