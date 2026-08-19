@if ($paginator->hasPages())
    <ul class="pagination" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                <span aria-hidden="true">@include('partials.icon', ['name' => 'arrow-left', 'size' => 15])</span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    @include('partials.icon', ['name' => 'arrow-left', 'size' => 15])
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">
                    @include('partials.icon', ['name' => 'arrow-left', 'size' => 15, 'class' => 'icon-flip'])
                </a>
            </li>
        @else
            <li class="disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                <span aria-hidden="true">@include('partials.icon', ['name' => 'arrow-left', 'size' => 15, 'class' => 'icon-flip'])</span>
            </li>
        @endif
    </ul>
@endif

<style>
    .icon-flip { transform: scaleX(-1); }
</style>