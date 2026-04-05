@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination">
            {{-- Older posts = higher page number --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}">&laquo; Older</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">&laquo; Older</span>
                </li>
            @endif

            {{-- Latest = page 1 (newest posts) --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Latest</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ Request::url() }}">Latest</a>
                </li>
            @endif

            {{-- Newer posts = lower page number --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Newer &raquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Newer &raquo;</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
