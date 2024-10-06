@if ($paginator->hasPages())
    <ul class="pagination" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link"><span aria-hidden="true">&laquo;</span> Next</span>
            </li>
             <li class="page-item disabled">
                <a class="page-link" href="{{ Request::url() }}">Latest</a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><span aria-hidden="true">&laquo;</span> Next</a>
            </li>

            {{-- Latest Page Link --}}
            <li class="page-item">
                <a class="page-link" href="{{ Request::url() }}">Latest</a>
            </li>
        @endif



        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Previous <span aria-hidden="true">&raquo;</span></a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link">Previous <span aria-hidden="true">&raquo;</span></span>
            </li>
        @endif
    </ul>
@endif


