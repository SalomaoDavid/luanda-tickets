@extends('layouts.app')

@section('title', 'Explorar Eventos — Luanda Bilhetes')

@section('content')

<style>
:root {
  --bg: #070a12; --surface: rgba(15,23,42,0.92); --surface2: rgba(20,30,55,0.95);
  --border: rgba(59,130,246,0.2); --accent: #06b6d4; --accent2: #0ea5e9;
  --gold: #f59e0b; --text: #f1f5f9; --muted: #94a3b8;
  --green: #10b981; --red: #f43f5e; --purple: #a78bfa;
}

.explore-hero { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.page-header-eyebrow { font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--accent); margin-bottom:4px; }
.page-header-title { font-size:24px; font-weight:700; letter-spacing:-1px; line-height:1; color:#fff; }
@media(min-width:768px){ .page-header-title { font-size:38px; } }
.page-header-title span { color:var(--accent); }
.page-header-sub { font-size:12px; color:var(--muted); margin-top:4px; }

.search-row { display:flex; gap:10px; align-items:center; margin-bottom:20px; }
.page-search { flex:1; display:flex; align-items:center; gap:10px; background:rgba(15,23,42,0.95); border:1px solid rgba(59,130,246,0.3); border-radius:14px; padding:10px 16px; transition:border-color 0.2s; }
.page-search:focus-within { border-color:var(--accent); box-shadow:0 0 0 3px rgba(6,182,212,0.15); }
.page-search input { background:none; border:none; outline:none; color:#fff; font-size:14px; width:100%; }
.page-search input::placeholder { color:var(--muted); }
.btn-filtros-mobile { display:flex; align-items:center; gap:6px; padding:10px 16px; border-radius:14px; flex-shrink:0; background:rgba(6,182,212,0.15); border:1px solid rgba(6,182,212,0.4); color:var(--accent); font-size:13px; font-weight:700; cursor:pointer; }
@media(min-width:768px){ .btn-filtros-mobile { display:none; } }

.fchip { flex-shrink:0; display:flex; align-items:center; gap:6px; padding:7px 14px; border-radius:20px; border:1px solid rgba(59,130,246,0.25); background:rgba(15,23,42,0.90); color:#94a3b8; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.2s; white-space:nowrap; text-decoration:none; }
.fchip:hover { color:#fff; border-color:rgba(59,130,246,0.5); }
.fchip.active { background:rgba(6,182,212,0.2); border-color:rgba(6,182,212,0.6); color:var(--accent); }
.fchip .dot { width:7px; height:7px; border-radius:50%; background:currentColor; opacity:0.7; }
.filters-desktop { display:none; }
@media(min-width:768px){ .filters-desktop { display:flex; gap:8px; overflow-x:auto; scrollbar-width:none; margin-bottom:28px; } .filters-desktop::-webkit-scrollbar { display:none; } }
.active-filter-row { display:flex; gap:8px; overflow-x:auto; scrollbar-width:none; margin-bottom:16px; }
@media(min-width:768px){ .active-filter-row { display:none; } }

.sec-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.sec-title { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:700; color:#fff; }
@media(min-width:768px){ .sec-title { font-size:19px; } }
.sec-icon { width:30px; height:30px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:14px; }
.sec-icon.cyan { background:rgba(6,182,212,0.25); }
.sec-icon.gold { background:rgba(245,158,11,0.25); }
.sec-icon.red  { background:rgba(244,63,94,0.25); }
.sec-count { font-size:11px; color:#94a3b8; font-weight:600; background:rgba(15,23,42,0.90); border:1px solid rgba(59,130,246,0.2); border-radius:20px; padding:3px 10px; }

.cat-strip { display:flex; gap:10px; overflow-x:auto; scrollbar-width:none; margin-bottom:24px; padding-bottom:4px; }
.cat-strip::-webkit-scrollbar { display:none; }
.cat-strip-item { flex-shrink:0; display:flex; flex-direction:column; align-items:center; gap:5px; padding:12px 14px; border-radius:14px; min-width:80px; border:1px solid rgba(59,130,246,0.2); background:rgba(15,23,42,0.92); cursor:pointer; transition:all 0.22s; text-align:center; text-decoration:none; }
.cat-strip-item:hover { background:rgba(20,30,55,0.97); transform:translateY(-2px); }
.cat-strip-item.active { border-color:currentColor; background:rgba(20,30,60,0.97); }
.cat-strip-emoji { font-size:22px; display:inline-block; transition:transform .25s ease; }
.cat-strip-item:hover .cat-strip-emoji { transform:scale(1.25) rotate(-6deg); }
.cat-strip-name { font-size:10px; font-weight:700; white-space:nowrap; }
.cat-strip-count { font-size:9px; color:#94a3b8; }

.hoje-strip { display:flex; gap:12px; overflow-x:auto; scrollbar-width:none; padding-bottom:4px; margin-bottom:28px; }
.hoje-strip::-webkit-scrollbar { display:none; }
.hoje-card { flex-shrink:0; width:155px; background:rgba(15,23,42,0.95); border:1px solid rgba(59,130,246,0.2); border-radius:16px; overflow:hidden; cursor:pointer; transition:all 0.22s; text-decoration:none; display:block; }
@media(min-width:768px){ .hoje-card { width:185px; } }
.hoje-card:hover { transform:translateY(-3px); border-color:rgba(6,182,212,0.5); box-shadow:0 16px 32px rgba(0,0,0,0.5); }
.hoje-card-img { height:85px; position:relative; overflow:hidden; }
@media(min-width:768px){ .hoje-card-img { height:100px; } }
.hoje-card-img img { width:100%; height:100%; object-fit:cover; }
.hoje-card-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:32px; }
.hoje-card-time { position:absolute; bottom:8px; left:8px; background:rgba(7,10,18,0.92); backdrop-filter:blur(8px); border:1px solid rgba(6,182,212,0.3); border-radius:6px; font-size:10px; font-weight:700; color:var(--accent); padding:3px 8px; }
.hoje-card-body { padding:10px; }
.hoje-card-name { font-size:11px; font-weight:700; line-height:1.3; margin-bottom:4px; color:#fff; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.hoje-card-place { font-size:10px; color:#94a3b8; margin-bottom:5px; }
.hoje-card-footer { display:flex; justify-content:space-between; align-items:center; }
.hoje-card-price { font-size:10px; font-weight:700; color:var(--gold); }
.hoje-card-price.free { color:var(--green); }
.hoje-card-btn { padding:3px 8px; border-radius:6px; background:rgba(6,182,212,0.2); border:1px solid rgba(6,182,212,0.4); color:var(--accent); font-size:9px; font-weight:700; }

/* ── GRID DE EVENTOS ── */
.events-grid { display:grid; grid-template-columns:1fr; gap:14px; margin-bottom:36px; }
@media(min-width:480px){ .events-grid { grid-template-columns:repeat(2,1fr); } }
@media(min-width:1024px){ .events-grid { grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:18px; margin-bottom:48px; } }

.ev-card { background:rgba(15,23,42,0.95); border:1px solid rgba(59,130,246,0.2); border-radius:18px; overflow:hidden; cursor:pointer; transition:transform 0.25s,box-shadow 0.25s,border-color 0.25s; position:relative; }
.ev-card:hover { transform:translateY(-4px); box-shadow:0 20px 40px rgba(0,0,0,0.6); border-color:rgba(6,182,212,0.4); }

/* Imagem + vídeo overlay */
.ev-img { position:relative; height:160px; overflow:hidden; }
@media(min-width:768px){ .ev-img { height:180px; } }
.ev-img img { width:100%; height:100%; object-fit:cover; transition:transform 0.4s; }
.ev-card:hover .ev-img img { transform:scale(1.06); }
.ev-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:60px; }
.ev-img::after { content:''; position:absolute; inset:0; background:linear-gradient(to bottom,transparent 40%,rgba(7,10,18,0.8) 100%); pointer-events:none; }

/* Overlay de vídeo — aparece no hover */
.ev-video-wrap { position:absolute; inset:0; opacity:0; transition:opacity .3s; background:#000; z-index:1; pointer-events:none; }
.ev-img:hover .ev-video-wrap { opacity:1; pointer-events:auto; }
.ev-video-wrap iframe,
.ev-video-wrap video { width:100%; height:100%; border:none; object-fit:cover; }

/* Botão som */
.ev-sound-btn { position:absolute; bottom:8px; right:8px; z-index:3; width:30px; height:30px; border-radius:50%; background:rgba(0,0,0,.6); backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.3); color:#fff; font-size:13px; display:none; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; }
.ev-img:hover .ev-sound-btn { display:flex; }
.ev-sound-btn:hover { background:rgba(6,182,212,.7); }

/* Círculo de vídeo */
.ev-video-circle { position:absolute; top:8px; right:8px; z-index:3; width:34px; height:34px; border-radius:50%; border:2px solid rgba(255,255,255,.8); overflow:hidden; cursor:pointer; background:#000; box-shadow:0 2px 8px rgba(0,0,0,.4); transition:transform .2s; }
.ev-video-circle:hover { transform:scale(1.1); }
.ev-video-circle img { width:100%; height:100%; object-fit:cover; }
.ev-video-circle-play { position:absolute; inset:0; background:rgba(0,0,0,.45); display:flex; align-items:center; justify-content:center; font-size:11px; color:#fff; }

/* Modal vídeo fullscreen */
.ev-video-modal { display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.95); align-items:center; justify-content:center; flex-direction:column; }
.ev-video-modal.open { display:flex; }
.ev-video-modal-close { position:absolute; top:16px; right:16px; width:40px; height:40px; border-radius:50%; background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.ev-video-modal iframe { width:90vw; max-width:800px; height:50vw; max-height:450px; border-radius:16px; border:none; }

.ev-badges { position:absolute; top:10px; left:10px; right:10px; display:flex; justify-content:space-between; align-items:flex-start; z-index:2; }
.ev-badge { font-size:9px; font-weight:700; letter-spacing:0.6px; text-transform:uppercase; padding:3px 8px; border-radius:20px; }
.ev-badge.hot  { background:rgba(244,63,94,0.95);  color:#fff; }
.ev-badge.free { background:rgba(16,185,129,0.95); color:#fff; }
.ev-badge.new  { background:rgba(14,165,233,0.95); color:#fff; }
.ev-badge.soon { background:rgba(245,158,11,0.95); color:#000; }
.ev-date-pill { position:absolute; bottom:10px; left:10px; z-index:2; background:rgba(7,10,18,0.90); backdrop-filter:blur(10px); border:1px solid rgba(59,130,246,0.3); border-radius:7px; padding:3px 8px; font-size:10px; font-weight:700; color:#fff; display:flex; align-items:center; gap:5px; }

.ev-body { padding:12px 14px 14px; }
.ev-cat { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:4px; }
.ev-title { font-size:14px; font-weight:700; line-height:1.25; margin-bottom:7px; color:#fff; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
@media(min-width:768px){ .ev-title { font-size:16px; } }
.ev-info { display:flex; flex-direction:column; gap:3px; margin-bottom:10px; }
.ev-info-row { display:flex; align-items:center; gap:6px; font-size:11px; color:#94a3b8; }
.ev-tickets { margin-bottom:10px; }
.ev-tickets-row { display:flex; justify-content:space-between; font-size:10px; margin-bottom:4px; }
.ev-tickets-row span { color:#94a3b8; }
.ev-tickets-row strong { color:#fff; }
.ev-bar { height:4px; background:rgba(255,255,255,0.1); border-radius:2px; overflow:hidden; }
.ev-bar-fill { height:100%; border-radius:2px; width:0; transition:width 1.1s cubic-bezier(.16,1,.3,1); }
.ev-bar-fill.ok   { background:linear-gradient(to right,var(--accent),var(--accent2)); }
.ev-bar-fill.warn { background:linear-gradient(to right,var(--gold),#f97316); }
.ev-bar-fill.crit { background:linear-gradient(to right,var(--red),#f97316); }
.ev-footer { display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid rgba(59,130,246,0.15); gap:8px; }
.ev-price { font-size:17px; font-weight:700; color:var(--gold); line-height:1; }
.ev-price small { font-size:10px; color:#94a3b8; font-weight:400; }
.ev-price.free { color:var(--green); font-size:13px; }
.ev-actions { display:flex; gap:6px; align-items:center; }
.ev-like { width:30px; height:30px; border-radius:8px; background:rgba(20,30,55,0.95); border:1px solid rgba(59,130,246,0.2); display:flex; align-items:center; justify-content:center; font-size:12px; cursor:pointer; transition:all 0.2s; }
.ev-like:hover { transform:scale(1.15); }
.ev-buy { padding:6px 12px; border-radius:9px; background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; font-size:11px; font-weight:700; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }

/* Timeline */
.timeline { margin-bottom:36px; }
.timeline-day { margin-bottom:24px; }
.timeline-day-header { display:flex; align-items:center; gap:10px; margin-bottom:12px; flex-wrap:wrap; }
.timeline-day-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.timeline-day-label { font-size:14px; font-weight:700; color:#fff; }
.timeline-day-label span { color:#94a3b8; font-size:12px; font-weight:400; margin-left:6px; }
.timeline-day-line { flex:1; height:1px; background:rgba(59,130,246,0.15); min-width:20px; }
.timeline-row { display:flex; gap:10px; overflow-x:auto; scrollbar-width:none; padding-bottom:4px; }
.timeline-row::-webkit-scrollbar { display:none; }
.tl-card { flex-shrink:0; width:210px; display:flex; gap:10px; align-items:center; background:rgba(15,23,42,0.95); border:1px solid rgba(59,130,246,0.2); border-radius:14px; padding:12px; cursor:pointer; transition:all 0.2s; text-decoration:none; }
.tl-card:hover { border-color:rgba(6,182,212,0.5); transform:translateY(-2px); }
.tl-card-emoji { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; background:rgba(20,30,55,0.95); }
.tl-card-time { font-size:10px; color:var(--accent); font-weight:700; margin-bottom:2px; }
.tl-card-name { font-size:12px; font-weight:700; line-height:1.3; margin-bottom:3px; color:#fff; }
.tl-card-place { font-size:10px; color:#94a3b8; }
.tl-card-price { font-size:11px; color:var(--gold); font-weight:700; margin-top:4px; }

.empty-state { text-align:center; padding:60px 20px; }
.empty-state p { color:#94a3b8; font-size:16px; margin-top:12px; }

.drawer-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); }
.drawer-overlay.open { display:flex; align-items:flex-end; }
.drawer-box { width:100%; background:rgba(10,16,30,0.99); border:1px solid rgba(59,130,246,0.2); border-radius:24px 24px 0 0; padding:20px 20px 36px; max-height:85vh; overflow-y:auto; }
.drawer-handle { width:36px; height:4px; border-radius:2px; background:rgba(59,130,246,0.3); margin:0 auto 18px; }
.drawer-title { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.drawer-title h3 { font-size:16px; font-weight:800; color:#fff; }
.drawer-close { background:none; border:none; font-size:20px; color:#64748b; cursor:pointer; }
.drawer-close:hover { color:#fff; }
.drawer-section-label { font-size:10px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:#64748b; margin-bottom:12px; }

.scroll-reveal { opacity:0; transform:translateY(30px) scale(.95); transition:opacity .7s cubic-bezier(.16,1,.3,1),transform .7s cubic-bezier(.16,1,.3,1); }
.scroll-reveal.is-visible { opacity:1; transform:translateY(0) scale(1); }

.page-header-title span { background:linear-gradient(90deg,var(--accent),#a78bfa,var(--accent2),var(--accent)); background-size:300% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; animation:shimmerText 3s linear infinite; }
@keyframes shimmerText { to { background-position:200% center; } }
.explore-hero { animation:heroFadeIn .5s ease-out both; }
.search-row { animation:heroFadeIn .5s ease-out .08s both; }
@keyframes heroFadeIn { from{opacity:0;transform:translateY(-10px);} to{opacity:1;transform:translateY(0);} }
@media (prefers-reduced-motion:reduce) { *, .scroll-reveal { animation:none!important; transition:none!important; opacity:1!important; transform:none!important; -webkit-text-fill-color:currentColor!important; background:none!important; } }

/* ══ ANIMAÇÕES ══ */
@keyframes fadeUp { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }
@keyframes badgePulse { 0%,100%{box-shadow:0 0 0 rgba(255,255,255,0);transform:scale(1);} 50%{box-shadow:0 0 14px rgba(255,255,255,.45);transform:scale(1.05);} }
@keyframes barGlow { 0%,100%{filter:brightness(1);} 50%{filter:brightness(1.4);} }
@keyframes heartBeat { 0%,100%{transform:scale(1.15);} 50%{transform:scale(1.32);} }
@keyframes glowPulseCyan { 0%,100%{box-shadow:0 4px 12px rgba(6,182,212,.35);} 50%{box-shadow:0 4px 22px rgba(6,182,212,.65);} }
@keyframes dotPulse { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.5);opacity:.65;} }
@keyframes floatSlow { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-4px);} }
@keyframes kenBurns { 0%{transform:scale(1);} 100%{transform:scale(1.08);} }
@keyframes spinRing { 0%{transform:rotate(0deg);} 100%{transform:rotate(360deg);} }
@keyframes glowBreatheOpacity { 0%,100%{opacity:.2;} 50%{opacity:.7;} }

/* Badges com pulso */
.ev-badge.hot  { animation:badgePulse 1.6s ease-in-out infinite; }
.ev-badge.new  { animation:badgePulse 1.9s ease-in-out infinite; }
.ev-bar-fill.crit { animation:barGlow 1.4s ease-in-out infinite; }
.ev-like:hover { animation:heartBeat .7s ease-in-out infinite; }
.ev-buy { animation:glowPulseCyan 2.4s ease-in-out infinite; }
.btn-filtros-mobile { animation:glowPulseCyan 2.6s ease-in-out infinite; }
.timeline-day-dot { animation:dotPulse 1.8s ease-in-out infinite; }
.sec-icon { animation:floatSlow 3s ease-in-out infinite; }

/* Ken Burns nas imagens */
.ev-img img { animation:kenBurns 9s ease-in-out infinite alternate; }
.ev-card:hover .ev-img img { animation-play-state:paused; transform:scale(1.1); }
.ev-card:hover { transform:translateY(-8px) scale(1.015); }
.hoje-card:hover { transform:translateY(-6px) scale(1.03); }
.cat-strip-item:hover { transform:translateY(-5px) scale(1.06); }

/* Shimmer nos preços */
.ev-price:not(.free), .hoje-card-price:not(.free), .tl-card-price {
    background:linear-gradient(90deg,var(--gold) 0%,#fde68a 50%,var(--gold) 100%);
    background-size:200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    background-clip:text; animation:shimmerText 2.5s linear infinite;
}

/* Anel giratório nos cards */
.ev-card,.hoje-card,.tl-card,.cat-strip-item { position:relative; }
.ev-card::before,.hoje-card::before,.tl-card::before,.cat-strip-item::before {
    content:''; position:absolute; inset:-1.5px; border-radius:inherit; padding:2px;
    background:conic-gradient(from 0deg, transparent 0%, var(--accent) 12%, transparent 28%, transparent 70%, var(--gold) 85%, transparent 100%);
    animation:spinRing 4.5s linear infinite;
    -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite:xor; mask-composite:exclude;
    pointer-events:none; z-index:1;
}
.ev-card > *,.hoje-card > *,.tl-card > *,.cat-strip-item > * { position:relative; z-index:2; }

/* Respiração de luz por dentro */
.ev-card::after,.hoje-card::after {
    content:''; position:absolute; inset:0; border-radius:inherit; pointer-events:none; z-index:0;
    background:radial-gradient(circle at 50% 20%, rgba(6,182,212,.22), transparent 65%);
    animation:glowBreatheOpacity 3.5s ease-in-out infinite;
}
</style>

@php $catAtiva = request('categoria'); $subAtiva = request('subcategoria'); @endphp

{{-- HERO --}}
<div class="explore-hero">
    <div class="explore-hero-left">
        <div class="page-header-eyebrow">🗺 Descobrir</div>
        <div class="page-header-title">Explorar <span>Eventos</span></div>
        <div class="page-header-sub">{{ $eventos->total() }} eventos disponíveis</div>
    </div>
</div>

{{-- SEARCH --}}
<div class="search-row">
    <form action="{{ route('eventos.todos') }}" method="GET" style="flex:1;">
        <div class="page-search">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar eventos, artistas, locais...">
        </div>
    </form>
    <button class="btn-filtros-mobile" onclick="document.getElementById('drawer-filtros').classList.add('open')">☰ Filtros</button>
</div>

{{-- ACTIVE FILTER BADGE MOBILE --}}
@if(request('filter') || $catAtiva)
<div class="active-filter-row">
    @if(request('filter'))
    <span class="fchip active">
        @php $labels = ['hoje'=>'📅 Hoje','amanha'=>'📆 Amanhã','fds'=>'🗓 Fim-de-semana','semana'=>'📌 Esta semana','populares'=>'🔥 Populares','novos'=>'✨ Novos']; @endphp
        {{ $labels[request('filter')] ?? request('filter') }}
        <a href="{{ route('eventos.todos') }}" style="color:currentColor;margin-left:4px;text-decoration:none;">✕</a>
    </span>
    @endif
    @if($catAtiva && $categorias->firstWhere('id', $catAtiva))
    @php $catN = $categorias->firstWhere('id', $catAtiva); @endphp
    <span class="fchip active">{{ $catN->emoji }} {{ $catN->nome }} <a href="{{ route('eventos.todos') }}" style="color:currentColor;margin-left:4px;text-decoration:none;">✕</a></span>
    @endif
</div>
@endif

{{-- CATEGORIAS --}}
<div class="sec-head"><div class="sec-title"><div class="sec-icon cyan">🗂</div> Categorias</div></div>
<div class="cat-strip">
    @foreach($categorias as $cat)
    @php $catColor = match(strtolower($cat->nome)) {'música','musica'=>'var(--accent)','arte'=>'var(--purple)','festa','festas'=>'var(--gold)','desporto'=>'var(--green)','gastronomia'=>'#f97316','negócios','negocios'=>'var(--accent2)',default=>'var(--accent)'}; @endphp
    <a href="{{ route('eventos.todos', ['categoria' => $cat->id]) }}" class="cat-strip-item scroll-reveal {{ (string)$catAtiva === (string)$cat->id ? 'active' : '' }}" style="color:{{ $catColor }};">
        <div class="cat-strip-emoji">{{ $cat->emoji }}</div>
        <div class="cat-strip-name">{{ $cat->nome }}</div>
        <div class="cat-strip-count">{{ $eventos->getCollection()->filter(fn($e) => $e->categoria_id === $cat->id)->count() }}</div>
    </a>
    @endforeach
</div>

{{-- SUBCATEGORIAS --}}
@if($catAtiva && $categorias->firstWhere('id', $catAtiva)?->subcategorias->count() > 0)
@php $catSelec = $categorias->firstWhere('id', $catAtiva); @endphp
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
    <a href="{{ route('eventos.todos', ['categoria' => $catAtiva]) }}" class="fchip {{ !$subAtiva ? 'active' : '' }}">Todas as {{ $catSelec->nome }}</a>
    @foreach($catSelec->subcategorias as $sub)
    <a href="{{ route('eventos.todos', ['categoria' => $catAtiva, 'subcategoria' => $sub->id]) }}" class="fchip {{ (string)$subAtiva === (string)$sub->id ? 'active' : '' }}">{{ $sub->nome }}</a>
    @endforeach
</div>
@endif

{{-- FILTROS DESKTOP --}}
<div class="filters-desktop">
    <a href="{{ route('eventos.todos') }}" class="fchip {{ !request()->anyFilled(['filter','categoria']) ? 'active' : '' }}"><div class="dot"></div> Todos</a>
    <a href="{{ route('eventos.todos', ['filter'=>'hoje']) }}" class="fchip {{ request('filter')==='hoje' ? 'active' : '' }}">📅 Hoje</a>
    <a href="{{ route('eventos.todos', ['filter'=>'amanha']) }}" class="fchip {{ request('filter')==='amanha' ? 'active' : '' }}">📆 Amanhã</a>
    <a href="{{ route('eventos.todos', ['filter'=>'fds']) }}" class="fchip {{ request('filter')==='fds' ? 'active' : '' }}">🗓 Este fim-de-semana</a>
    <a href="{{ route('eventos.todos', ['filter'=>'semana']) }}" class="fchip {{ request('filter')==='semana' ? 'active' : '' }}">📌 Esta semana</a>
    <a href="{{ route('eventos.todos', ['filter'=>'populares']) }}" class="fchip {{ request('filter')==='populares' ? 'active' : '' }}">🔥 Mais populares</a>
    <a href="{{ route('eventos.todos', ['filter'=>'novos']) }}" class="fchip {{ request('filter')==='novos' ? 'active' : '' }}">✨ Recém adicionados</a>
</div>

@if($eventos->isEmpty())
<div class="empty-state"><p style="font-size:48px">🎟</p><p>Nenhum evento encontrado.</p></div>
@else

{{-- ACONTECE HOJE --}}
@php $hoje = $eventos->getCollection()->filter(fn($e) => \Carbon\Carbon::parse($e->data_evento)->isToday()); @endphp
@if($hoje->count() > 0)
<div class="sec-head"><div class="sec-title"><div class="sec-icon gold">⚡</div> Acontece Hoje <span class="sec-count">{{ $hoje->count() }} eventos</span></div></div>
<div class="hoje-strip">
    @foreach($hoje as $ev)
    @php $preco = $ev->tiposIngresso->min('preco') ?? 0; @endphp
    <a href="{{ route('evento.detalhes', $ev->id) }}" class="hoje-card scroll-reveal">
        <div class="hoje-card-img">
            @if($ev->imagem_capa)<img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
            @else<div class="hoje-card-img-placeholder" style="background:linear-gradient(135deg,#0c1a2e,#1e3a5f)">🎟</div>@endif
            <div class="hoje-card-time">{{ $ev->hora_inicio ? substr($ev->hora_inicio,0,5) : '—' }}</div>
        </div>
        <div class="hoje-card-body">
            <div class="hoje-card-name">{{ $ev->titulo }}</div>
            <div class="hoje-card-place">📍 {{ Str::limit($ev->localizacao,22) }}</div>
            <div class="hoje-card-footer">
                <div class="hoje-card-price {{ $preco == 0 ? 'free' : '' }}">{{ $preco == 0 ? 'Gratuito' : 'Kz '.number_format($preco,0,',','.') }}</div>
                <span class="hoje-card-btn">Ver</span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- EM DESTAQUE --}}
<div class="sec-head"><div class="sec-title"><div class="sec-icon red">🔥</div> Em Destaque <span class="sec-count">{{ $eventos->total() }} eventos</span></div></div>
<div class="events-grid">
@foreach($eventos as $evento)
@php
    $categoria        = optional($evento->categoria)->nome ?? 'Evento';
    $subcategoriaNome = optional($evento->subcategoria)->nome ?? null;
    $preco            = $evento->tiposIngresso->min('preco') ?? 0;
    $lotacao          = $evento->lotacao_maxima ?? 0;

    // FIX BD: vendidos = total - disponivel (dados reais da BD)
    $disponiveis = $evento->tiposIngresso->sum('quantidade_disponivel');
    $totalIngressos = $evento->tiposIngresso->sum('quantidade_total');
    $vendidos = max(0, $totalIngressos - $disponiveis);
    $pct = $lotacao > 0 ? round(($vendidos / $lotacao) * 100) : 0;

    $barClass = $pct >= 80 ? 'crit' : ($pct >= 50 ? 'warn' : 'ok');
    $catEmoji = optional($evento->categoria)->emoji ?? '🎟';
    $catColor = match(true) {
        str_contains($categoria,'Show')        => 'var(--accent)',
        str_contains($categoria,'Festival')    => 'var(--gold)',
        str_contains($categoria,'Viagem')      => 'var(--accent2)',
        str_contains($categoria,'Desporto')    => 'var(--green)',
        str_contains($categoria,'Conferência') => 'var(--purple)',
        str_contains($categoria,'Workshop')    => '#f97316',
        str_contains($categoria,'Cultura')     => '#ec4899',
        default                               => 'var(--accent)'
    };

    // Vídeo embed — URL externa ou ficheiro local
    $cardEmbedMudo  = null; $cardEmbedSom = null;
    $cardVideoLocal = null;
    $ym = []; $vm = [];
    $vUrl = $evento->video_preview ?? null;
    if ($vUrl) {
        if (str_starts_with($vUrl, 'http')) {
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $vUrl, $ym)) {
                $cardEmbedMudo = "https://www.youtube.com/embed/{$ym[1]}?autoplay=1&mute=1&loop=1&playlist={$ym[1]}&controls=0&playsinline=1";
                $cardEmbedSom  = "https://www.youtube.com/embed/{$ym[1]}?autoplay=1&mute=0&loop=1&playlist={$ym[1]}&controls=1&playsinline=1";
            } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrl, $vm)) {
                $cardEmbedMudo = "https://player.vimeo.com/video/{$vm[1]}?autoplay=1&muted=1&loop=1&controls=0";
                $cardEmbedSom  = "https://player.vimeo.com/video/{$vm[1]}?autoplay=1&muted=0&loop=1";
            }
        } else {
            $cardVideoLocal = asset('storage/'.$vUrl);
        }
    }
@endphp

<div class="ev-card scroll-reveal" onclick="window.location='{{ route('evento.detalhes', $evento->id) }}'">
    <div class="ev-img"
         @if($cardEmbedMudo || !empty($cardVideoLocal)) onmouseenter="activarVideo(this)" onmouseleave="desactivarVideo(this)" @endif
         style="position:relative;">
        {{-- Imagem de capa --}}
        @if($evento->imagem_capa)
            <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
        @else
            <div class="ev-img-placeholder" style="background:linear-gradient(135deg,#0c1a2e,#1e3a5f)">{{ $catEmoji }}</div>
        @endif

        {{-- Overlay de vídeo --}}
        @if($cardEmbedMudo)
        <div class="ev-video-wrap" data-mudo="{{ $cardEmbedMudo }}" data-som="{{ $cardEmbedSom }}"></div>
        <button class="ev-sound-btn" onclick="event.stopPropagation(); toggleSom(this)" title="Som">🔇</button>
        <div class="ev-video-circle" onclick="event.stopPropagation(); abrirVideoModal('vmodal-{{ $evento->id }}')" title="Ver vídeo">
            @if($evento->imagem_capa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">@endif
            <div class="ev-video-circle-play">▶</div>
        </div>
        @elseif(!empty($cardVideoLocal))
        {{-- Vídeo local dentro do wrap: CSS hover controla visibilidade --}}
        <div class="ev-video-wrap" data-local="{{ $cardVideoLocal }}">
            {{-- Sem autoplay: JS inicia play() com muted garantido --}}
        </div>
        <button class="ev-sound-btn" onclick="event.stopPropagation(); toggleSom(this)" title="Som">🔇</button>
        <div class="ev-video-circle" onclick="event.stopPropagation(); abrirVideoModal('vmodal-{{ $evento->id }}')" title="Ver vídeo">
            @if($evento->imagem_capa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">@endif
            <div class="ev-video-circle-play">▶</div>
        </div>
        @endif

        <div class="ev-badges">
            @if($pct >= 80) <span class="ev-badge hot">🔥 A Esgotar</span>
            @elseif($preco == 0) <span class="ev-badge free">Gratuito</span>
            @elseif(\Carbon\Carbon::parse($evento->created_at)->isCurrentWeek()) <span class="ev-badge new">✨ Novo</span>
            @elseif(\Carbon\Carbon::parse($evento->data_evento)->isFuture()) <span class="ev-badge soon">⏰ Em breve</span>
            @else <span></span> @endif
            <form method="POST" action="{{ route('evento.curtir', $evento->id) }}" onclick="event.stopPropagation()">
                @csrf <button class="ev-like" title="Curtir">❤️</button>
            </form>
        </div>
        <div class="ev-date-pill">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }}{{ $evento->hora_inicio ? ' · '.substr($evento->hora_inicio,0,5) : '' }}</div>
    </div>

    <div class="ev-body">
        <div class="ev-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $subcategoriaNome ?? $categoria }}</div>
        <div class="ev-title">{{ $evento->titulo }}</div>
        <div class="ev-info">
            <div class="ev-info-row">📍 {{ $evento->localizacao ?? 'Local não informado' }}</div>
            @if($evento->hora_inicio)<div class="ev-info-row">🕐 {{ substr($evento->hora_inicio,0,5) }}</div>@endif
        </div>

        {{-- Curtidas --}}
        @if($evento->usuariosQueCurtiram->count() > 0)
        @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
        <div class="flex items-center gap-2 mb-2 cursor-pointer" onclick="event.stopPropagation(); abrirModal('modal-curtidas-{{ $evento->id }}')">
            <div class="flex">@foreach($curtidores->take(3) as $u)<img src="{{ $u->avatar ? asset('storage/'.$u->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-6 h-6 rounded-full border-2 object-cover -ml-2 first:ml-0" style="border-color:rgba(15,23,42,0.95)">@endforeach</div>
            <span style="font-size:11px;color:#94a3b8;">❤️ {{ $curtidores->take(2)->pluck('name')->map(fn($n)=>explode(' ',$n)[0])->join(', ') }}@if($curtidores->count()>2) e mais {{ $curtidores->count()-2 }}@endif</span>
        </div>
        @endif

        {{-- Comentadores --}}
        @if($evento->usuariosQueComentaram->count() > 0)
        @php $comentadores = $evento->usuariosQueComentaram->unique('id'); @endphp
        <div class="flex items-center gap-2 mb-3 cursor-pointer" onclick="event.stopPropagation(); abrirModal('modal-comentarios-{{ $evento->id }}')">
            <div class="flex">@foreach($comentadores->take(3) as $u)<img src="{{ $u->avatar ? asset('storage/'.$u->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=8b5cf6&color=fff&size=64' }}" class="w-6 h-6 rounded-full border-2 object-cover -ml-2 first:ml-0" style="border-color:rgba(15,23,42,0.95)">@endforeach</div>
            <span style="font-size:11px;color:#94a3b8;">💬 {{ $comentadores->take(2)->pluck('name')->map(fn($n)=>explode(' ',$n)[0])->join(', ') }}@if($comentadores->count()>2) e mais {{ $comentadores->count()-2 }}@endif</span>
        </div>
        @endif

        {{-- Barra de bilhetes --}}
        @if($lotacao > 0 && $totalIngressos > 0)
        <div class="ev-tickets">
            <div class="ev-tickets-row"><span>Bilhetes vendidos</span><strong>{{ $vendidos }} / {{ $totalIngressos }}</strong></div>
            <div class="ev-bar"><div class="ev-bar-fill {{ $barClass }}" data-pct="{{ $pct }}" style="width:0"></div></div>
        </div>
        @endif

        <div class="ev-footer">
            <div class="ev-price {{ $preco == 0 ? 'free' : '' }}">
                @if($preco == 0) ✅ Gratuito @else {{ number_format($preco,0,',','.') }} <small>Kz</small> @endif
            </div>
            <div class="ev-actions">
                <a href="{{ route('evento.detalhes', $evento->id) }}" class="ev-buy" onclick="event.stopPropagation()">🎟 Comprar</a>
            </div>
        </div>
    </div>
</div>

{{-- Modal vídeo fullscreen --}}
@if($cardEmbedSom || !empty($cardVideoLocal))
<div id="vmodal-{{ $evento->id }}" class="ev-video-modal">
    <button class="ev-video-modal-close" onclick="fecharVideoModal('vmodal-{{ $evento->id }}')">✕</button>
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:10px;text-align:center;">{{ e($evento->titulo) }}</div>
    @if(!empty($cardVideoLocal))
        <video data-local="{{ $cardVideoLocal }}" controls loop playsinline
               style="width:90vw;max-width:800px;border-radius:16px;max-height:450px;display:none;"></video>
    @else
        <iframe data-src="{{ $cardEmbedSom }}" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
    @endif
    <a href="{{ route('evento.detalhes', $evento->id) }}" style="margin-top:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;padding:8px 20px;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;">🎟 Ver evento</a>
</div>
@endif


{{-- Modal curtidas --}}
<div id="modal-curtidas-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
    <div class="rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden" style="background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
        <div class="flex justify-between items-center p-4" style="border-bottom:1px solid rgba(59,130,246,0.15);"><h3 class="font-bold text-white text-sm">❤️ Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3><button onclick="fecharModal('modal-curtidas-{{ $evento->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button></div>
        <div class="overflow-y-auto max-h-72 p-4 space-y-3">@foreach($evento->usuariosQueCurtiram->reverse() as $user)<a href="{{ route('profile.show', $user->id) }}" class="flex items-center space-x-3 p-2 rounded-xl transition hover:bg-white/5"><img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover"><span class="text-sm font-semibold text-white">{{ $user->name }}</span></a>@endforeach</div>
    </div>
</div>

{{-- Modal comentários --}}
<div id="modal-comentarios-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
    <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col" style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
        <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);"><h3 class="font-black text-white text-sm uppercase tracking-widest">💬 Comentários ({{ $evento->comentarios->count() }})</h3><button onclick="fecharModal('modal-comentarios-{{ $evento->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button></div>
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" style="scrollbar-width:thin;">
            @forelse($evento->comentarios as $comentario)
            <div class="flex gap-3"><a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0"><img src="{{ $comentario->user->avatar ? asset('storage/'.$comentario->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($comentario->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><div class="flex justify-between items-start gap-2"><a href="{{ route('profile.show', $comentario->user->id) }}" class="font-bold text-white text-xs hover:text-blue-400">{{ $comentario->user->name }}</a>@if(auth()->id() === $comentario->user_id)<form method="POST" action="{{ route('comentario.eliminar', $comentario->id) }}" onclick="event.stopPropagation()">@csrf @method('DELETE')<button type="submit" class="text-gray-600 hover:text-red-400 text-xs">🗑</button></form>@endif</div><p class="text-gray-200 text-sm mt-1">{{ $comentario->corpo }}</p></div><div class="flex items-center gap-3 mt-1 ml-1"><span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span><form method="POST" action="{{ route('comentario.like', $comentario->id) }}" class="inline" onclick="event.stopPropagation()">@csrf<button type="submit" class="text-[11px] font-bold {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">👍 {{ $comentario->likes->count() ?: '' }}</button></form>@auth<button onclick="toggleResposta('resp-{{ $comentario->id }}')" class="text-[11px] font-bold text-gray-500 hover:text-blue-400">Responder</button>@endauth</div>@auth<div id="resp-{{ $comentario->id }}" class="hidden mt-2"><form method="POST" action="{{ route('evento.comentar', $evento->id) }}" class="flex gap-2" onclick="event.stopPropagation()">@csrf<input type="hidden" name="parent_id" value="{{ $comentario->id }}"><img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0"><input type="text" name="corpo" placeholder="Responder..." class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button type="submit" class="text-white text-xs font-bold px-3 py-1.5 rounded-xl" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button></form></div>@endauth
            </div></div>
            @empty<div class="text-center py-8 text-gray-600"><p class="text-2xl mb-2">💬</p><p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p></div>@endforelse
        </div>
        @auth<div class="px-6 py-4" style="border-top:1px solid rgba(59,130,246,0.15);"><form method="POST" action="{{ route('evento.comentar', $evento->id) }}" class="flex gap-3 items-center" onclick="event.stopPropagation()">@csrf<img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0"><input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button></form></div>@else<div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);"><a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold">Entra para comentar →</a></div>@endauth
    </div>
</div>

@endforeach
</div>

{{-- PAGINAÇÃO --}}
<div style="margin-top:24px;">{{ $eventos->links() }}</div>

{{-- TIMELINE --}}
@php $porData = $eventos->getCollection()->groupBy(fn($e) => \Carbon\Carbon::parse($e->data_evento)->format('Y-m-d'))->sortKeys()->take(3); @endphp
@if($porData->count() > 0)
<div class="sec-head" style="margin-top:32px;"><div class="sec-title"><div class="sec-icon cyan">📅</div> Por Data de Realização</div></div>
<div class="timeline">
    @foreach($porData as $data => $evsDia)
    @php $carbon = \Carbon\Carbon::parse($data); $dotColor = $carbon->isToday() ? 'var(--accent)' : ($carbon->isTomorrow() ? 'var(--gold)' : 'var(--purple)'); @endphp
    <div class="timeline-day">
        <div class="timeline-day-header">
            <div class="timeline-day-dot" style="background:{{ $dotColor }}"></div>
            <div class="timeline-day-label">@if($carbon->isToday()) Hoje @elseif($carbon->isTomorrow()) Amanhã @else {{ $carbon->translatedFormat('l') }} @endif<span>{{ $carbon->translatedFormat('d \d\e F') }}</span></div>
            <div class="timeline-day-line"></div>
            <span class="sec-count">{{ $evsDia->count() }} eventos</span>
        </div>
        <div class="timeline-row">
            @foreach($evsDia as $ev)
            @php $p = $ev->tiposIngresso->min('preco') ?? 0; $emoji = optional($ev->categoria)->emoji ?? '🎟'; @endphp
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="tl-card scroll-reveal">
                <div class="tl-card-emoji">@if($ev->imagem_capa)<img src="{{ asset('storage/'.$ev->imagem_capa) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">@else {{ $emoji }}@endif</div>
                <div>
                    <div class="tl-card-time">{{ $ev->hora_inicio ? substr($ev->hora_inicio,0,5) : '—' }}</div>
                    <div class="tl-card-name">{{ $ev->titulo }}</div>
                    <div class="tl-card-place">📍 {{ Str::limit($ev->localizacao,20) }}</div>
                    <div class="tl-card-price" style="{{ $p==0 ? 'color:var(--green)' : '' }}">{{ $p==0 ? 'Gratuito' : 'Kz '.number_format($p,0,',','.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

@endif

{{-- DRAWER FILTROS MOBILE --}}
<div class="drawer-overlay" id="drawer-filtros" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title"><h3>☰ Filtros & Categorias</h3><button class="drawer-close" onclick="document.getElementById('drawer-filtros').classList.remove('open')">✕</button></div>
        <div class="drawer-section-label">Por Data</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:24px;">
            <a href="{{ route('eventos.todos') }}" class="fchip {{ !request()->anyFilled(['filter','categoria']) ? 'active' : '' }}"><div class="dot"></div> Todos</a>
            <a href="{{ route('eventos.todos', ['filter'=>'hoje']) }}" class="fchip {{ request('filter')==='hoje' ? 'active' : '' }}">📅 Hoje</a>
            <a href="{{ route('eventos.todos', ['filter'=>'amanha']) }}" class="fchip {{ request('filter')==='amanha' ? 'active' : '' }}">📆 Amanhã</a>
            <a href="{{ route('eventos.todos', ['filter'=>'fds']) }}" class="fchip {{ request('filter')==='fds' ? 'active' : '' }}">🗓 Fim-de-semana</a>
            <a href="{{ route('eventos.todos', ['filter'=>'semana']) }}" class="fchip {{ request('filter')==='semana' ? 'active' : '' }}">📌 Esta semana</a>
            <a href="{{ route('eventos.todos', ['filter'=>'populares']) }}" class="fchip {{ request('filter')==='populares' ? 'active' : '' }}">🔥 Mais populares</a>
            <a href="{{ route('eventos.todos', ['filter'=>'novos']) }}" class="fchip {{ request('filter')==='novos' ? 'active' : '' }}">✨ Recentes</a>
        </div>
        <div class="drawer-section-label">Categorias</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($categorias as $cat)
            <a href="{{ route('eventos.todos', ['categoria' => $cat->id]) }}" class="fchip {{ (string)request('categoria') === (string)$cat->id ? 'active' : '' }}">{{ $cat->emoji }} {{ $cat->nome }}</a>
            @endforeach
        </div>
    </div>
</div>

<script>
// ── Modais ───────────────────────────────────────────────
function abrirModal(id) {
    const m = document.getElementById(id);
    m.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';
    m.classList.remove('hidden');
    document.body.appendChild(m);
}
function fecharModal(id) {
    const m = document.getElementById(id);
    m.style.display = 'none'; m.classList.add('hidden');
}
function toggleResposta(id) {
    const f = document.getElementById(id);
    f.classList.toggle('hidden');
    if (!f.classList.contains('hidden')) f.querySelector('input').focus();
}
document.addEventListener('click', function(e) {
    ['modal-curtidas-','modal-comentarios-'].forEach(p => {
        document.querySelectorAll(`[id^="${p}"]`).forEach(m => {
            if (e.target === m) fecharModal(m.id);
        });
    });
});

// ── Modal vídeo ──────────────────────────────────────────
function abrirVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // iframe URL externa
    const iframe = modal.querySelector('iframe');
    if (iframe && iframe.dataset.src && !iframe.src) iframe.src = iframe.dataset.src;
    // video local — só define src ao abrir (evita autoplay escondido)
    const video = modal.querySelector('video[data-local]');
    if (video && video.dataset.local) {
        video.src = video.dataset.local;
        video.style.display = 'block';
        video.muted = false;
        video.play().catch(function(){});
    }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fecharVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // Parar iframe
    const iframe = modal.querySelector('iframe');
    if (iframe) iframe.src = '';
    // Parar e limpar video local
    const video = modal.querySelector('video[data-local]');
    if (video) {
        video.pause();
        video.src = '';
        video.style.display = 'none';
    }
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('click', function(e) {
    document.querySelectorAll('.ev-video-modal.open').forEach(m => { if (e.target === m) fecharVideoModal(m.id); });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') document.querySelectorAll('.ev-video-modal.open').forEach(m => fecharVideoModal(m.id));
});

// ── Vídeo no card (hover) ─────────────────────────────────
function activarVideo(cardImg) {
    const wrap = cardImg.querySelector('.ev-video-wrap');
    if (!wrap || wrap.querySelector('iframe') || wrap.querySelector('video')) return;

    const localSrc = wrap.dataset.local;
    const mudo     = wrap.dataset.mudo;

    if (localSrc) {
        const v = document.createElement('video');
        // NÃO usar setAttribute('muted') — impede unmute posterior
        v.loop   = true;
        v.playsInline = true;
        v.muted  = true;
        v.volume = 0;
        v.style.cssText = 'width:100%;height:100%;object-fit:cover;pointer-events:none;border:none;';
        v.src = localSrc;
        wrap.appendChild(v);
        v.muted  = true;
        v.volume = 0;
        v.play().catch(function(){});
    } else if (mudo) {
        const urlComApi = mudo.includes('enablejsapi') ? mudo : mudo + '&enablejsapi=1';
        const iframe = document.createElement('iframe');
        iframe.src = urlComApi;
        iframe.setAttribute('allow', 'autoplay');
        iframe.setAttribute('frameborder', '0');
        iframe.style.cssText = 'width:100%;height:100%;border:none;';
        iframe.dataset.mudo   = urlComApi;
        iframe.dataset.som    = wrap.dataset.som;
        iframe.dataset.isMudo = '1';
        wrap.appendChild(iframe);
    }
}
function desactivarVideo(cardImg) {
    const wrap = cardImg.querySelector('.ev-video-wrap');
    if (!wrap) return;
    const iframe = wrap.querySelector('iframe');
    const video  = wrap.querySelector('video');
    if (iframe) { iframe.src = ''; iframe.remove(); }
    if (video)  { video.pause(); video.muted = true; video.volume = 0; video.src = ''; video.remove(); }
    const btn = cardImg.querySelector('.ev-sound-btn');
    if (btn) btn.textContent = '🔇';
}

// ── SOM PARA VÍDEO LOCAL (tag <video>) ───────────────────
function toggleSomLocalCard(btn) {
    const cardImg = btn.closest('.ev-img');
    if (!cardImg) return;
    const video = cardImg.querySelector('video');
    if (!video) return;
    video.muted = !video.muted;
    btn.textContent = video.muted ? '🔇' : '🔊';
}

// ── TOGGLE SOM — suporta <video> local e <iframe> URL ────
var _somActivo = null;
function toggleSom(btn) {
    const cardImg = btn.closest('.ev-img');
    const wrap    = cardImg ? cardImg.querySelector('.ev-video-wrap') : null;
    if (!wrap) return;

    // Vídeo local
    const video = wrap.querySelector('video');
    if (video) {
        if (video.muted) {
            video.removeAttribute('muted');
            video.muted  = false;
            video.volume = 1;
            btn.textContent = '🔊';
        } else {
            video.muted  = true;
            video.volume = 0;
            btn.textContent = '🔇';
        }
        return;
    }

    // iframe URL
    const iframe = wrap.querySelector('iframe');
    if (!iframe) return;
    const isMudo = iframe.dataset.isMudo === '1';

    if (!isMudo && _somActivo && _somActivo !== iframe) {
        _somActivo.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'mute', args:[]}), '*'
        );
        _somActivo.dataset.isMudo = '1';
        const outroCard = _somActivo.closest('.ev-img');
        if (outroCard) {
            const outroBtn = outroCard.querySelector('.ev-sound-btn');
            if (outroBtn) outroBtn.textContent = '🔇';
        }
    }

    if (isMudo) {
        iframe.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'unMute', args:[]}), '*'
        );
        iframe.dataset.isMudo = '0';
        btn.textContent = '🔊';
        _somActivo = iframe;
    } else {
        iframe.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'mute', args:[]}), '*'
        );
        iframe.dataset.isMudo = '1';
        btn.textContent = '🔇';
        _somActivo = null;
    }
}

// ── Scroll reveal + barras + IntersectionObserver vídeos ──
document.addEventListener('DOMContentLoaded', function () {
    // Scroll reveal cards
    var els = document.querySelectorAll('.scroll-reveal');
    if (!('IntersectionObserver' in window)) {
        els.forEach(el => el.classList.add('is-visible'));
        document.querySelectorAll('.ev-bar-fill').forEach(b => { b.style.width = (b.dataset.pct||0)+'%'; });
    } else {
        var obsReveal = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    var bar = entry.target.querySelector('.ev-bar-fill');
                    if (bar) setTimeout(() => { bar.style.width = (bar.dataset.pct||0)+'%'; }, 200);
                    obsReveal.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        els.forEach(el => obsReveal.observe(el));

        // Pausar vídeos fora do viewport (performance mobile)
        var obsVideo = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) return;
                var wrap = entry.target.querySelector('.ev-video-wrap');
                if (!wrap) return;
                var iframe = wrap.querySelector('iframe');
                var video  = wrap.querySelector('video');
                if (iframe) { iframe.src = ''; iframe.remove(); }
                if (video)  { video.pause(); video.muted = true; video.volume = 0; }
                var somBtn = entry.target.querySelector('.ev-sound-btn');
                if (somBtn) somBtn.textContent = '🔇';
            });
        }, { threshold: 0.3 });
        document.querySelectorAll('.ev-card').forEach(card => obsVideo.observe(card));
    }
});
</script>

@endsection