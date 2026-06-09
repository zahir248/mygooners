@extends('layouts.admin')

@section('title', __('Tetapan Aplikasi'))

@push('breadcrumbs')
    <span class="text-gray-400">/</span>
    <span class="text-gray-900">{{ __('Tetapan') }}</span>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Tetapan Aplikasi') }}</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ __('Urus tetapan sistem dan konfigurasi aplikasi') }}</p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('admin.settings.reset') }}" method="POST" class="inline" 
                          onsubmit="return confirm(@json(__('Adakah anda pasti mahu reset semua tetapan kepada nilai lalai?')))">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Reset kepada Lalai') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="space-y-8">
                @foreach($groupedSettings as $group => $settings)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">{{ trans('admin_settings.groups')[$group] ?? ucfirst($group) }}</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            @foreach($settings as $setting)
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                                    <div class="lg:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ trans('admin_settings.keys')[$setting->key] ?? ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        @php
                                            $settingDescription = trans('admin_settings.descriptions')[$setting->key] ?? $setting->description;
                                        @endphp
                                        @if($settingDescription)
                                            <p class="text-sm text-gray-500">{{ $settingDescription }}</p>
                                        @endif
                                    </div>
                                    
                                    <div class="lg:col-span-2">
                                        @if($setting->type === 'boolean')
                                            <div class="flex items-center">
                                                <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                                <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                                                <input type="hidden" name="settings[{{ $setting->key }}][group]" value="{{ $setting->group }}">
                                                <input type="hidden" name="settings[{{ $setting->key }}][description]" value="{{ $setting->description }}">
                                                
                                                <!-- Hidden input to always send a value -->
                                                <input type="hidden" name="settings[{{ $setting->key }}][value]" value="false">
                                                
                                                <input type="checkbox" 
                                                       name="settings[{{ $setting->key }}][value]" 
                                                       value="true"
                                                       {{ filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                                <span class="ml-2 text-sm text-gray-700">{{ __('Aktif') }}</span>
                                            </div>
                                        @elseif($setting->type === 'integer')
                                            <input type="number" 
                                                   name="settings[{{ $setting->key }}][value]" 
                                                   value="{{ $setting->value }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                            
                                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][group]" value="{{ $setting->group }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][description]" value="{{ $setting->description }}">
                                        @else
                                            <input type="text" 
                                                   name="settings[{{ $setting->key }}][value]" 
                                                   value="{{ $setting->value }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                            
                                            <input type="hidden" name="settings[{{ $setting->key }}][key]" value="{{ $setting->key }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][type]" value="{{ $setting->type }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][group]" value="{{ $setting->group }}">
                                            <input type="hidden" name="settings[{{ $setting->key }}][description]" value="{{ $setting->description }}">
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
                    {{ __('Simpan Tetapan') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 