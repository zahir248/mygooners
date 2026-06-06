@if(isset($paginator) && method_exists($paginator, 'total') && $paginator->total() > 0)
@php
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $pageNumbers = [];

    if ($lastPage > 1) {
        if ($lastPage <= 7) {
            $pageNumbers = range(1, $lastPage);
        } else {
            $candidates = collect([1, $lastPage]);
            for ($page = max(2, $currentPage - 2); $page <= min($lastPage - 1, $currentPage + 2); $page++) {
                $candidates->push($page);
            }
            $candidates = $candidates->unique()->sort()->values();

            $previous = null;
            foreach ($candidates as $page) {
                if ($previous !== null && $page - $previous > 1) {
                    $pageNumbers[] = '...';
                }
                $pageNumbers[] = $page;
                $previous = $page;
            }
        }
    }
@endphp
<div class="bg-white px-6 py-3 border-t border-gray-200">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="text-sm text-gray-700">
            {{ __('admin.pagination_showing') }} <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span> {{ __('admin.pagination_to') }} <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span> {{ __('admin.pagination_of') }} <span class="font-medium">{{ $paginator->total() }}</span> {{ $label ?? __('admin.pagination_results') }}
        </div>
        @if($lastPage > 1)
        <nav class="flex flex-wrap items-center gap-1" aria-label="{{ __('admin.pagination_page') }}">
            @if($paginator->onFirstPage())
                <span class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                    {{ __('admin.pagination_previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('admin.pagination_previous') }}
                </a>
            @endif

            @foreach($pageNumbers as $page)
                @if($page === '...')
                    <span class="px-2 py-1 text-sm text-gray-500 select-none">{{ __('…') }}</span>
                @elseif($page == $currentPage)
                    <span class="min-w-[2.25rem] px-3 py-1 border border-red-600 rounded-md text-sm font-medium text-center text-white bg-red-600" aria-current="page">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="min-w-[2.25rem] px-3 py-1 border border-gray-300 rounded-md text-sm text-center text-gray-700 bg-white hover:bg-gray-50">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('admin.pagination_next') }}
                </a>
            @else
                <span class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-500 bg-gray-50 cursor-not-allowed">
                    {{ __('admin.pagination_next') }}
                </span>
            @endif
        </nav>
        @endif
    </div>
</div>
@endif
