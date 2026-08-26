@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination">
        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span style="padding:5px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--tx4);">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="padding:5px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--tx2);text-decoration:none;">&laquo;</a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="padding:5px 10px;font-size:12px;color:var(--tx4);">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="padding:5px 10px;border-radius:6px;font-size:12px;background:var(--accent);color:var(--accent-fg);border:1px solid var(--accent);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding:5px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--tx2);text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="padding:5px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--tx2);text-decoration:none;">&raquo;</a>
            @else
                <span style="padding:5px 10px;border:1px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--tx4);">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
