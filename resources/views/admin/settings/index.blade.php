@extends('layouts.admin')

@section('title', 'Tetapan Aplikasi')

@push('breadcrumbs')
    <span class="text-gray-400">/</span>
    <span class="text-gray-900">Tetapan</span>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tetapan Aplikasi</h1>
                    <p class="mt-2 text-sm text-gray-600">Urus tetapan sistem dan konfigurasi aplikasi</p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.settings.reset') }}" method="POST" class="inline" 
                          onsubmit="return confirm('Adakah anda pasti mahu reset semua tetapan kepada nilai lalai?')">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset kepada Lalai
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="space-y-8">
                @foreach($groupedSettings as $group => $settings)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 capitalize">{{ $group }}</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            @foreach($settings as $setting)
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                                    <div class="lg:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        @if($setting->description)
                                            <p class="text-sm text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="lg:col-span-2">
                                        @if($setting->type === 'boolean')
                                            <div class="flex items-center">
                                                <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                                <input type="hidden" name="settings[{{ $loop->index }}][type]" value="{{ $setting->type }}">
                                                <input type="hidden" name="settings[{{ $loop->index }}][group]" value="{{ $setting->group }}">
                                                <input type="hidden" name="settings[{{ $loop->index }}][description]" value="{{ $setting->description }}">
                                                
                                                <input type="checkbox" 
                                                       name="settings[{{ $loop->index }}][value]" 
                                                       value="true"
                                                       {{ $setting->value == 'true' ? 'checked' : '' }}
                                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                                <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                            </div>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" 
                                                   name="settings[{{ $loop->index }}][value]" 
                                                   value="{{ $setting->value }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                            
                                            <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][type]" value="{{ $setting->type }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][group]" value="{{ $setting->group }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][description]" value="{{ $setting->description }}">
                                        @else
                                            <input type="text" 
                                                   name="settings[{{ $loop->index }}][value]" 
                                                   value="{{ $setting->value }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                            
                                            <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][type]" value="{{ $setting->type }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][group]" value="{{ $setting->group }}">
                                            <input type="hidden" name="settings[{{ $loop->index }}][description]" value="{{ $setting->description }}">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Tetapan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 