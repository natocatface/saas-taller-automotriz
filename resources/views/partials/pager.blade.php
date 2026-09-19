@if ($paginator->hasPages())
    <div class="pagination">
        <span class="cell-muted" style="margin-right:auto;">
            Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        </span>
        @if ($paginator->onFirstPage())
            <span aria-disabled="true"><i class="fa-solid fa-angle-left"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-angle-left"></i></a>
        @endif

        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"><i class="fa-solid fa-angle-right"></i></a>
        @else
            <span aria-disabled="true"><i class="fa-solid fa-angle-right"></i></span>
        @endif
    </div>
@endif
