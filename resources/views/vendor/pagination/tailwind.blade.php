@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-6 space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded cursor-default">
                Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100">
                Prev
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-3 py-2 text-sm text-gray-500">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="px-3 py-2 text-sm font-bold text-white bg-blue-600 border border-blue-600 rounded">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100">
                Next
            </a>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded cursor-default">
                Next
            </span>
        @endif
    </nav>
@endif
