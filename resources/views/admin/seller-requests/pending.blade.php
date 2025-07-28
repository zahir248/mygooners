@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Permohonan Penjual Menunggu</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.seller-requests.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Senarai
            </a>
        </div>
    </div>

    <!-- Pending Sellers Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Permohonan Menunggu ({{ $sellers->total() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perniagaan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Maklumat</th>
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
                                    <div class="text-sm text-gray-500">{{ $seller->phone ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $seller->business_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $seller->business_type ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $seller->business_address ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <div><strong>Pengalaman:</strong> {{ $seller->years_experience ?? 'N/A' }} tahun</div>
                                <div><strong>Kawasan Operasi:</strong> {{ $seller->operating_area ?? 'N/A' }}</div>
                                <div><strong>Kemahiran:</strong> {{ Str::limit($seller->skills ?? 'N/A', 50) }}</div>
                            </div>
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
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tiada permohonan penjual menunggu.
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
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Tolak Permohonan Penjual</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">
                    Adakah anda pasti mahu menolak permohonan penjual untuk "<span id="rejectSellerName" class="font-medium"></span>"?
                </p>
                <form id="rejectSellerFormModal" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="seller_rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Sebab Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="seller_rejection_reason" 
                            name="seller_rejection_reason" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                            placeholder="Sila berikan sebab penolakan permohonan ini..."
                            required></textarea>
                    </div>
                    <div class="flex justify-center space-x-4">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Tolak
                        </button>
                    </div>
                </form>
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
</script>
@endsection 