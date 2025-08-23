@if ($users->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($users->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Next Page Link --}}
            @if ($users->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">Next</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
            @endif
        </ul>
    </nav>
@endif
