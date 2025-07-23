@extends('layouts.admin')

@section('title', 'Perkhidmatan Menunggu Kelulusan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Perkhidmatan Menunggu Kelulusan</h1>
            <p class="mt-2 text-sm text-gray-700">Senarai perkhidmatan yang menunggu kelulusan admin</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-2">
            <a href="{{ route('admin.services.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Semua Perkhidmatan
            </a>
        </div>
    </div>
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Menunggu Kelulusan ({{ $services->total() }})
            </h3>
        </div>
        @if($services->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perkhidmatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyedia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarikh</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Tindakan</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($services as $service)
                            @php
                                $displayService = $service->is_update_request && $service->original_service_id ? \App\Models\Service::find($service->original_service_id) : $service;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 h-16 w-24">
                                            @if($displayService->images && is_array($displayService->images) && count($displayService->images) > 0)
                                                <img class="h-16 w-24 rounded-lg object-cover" src="{{ route('service.image', ['filename' => basename($displayService->images[0])]) }}" alt="{{ $displayService->title }}">
                                            @else
                                                <div class="h-16 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <p class="text-sm font-bold text-gray-900 truncate">{{ $displayService->title }}</p>
                                                @if($displayService->is_verified)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disahkan</span>
                                                @endif
                                                @if($service->is_update_request)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Kemaskini</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 line-clamp-2">
                                                ID Perkhidmatan: #{{ $displayService->id }} • {{ $displayService->location }} • {{ $displayService->pricing }}
                                                @if($service->is_update_request && $service->original_service_id)
                                                    <br><span class="text-blue-600">Kemaskini untuk perkhidmatan #{{ $service->original_service_id }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $service->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $service->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $displayService->category }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>
                                        <div class="font-medium">{{ $displayService->created_at->format('j M Y') }}</div>
                                        <div class="text-xs">{{ $displayService->updated_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <!-- Approve Button -->
                                        <button type="button" 
                                                onclick="openApproveModal({{ $service->id }}, '{{ $service->title }}')"
                                                class="text-green-600 hover:text-green-900" 
                                                title="Lulus Perkhidmatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <!-- Reject Button -->
                                        <button type="button" 
                                                onclick="openRejectModal({{ $service->id }}, '{{ $service->title }}')"
                                                class="text-red-600 hover:text-red-900" 
                                                title="Tolak Perkhidmatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <!-- View Service -->
                                        <button type="button" 
                                                onclick="openServiceModal({{ $service->id }})"
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="Lihat Perkhidmatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $services->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $services->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $services->total() }}</span> results
                    </div>
                    <div class="flex space-x-2">
                        @if($services->onFirstPage())
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                                Previous
                            </button>
                        @else
                            <a href="{{ $services->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        @endif
                        
                        @if($services->hasMorePages())
                            <a href="{{ $services->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m8 5H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada perkhidmatan menunggu</h3>
                <p class="mt-1 text-sm text-gray-500">Semua perkhidmatan telah diluluskan atau ditolak.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.services.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Lihat Semua Perkhidmatan
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Service Details Modal -->
<div id="serviceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 60;">
    <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-900">Butiran Permohonan</h3>
            <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="serviceModalContent" class="space-y-6">
            <!-- Loading spinner -->
            <div id="serviceModalLoading" class="flex justify-center items-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Service content will be loaded here -->
            <div id="serviceModalData" class="hidden">
                <!-- Changes Summary -->
                <div id="changesSummary" class="mb-6"></div>
                
                <!-- Service Images -->
                <div id="serviceImages" class="mb-6"></div>
                
                <!-- Service Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Service Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Maklumat Perkhidmatan</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tajuk Perkhidmatan</label>
                                <p id="serviceTitle" class="text-gray-900 font-medium"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Penerangan</label>
                                <p id="serviceDescription" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Kategori</label>
                                <p id="serviceCategory" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Lokasi</label>
                                <p id="serviceLocation" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Harga</label>
                                <p id="servicePricing" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Maklumat Hubungan</label>
                                <p id="serviceContact" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tag</label>
                                <div id="serviceTags" class="flex flex-wrap gap-2"></div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Tarikh Permohonan</label>
                                <p id="serviceCreatedAt" class="text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Maklumat Pengguna</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama</label>
                                <p id="userName" class="text-gray-900 font-medium"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Email</label>
                                <p id="userEmail" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">No. Telefon</label>
                                <p id="userPhone" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Bio</label>
                                <p id="userBio" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Lokasi</label>
                                <p id="userLocation" class="text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Status Penjual</label>
                                <p id="userSellerStatus" class="text-gray-900"></p>
                            </div>
                            
                            <!-- User Images Section -->
                            <div>
                                <label class="text-sm font-medium text-gray-600">Gambar Pengguna</label>
                                <div id="userImages" class="mt-2 space-y-3">
                                    <!-- Profile Image -->
                                    <div id="userProfileImage" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Gambar Profil</label>
                                        <div class="mt-1">
                                            <img id="profileImageSrc" src="" alt="Profile Image" class="w-24 h-24 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                    
                                    <!-- ID Document -->
                                    <div id="userIdDocument" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Dokumen Pengenalan</label>
                                        <div class="mt-1">
                                            <img id="idDocumentSrc" src="" alt="ID Document" class="w-32 h-20 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                    
                                    <!-- Selfie with ID -->
                                    <div id="userSelfieWithId" class="hidden">
                                        <label class="text-xs font-medium text-gray-500">Selfie dengan Dokumen Pengenalan</label>
                                        <div class="mt-1">
                                            <img id="selfieWithIdSrc" src="" alt="Selfie with ID" class="w-32 h-20 object-cover rounded-lg border">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="sellerDetails" class="hidden space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Nama Perniagaan</label>
                                    <p id="userBusinessName" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Jenis Perniagaan</label>
                                    <p id="userBusinessType" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Alamat Perniagaan</label>
                                    <p id="userBusinessAddress" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kawasan Operasi</label>
                                    <p id="userOperatingArea" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Tahun Pengalaman</label>
                                    <p id="userYearsExperience" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kemahiran</label>
                                    <p id="userSkills" class="text-gray-900"></p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-600">Kawasan Perkhidmatan</label>
                                    <p id="userServiceAreas" class="text-gray-900"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add some bottom padding to ensure content is visible -->
        <div class="h-8"></div>
        
        <!-- Scroll to top button -->
        <button onclick="scrollToTop()" 
                class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors duration-200"
                style="z-index: 70;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Lulus Perkhidmatan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Adakah anda pasti mahu meluluskan "<span id="approveServiceTitle"></span>"? Perkhidmatan ini akan menjadi aktif dan boleh dilihat oleh pengguna.
                </p>
            </div>
            <div class="flex justify-center space-x-4 mt-4">
                <button onclick="closeApproveModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
                <form id="approveServiceFormModal" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Lulus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm overflow-y-auto h-full w-full hidden" style="z-index: 50;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4 text-center">Tolak Perkhidmatan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 text-center">
                    Adakah anda pasti mahu menolak "<span id="rejectServiceTitle"></span>"?
                </p>
                <form id="rejectServiceFormModal" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Sebab Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="rejection_reason" 
                            name="rejection_reason" 
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

@push('scripts')
<script>
function openApproveModal(serviceId, serviceTitle) {
    // Disable background page interaction
    document.body.style.overflow = 'hidden';
    document.body.style.pointerEvents = 'none';
    
    document.getElementById('approveServiceTitle').textContent = serviceTitle;
    var modalForm = document.getElementById('approveServiceFormModal');
    const baseUrl = '{{ route("admin.services.approve", ":id") }}';
    modalForm.action = baseUrl.replace(':id', serviceId);
    document.getElementById('approveModal').classList.remove('hidden');
    
    // Re-enable pointer events for modal only
    document.getElementById('approveModal').style.pointerEvents = 'auto';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    
    // Re-enable background page interaction
    document.body.style.overflow = '';
    document.body.style.pointerEvents = '';
}

function openRejectModal(serviceId, serviceTitle) {
    // Disable background page interaction
    document.body.style.overflow = 'hidden';
    document.body.style.pointerEvents = 'none';
    
    document.getElementById('rejectServiceTitle').textContent = serviceTitle;
    var modalForm = document.getElementById('rejectServiceFormModal');
    const baseUrl = '{{ route("admin.services.reject", ":id") }}';
    modalForm.action = baseUrl.replace(':id', serviceId);
    document.getElementById('rejectModal').classList.remove('hidden');
    
    // Re-enable pointer events for modal only
    document.getElementById('rejectModal').style.pointerEvents = 'auto';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    
    // Re-enable background page interaction
    document.body.style.overflow = '';
    document.body.style.pointerEvents = '';
}

function openServiceModal(serviceId) {
    // Disable background page interaction
    document.body.style.overflow = 'hidden';
    document.body.style.pointerEvents = 'none';
    
    // Show loading
    document.getElementById('serviceModalLoading').classList.remove('hidden');
    document.getElementById('serviceModalData').classList.add('hidden');
    document.getElementById('serviceModal').classList.remove('hidden');
    
    // Re-enable pointer events for modal only
    document.getElementById('serviceModal').style.pointerEvents = 'auto';
    
    // Scroll modal to top
    document.getElementById('serviceModal').scrollTo({ top: 0, behavior: 'instant' });
    
    // Fetch service details
    fetch(`{{ route('admin.services.details', ':id') }}`.replace(':id', serviceId))
        .then(response => response.json())
        .then(data => {
            // Hide loading and show data
            document.getElementById('serviceModalLoading').classList.add('hidden');
            document.getElementById('serviceModalData').classList.remove('hidden');
            
            // Populate changes summary if this is an update request
            this.populateChangesSummary(data.changes);
            
            // Populate service information with change highlighting
            this.populateServiceField('serviceTitle', data.service.title, data.changes?.title);
            this.populateServiceField('serviceDescription', data.service.description, data.changes?.description);
            this.populateServiceField('serviceCategory', data.service.category, data.changes?.category);
            this.populateServiceField('serviceLocation', data.service.location, data.changes?.location);
            this.populateServiceField('servicePricing', data.service.pricing, data.changes?.pricing);
            this.populateServiceField('serviceContact', data.service.contact_info, data.changes?.contact_info);
            document.getElementById('serviceCreatedAt').textContent = data.created_at;
            
            // Populate tags with change highlighting
            const tagsContainer = document.getElementById('serviceTags');
            tagsContainer.innerHTML = '';
            if (data.service.tags && data.service.tags.length > 0) {
                data.service.tags.forEach(tag => {
                    const tagElement = document.createElement('span');
                    const isChanged = data.changes?.tags && data.changes.tags.new.includes(tag);
                    tagElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isChanged ? 'bg-green-100 text-green-800 border-2 border-green-300' : 'bg-blue-100 text-blue-800'}`;
                    tagElement.textContent = tag;
                    if (isChanged) {
                        tagElement.title = 'Tag baharu';
                    }
                    tagsContainer.appendChild(tagElement);
                });
            } else {
                tagsContainer.innerHTML = '<span class="text-gray-500 text-sm">Tiada tag</span>';
            }
            
            // Populate images with change highlighting
            const imagesContainer = document.getElementById('serviceImages');
            imagesContainer.innerHTML = '';

            if (data.original_service && data.service) {
                // Existing Images
                const existingLabel = document.createElement('div');
                existingLabel.className = 'mb-2 font-semibold text-gray-700';
                existingLabel.textContent = 'Gambar Sedia Ada';
                imagesContainer.appendChild(existingLabel);

                const existingGrid = document.createElement('div');
                existingGrid.className = 'grid grid-cols-2 md:grid-cols-4 gap-4 mb-4';
                if (data.original_service.images && data.original_service.images.length > 0) {
                    data.original_service.images.forEach(imageUrl => {
                        const imgDiv = document.createElement('div');
                        imgDiv.innerHTML = `<img src="${imageUrl}" alt="Gambar Sedia Ada" class="w-full h-32 object-cover rounded-lg">`;
                        existingGrid.appendChild(imgDiv);
                    });
                } else {
                    existingGrid.innerHTML = '<p class="text-gray-500 text-sm">Tiada gambar</p>';
                }
                imagesContainer.appendChild(existingGrid);

                // Proposed Images
                const proposedLabel = document.createElement('div');
                proposedLabel.className = 'mb-2 font-semibold text-gray-700';
                proposedLabel.textContent = 'Gambar Dicadangkan';
                imagesContainer.appendChild(proposedLabel);

                const proposedGrid = document.createElement('div');
                proposedGrid.className = 'grid grid-cols-2 md:grid-cols-4 gap-4';
                if (data.images && data.images.length > 0) {
                    data.images.forEach(imageUrl => {
                        const imgDiv = document.createElement('div');
                        imgDiv.innerHTML = `<img src="${imageUrl}" alt="Gambar Dicadangkan" class="w-full h-32 object-cover rounded-lg">`;
                        proposedGrid.appendChild(imgDiv);
                    });
                } else {
                    proposedGrid.innerHTML = '<p class="text-gray-500 text-sm">Tiada gambar</p>';
                }
                imagesContainer.appendChild(proposedGrid);
            } else if (data.images && data.images.length > 0) {
                // For new services (not update requests)
                const label = document.createElement('div');
                label.className = 'mb-2 font-semibold text-gray-700';
                label.textContent = 'Gambar';
                imagesContainer.appendChild(label);

                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-2 md:grid-cols-4 gap-4 mb-4';
                data.images.forEach(imageUrl => {
                    const imgDiv = document.createElement('div');
                    imgDiv.innerHTML = `<img src="${imageUrl}" alt="Gambar" class="w-full h-32 object-cover rounded-lg">`;
                    grid.appendChild(imgDiv);
                });
                imagesContainer.appendChild(grid);
            } else {
                imagesContainer.innerHTML = '<p class="text-gray-500 text-sm">Tiada gambar</p>';
            }
            
            // Populate user information
            document.getElementById('userName').textContent = data.user.name;
            document.getElementById('userEmail').textContent = data.user.email;
            document.getElementById('userPhone').textContent = data.user.phone || 'Tiada maklumat';
            document.getElementById('userBio').textContent = data.user.bio || 'Tiada maklumat';
            document.getElementById('userLocation').textContent = data.user.location || 'Tiada maklumat';
            document.getElementById('userSellerStatus').textContent = data.user.is_seller ? 'Penjual Disahkan' : 'Pengguna Biasa';
            
            // Populate user images
            const profileImageDiv = document.getElementById('userProfileImage');
            const idDocumentDiv = document.getElementById('userIdDocument');
            const selfieWithIdDiv = document.getElementById('userSelfieWithId');
            
            // Profile Image
            if (data.user.profile_image) {
                document.getElementById('profileImageSrc').src = '{{ asset("storage/") }}/' + data.user.profile_image;
                profileImageDiv.classList.remove('hidden');
            } else {
                profileImageDiv.classList.add('hidden');
            }
            
            // ID Document
            if (data.user.id_document) {
                document.getElementById('idDocumentSrc').src = '{{ asset("storage/") }}/' + data.user.id_document;
                idDocumentDiv.classList.remove('hidden');
            } else {
                idDocumentDiv.classList.add('hidden');
            }
            
            // Selfie with ID
            if (data.user.selfie_with_id) {
                document.getElementById('selfieWithIdSrc').src = '{{ asset("storage/") }}/' + data.user.selfie_with_id;
                selfieWithIdDiv.classList.remove('hidden');
            } else {
                selfieWithIdDiv.classList.add('hidden');
            }
            
            // Show/hide seller details
            const sellerDetails = document.getElementById('sellerDetails');
            if (data.user.is_seller) {
                sellerDetails.classList.remove('hidden');
                document.getElementById('userBusinessName').textContent = data.user.business_name || 'Tiada maklumat';
                document.getElementById('userBusinessType').textContent = data.user.business_type || 'Tiada maklumat';
                document.getElementById('userBusinessAddress').textContent = data.user.business_address || 'Tiada maklumat';
                document.getElementById('userOperatingArea').textContent = data.user.operating_area || 'Tiada maklumat';
                document.getElementById('userYearsExperience').textContent = data.user.years_experience || 'Tiada maklumat';
                document.getElementById('userSkills').textContent = data.user.skills || 'Tiada maklumat';
                document.getElementById('userServiceAreas').textContent = data.user.service_areas || 'Tiada maklumat';
            } else {
                sellerDetails.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error fetching service details:', error);
            document.getElementById('serviceModalLoading').innerHTML = '<p class="text-red-500">Ralat memuat data perkhidmatan</p>';
        });
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.add('hidden');
    
    // Re-enable background page interaction
    document.body.style.overflow = '';
    document.body.style.pointerEvents = '';
}

function scrollToTop() {
    const modal = document.getElementById('serviceModal');
    modal.scrollTo({ top: 0, behavior: 'smooth' });
}

function populateServiceField(elementId, value, change) {
    const element = document.getElementById(elementId);
    if (change) {
        // Field has changed - show with highlighting
        element.innerHTML = `
            <div class="bg-green-50 border-l-4 border-green-400 p-3 rounded">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-green-800">Nilai Baharu</p>
                        <p class="text-sm text-green-700 mt-1">${value}</p>
                        <details class="mt-2">
                            <summary class="text-xs text-green-600 cursor-pointer hover:text-green-800">Lihat nilai asal</summary>
                            <p class="text-xs text-gray-600 mt-1 bg-gray-50 p-2 rounded">${change.old}</p>
                        </details>
                    </div>
                </div>
            </div>
        `;
    } else {
        // Field unchanged - show normally
        element.textContent = value;
    }
}

function populateChangesSummary(changes) {
    const summaryContainer = document.getElementById('changesSummary');
    
    if (!changes || Object.keys(changes).length === 0) {
        summaryContainer.innerHTML = '';
        return;
    }
    
    const changedFields = Object.keys(changes);
    const fieldLabels = {
        'title': 'Tajuk Perkhidmatan',
        'description': 'Penerangan',
        'location': 'Lokasi',
        'pricing': 'Harga',
        'contact_info': 'Maklumat Hubungan',
        'category': 'Kategori',
        'tags': 'Tag',
        'images': 'Gambar'
    };
    
    const summaryHTML = `
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Ringkasan Perubahan</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p class="mb-2">Bidang yang dikemaskini:</p>
                        <ul class="list-disc list-inside space-y-1">
                            ${changedFields.map(field => `<li>${fieldLabels[field] || field}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    summaryContainer.innerHTML = summaryHTML;
}

// Close modals when clicking outside
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

document.getElementById('serviceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServiceModal();
    }
});
</script>
@endpush

@endsection 