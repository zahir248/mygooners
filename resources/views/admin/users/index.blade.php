@extends('layouts.admin')

@section('title', 'Pengurusan Pengguna')

@section('content')
<!-- Header Section -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengurusan Pengguna</h1>
            <p class="mt-2 text-sm text-gray-700">Urus ahli komuniti dan kebenaran mereka</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @php $isSuperAdmin = auth()->user()->role === 'super_admin'; @endphp
            @if($isSuperAdmin)
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Cipta Pengguna
                </a>
            @else
                <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-300 cursor-not-allowed">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Cipta Pengguna
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="mx-4 bg-white shadow rounded-lg mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">Tapis Pengguna</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari pengguna..."
                           value="{{ request('search') }}"
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Role Filter -->
                <select name="role" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Peranan</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Pengguna</option>
                    <option value="writer" {{ request('role') === 'writer' ? 'selected' : '' }}>Writer</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                <!-- Status Filter -->
                <select name="status" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Digantung</option>
                </select>
                <!-- Verified Filter -->
                <select name="verified" class="border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    <option value="">Semua</option>
                    <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Disahkan</option>
                    <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Tidak Disahkan</option>
                </select>
                <!-- Filter Buttons -->
                <div class="flex gap-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm">
                        Tapis
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Filter Summary (moved below filter form, above table) -->
@if(request('search') || request('role') || request('status') || request('verified'))
    <div class="mx-4 mb-2">
        <div class="flex items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="text-gray-700 font-medium">Tapisan aktif:</span>
                @if(request('search'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Cari: "{{ request('search') }}"
                    </span>
                @endif
                @if(request('role'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        Peranan: {{ ucfirst(request('role')) }}
                    </span>
                @endif
                @if(request('status'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Status: {{ ucfirst(request('status')) }}
                    </span>
                @endif
                @if(request('verified'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Disahkan: {{ request('verified') == '1' ? 'Ya' : 'Tidak' }}
                    </span>
                @endif
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Kosongkan
            </a>
        </div>
    </div>
@endif

<!-- Users Table -->
<div class="mx-4 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                Pengguna ({{ $users->total() }})
            </h3>
        </div>
    </div>
    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peranan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disahkan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Masuk Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menyertai</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Tindakan</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($user->profile_image)
                                            @if(Str::startsWith($user->profile_image, 'http'))
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->profile_image }}" alt="{{ $user->name }}">
                                            @else
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                            @endif
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->role === 'super_admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Super Admin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Admin
                                    </span>
                                @elseif($user->role === 'writer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Penulis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Pengguna
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @elseif($user->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @elseif($user->status === 'suspended')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Digantung
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login ? $user->last_login->diffForHumans() : 'Tidak pernah' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="text-gray-600 hover:text-gray-900"
                                       title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(!$user->is_verified && $user->role !== 'super_admin')
                                        <button type="button" onclick="openActionModal('verify', {{ $user->id }}, '{{ $user->name }}', '{{ route('admin.users.verify', [':id']) }}')" class="text-green-600 hover:text-green-800 btn-verify" title="Sahkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($user->status === 'active' && $user->role !== 'super_admin')
                                        <button type="button" onclick="openActionModal('suspend', {{ $user->id }}, '{{ $user->name }}', '{{ route('admin.users.suspend', [':id']) }}')" class="text-yellow-600 hover:text-yellow-800 btn-suspend" title="Gantung">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                                            </svg>
                                        </button>
                                    @elseif($user->status === 'suspended')
                                        <button type="button" onclick="openActionModal('activate', {{ $user->id }}, '{{ $user->name }}', '{{ route('admin.users.activate', [':id']) }}')" class="text-blue-600 hover:text-blue-800 btn-activate" title="Aktifkan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    @if(auth()->user()->role === 'super_admin' && $user->role !== 'super_admin' && $user->id !== auth()->id())
                                        <button type="button" onclick="openActionModal('delete', {{ $user->id }}, '{{ $user->name }}', '{{ route('admin.users.destroy', [':id']) }}')" class="text-red-600 hover:text-red-800 btn-delete" title="Padam">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
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
                    Menunjukkan <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span> hingga <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $users->total() }}</span> keputusan
                </div>
                <div class="flex space-x-2">
                    @if($users->onFirstPage())
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Sebelumnya
                        </button>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Sebelumnya
                        </a>
                    @endif
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                            Seterusnya
                        </a>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                            Seterusnya
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tiada pengguna</h3>
            <p class="mt-1 text-sm text-gray-500">Tiada pengguna mengikut tapisan semasa.</p>
        </div>
    @endif
</div>

<!-- Action Confirmation Modal -->
<div id="actionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="actionModalIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <!-- Icon will be injected here -->
            </div>
            <h3 id="actionModalTitle" class="text-lg font-medium text-gray-900 mt-2">Sahkan Tindakan</h3>
            <div class="mt-2 px-7 py-3">
                <p id="actionModalMessage" class="text-sm text-gray-500">
                    <!-- Message will be injected here -->
                </p>
            </div>
            <div class="items-center px-4 py-3 flex justify-center space-x-2">
                <form id="actionModalForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="_method" id="actionModalMethod" value="POST">
                    <button type="submit" id="actionModalConfirmBtn" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2">
                        Sahkan
                    </button>
                </form>
                <button onclick="closeActionModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal logic for all actions
    function openActionModal(type, userId, userName, route) {
        const modal = document.getElementById('actionModal');
        const form = document.getElementById('actionModalForm');
        const methodInput = document.getElementById('actionModalMethod');
        const title = document.getElementById('actionModalTitle');
        const message = document.getElementById('actionModalMessage');
        const icon = document.getElementById('actionModalIcon');
        const confirmBtn = document.getElementById('actionModalConfirmBtn');

        // Set form action
        form.action = route.replace(':id', userId);

        // Set modal content based on action type
        if (type === 'verify') {
            title.textContent = 'Sahkan Pengguna';
            message.textContent = `Adakah anda pasti mahu mengesahkan pengguna "${userName}"?`;
            icon.innerHTML = `<svg class='h-6 w-6 text-green-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'></path></svg>`;
            confirmBtn.textContent = 'Sahkan';
            confirmBtn.className = 'px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 mr-2';
            methodInput.value = 'POST';
        } else if (type === 'suspend') {
            title.textContent = 'Gantung Pengguna';
            message.textContent = `Adakah anda pasti mahu menggantung pengguna "${userName}"?`;
            icon.innerHTML = `<svg class='h-6 w-6 text-yellow-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M18 12H6'></path></svg>`;
            confirmBtn.textContent = 'Gantung';
            confirmBtn.className = 'px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 mr-2';
            methodInput.value = 'POST';
        } else if (type === 'activate') {
            title.textContent = 'Aktifkan Pengguna';
            message.textContent = `Adakah anda pasti mahu mengaktifkan pengguna "${userName}"?`;
            icon.innerHTML = `<svg class='h-6 w-6 text-blue-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 12h12'></path></svg>`;
            confirmBtn.textContent = 'Aktifkan';
            confirmBtn.className = 'px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2';
            methodInput.value = 'POST';
        } else if (type === 'delete') {
            title.textContent = 'Padam Pengguna';
            message.textContent = `Adakah anda pasti mahu memadamkan pengguna "${userName}"? Tindakan ini tidak boleh diundur dan akan memadam semua data dan perkhidmatan mereka.`;
            icon.innerHTML = `<svg class='h-6 w-6 text-red-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path></svg>`;
            confirmBtn.textContent = 'Padam';
            confirmBtn.className = 'px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 mr-2';
            methodInput.value = 'DELETE';
        }
        modal.classList.remove('hidden');
    }
    function closeActionModal() {
        document.getElementById('actionModal').classList.add('hidden');
    }
    // Close modal when clicking outside
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeActionModal();
        }
    });
</script>

<!-- Update action buttons to use modal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-verify').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openActionModal('verify', this.dataset.userid, this.dataset.username, this.dataset.route);
            });
        });
        document.querySelectorAll('.btn-suspend').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openActionModal('suspend', this.dataset.userid, this.dataset.username, this.dataset.route);
            });
        });
        document.querySelectorAll('.btn-activate').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openActionModal('activate', this.dataset.userid, this.dataset.username, this.dataset.route);
            });
        });
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openActionModal('delete', this.dataset.userid, this.dataset.username, this.dataset.route);
            });
        });
    });
</script>
@endsection 