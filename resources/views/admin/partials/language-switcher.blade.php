@php
    $currentLocale = app()->getLocale();
@endphp
<div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 p-0.5" role="group" aria-label="{{ __('admin.language') }}">
    <form method="POST" action="{{ route('admin.locale.switch', 'ms') }}" class="inline">
        @csrf
        <button type="submit"
                title="{{ __('admin.language_ms_full') }}"
                class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentLocale === 'ms' ? 'bg-white text-red-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:text-gray-900' }}">
            {{ __('admin.language_ms') }}
        </button>
    </form>
    <form method="POST" action="{{ route('admin.locale.switch', 'en') }}" class="inline">
        @csrf
        <button type="submit"
                title="{{ __('admin.language_en_full') }}"
                class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors {{ $currentLocale === 'en' ? 'bg-white text-red-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:text-gray-900' }}">
            {{ __('admin.language_en') }}
        </button>
    </form>
</div>
