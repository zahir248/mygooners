@extends('layouts.admin')

@section('title', __('Tambah Kategori Produk'))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.product-categories.index') }}" class="text-sm text-red-600 hover:text-red-700">&larr; Kembali ke Kategori Produk</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ __('Tambah Kategori Produk') }}</h1>
    </div>

    <div class="max-w-xl bg-white shadow rounded-lg p-6">
        <form action="{{ route('admin.product-categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Kunci Sistem') }}<span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       placeholder="{{ __('Contoh: Jerseys, Accessories') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">{{ __('Nilai ini disimpan dalam pangkalan data produk.') }}</p>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="label" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Label Paparan') }}<span class="text-red-500">*</span></label>
                <input type="text" name="label" id="label" value="{{ old('label') }}" required
                       placeholder="{{ __('Contoh: Jersi, Aksesori') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500 @error('label') border-red-500 @enderror">
                @error('label')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Susunan') }}</label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:border-red-500">
                <p class="mt-1 text-sm text-gray-500">{{ __('Nombor lebih kecil dipaparkan dahulu dalam senarai.') }}</p>
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">{{ __('Aktif (boleh dipilih semasa cipta produk)') }}</label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">{{ __('Simpan') }}</button>
                <a href="{{ route('admin.product-categories.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">{{ __('Batal') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
