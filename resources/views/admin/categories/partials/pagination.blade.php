@if ($categories->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous --}}
            @if ($categories->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $categories->previousPageUrl() }}">Previous</a></li>
            @endif

            {{-- Pages --}}
            @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $categories->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach

            {{-- Next --}}
            @if ($categories->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $categories->nextPageUrl() }}">Next</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
            @endif
        </ul>
    </nav>
@endif
