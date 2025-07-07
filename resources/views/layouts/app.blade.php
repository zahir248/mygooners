<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MyGooners - Arsenal Fan Community')</title>
    <meta name="description" content="@yield('meta_description', 'The ultimate Arsenal fan community featuring latest news, videos, services marketplace, and exclusive merchandise.')">

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
                        'arsenal': {
                            50: '#fff5f5',
                            100: '#ffe5e5',
                            200: '#ffcccc',
                            300: '#ffb3b3',
                            400: '#ff6666',
                            500: '#ff3333',
                            600: '#ff0000',
                            700: '#cc0000',
                            800: '#990000',
                            900: '#660000',
                        },
                        'red': {
                            50: '#fff5f5',
                            100: '#ffe5e5',
                            200: '#ffcccc',
                            300: '#ffb3b3',
                            400: '#ff6666',
                            500: '#ff3333',
                            600: '#ff0000',
                            700: '#cc0000',
                            800: '#990000',
                            900: '#660000',
                        }
                    }
                }
            }
        }
    </script>

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
            background-color: #ff0000 !important;
        }
        
        .text-arsenal {
            color: #ff0000 !important;
        }
        
        .border-arsenal {
            border-color: #ff0000 !important;
        }
        
        .hover\:bg-arsenal:hover {
            background-color: #cc0000 !important;
        }
        
        .hover\:text-arsenal:hover {
            color: #ff0000 !important;
        }
        
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #ff0000;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #cc0000;
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="min-h-full">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top bar -->
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="bg-arsenal text-white rounded-lg px-3 py-2 font-bold text-xl">
                                MG
                            </div>
                            <span class="ml-2 text-xl font-bold text-gray-900">MyGooners</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Home
                        </a>
                        <a href="{{ route('blog.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('blog.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Blog
                        </a>
                        <a href="{{ route('videos.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('videos.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Videos
                        </a>
                        <a href="{{ route('services.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('services.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Services
                        </a>
                        <a href="{{ route('shop.index') }}" class="text-gray-900 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('shop.*') ? 'text-red-600 border-b-2 border-red-600' : '' }}">
                            Shop
                        </a>
                    </nav>

                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden lg:block">
                            <form action="{{ request()->routeIs('blog.*') ? route('blog.index') : (request()->routeIs('services.*') ? route('services.index') : route('shop.index')) }}" method="GET" class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search..." 
                                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </form>
                        </div>

                        <!-- Auth -->
                        @auth
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <img class="h-8 w-8 rounded-full bg-gray-300" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=dc2626&color=fff" alt="{{ auth()->user()->name }}">
                                    <span class="ml-2 text-gray-700 font-medium">{{ auth()->user()->name }}</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex space-x-2">
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 text-sm font-medium transition-colors">
                                    Sign in
                                </a>
                                <a href="{{ route('register') }}" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                    Join Us
                                </a>
                            </div>
                        @endauth

                        <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button x-data="{ open: false }" @click="open = !open" type="button" class="text-gray-700 hover:text-red-600 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Navigation -->
                <div x-data="{ open: false }" x-show="open" @click.away="open = false" x-cloak class="md:hidden border-t border-gray-200 py-4">
                    <div class="space-y-1">
                        <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50">Home</a>
                        <a href="{{ route('blog.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50">Blog</a>
                        <a href="{{ route('videos.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50">Videos</a>
                        <a href="{{ route('services.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50">Services</a>
                        <a href="{{ route('shop.index') }}" class="block px-3 py-2 text-base font-medium text-gray-900 hover:text-red-600 hover:bg-gray-50">Shop</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md mx-4 mt-4" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mx-4 mt-4" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

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
                                The ultimate Arsenal fan community featuring latest news, videos, services marketplace, and exclusive merchandise.
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Content</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white transition-colors">Latest News</a></li>
                                <li><a href="{{ route('videos.index') }}" class="text-gray-300 hover:text-white transition-colors">Video Podcasts</a></li>
                                <li><a href="{{ route('blog.category', 'match-reports') }}" class="text-gray-300 hover:text-white transition-colors">Match Reports</a></li>
                                <li><a href="{{ route('blog.category', 'transfer-news') }}" class="text-gray-300 hover:text-white transition-colors">Transfer News</a></li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Community</h3>
                            <ul class="space-y-2 text-sm">
                                <li><a href="{{ route('services.index') }}" class="text-gray-300 hover:text-white transition-colors">Services</a></li>
                                <li><a href="{{ route('shop.index') }}" class="text-gray-300 hover:text-white transition-colors">Shop</a></li>
                                <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-white transition-colors">Join Community</a></li>
                            </ul>
                            
                            <h3 class="text-lg font-semibold mb-4 mt-8">Connect</h3>
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
                        <h3 class="text-2xl font-bold text-white mb-2">Stay in the Loop, Gooner!</h3>
                        <p class="text-gray-300 mb-6">Get the latest Arsenal news, exclusive content, and community updates delivered straight to your inbox.</p>
                        
                        <form class="space-y-4" action="#" method="POST">
                            @csrf
                            <div>
                                <input type="email" 
                                       name="email" 
                                       placeholder="Enter your email address" 
                                       required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-800 text-white placeholder-gray-400 focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all">
                            </div>
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 hover:scale-105 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-gray-900">
                                Subscribe
                            </button>
                        </form>
                        
                        <div class="grid grid-cols-1 gap-2 mt-4 text-sm text-gray-400">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Latest news
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Exclusive content
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                No spam, unsubscribe anytime
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400 text-sm">
                        © {{ date('Y') }} MyGooners. All rights reserved. Built with ❤️ for Arsenal fans.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('scripts')
</body>
</html> 