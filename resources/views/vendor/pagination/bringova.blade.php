@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination">
        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;justify-content:center;">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:600;color:#8a8f9d;background:rgba(255,255,255,0.45);border:1px solid rgba(26,26,46,0.08);opacity:.55;cursor:not-allowed;">
                    <x-hi name="arrow-left-01" style="font-size:11px;" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:600;color:#1a1a2e;background:rgba(255,255,255,0.6);border:1px solid rgba(26,26,46,0.08);text-decoration:none;transition:background-color .15s ease,color .15s ease;" onmouseover="this.style.background='#fff';this.style.color='#FF6B6B';" onmouseout="this.style.background='rgba(255,255,255,0.6)';this.style.color='#1a1a2e';">
                    <x-hi name="arrow-left-01" style="font-size:11px;" />
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 10px;font-size:13px;color:#8a8f9d;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,#FF6B6B,#FF8E6E);box-shadow:0 6px 14px -8px rgba(255,107,107,0.55);">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:600;color:#1a1a2e;background:rgba(255,255,255,0.6);border:1px solid rgba(26,26,46,0.08);text-decoration:none;transition:background-color .15s ease,color .15s ease;" onmouseover="this.style.background='#fff';this.style.color='#FF6B6B';" onmouseout="this.style.background='rgba(255,255,255,0.6)';this.style.color='#1a1a2e';">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:600;color:#1a1a2e;background:rgba(255,255,255,0.6);border:1px solid rgba(26,26,46,0.08);text-decoration:none;transition:background-color .15s ease,color .15s ease;" onmouseover="this.style.background='#fff';this.style.color='#FF6B6B';" onmouseout="this.style.background='rgba(255,255,255,0.6)';this.style.color='#1a1a2e';">
                    <x-hi name="arrow-right-01" style="font-size:11px;" />
                </a>
            @else
                <span aria-disabled="true" style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:36px;padding:0 12px;border-radius:10px;font-size:13px;font-weight:600;color:#8a8f9d;background:rgba(255,255,255,0.45);border:1px solid rgba(26,26,46,0.08);opacity:.55;cursor:not-allowed;">
                    <x-hi name="arrow-right-01" style="font-size:11px;" />
                </span>
            @endif
        </div>
    </nav>
@endif
