<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reset Kata Laluan Admin - MyGooners</title>
    <meta name="description" content="Reset kata laluan admin MyGooners">
    
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
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .transition-all {
            transition: all 0.15s ease-in-out;
        }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/official-logo.png') }}" alt="MyGooners Logo" class="h-12 w-auto rounded-lg">
                    <span class="ml-3 text-2xl font-bold text-admin-900">Admin</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Kata Laluan
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan kata laluan baharu untuk akaun admin anda.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 border border-gray-200">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded p-3">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded p-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 font-medium text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4">
                        <div class="font-medium text-red-600 mb-2">{{ __('Aduh! Ada yang tidak kena.') }}</div>
                        <ul class="text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-6" method="POST" action="{{ route('admin.password.update') }}">
                    @csrf
                    
                    <!-- Hidden fields for token and email -->
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Alamat Emel
                            </label>
                            <input id="email" 
                                   type="email" 
                                   value="{{ $email }}" 
                                   disabled
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 bg-gray-50 text-gray-500 rounded-md sm:text-sm">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Kata Laluan Baharu
                            </label>
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required 
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all @error('password') border-red-500 @enderror" 
                                   placeholder="Kata laluan baharu (minimum 8 aksara)">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Sahkan Kata Laluan
                            </label>
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   required 
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-md focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all @error('password_confirmation') border-red-500 @enderror" 
                                   placeholder="Sahkan kata laluan baharu">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-admin-700 hover:bg-admin-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 transition-all">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-admin-500 group-hover:text-admin-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                            Reset Kata Laluan
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Ingat kata laluan anda? 
                        <a href="{{ route('admin.login') }}" class="font-medium text-admin-600 hover:text-admin-500 transition-colors">
                            Log masuk di sini
                        </a>
                    </p>
                </div>

                <!-- Back to Site -->
                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        ← Kembali ke MyGooners
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                MyGooners Panel Admin &copy; {{ date('Y') }} - Akses Selamat Sahaja
            </p>
        </div>
    </div>
</body>
</html>
