@extends('layouts.admin')

@section('title', 'Profil Saya - Panel Admin')

@push('breadcrumbs')
    <span class="text-gray-400">/</span>
    <span class="text-gray-600">Profil Saya</span>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profil Saya</h1>
            <p class="mt-2 text-gray-600">Maklumat peribadi dan statistik pentadbir</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="text-center">
                        <!-- Profile Avatar -->
                        <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white text-3xl font-bold mb-4">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-1">{{ $admin->name }}</h2>
                        <p class="text-sm text-gray-500 mb-4">Pentadbir Sistem</p>
                        
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3"/>
                            </svg>
                            Aktif
                        </span>
                    </div>

                    <!-- Profile Details -->
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $admin->email }}</span>
                        </div>
                        
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600">Ahli sejak {{ $stats['member_since'] }}</span>
                        </div>
                        
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-gray-600">Log masuk terakhir: {{ $stats['last_login'] }}</span>
                        </div>
                    </div>

                                         <!-- Quick Actions -->
                     <div class="mt-6 pt-6 border-t border-gray-200">
                         <button @click="$dispatch('open-modal', 'update-profile')" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                             Kemas Kini Profil
                         </button>
                     </div>
                </div>
            </div>

            <!-- Statistics and Activity -->
            <div class="lg:col-span-2 space-y-6">
                                 <!-- Statistics Grid -->
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                         <div class="flex items-center">
                             <div class="flex-shrink-0">
                                 <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                     <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                     </svg>
                                 </div>
                             </div>
                             <div class="ml-4">
                                 <p class="text-sm font-medium text-gray-500">Artikel Diurus</p>
                                 <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_articles_managed'] }}</p>
                             </div>
                         </div>
                     </div>

                     <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                         <div class="flex items-center">
                             <div class="flex-shrink-0">
                                 <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                                     <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                     </svg>
                                 </div>
                             </div>
                             <div class="ml-4">
                                 <p class="text-sm font-medium text-gray-500">Artikel Diterbitkan</p>
                                 <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_published_articles'] }}</p>
                             </div>
                         </div>
                     </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Perkhidmatan Diurus</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_services_managed'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Produk Diurus</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_products_managed'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pengguna Diurus</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users_managed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Aktiviti Terkini</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                                                         <div class="flex items-center text-sm">
                                 <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                                 <span class="text-gray-600">Menguruskan {{ $stats['total_articles_managed'] }} artikel</span>
                                 <span class="ml-auto text-gray-400">Hari ini</span>
                             </div>
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></div>
                                <span class="text-gray-600">{{ $stats['total_published_articles'] }} artikel diterbitkan</span>
                                <span class="ml-auto text-gray-400">Hari ini</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-gray-600">Menguruskan {{ $stats['total_products_managed'] }} produk</span>
                                <span class="ml-auto text-gray-400">Hari ini</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-purple-400 rounded-full mr-3"></div>
                                <span class="text-gray-600">Menguruskan {{ $stats['total_users_managed'] }} pengguna</span>
                                <span class="ml-auto text-gray-400">Hari ini</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <div class="w-2 h-2 bg-orange-400 rounded-full mr-3"></div>
                                <span class="text-gray-600">Log masuk terakhir: {{ $stats['last_login'] }}</span>
                                <span class="ml-auto text-gray-400">{{ $stats['last_login'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Maklumat Sistem</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Versi Sistem:</span>
                                <span class="ml-2 text-gray-900 font-medium">1.0</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="ml-2 text-green-600 font-medium">Operasi Normal</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Kemaskini Terakhir:</span>
                                <span class="ml-2 text-gray-900 font-medium">Hari ini</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Masa Sistem:</span>
                                <span class="ml-2 text-gray-900 font-medium">{{ now()->format('d/m/Y H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                         </div>
         </div>
     </div>
 </div>

 <!-- Update Profile Modal -->
 <div x-data="{ show: false }" 
      x-show="show" 
      @open-modal.window="if ($event.detail === 'update-profile') show = true"
      @close-modal.window="show = false"
      x-cloak
      class="fixed inset-0 z-50 overflow-y-auto"
      style="display: none;">
     
     <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
         <!-- Background overlay -->
         <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>
         
         <!-- Modal panel -->
         <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
             <form action="{{ route('admin.profile.update') }}" method="POST">
                 @csrf
                 @method('PUT')
                 
                 <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                     <div class="sm:flex sm:items-start">
                         <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                             <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                             </svg>
                         </div>
                         <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                             <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                 Kemas Kini Profil
                             </h3>
                             
                             <!-- Name Field -->
                             <div class="mb-4">
                                 <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                                 <input type="text" 
                                        name="name" 
                                        id="name" 
                                        value="{{ $admin->name }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        required>
                                 @error('name')
                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                 @enderror
                             </div>
                             
                             <!-- Email Field -->
                             <div class="mb-4">
                                 <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Emel</label>
                                 <input type="email" 
                                        name="email" 
                                        id="email" 
                                        value="{{ $admin->email }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        required>
                                 @error('email')
                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                 @enderror
                             </div>
                             
                             <!-- Current Password Field -->
                             <div class="mb-4">
                                 <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Kata Laluan Semasa</label>
                                 <input type="password" 
                                        name="current_password" 
                                        id="current_password" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Kosongkan jika tidak mahu tukar kata laluan">
                                 @error('current_password')
                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                 @enderror
                             </div>
                             
                             <!-- New Password Field -->
                             <div class="mb-4">
                                 <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Kata Laluan Baru</label>
                                 <input type="password" 
                                        name="new_password" 
                                        id="new_password" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Kosongkan jika tidak mahu tukar kata laluan">
                                 @error('new_password')
                                     <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                 @enderror
                             </div>
                             
                             <!-- Confirm New Password Field -->
                             <div class="mb-4">
                                 <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Sahkan Kata Laluan Baru</label>
                                 <input type="password" 
                                        name="new_password_confirmation" 
                                        id="new_password_confirmation" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                        placeholder="Kosongkan jika tidak mahu tukar kata laluan">
                             </div>
                         </div>
                     </div>
                 </div>
                 
                 <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                     <button type="submit" 
                             class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                         Kemas Kini
                     </button>
                     <button type="button" 
                             @click="show = false"
                             class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                         Batal
                     </button>
                 </div>
             </form>
         </div>
     </div>
 </div>
 @endsection
