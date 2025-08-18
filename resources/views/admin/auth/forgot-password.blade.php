<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lupa Kata Laluan Admin - MyGooners</title>
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
                Lupa Kata Laluan?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan alamat emel anda dan kami akan menghantar pautan untuk menetapkan semula kata laluan anda.
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

                <form class="space-y-6" method="POST" action="{{ route('admin.password.email') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Alamat emel
                        </label>
                        <div class="mt-1">
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required 
                                   value="{{ old('email') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all @error('email') border-red-500 @enderror"
                                   placeholder="admin@mygooners.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-admin-700 hover:bg-admin-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 transition-all">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-admin-500 group-hover:text-admin-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2h-12zm0 2h12v12H2.003V4z" />
                                    <path d="M10 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm0-2a1 1 0 100-2 1 1 0 000 2z" />
                                </svg>
                            </span>
                            Hantar Pautan Reset
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
