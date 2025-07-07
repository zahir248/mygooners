<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Request Admin Access - MyGooners</title>
    <meta name="description" content="Request admin access to MyGooners admin panel">

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
                    <div class="bg-admin-800 text-white rounded-lg px-4 py-3 font-bold text-xl">
                        MG
                    </div>
                    <span class="ml-3 text-2xl font-bold text-admin-900">Admin</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Request admin access
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Apply for MyGooners administration privileges
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 border border-gray-200">
                <!-- Info Notice -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Admin access requests are reviewed manually. You'll be contacted once your application is processed.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-4">
                        <div class="font-medium text-red-600 mb-2">{{ __('Please fix the following errors:') }}</div>
                        <ul class="text-sm text-red-600 bg-red-50 border border-red-200 rounded p-3 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-6" method="POST" action="{{ route('admin.register') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full name <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   autocomplete="name" 
                                   required
                                   value="{{ old('name') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all"
                                   placeholder="John Smith">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required
                                   value="{{ old('email') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all"
                                   placeholder="john@example.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm password <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   autocomplete="new-password" 
                                   required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Role/Position -->
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">
                            Requested role/position
                        </label>
                        <div class="mt-1">
                            <select id="position" 
                                    name="position" 
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all">
                                <option value="">Select a role</option>
                                <option value="content_moderator" {{ old('position') == 'content_moderator' ? 'selected' : '' }}>Content Moderator</option>
                                <option value="user_moderator" {{ old('position') == 'user_moderator' ? 'selected' : '' }}>User Moderator</option>
                                <option value="service_reviewer" {{ old('position') == 'service_reviewer' ? 'selected' : '' }}>Service Reviewer</option>
                                <option value="product_manager" {{ old('position') == 'product_manager' ? 'selected' : '' }}>Product Manager</option>
                                <option value="admin" {{ old('position') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700">
                            Why do you need admin access?
                        </label>
                        <div class="mt-1">
                            <textarea id="reason" 
                                      name="reason" 
                                      rows="3"
                                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-admin-500 focus:border-admin-500 sm:text-sm transition-all"
                                      placeholder="Explain your background and why you need administrative access...">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" 
                                   name="terms" 
                                   type="checkbox" 
                                   required
                                   class="focus:ring-admin-500 h-4 w-4 text-admin-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I agree to the admin terms and conditions <span class="text-red-500">*</span>
                            </label>
                            <p class="text-gray-500">
                                I understand that admin privileges come with responsibility and will be used appropriately.
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-admin-700 hover:bg-admin-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 transition-all">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-admin-500 group-hover:text-admin-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </span>
                            Submit admin access request
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Already have admin access?</span>
                        </div>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('admin.login') }}" class="font-medium text-admin-600 hover:text-admin-500 transition-colors">
                            Sign in to admin panel
                        </a>
                    </p>
                </div>

                <!-- Back to Site -->
                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        ← Back to MyGooners
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                MyGooners Admin Panel &copy; {{ date('Y') }} - Secure Access Only
            </p>
        </div>
    </div>
</body>
</html> 