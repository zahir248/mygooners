@extends('layouts.admin')

@section('title', __('Cipta Pengguna'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Cipta Pengguna Baharu') }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ __('Isi maklumat untuk pengguna baharu') }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Kembali ke Senarai Pengguna') }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8 px-4 sm:px-6 lg:px-8 mt-8 max-w-2xl mx-auto">
        @csrf
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Maklumat Pengguna') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nama') }}<span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Emel') }}<span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Peranan') }}<span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('role') border-red-500 @enderror">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>{{ __('Pengguna') }}</option>
                        <option value="writer" {{ old('role') == 'writer' ? 'selected' : '' }}>{{ __('Penulis') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Pentadbir') }}</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Pentadbir Utama') }}</option>
                    </select>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="is_verified" class="ml-2 block text-sm text-gray-900">{{ __('Disahkan?') }}</label>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Kata Laluan') }}<span class="text-red-500">*</span></label>
                    <input type="password" name="password" id="password" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sahkan Kata Laluan') }}<span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">{{ __('Cipta Pengguna') }}</button>
        </div>
    </form>
</div>
@endsection 