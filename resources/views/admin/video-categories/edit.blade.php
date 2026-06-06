@extends('layouts.admin')

@section('title', __('Sunting Kategori Video'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.video-categories.index') }}" class="text-sm text-red-600 hover:text-red-700">&larr; Kembali ke Kategori Video</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ __('Sunting Kategori Video') }}</h1>
    </div>

    <div class="max-w-xl bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.video-categories.update', $videoCategory) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nama Kategori') }}<span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $videoCategory->name) }}" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($videoCategory->videosCount() > 0)
                    <p class="mt-1 text-sm text-amber-600">Menukar nama akan mengemas kini {{ $videoCategory->videosCount() }} video sedia ada.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Slug') }}</label>
                <p class="text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">{{ $videoCategory->slug }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('Slug dikemas kini secara automatik apabila nama ditukar.') }}</p>
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Susunan') }}</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $videoCategory->sort_order) }}" min="0"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $videoCategory->is_active) ? 'checked' : '' }}
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">{{ __('Aktif') }}</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">{{ __('Kemas Kini') }}</button>
                <a href="{{ route('admin.video-categories.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">{{ __('Batal') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
