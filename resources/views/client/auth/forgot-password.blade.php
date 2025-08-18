<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Kata Laluan - MyGooners</title>
    <meta name="description" content="Reset kata laluan MyGooners">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <!-- Flash Messages - Floating Right Side -->
    <div class="fixed top-4 right-4 z-50">
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

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative" style="background-image: url('{{ asset('images/hero-section.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black bg-opacity-75"></div>
        <div class="max-w-md w-full relative z-10">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center space-x-2 mb-4 pl-1 pt-1">
                    <a href="{{ route('login') }}" class="flex items-center text-gray-500 text-sm font-normal hover:text-gray-700 focus:outline-none" aria-label="Kembali ke Log Masuk">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Back</span>
                    </a>
                </div>
                
                <div class="text-center mb-6">
                    <svg class="mx-auto h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="mt-4 text-3xl font-extrabold text-gray-900">
                        Lupa Kata Laluan?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Masukkan alamat emel anda dan kami akan menghantar pautan untuk menetapkan semula kata laluan anda.
                    </p>
                </div>

                <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div>
                        <label for="email" class="sr-only">Alamat emel</label>
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="{{ old('email') }}"
                               class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                               placeholder="Alamat emel">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-red-500 group-hover:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2h-12zm0 2h12v12H2.003V4z" />
                                    <path d="M10 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm0-2a1 1 0 100-2 1 1 0 000 2z" />
                                </svg>
                            </span>
                            Hantar Pautan Reset
                        </button>
                    </div>
                </form>

                <div class="text-center mt-8">
                    <p class="text-sm text-gray-600">
                        Ingat kata laluan anda? 
                        <a href="{{ route('login') }}" class="font-medium text-red-600 hover:text-red-500">
                            Log masuk di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
