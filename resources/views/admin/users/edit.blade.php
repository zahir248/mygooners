@extends('layouts.admin')

@section('title', __('Sunting Pengguna'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-500">{{ __('Pengguna') }}</a>
                    </li>
                    <li class="text-gray-300">/</li>
                    <li>
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-gray-400 hover:text-gray-500">{{ $user->name }}</a>
                    </li>
                    <li class="text-gray-300">/</li>
                    <li class="text-gray-500">{{ __('Sunting') }}</li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Sunting Pengguna') }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ __('Kemas kini maklumat profil dan kebenaran pengguna') }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.users.show', $user->id) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('Kembali ke Butiran Pengguna') }}
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-8 px-4 sm:px-6 lg:px-8 mt-8 max-w-2xl mx-auto">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Maklumat Asas') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nama') }}<span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Emel') }}<span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Telefon') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Lokasi') }}</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $user->location) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Bio') }}</label>
                    <textarea name="bio" id="bio" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('bio') border-red-500 @enderror">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Akaun & Kebenaran') }}</h3>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Peranan') }}<span class="text-red-500">*</span></label>
                    <select name="role" id="role" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('role') border-red-500 @enderror" @if($user->id === auth()->id() && auth()->user()->role === 'super_admin') disabled @endif>
                        @foreach($assignableRoles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                @if($role === 'super_admin')
                                    {{ __('Pentadbir Utama') }}
                                @elseif($role === 'admin')
                                    {{ __('Pentadbir') }}
                                @elseif($role === 'writer')
                                    {{ __('Penulis') }}
                                @else
                                    {{ __('Pengguna') }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @if($user->id === auth()->id() && auth()->user()->role === 'super_admin')
                        <input type="hidden" name="role" value="super_admin">
                        <p class="mt-1 text-sm text-gray-500">{{ __('Anda tidak boleh menukar peranan akaun sendiri.') }}</p>
                    @endif
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}<span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                        <option value="pending" {{ old('status', $user->status) === 'pending' ? 'selected' : '' }}>{{ __('Menunggu') }}</option>
                        <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>{{ __('Digantung') }}</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified', $user->is_verified) ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="is_verified" class="ml-2 block text-sm text-gray-900">{{ __('Disahkan?') }}</label>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Tukar Kata Laluan') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Biarkan kosong jika tidak mahu menukar kata laluan.') }}</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Kata Laluan Baharu') }}</label>
                    <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Sahkan Kata Laluan Baharu') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.users.show', $user->id) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm font-medium">
                {{ __('Batal') }}
            </a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 text-sm font-medium">
                {{ __('Simpan Perubahan') }}
            </button>
        </div>
    </form>
</div>
@endsection
