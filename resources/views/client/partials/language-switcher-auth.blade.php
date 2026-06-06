@php
    $currentLocale = app()->getLocale();
@endphp
<div class="flex flex-col items-center gap-2" role="group" aria-label="{{ __('client.language') }}">
    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('client.language') }}</span>
    <div class="flex items-center rounded-lg border border-gray-300 bg-gray-100 p-0.5">
        <form method="POST" action="{{ route('locale.switch', 'ms') }}" class="inline">
            @csrf
            <button type="submit"
                    title="{{ __('client.language_ms_full') }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ $currentLocale === 'ms' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                {{ __('client.language_ms') }}
            </button>
        </form>
        <form method="POST" action="{{ route('locale.switch', 'en') }}" class="inline">
            @csrf
            <button type="submit"
                    title="{{ __('client.language_en_full') }}"
                    class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors {{ $currentLocale === 'en' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                {{ __('client.language_en') }}
            </button>
        </form>
    </div>
</div>
