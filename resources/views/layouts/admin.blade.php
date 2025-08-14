<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel Admin - MyGooners')</title>
    <meta name="description" content="Panel Admin MyGooners - Urus kandungan komuniti peminat Arsenal, pengguna, dan perkhidmatan.">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'admin': {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        'red': {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom Admin Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        .transition-all {
            transition: all 0.15s ease-in-out;
        }
        
        /* Custom scrollbar for admin */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Admin sidebar */
        .admin-sidebar {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar.closed {
                transform: translateX(-100%);
            }
        }
    </style>

    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" x-cloak>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
        </div>

        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-admin-800 admin-sidebar lg:translate-x-0" 
             :class="{ 'closed': !sidebarOpen }"
             x-show="sidebarOpen || window.innerWidth >= 1024">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-admin-900">
                <div class="flex items-center">
                    <div class="bg-red-600 text-white rounded-lg px-3 py-2 font-bold text-lg">
                        MG
                    </div>
                    <span class="ml-2 text-lg font-bold text-white">Admin</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-8 px-4">
                <ul class="space-y-2">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v4H8V5z"></path>
                            </svg>
                            Panel Kawalan
                        </a>
                    </li>

                    <!-- Articles -->
                    <li>
                        <a href="{{ route('admin.articles.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.articles.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Artikel
                        </a>
                    </li>

                    <!-- Videos -->
                    <li>
                        <a href="{{ route('admin.videos.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.videos.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Video
                        </a>
                    </li>

                    <!-- Services -->
                    <li x-data="{ open: {{ request()->routeIs('admin.services.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button"
                                class="flex items-center w-full px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z"></path>
                            </svg>
                            Perkhidmatan
                            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open" class="ml-8 mt-1 space-y-1" x-cloak>
                            <li>
                                <a href="{{ route('admin.services.index') }}"
                                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.services.index') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                                    Semua Perkhidmatan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.services.pending') }}"
                                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.services.pending') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                                    Menunggu Kelulusan
                                    @if(isset($stats['pending_services']) && $stats['pending_services'] > 0)
                                        <span class="ml-2 bg-red-600 text-white text-xs rounded-full px-2 py-1">
                                            {{ $stats['pending_services'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Products -->
                    <li>
                        <a href="{{ route('admin.products.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Produk
                        </a>
                    </li>

                    <!-- Reviews -->
                    <li>
                        <a href="{{ route('admin.reviews.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.reviews.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Ulasan Produk
                        </a>
                    </li>

                    <!-- Orders -->
                    <li>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Pesanan
                        </a>
                    </li>

                    <!-- Refunds -->
                    <li>
                        <a href="{{ route('admin.refunds.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.refunds.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            Refund
                        </a>
                    </li>

                    <!-- Users -->
                    <li x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.seller-requests.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" type="button"
                                class="flex items-center w-full px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.seller-requests.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Pengguna
                            <svg class="ml-auto h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul x-show="open" class="ml-8 mt-1 space-y-1" x-cloak>
                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                                    Semua Pengguna
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.seller-requests.index') }}"
                                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.seller-requests.index') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                                    Senarai Penjual
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.seller-requests.pending') }}"
                                   class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.seller-requests.pending') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                                    Menunggu Kelulusan
                                    @if(isset($stats['pending_sellers']) && $stats['pending_sellers'] > 0)
                                        <span class="ml-2 bg-red-600 text-white text-xs rounded-full px-2 py-1">
                                            {{ $stats['pending_sellers'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Settings -->
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Tetapan
                        </a>
                    </li>

                    <!-- Logs -->
                    <li>
                        <a href="{{ route('admin.logs.index') }}" 
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.logs.*') ? 'bg-admin-700 text-white' : 'text-admin-300 hover:bg-admin-700 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Log Sistem
                        </a>
                    </li>

                    <!-- Divider -->
                    <li class="border-t border-admin-700 my-4"></li>

                    <!-- Client Site -->
                    <li>
                        <a href="{{ route('home') }}" 
                           target="_blank"
                           class="flex items-center px-4 py-2 text-sm font-medium rounded-md text-admin-300 hover:bg-admin-700 hover:text-white transition-colors">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Lihat Laman
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main content -->
        <div class="lg:ml-64 flex flex-col min-h-screen">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Breadcrumbs -->
                        <div class="hidden lg:flex items-center space-x-2 text-sm text-gray-500">
                            @stack('breadcrumbs')
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="p-2 text-gray-400 hover:text-gray-500 relative">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15"></path>
                                </svg>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
                            </button>

                            <!-- User dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <img class="h-8 w-8 rounded-full bg-gray-300" 
                                         src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=dc2626&color=fff" 
                                         alt="{{ auth()->user()->name }}">
                                    <span class="ml-2 text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                                    <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" x-cloak 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                                    <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">
                                        Pentadbir
                                    </div>
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tetapan</a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Log Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 m-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 px-4 py-4">
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <p>© {{ date('Y') }} Panel Admin MyGooners. Dibina dengan ❤️ untuk peminat Arsenal.</p>
                    <p>Version 1.0</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')
</body>
</html> 