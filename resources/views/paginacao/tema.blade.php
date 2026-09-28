@if ($paginator->hasPages())
<style>
.ltpag{display:flex;align-items:center;justify-content:center;gap:6px;flex-wrap:wrap;margin-top:8px;}
.ltpag-btn{
    min-width:36px;height:36px;padding:0 10px;border-radius:10px;
    background:rgba(15,23,42,0.95);border:1px solid rgba(59,130,246,0.2);
    color:#94a3b8;font-size:13px;font-weight:700;text-decoration:none;
    display:flex;align-items:center;justify-content:center;transition:all .15s;
}
.ltpag-btn:hover{border-color:rgba(6,182,212,.5);color:#06b6d4;}
.ltpag-btn.active{background:linear-gradient(135deg,#06b6d4,#0ea5e9);border-color:transparent;color:#fff;cursor:default;}
.ltpag-btn.disabled{opacity:.35;cursor:not-allowed;pointer-events:none;}
.ltpag-dots{color:#475569;font-size:13px;padding:0 4px;}
</style>
<nav class="ltpag" role="navigation" aria-label="Paginação">

    {{-- Anterior --}}
    @if ($paginator->onFirstPage())
        <span class="ltpag-btn disabled">‹</span>
    @else
        <a class="ltpag-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
    @endif

    {{-- Páginas --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="ltpag-dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="ltpag-btn active">{{ $page }}</span>
                @else
                    <a class="ltpag-btn" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Próximo --}}
    @if ($paginator->hasMorePages())
        <a class="ltpag-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
    @else
        <span class="ltpag-btn disabled">›</span>
    @endif

</nav>
@endif