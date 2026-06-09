@php
    $type = $type ?? 'info';
    $message = $message ?? '';
    $items = $items ?? [];
    $dismissible = $dismissible ?? true;
    $fixed = $fixed ?? false;
    $class = $class ?? '';

    $type = in_array($type, ['success', 'error', 'warning', 'info', 'status'], true) ? $type : 'info';
    $resolvedType = $type === 'status' ? 'success' : $type;

    $styles = [
        'success' => [
            'container' => 'bg-green-50 border-green-400',
            'icon' => 'text-green-400',
            'text' => 'text-green-700',
            'button' => 'text-green-700',
            'icon_path' => 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
        ],
        'error' => [
            'container' => 'bg-red-50 border-red-400',
            'icon' => 'text-red-400',
            'text' => 'text-red-700',
            'button' => 'text-red-700',
            'icon_path' => 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
        ],
        'warning' => [
            'container' => 'bg-yellow-50 border-yellow-400',
            'icon' => 'text-yellow-400',
            'text' => 'text-yellow-700',
            'button' => 'text-yellow-700',
            'icon_path' => 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
        ],
        'info' => [
            'container' => 'bg-blue-50 border-blue-400',
            'icon' => 'text-blue-400',
            'text' => 'text-blue-700',
            'button' => 'text-blue-700',
            'icon_path' => 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
        ],
    ];

    $style = $styles[$resolvedType];
    $wrapperClass = trim(($fixed ? 'fixed top-4 right-4 z-50 max-w-sm w-full ' : '') . $class);
@endphp

<div class="{{ $wrapperClass }}" role="alert">
    <div class="{{ $style['container'] }} border-l-4 p-4 {{ $fixed ? 'm-4 shadow-lg' : '' }}">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 {{ $style['icon'] }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="{{ $style['icon_path'] }}" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                @if($message)
                    <p class="text-sm {{ $style['text'] }}">{{ $message }}</p>
                @endif
                @if(!empty($items))
                    <ul class="mt-2 list-disc list-inside space-y-1 text-sm {{ $style['text'] }}">
                        @foreach($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
                @isset($slot)
                    {!! $slot !!}
                @endisset
            </div>
            @if($dismissible)
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button"
                                onclick="this.closest('[role=alert]').remove()"
                                class="inline-flex rounded-md p-1.5 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 {{ $style['button'] }}">
                            <span class="sr-only">{{ __('Dismiss') }}</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
