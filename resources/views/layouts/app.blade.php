<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MyGooners - Komuniti Peminat Arsenal')</title>
    <meta name="description" content="@yield('meta_description', 'Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif.')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
        
        .transition-colors {
            transition: color 0.15s ease-in-out;
        }
        
        .transition-all {
            transition: all 0.15s ease-in-out;
        }
        
        /* Arsenal Official Colors */
        .bg-arsenal {
            background-color: #dc2626 !important;
        }
        
        .text-arsenal {
            color: #dc2626 !important;
        }
        
        .border-arsenal {
            border-color: #dc2626 !important;
        }
        
        .hover\:bg-arsenal:hover {
            background-color: #b91c1c !important;
        }
        
        .hover\:text-arsenal:hover {
            color: #dc2626 !important;
        }
        
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
        
        /* Cart icon animations */
        .cart-count {
            animation: cartPulse 2s infinite;
        }
        
        @keyframes cartPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 4px rgba(220, 38, 38, 0);
            }
        }
        
        /* Modern cart icon styling */
        .cart-icon-modern {
            background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
            border: 2px solid #fecaca;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .cart-icon-modern:hover {
            border-color: #f87171;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
            transform: translateY(-1px);
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="min-h-full">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top bar -->
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="bg-red-600 text-white rounded-lg px-3 py-2 font-bold text-xl">
                                MG
                            </div>
                            <span class="ml-2 text-xl font-bold text-gray-900">MyGooners</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Utama
                        </a>
                        <a href="{{ route('blog.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('blog.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Berita
                        </a>
                        <a href="{{ route('videos.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('videos.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Video
                        </a>
                        <a href="{{ route('services.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('services.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Perkhidmatan
                        </a>
                        <a href="{{ route('shop.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('shop.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Kedai
                        </a>
                    </nav>

                    <!-- Right side -->
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <!-- Cart Icon - Only for logged in users -->
                        @auth
                        <div class="relative group">
                            <a href="{{ route('cart.index') }}" class="flex items-center space-x-1 md:space-x-3 px-2 md:px-3 py-2 text-gray-700 hover:text-red-600 transition-colors">
                                <div class="relative">
                                    <svg class="w-5 h-5 md:w-6 md:h-6 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                    @if(\App\Models\Cart::getOrCreateCart()->item_count > 0)
                                        <span class="cart-count absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center">
                                            {{ \App\Models\Cart::getOrCreateCart()->item_count }}
                                        </span>
                                    @else
                                        <span class="cart-count absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center hidden">
                                            0
                                        </span>
                                    @endif
                                </div>
                                <div class="hidden lg:block">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-600 transition-colors">Troli</span>
                                    <div class="text-xs text-gray-500 cart-total">
                                        RM{{ number_format(\App\Models\Cart::getOrCreateCart()->total, 2) }}
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endauth
                        


                        <!-- Auth -->
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    @if(auth()->user()->profile_image)
                                        @if(Str::startsWith(auth()->user()->profile_image, 'http'))
                                            <img class="h-7 w-7 md:h-8 md:w-8 rounded-full object-cover" src="{{ auth()->user()->profile_image }}" alt="{{ auth()->user()->name }}">
                                        @else
                                            <img class="h-7 w-7 md:h-8 md:w-8 rounded-full object-cover" src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}">
                                        @endif
                                    @else
                                        <img class="h-7 w-7 md:h-8 md:w-8 rounded-full bg-gray-300" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=dc2626&color=fff" alt="{{ auth()->user()->name }}">
                                    @endif
                                    <span class="ml-1 md:ml-2 text-gray-700 font-medium text-sm md:text-base hidden sm:block">{{ auth()->user()->name }}</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Panel Kawalan
                                    </a>
                                    <a href="{{ route('checkout.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                        Pesanan Saya
                                    </a>
                                    <a href="{{ route('addresses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Alamat
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex space-x-2">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                                    Log Masuk
                                </a>
                                <a href="{{ route('register') }}" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Sertai Kami
                                </a>
                            </div>
                        @endauth

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-gray-700 hover:text-red-600 focus:outline-none p-1">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     x-cloak 
                     class="md:hidden absolute top-full left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
                    <div class="px-4 py-4 space-y-1">
                        <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Utama</a>
                        <a href="{{ route('blog.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Berita</a>
                        <a href="{{ route('videos.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Video</a>
                        <a href="{{ route('services.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Perkhidmatan</a>
                        <a href="{{ route('shop.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Kedai</a>
                        @auth
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Panel Kawalan</a>
                                <a href="{{ route('checkout.orders') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Pesanan Saya</a>
                                <a href="{{ route('addresses.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50 rounded-md">Alamat</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages - Floating Right Side -->
        <div class="fixed top-20 right-4 z-50">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button @click="show = false" class="inline-flex text-green-400 hover:text-green-600 focus:outline-none focus:text-green-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ session('warning') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button @click="show = false" class="inline-flex text-yellow-400 hover:text-yellow-600 focus:outline-none focus:text-yellow-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-sm" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button @click="show = false" class="inline-flex text-red-400 hover:text-red-600 focus:outline-none focus:text-red-600">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Main Footer Content -->
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                    <!-- Left Side - Footer Links -->
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="bg-red-600 text-white rounded-lg px-3 py-2 font-bold text-xl">
                                    MG
                                </div>
                                <span class="ml-2 text-xl font-bold">MyGooners</span>
                            </div>
                            <p class="text-gray-300 text-sm">
                                Komuniti peminat Arsenal terbaik yang menampilkan berita terkini, video, pasaran perkhidmatan, dan barangan eksklusif.
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Kandungan</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white transition-colors">Berita Terkini</a></li>
                                <li><a href="{{ route('videos.index') }}" class="text-gray-300 hover:text-white transition-colors">Podcast Video</a></li>
                                <li><a href="{{ route('blog.category', 'match-reports') }}" class="text-gray-300 hover:text-white transition-colors">Laporan Perlawanan</a></li>
                                <li><a href="{{ route('blog.category', 'transfer-news') }}" class="text-gray-300 hover:text-white transition-colors">Berita Pemindahan</a></li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Komuniti</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('services.index') }}" class="text-gray-300 hover:text-white transition-colors">Perkhidmatan</a></li>
                                <li><a href="{{ route('shop.index') }}" class="text-gray-300 hover:text-white transition-colors">Kedai</a></li>
                                <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-white transition-colors">Sertai Komuniti</a></li>
                            </ul>
                            
                            <h3 class="text-lg font-semibold mb-4 mt-8">Berhubung</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-300 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.758-1.378l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.624 0 11.99-5.367 11.99-11.987C24.007 5.367 18.641.001 12.017.001z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Side - Newsletter Section -->
                    <div class="lg:w-96 lg:pl-8">
                        <h3 class="text-2xl font-bold text-white mb-2">Kekal Terkini, Gooner!</h3>
                        <p class="text-gray-300 mb-6">Dapatkan berita Arsenal terkini, kandungan eksklusif, dan kemas kini komuniti terus ke peti mel anda.</p>
                        
                        <form class="space-y-4" action="#" method="POST">
                            @csrf
                            <div>
                                <input type="email" 
                                       name="email" 
                                       placeholder="Masukkan alamat emel anda" 
                                       required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all">
                            </div>
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 hover:scale-105 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-gray-900">
                                Langgan
                            </button>
                        </form>
                        
                        <div class="grid grid-cols-1 gap-2 mt-4 text-sm text-gray-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Berita terkini
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Kandungan eksklusif
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Tiada spam, berhenti melanggan bila-bila masa
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} MyGooners. Hak cipta terpelihara. Dibina dengan ❤️ untuk peminat Arsenal.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')

    <!-- FAQ Chatbot -->
    <div x-data="faqChatbot()" x-init="init" class="fixed bottom-6 right-6 z-50" x-cloak>
        <!-- Floating Button -->
        <button @click="toggleChat()" 
                class="bg-red-600 hover:bg-red-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                :class="{ 'rotate-45': isOpen }">
            <svg x-show="!isOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.477 8-10 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.477-8 10-8s10 3.582 10 8z"></path>
            </svg>
            <svg x-show="isOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Chat Interface -->
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="absolute bottom-20 right-0 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
            
            <!-- Chat Header -->
            <div class="bg-red-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold">MG</span>
                        </div>
                        <div>
                            <h3 class="font-semibold">MyGooners Assistant</h3>
                            <p class="text-xs text-red-100">Tanya saya apa-apa!</p>
                        </div>
                    </div>
                    <button @click="toggleChat()" class="text-red-100 hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="h-64 overflow-y-auto p-4 space-y-3" x-ref="messageContainer">
                <!-- Welcome Message -->
                <div class="flex items-start space-x-2">
                    <div class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-xs text-white font-bold">MG</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                        <p class="text-sm">Halo! Saya adalah pembantu MyGooners. Saya boleh membantu anda dengan soalan tentang Arsenal, laman web ini, atau apa-apa sahaja! Apa yang anda ingin tahu?</p>
                    </div>
                </div>

                <!-- Dynamic Messages -->
                <template x-for="message in messages" :key="message.id">
                    <div class="flex items-start space-x-2" :class="message.type === 'user' ? 'justify-end' : ''">
                        <div x-show="message.type === 'bot'" class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs text-white font-bold">MG</span>
                        </div>
                        <div class="rounded-lg p-3 max-w-xs"
                             :class="message.type === 'user' ? 'bg-red-600 text-white' : 'bg-gray-100'">
                            <p class="text-sm" x-text="message.content"></p>
                        </div>
                    </div>
                </template>

                <!-- Typing Indicator -->
                <div x-show="isTyping" class="flex items-start space-x-2">
                    <div class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-xs text-white font-bold">MG</span>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="p-3 border-t border-gray-200">
                <div class="text-xs text-gray-500 mb-2">Soalan Popular:</div>
                <div class="flex flex-wrap gap-2">
                    <button @click="sendQuickMessage('Bagaimana nak join komuniti?')" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors">
                        Join komuniti
                    </button>
                    <button @click="sendQuickMessage('Bagaimana nak beli barang?')" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors">
                        Beli barang
                    </button>
                    <button @click="sendQuickMessage('Tentang Arsenal')" 
                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-xs rounded-full transition-colors">
                        Tentang Arsenal
                    </button>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <input type="text" 
                           x-model="currentMessage"
                           @keydown.enter="sendMessage()"
                           placeholder="Taip mesej anda..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                    <button @click="sendMessage()" 
                            :disabled="!currentMessage.trim()"
                            class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Chatbot Script -->
    <script>
        function faqChatbot() {
            return {
                isOpen: false,
                messages: [],
                currentMessage: '',
                isTyping: false,
                
                faqData: {
                    'join komuniti': 'Untuk menyertai komuniti MyGooners, klik butang "Sertai Kami" di bahagian atas laman web dan daftar akaun baru. Selepas pendaftaran, anda boleh mengambil bahagian dalam perbincangan dan mengakses kandungan eksklusif!',
                    'beli barang': 'Untuk membeli barang, lawati bahagian "Kedai" di menu utama. Anda akan menemui pelbagai barangan Arsenal dan produk komuniti. Pastikan anda log masuk untuk proses pembelian yang lancar.',
                    'tentang arsenal': 'Arsenal FC adalah kelab bola sepak terkenal dari London yang bermain di Premier League. Mereka dikenali sebagai "The Gunners" dan mempunyai sejarah yang kaya dengan 13 kejuaraan liga dan 14 Piala FA.',
                    'perkhidmatan': 'MyGooners menawarkan pelbagai perkhidmatan termasuk analisis perlawanan, podcast eksklusif, dan platform untuk peminat berkongsi pandangan. Lawati bahagian "Perkhidmatan" untuk maklumat lanjut.',
                    'berita': 'Dapatkan berita Arsenal terkini di bahagian "Berita" laman web kami. Kami menyediakan laporan perlawanan, berita pemindahan pemain, dan analisis mendalam tentang prestasi pasukan.',
                    'video': 'Tonton video dan podcast eksklusif di bahagian "Video". Kami ada kandungan seperti ulasan perlawanan, temubual pemain, dan analisis taktik yang menarik.',
                    'help': 'Saya boleh membantu dengan soalan tentang:\n• Cara menyertai komuniti\n• Membeli barangan\n• Maklumat tentang Arsenal\n• Navigasi laman web\n• Perkhidmatan yang tersedia\n\nApa yang anda ingin tahu?'
                },
                
                init() {
                    // On component init, check localStorage for isOpen
                    const saved = localStorage.getItem('faqChatbotOpen');
                    this.isOpen = saved === 'true' ? true : false;
                },
                
                toggleChat() {
                    this.isOpen = !this.isOpen;
                    localStorage.setItem('faqChatbotOpen', this.isOpen);
                    if (this.isOpen) {
                        this.$nextTick(() => {
                            this.scrollToBottom();
                        });
                    }
                },
                
                sendMessage() {
                    if (!this.currentMessage.trim()) return;
                    
                    // Add user message
                    this.messages.push({
                        id: Date.now(),
                        type: 'user',
                        content: this.currentMessage
                    });
                    
                    const userMessage = this.currentMessage.toLowerCase();
                    this.currentMessage = '';
                    
                    // Show typing indicator
                    this.isTyping = true;
                    this.scrollToBottom();
                    
                    // Simulate bot response delay
                    setTimeout(() => {
                        this.isTyping = false;
                        
                        // Find appropriate response
                        let response = this.getBotResponse(userMessage);
                        
                        this.messages.push({
                            id: Date.now(),
                            type: 'bot',
                            content: response
                        });
                        
                        this.scrollToBottom();
                    }, 1000);
                },
                
                sendQuickMessage(message) {
                    this.currentMessage = message;
                    this.sendMessage();
                },
                
                getBotResponse(message) {
                    // Check for keywords in the message
                    for (let keyword in this.faqData) {
                        if (message.includes(keyword)) {
                            return this.faqData[keyword];
                        }
                    }
                    
                    // Default responses for common patterns
                    if (message.includes('halo') || message.includes('hello') || message.includes('hi')) {
                        return 'Halo! Selamat datang ke MyGooners. Bagaimana saya boleh membantu anda hari ini?';
                    }
                    
                    if (message.includes('terima kasih') || message.includes('thank you')) {
                        return 'Sama-sama! Saya sentiasa di sini untuk membantu. Ada lagi yang anda ingin tahu?';
                    }
                    
                    if (message.includes('bye') || message.includes('selamat tinggal')) {
                        return 'Selamat tinggal! Jangan lupa untuk kembali lagi ke MyGooners. COYG! 🔴';
                    }
                    
                    // Default response
                    return 'Maaf, saya tidak faham soalan anda. Boleh cuba tanya dengan cara yang lain? Atau pilih dari soalan popular di bawah. Saya boleh membantu dengan maklumat tentang Arsenal, cara guna laman web ini, atau soalan am lain!';
                },
                
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messageContainer;
                        container.scrollTop = container.scrollHeight;
                    });
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html> 