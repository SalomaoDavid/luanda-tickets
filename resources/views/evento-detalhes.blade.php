@extends('layouts.app')
@section('title', $evento->titulo)
@section('content')

@php
$fotos         = $evento->fotos;
$temCapa   = $evento->imagem_capa;
$temFotos  = $fotos->count() > 0;
$preco     = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
$totalDisp = $evento->tiposIngresso->sum('quantidade_disponivel');

$meta = [];
$rawMeta = $evento->meta ?? null;
if (is_array($rawMeta))      { $meta = $rawMeta; }
elseif (is_string($rawMeta)) { $meta = json_decode($rawMeta, true) ?? []; }

$catNome  = strtolower(optional($evento->categoria)->nome ?? '');
$catNome2 = strtolower(optional($evento->categoria)->nome ?? '');
$catEmoji = match(true) {
    str_contains($catNome2,'música') || str_contains($catNome2,'musica') || str_contains($catNome2,'show') => '🎵',
    str_contains($catNome2,'festa') || str_contains($catNome2,'festival') => '🎉',
    str_contains($catNome2,'desporto') => '⚽',
    str_contains($catNome2,'arte') || str_contains($catNome2,'cultura') => '🎨',
    str_contains($catNome2,'gastro') || str_contains($catNome2,'comida') => '🍽',
    str_contains($catNome2,'negócio') || str_contains($catNome2,'negocio') => '💼',
    str_contains($catNome2,'viagem') => '✈️',
    str_contains($catNome2,'confer') || str_contains($catNome2,'workshop') => '🎙️',
    default => '🎟',
};
$temMeta  = !empty(array_filter($meta));

$tParagens     = isset($meta['paragens'])     ? (is_array($meta['paragens'])     ? $meta['paragens']     : (json_decode($meta['paragens'],     true) ?? [])) : [];
$tArtistas     = isset($meta['artistas'])     ? (is_array($meta['artistas'])     ? $meta['artistas']     : (json_decode($meta['artistas'],     true) ?? [])) : [];
$tPalestrantes = isset($meta['palestrantes']) ? (is_array($meta['palestrantes']) ? $meta['palestrantes'] : (json_decode($meta['palestrantes'],  true) ?? [])) : [];
$tElenco       = isset($meta['elenco'])       ? (is_array($meta['elenco'])       ? $meta['elenco']       : (json_decode($meta['elenco'],       true) ?? [])) : [];

// ── Detecção do tipo de vídeo ─────────────────────────────
$videoPreview = $evento->video_preview ?? null;
$videoEmbed   = null;  // URL externa → iframe mudo no hero
$videoLocal   = null;  // ficheiro local → sem src até abrir modal
if ($videoPreview) {
    if (str_starts_with($videoPreview, 'http')) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $videoPreview, $yv)) {
            $videoEmbed = "https://www.youtube.com/embed/{$yv[1]}?autoplay=1&mute=1&loop=1&playlist={$yv[1]}&controls=0&playsinline=1&modestbranding=1&rel=0&enablejsapi=1";
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoPreview, $vv)) {
            $videoEmbed = "https://player.vimeo.com/video/{$vv[1]}?autoplay=1&muted=1&loop=1&controls=0";
        }
    } else {
        $videoLocal = asset('storage/'.$videoPreview);
    }
}
@endphp

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
@verbatim
[x-cloak]{display:none!important;}
:root{
    --ink:#04060d;
    --card:#08101f;
    --card2:#0c1830;
    --card3:#101e38;
    --cyan:#00d4ff;
    --cyan2:#0099cc;
    --gold:#f5a623;
    --emerald:#00c896;
    --rose:#ff4d6d;
    --purple:#9b5cff;
    --txt:#eef2ff;
    --muted:#6b7a99;
    --muted2:#9aafc7;
    --border:rgba(0,212,255,.1);
    --border2:rgba(0,212,255,.22);
    --glow:rgba(0,212,255,.15);
}
body{
    font-family:'DM Sans',sans-serif;
    background:var(--ink);
    color:var(--txt);
    min-height:100vh;
}

/* ─── PAGE ─── */
.page{width:100%;padding:6px 0 80px;}
@media only screen and (min-width:768px){.page{max-width:1080px;margin:0 auto;padding:10px 0 80px;}}
/* ─── HERO BANNER ─── */
.hero{
    position:relative;
    border-radius:24px;
    overflow:hidden;
    margin-bottom:8px;
    height:260px;
    background:linear-gradient(135deg,#050d1a,#0a1f3a);
}
.hero img{
    width:100%;height:100%;object-fit:cover;
    filter:brightness(.55);
}
.hero-ph{
    width:100%;height:100%;
    display:flex;align-items:center;justify-content:center;
    font-size:80px;
    background:linear-gradient(135deg,#050d1a,#0a1f3a,#071528);
}
.hero-gradient{
    position:absolute;inset:0;
    background:
        linear-gradient(to top, rgba(4,6,13,1) 0%, rgba(4,6,13,.5) 40%, transparent 70%),
        linear-gradient(to right, rgba(4,6,13,.7) 0%, transparent 60%);
}
.hero-content{
    position:absolute;bottom:0;left:0;right:0;
    padding:10px 12px 14px;
    transition:opacity .4s ease, transform .4s ease;
    animation:heroTextBreathe 4.5s ease-in-out infinite;
}
.hero-content.hero-content-escondido{
    opacity:0;transform:translateY(6px);pointer-events:none;
    animation:none;
}
@keyframes heroTextBreathe{
    0%,100%{opacity:1;filter:drop-shadow(0 2px 14px rgba(0,212,255,.15));}
    50%{opacity:.62;filter:drop-shadow(0 2px 6px rgba(0,212,255,0));}
}
@media (prefers-reduced-motion: reduce){
    .hero-content{animation:none;}
}
.hero-category{
    display:inline-flex;align-items:center;gap:6px;
    font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;
    color:var(--cyan);
    background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.2);
    padding:4px 12px;border-radius:20px;margin-bottom:12px;
}
.hero-title{
    font-family:'Syne',sans-serif;
    font-size:28px;font-weight:900;
    color:#fff;line-height:1.1;
    margin-bottom:10px;
    text-shadow:0 2px 20px rgba(0,0,0,.5);
}
.hero-pills{display:flex;flex-wrap:wrap;gap:8px;}
.hero-pill{
    display:flex;align-items:center;gap:5px;
    font-size:12px;color:rgba(255,255,255,.8);
    background:rgba(0,0,0,.5);backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.1);
    padding:5px 12px;border-radius:20px;
}
.hero-badge{
    position:absolute;top:18px;left:18px;
    font-size:10px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;
    padding:5px 13px;border-radius:20px;
}
.badge-sold{background:rgba(255,77,109,.9);color:#fff;}
.badge-free{background:rgba(0,200,150,.9);color:#fff;}
.badge-new{background:rgba(0,212,255,.9);color:#000;}
.hero-fotos-btn{
    position:absolute;top:18px;right:18px;
    display:flex;align-items:center;gap:5px;
    background:rgba(0,0,0,.6);backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.15);
    border-radius:10px;padding:7px 14px;
    font-size:11px;font-weight:700;color:#fff;cursor:pointer;transition:all .2s;
}
.hero-fotos-btn:hover{background:rgba(0,212,255,.25);border-color:var(--cyan);}

/* ─── MAIN LAYOUT ─── */
.layout{
    display:grid;
    grid-template-columns:1fr;
    row-gap:8px;column-gap:0;
}
.layout-left{display:flex;flex-direction:column;gap:8px;}
.layout-right{display:flex;flex-direction:column;gap:8px;}

/* ─── CARDS HORIZONTAIS (abaixo do hero) ─── */
.cards-row{
    display:grid;
    grid-template-columns:1fr;
    row-gap:6px;column-gap:0;
    margin-bottom:0;
}
@media only screen and (min-width:640px){
    .cards-row{grid-template-columns:1fr 1fr;column-gap:6px;}
}
@media only screen and (min-width:1024px){
    .cards-row{grid-template-columns:1fr 1fr 1fr;column-gap:6px;}
}

/* Cards compactos no desktop */
@media only screen and (min-width:768px){
    .card-body{padding:10px 10px;}
    .card-head{padding:8px 10px 6px;}
    .info-item{padding:6px 8px;}
    .sobre-text{font-size:13px;}
}

/* ─── CARD BASE ─── */
.card{
    background:var(--card);
    border:1px solid var(--border2);
    border-radius:20px;
    overflow:hidden;
}
.card-head{
    padding:8px 10px 6px;
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
}
.card-title{
    font-family:'Syne',sans-serif;
    font-size:14px;font-weight:800;color:var(--txt);
    display:flex;align-items:center;gap:8px;
}
.card-body{padding:10px 10px;}

/* ─── SOBRE ─── */
.sobre-text{
    font-size:14px;color:var(--muted2);
    line-height:1.85;
}
.ver-mais-btn{
    display:inline-flex;align-items:center;gap:4px;
    font-size:12px;font-weight:600;color:var(--cyan);
    background:none;border:none;cursor:pointer;margin-top:10px;padding:0;
}

/* ─── INFO GRID ─── */
.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}
.info-item{
    background:var(--card2);border:1px solid var(--border);
    border-radius:12px;padding:12px 14px;
}
.info-item-icon{font-size:18px;margin-bottom:5px;}
.info-item-lbl{font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);margin-bottom:2px;}
.info-item-val{font-size:13px;font-weight:600;color:var(--txt);}

/* ─── META ITEMS ─── */
.meta-list{display:flex;flex-direction:column;gap:8px;}
.meta-row{
    display:flex;align-items:flex-start;gap:10px;
    padding:10px 12px;
    background:var(--card2);border:1px solid var(--border);border-radius:11px;
}
.meta-row-icon{
    width:32px;height:32px;flex-shrink:0;border-radius:9px;
    background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.2);
    display:flex;align-items:center;justify-content:center;font-size:15px;
}
.meta-row-lbl{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:2px;}
.meta-row-val{font-size:13px;font-weight:600;color:var(--txt);line-height:1.4;}

/* Rota strip */
.rota-strip{
    display:flex;align-items:center;
    background:var(--card2);border:1px solid var(--border);border-radius:12px;
    overflow:hidden;margin-bottom:10px;
}
.rota-node{flex:1;padding:12px;text-align:center;}
.rota-lbl{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:3px;}
.rota-val{font-size:13px;font-weight:700;color:var(--txt);}
.rota-sep{flex-shrink:0;width:36px;display:flex;align-items:center;justify-content:center;font-size:16px;border-left:1px solid var(--border);border-right:1px solid var(--border);}

/* Tags */
.tag-row{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;}
.tag{
    font-size:11px;font-weight:600;
    padding:4px 10px;border-radius:20px;
    background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);color:var(--cyan);
}

/* Artistas */
.artist-list{display:flex;flex-direction:column;gap:8px;}
.artist-item{
    display:flex;align-items:center;gap:10px;
    padding:9px 12px;border-radius:11px;
    background:var(--card2);border:1px solid transparent;transition:border-color .2s;
}
.artist-item:hover{border-color:var(--border2);}
.artist-avatar{
    width:38px;height:38px;border-radius:50%;flex-shrink:0;
    background:linear-gradient(135deg,var(--card3),#1a2a50);
    border:2px solid var(--border2);
    display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:800;color:var(--cyan);
}
.artist-name{font-size:13px;font-weight:600;color:var(--txt);}
.artist-role{font-size:10px;color:var(--muted);}

/* ─── GALERIA ─── */
.gallery-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:6px;
}
.gallery-item{
    aspect-ratio:1;border-radius:10px;overflow:hidden;
    background:var(--card2);cursor:pointer;transition:transform .2s;
}
.gallery-item:hover{transform:scale(1.04);}
.gallery-item img{width:100%;height:100%;object-fit:cover;}
.gallery-item-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:22px;}
.gallery-more{
    width:100%;margin-top:10px;padding:9px;border-radius:10px;
    background:rgba(0,212,255,.06);border:1px solid var(--border2);
    color:var(--cyan);font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;
}
.gallery-more:hover{background:rgba(0,212,255,.12);}

/* ─── BILHETES (CARD DIREITO) ─── */
.ticket-card{
    background:var(--card);border:1px solid var(--border2);
    border-radius:20px;overflow:hidden;
    position:sticky;top:90px;
}
.ticket-head{
    padding:20px 22px 16px;
    background:linear-gradient(135deg,rgba(0,212,255,.06),transparent);
    border-bottom:1px solid var(--border);
}
.ticket-head-price{
    font-family:'Syne',sans-serif;
    font-size:32px;font-weight:900;color:var(--gold);line-height:1;
}
.ticket-head-label{font-size:11px;color:var(--muted);margin-top:2px;}
.ticket-head-avail{
    display:inline-flex;align-items:center;gap:5px;
    margin-top:10px;font-size:12px;font-weight:600;
    color:var(--emerald);
}
.ticket-head-avail-dot{width:6px;height:6px;border-radius:50%;background:var(--emerald);}

.ticket-body{padding:18px 22px;}

.ticket-type{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 16px;border-radius:14px;
    background:var(--card2);border:1px solid var(--border);
    margin-bottom:10px;cursor:pointer;transition:all .2s;
    position:relative;overflow:hidden;
}
.ticket-type::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
    background:linear-gradient(to bottom,var(--cyan),var(--cyan2));
}
.ticket-type:hover{border-color:var(--border2);}
.ticket-type.esgotado{opacity:.5;cursor:not-allowed;}
.ticket-type-name{font-size:14px;font-weight:700;color:var(--txt);margin-bottom:2px;}
.ticket-type-avail{font-size:10px;color:var(--muted);}
.ticket-type-price{
    font-family:'Syne',sans-serif;
    font-size:16px;font-weight:900;color:var(--gold);text-align:right;
}
.ticket-type-price small{font-size:10px;color:var(--muted);font-weight:400;display:block;}

.buy-btn{
    width:100%;padding:15px;border-radius:15px;
    background:linear-gradient(135deg,var(--cyan),var(--cyan2));
    color:#000;font-size:15px;font-weight:800;
    font-family:'Syne',sans-serif;
    border:none;cursor:pointer;transition:all .2s;
    box-shadow:0 4px 24px rgba(0,212,255,.35);
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.buy-btn:hover{transform:translateY(-1px);box-shadow:0 6px 30px rgba(0,212,255,.45);}
.buy-btn:disabled{
    background:var(--card2);color:var(--muted);
    box-shadow:none;cursor:not-allowed;transform:none;
    border:1px solid var(--border);
}

.buy-notice{
    display:flex;align-items:center;justify-content:center;gap:5px;
    font-size:11px;color:var(--muted);text-align:center;
    margin-top:12px;line-height:1.6;
}

/* ─── SEPARADOR ─── */
.divider{height:1px;background:var(--border);margin:14px 0;}

/* ─── QUICK ACTIONS ─── */
.quick-actions{
    display:flex;gap:6px;flex-wrap:wrap;
    padding:8px 10px;border-top:1px solid var(--border);
}
.qa-btn{
    flex:1;min-width:100px;
    display:flex;align-items:center;justify-content:center;gap:6px;
    padding:10px;border-radius:11px;
    background:rgba(0,212,255,.06);border:1px solid var(--border);
    color:var(--muted2);font-size:12px;font-weight:600;
    cursor:pointer;transition:all .2s;text-decoration:none;
}
.qa-btn:hover{border-color:var(--border2);color:var(--cyan);}

/* ─── BOTTOM BAR MOBILE ─── */
.bottom-bar{
    position:fixed;bottom:0;left:0;right:0;z-index:200;
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;
    background:rgba(8,16,31,.97);backdrop-filter:blur(20px);
    border-top:1px solid var(--border2);
}
.bottom-bar-price{
    font-family:'Syne',sans-serif;
    font-size:20px;font-weight:900;color:var(--gold);
}
.bottom-bar-label{font-size:10px;color:var(--muted);}
.bottom-bar-btn{
    flex:1;padding:13px;border-radius:13px;
    background:linear-gradient(135deg,var(--cyan),var(--cyan2));
    color:#000;font-size:14px;font-weight:800;border:none;cursor:pointer;
}
.bottom-bar-btn:disabled{opacity:.4;}

/* ─── DRAWERS ─── */
.drawer-overlay{
    display:none;position:fixed;inset:0;z-index:500;
    background:rgba(0,0,0,.8);backdrop-filter:blur(8px);
}
.drawer-overlay.open{display:flex;align-items:flex-end;}
.drawer-box{
    width:100%;background:var(--card);
    border:1px solid var(--border2);
    border-radius:24px 24px 0 0;
    padding:20px 20px 40px;
    max-height:90vh;overflow-y:auto;scrollbar-width:none;
}
.drawer-box::-webkit-scrollbar{display:none;}
.drawer-handle{width:36px;height:4px;border-radius:2px;background:var(--border2);margin:0 auto 18px;}
.drawer-title-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.drawer-title{font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:var(--txt);}
.drawer-close{background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;}
.drawer-close:hover{color:var(--rose);}
.drawer-info-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px;}
.drawer-info-row:last-child{border-bottom:none;}
.drawer-info-lbl{color:var(--muted);font-weight:500;flex-shrink:0;}
.drawer-info-val{color:var(--txt);font-weight:600;text-align:right;}
.contactar-btn{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:16px;padding:13px;border-radius:13px;background:rgba(0,212,255,.08);border:1px solid var(--border2);color:var(--cyan);font-weight:700;font-size:14px;text-decoration:none;transition:all .2s;}
.contactar-btn:hover{background:rgba(0,212,255,.15);}
.gallery-drawer-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;}
.gallery-drawer-grid img{width:100%;height:90px;object-fit:cover;border-radius:10px;}

/* ─── MODAL COMPRA ─── */
.modal-overlay{position:fixed;inset:0;z-index:990;background:rgba(4,6,13,.92);backdrop-filter:blur(14px);}
.modal-center{position:fixed;inset:0;z-index:991;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;}
.modal-box{
    pointer-events:auto;width:100%;max-width:480px;
    background:var(--card);border:1px solid var(--border2);
    border-radius:28px 28px 0 0;
    max-height:96vh;overflow-y:auto;scrollbar-width:none;
}
.modal-box::-webkit-scrollbar{display:none;}
.modal-drag{width:36px;height:4px;border-radius:2px;background:var(--border2);margin:14px auto 0;display:block;}
.modal-top{
    position:sticky;top:0;z-index:5;
    background:var(--card);
    padding:14px 20px;border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
}
.modal-secure-badge{
    display:inline-flex;align-items:center;gap:5px;
    font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
    color:var(--emerald);
    background:rgba(0,200,150,.08);border:1px solid rgba(0,200,150,.2);
    padding:3px 9px;border-radius:20px;margin-bottom:5px;
}
.modal-secure-badge::before{content:'●';font-size:6px;animation:blink 1.5s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.modal-top-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:var(--txt);}
.modal-x{width:34px;height:34px;border-radius:10px;background:var(--card2);border:1px solid var(--border);color:var(--muted);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.modal-x:hover{background:rgba(255,77,109,.12);border-color:rgba(255,77,109,.3);color:var(--rose);}
.modal-body{padding:18px 20px 28px;}

.modal-ev-row{display:flex;gap:12px;align-items:center;padding:12px;background:var(--card2);border:1px solid var(--border);border-radius:13px;margin-bottom:16px;}
.modal-ev-thumb{width:48px;height:48px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid var(--border2);}
.modal-ev-thumb-ph{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,#0c1a2e,#1a3060);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.modal-ev-name{font-size:13px;font-weight:800;color:var(--txt);margin-bottom:3px;}
.modal-ev-sub{font-size:11px;color:var(--muted);}

.modal-selected-type{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 16px;background:var(--card2);border:1px solid var(--cyan);
    border-radius:14px;margin-bottom:16px;position:relative;overflow:hidden;
}
.modal-selected-type::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,var(--cyan),var(--cyan2));}
.modal-selected-name{font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--txt);}
.modal-selected-sub{font-size:11px;color:var(--muted);margin-top:2px;}
.modal-selected-price{font-family:'Syne',sans-serif;font-size:20px;font-weight:900;color:var(--gold);}
.modal-selected-kz{font-size:10px;color:var(--muted);}

.fg{margin-bottom:14px;}
.fl{display:block;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted2);margin-bottom:6px;}
.fi{width:100%;background:var(--card2);border:1.5px solid var(--border);border-radius:12px;padding:12px 14px;font-size:14px;color:var(--txt);outline:none;transition:border-color .2s;font-family:inherit;}
.fi:focus{border-color:var(--cyan);}
.fi::placeholder{color:var(--muted);}

.qty-wrapper{display:flex;align-items:center;justify-content:space-between;background:var(--card2);border:1.5px solid var(--border);border-radius:12px;padding:10px 14px;}
.qty-label{font-size:13px;color:var(--muted2);}
.qty-controls{display:flex;align-items:center;gap:12px;}
.qty-control-btn{width:32px;height:32px;border-radius:9px;background:var(--card3);border:1px solid var(--border2);color:var(--txt);font-size:18px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;line-height:1;}
.qty-control-btn:hover{border-color:var(--cyan);color:var(--cyan);}
.qty-value{font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:#fff;min-width:24px;text-align:center;}

.total-row{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:rgba(245,166,35,.06);border:1px solid rgba(245,166,35,.2);border-radius:12px;margin-bottom:16px;}
.total-label{font-size:12px;color:var(--muted2);}
.total-value{font-family:'Syne',sans-serif;font-size:22px;font-weight:900;color:var(--gold);}

.upload-area{position:relative;border:1.5px dashed var(--border2);border-radius:12px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;}
.upload-area:hover{border-color:var(--cyan);background:rgba(0,212,255,.04);}
.upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-icon{font-size:22px;margin-bottom:6px;}
.upload-label{font-size:13px;color:var(--muted2);}
.upload-hint{font-size:11px;color:var(--muted);margin-top:3px;}
.upload-preview{display:none;align-items:center;gap:8px;margin-top:10px;padding:9px 12px;background:rgba(0,200,150,.08);border:1px solid rgba(0,200,150,.2);border-radius:9px;}
.upload-preview-name{font-size:12px;color:var(--emerald);font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

.form-errs{background:rgba(255,77,109,.08);border:1px solid rgba(255,77,109,.22);color:#fca5a5;padding:12px 14px;border-radius:12px;margin-bottom:14px;font-size:13px;}

.submit-btn{width:100%;padding:15px;border-radius:16px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-size:15px;font-weight:800;border:none;cursor:pointer;font-family:'Syne',sans-serif;box-shadow:0 4px 24px rgba(0,212,255,.35);display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;margin-top:18px;}
.submit-btn:hover{transform:translateY(-1px);box-shadow:0 6px 30px rgba(0,212,255,.45);}
.submit-notice{font-size:11px;color:var(--muted);text-align:center;margin-top:12px;line-height:1.6;}

/* ─── RESPONSIVE ─── */
.hide-mobile{display:none;}
.show-mobile{display:flex;}

@media only screen and (min-width: 768px) {
    .hero{height:360px;}
    .hero-title{font-size:34px;}
    .layout{grid-template-columns:1fr;}
    .hide-mobile{display:block;}
    .show-mobile{display:none;}
    .modal-center{align-items:center;padding:20px;}
    .modal-box{border-radius:28px;max-height:90vh;}
    .modal-drag{display:none;}
    .drawer-overlay.open{align-items:center;justify-content:center;}
    .drawer-box{border-radius:24px;max-width:480px;max-height:82vh;}
    .drawer-handle{display:none;}
}
/* ─── MODAL BANCÁRIO ─── */
.modal-banco-overlay{position:fixed;inset:0;z-index:980;background:rgba(4,6,13,.92);backdrop-filter:blur(14px);}
.modal-banco-center{position:fixed;inset:0;z-index:981;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;}
.modal-banco-box{pointer-events:auto;width:100%;max-width:480px;background:var(--card);border:1px solid var(--border2);border-radius:28px 28px 0 0;max-height:92vh;overflow-y:auto;scrollbar-width:none;padding-bottom:20px;}
.modal-banco-box::-webkit-scrollbar{display:none;}
.modal-banco-drag{width:36px;height:4px;border-radius:2px;background:var(--border2);margin:14px auto 0;display:block;}
.modal-banco-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-banco-title{font-family:'Syne',sans-serif;font-size:17px;font-weight:900;color:var(--txt);}
.modal-banco-x{width:32px;height:32px;border-radius:9px;background:var(--card2);border:1px solid var(--border);color:var(--muted);font-size:15px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.modal-banco-body{padding:18px 20px;}
.modal-banco-sub{font-size:13px;color:var(--muted2);margin-bottom:16px;line-height:1.6;}
.banco-card{background:var(--card2);border:1px solid var(--border2);border-radius:14px;padding:14px 16px;margin-bottom:10px;}
.banco-card:last-of-type{margin-bottom:0;}
.banco-header{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.banco-logo{width:40px;height:40px;border-radius:9px;object-fit:contain;background:#fff;padding:4px;flex-shrink:0;}
.banco-logo-ph{width:40px;height:40px;border-radius:9px;background:linear-gradient(135deg,#0c1a2e,#1a3060);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.banco-nome{font-size:14px;font-weight:800;color:var(--txt);}
.banco-titular{font-size:11px;color:var(--muted);}
.banco-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-top:1px solid var(--border);}
.banco-lbl{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);}
.banco-val{font-family:'Space Mono',monospace;font-size:13px;font-weight:700;color:var(--txt);}
.copiar-btn{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:8px;background:rgba(0,212,255,.08);border:1px solid var(--border2);color:var(--cyan);font-size:11px;font-weight:700;cursor:pointer;transition:all .2s;}
.copiar-btn:hover{background:rgba(0,212,255,.16);}
.modal-banco-separator{height:1px;background:var(--border);margin:16px 0;}
.ja-paguei-btn{width:100%;padding:10px 14px;border-radius:14px;background:linear-gradient(135deg,var(--emerald),#00a87a);color:#fff;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;box-shadow:0 4px 20px rgba(0,200,150,.3);}
.ja-paguei-btn:hover{transform:translateY(-1px);box-shadow:0 6px 28px rgba(0,200,150,.4);}

/* ══════════════════════════════════════════════
   ANIMAÇÕES ADICIONADAS (não altera lógica)
   ══════════════════════════════════════════════ */
@keyframes fadeInUp{
    0%{opacity:0;transform:translateY(24px);}
    100%{opacity:1;transform:translateY(0);}
}
@keyframes heroZoom{
    0%{transform:scale(1.12);}
    100%{transform:scale(1);}
}
@keyframes glowPulse{
    0%,100%{box-shadow:0 4px 24px rgba(0,212,255,.35);}
    50%{box-shadow:0 4px 34px rgba(0,212,255,.6);}
}
@keyframes glowBreatheOpacity{
    0%,100%{opacity:.18;}
    50%{opacity:.6;}
}

@keyframes spinBorder{
    0%{transform:rotate(0deg);}
    100%{transform:rotate(360deg);}
}

.anim-fadeInUp{opacity:0;animation:fadeInUp .7s ease-out forwards;}
.anim-delay-1{animation-delay:.08s;}
.anim-delay-2{animation-delay:.18s;}
.anim-delay-3{animation-delay:.28s;}

.hero img{animation:heroZoom 1.6s ease-out forwards;}

.scroll-reveal{opacity:0;transform:translateY(24px);transition:all .7s cubic-bezier(.16,1,.3,1);}
.scroll-reveal.is-visible{opacity:1;transform:translateY(0);}

.card{position:relative;border-color:rgba(0,212,255,.08);}

/* Anel giratório em volta dos 3 cards (::before) — independente do glow (::after) */
.card::before{
    content:'';position:absolute;inset:-1px;border-radius:20px;padding:1.5px;
    background:conic-gradient(from 0deg, transparent 0%, var(--cyan) 12%, transparent 26%, transparent 74%, var(--gold) 88%, transparent 100%);
    animation:spinBorder 5s linear infinite;
    -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite:xor;mask-composite:exclude;
    pointer-events:none;z-index:0;
}
.card > *{position:relative;z-index:1;}

/* Respiração de luz: camada própria (::after), não interfere na opacidade do elemento */
.hero::after,
.card::after{
    content:'';
    position:absolute;inset:0;
    border-radius:inherit;
    pointer-events:none;
    z-index:-1;
    background:radial-gradient(circle at 50% 30%, rgba(0,212,255,.16), transparent 65%);
    animation:glowBreatheOpacity 4.5s ease-in-out infinite;
}

.buy-btn:not(:disabled){animation:glowPulse 2.4s ease-in-out infinite;}

/* ══════════════════════════════════════════════
   ANIMAÇÕES DOS MODAIS E DRAWERS
   ══════════════════════════════════════════════ */
@keyframes shimmerText{
    to{background-position:200% center;}
}
@keyframes slideFadeIn{
    0%{opacity:0;transform:translateY(30px);}
    100%{opacity:1;transform:translateY(0);}
}
@keyframes overlayFadeIn{
    0%{opacity:0;}
    100%{opacity:1;}
}
@keyframes rowPop{
    0%{opacity:0;transform:translateY(14px) scale(.97);}
    100%{opacity:1;transform:translateY(0) scale(1);}
}
@keyframes priceGlow{
    0%,100%{text-shadow:0 0 0 rgba(245,166,35,0);}
    50%{text-shadow:0 0 18px rgba(245,166,35,.55);}
}

/* Texto shimmer (títulos dos modais/drawers, mesmo estilo do menu lateral) */
.drawer-title,
.modal-top-title,
.modal-banco-title{
    background:linear-gradient(90deg,#ffffff,var(--cyan),#a78bfa,#ffffff);
    background-size:300% auto;
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    animation:shimmerText 3s linear infinite;
}

/* Preços com brilho dourado pulsante */
.modal-selected-price,
.total-value,
#banco-tipo-preco{
    animation:priceGlow 2.2s ease-in-out infinite;
}

/* Overlays: fade suave ao abrir */
.drawer-overlay.open,
#modal-banco-overlay,
#modal-banco-center{
    animation:overlayFadeIn .25s ease-out;
}

/* Drawer: entra a deslizar de baixo para cima */
.drawer-overlay.open .drawer-box{
    animation:slideFadeIn .35s cubic-bezier(.16,1,.3,1);
}

/* Modal bancário: entra a deslizar de baixo para cima */
.modal-banco-box{
    animation:slideFadeIn .35s cubic-bezier(.16,1,.3,1);
}

/* Linhas de tipos de bilhete: aparecem em cascata */
.ticket-type{animation:rowPop .4s cubic-bezier(.16,1,.3,1) backwards;}
.ticket-type:nth-child(1){animation-delay:.05s;}
.ticket-type:nth-child(2){animation-delay:.1s;}
.ticket-type:nth-child(3){animation-delay:.15s;}
.ticket-type:nth-child(4){animation-delay:.2s;}
.ticket-type:nth-child(5){animation-delay:.25s;}
.ticket-type:hover{transform:translateX(3px);}

/* Cards bancários: aparecem em cascata dentro do modal (delay via style inline no loop) */
.banco-card{animation:rowPop .4s cubic-bezier(.16,1,.3,1) backwards;}

/* Resumo do evento dentro do modal de inscrição */
.modal-ev-row{animation:rowPop .4s cubic-bezier(.16,1,.3,1) .05s backwards;}
.modal-selected-type{animation:rowPop .4s cubic-bezier(.16,1,.3,1) .1s backwards;}

/* Botões de ação com brilho */
.submit-btn{animation:glowPulse 2.4s ease-in-out infinite;}
.ja-paguei-btn{animation:glowPulse 2.6s ease-in-out infinite;}
.copiar-btn{transition:all .2s;}
.copiar-btn:active{transform:scale(.92);}

/* ─── VÍDEO NO HERO ─── */
.hero-video-btn{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    width:60px;height:60px;border-radius:50%;z-index:10;
    background:rgba(0,0,0,.55);backdrop-filter:blur(8px);
    border:2px solid rgba(255,255,255,.7);color:#fff;font-size:24px;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
    transition:all .2s;
}
.hero-video-btn:hover{background:rgba(0,212,255,.3);border-color:var(--cyan);}
.hero-som-btn{
    position:absolute;bottom:14px;right:14px;z-index:10;
    width:30px;height:30px;border-radius:50%;
    background:rgba(0,0,0,.6);backdrop-filter:blur(6px);
    border:1px solid rgba(255,255,255,.3);color:#fff;font-size:13px;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
}
.modal-video-fullscreen{
    display:none;position:fixed;inset:0;z-index:99999;
    background:rgba(0,0,0,.96);
    align-items:center;justify-content:center;flex-direction:column;
}
/* Em ecrãs a partir de 768px (onde o sidebar e o cabeçalho aparecem),
   o modal fica confinado só à zona de conteúdo — não tapa o sidebar
   (288px, igual ao md:ml-72 do layout) nem o cabeçalho (64px). */
@media(min-width:768px){
    .modal-video-fullscreen{ top:64px; left:288px; right:0; bottom:0; }
}
.modal-video-fullscreen.open{display:flex;}
.modal-video-close{
    position:absolute;top:16px;right:16px;
    width:40px;height:40px;border-radius:50%;
    background:rgba(255,255,255,.15);border:none;color:#fff;font-size:20px;cursor:pointer;
}

@media (prefers-reduced-motion: reduce){
    .anim-fadeInUp,.scroll-reveal,.hero img,.hero::after,.card::after,.card::before,
    .buy-btn:not(:disabled),.drawer-title,.modal-top-title,.modal-banco-title,
    .modal-selected-price,.total-value,#banco-tipo-preco,.drawer-overlay.open,
    #modal-banco-overlay,#modal-banco-center,.drawer-overlay.open .drawer-box,
    .modal-banco-box,.ticket-type,.banco-card,.modal-ev-row,.modal-selected-type,
    .submit-btn,.ja-paguei-btn{
        animation:none!important;transition:none!important;opacity:1!important;transform:none!important;
        -webkit-text-fill-color:var(--txt)!important;background:none!important;
    }
}
/* Anula o padding de 40px que o <main> do layout aplica (md:p-10), dando um
   espaçamento menor e controlado só a esta página — sem mexer no
   app.blade.php, que é partilhado por todas as páginas. */
@media(min-width:768px){
    .page-tight{margin:-40px -40px 0;padding:16px;}
}
@endverbatim
</style>

<div class="page-tight">
<div x-data="{
    modalAberto:false,
    ingressoNome:'',
    ingressoPreco:0,
    ingressoId:'',
    quantidade:1,
    abrirModal(nome,preco,id){
        this.ingressoNome=nome;
        this.ingressoPreco=preco;
        this.ingressoId=id;
        this.quantidade=1;
        this.modalAberto=true;
        document.body.style.overflow='hidden';
    },
    fecharModal(){this.modalAberto=false;document.body.style.overflow='';},
    inc(){if(this.quantidade<10)this.quantidade++;},
    dec(){if(this.quantidade>1)this.quantidade--;},
    total(){return(this.ingressoPreco*this.quantidade).toLocaleString('pt-PT');}
}"
    x-on:abrir-modal.window="abrirModal($event.detail.nome, $event.detail.preco, $event.detail.id)"
    class="page">

{{-- ═══ HERO ═══ --}}
<div class="hero anim-fadeInUp" id="heroBanner"
     onmouseenter="heroHoverIn()" onmouseleave="heroHoverOut()">
    @if($temCapa)
        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @elseif($temFotos)
        <img src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @else
        <div class="hero-ph">{{ $catEmoji }}</div>
    @endif
    <div class="hero-gradient"></div>

    {{-- Camada de vídeo URL externa: iframe mudo sempre visível --}}
    @if($videoEmbed)
    <iframe id="heroIframe" src="{{ $videoEmbed }}"
            style="position:absolute;inset:0;width:300%;height:300%;top:50%;left:50%;transform:translate(-50%,-50%);border:none;pointer-events:none;z-index:1;opacity:.55;"
            allow="autoplay" loading="lazy"></iframe>
    @endif

    {{-- Camada de vídeo local: slot vazio, JS cria o <video> no hover --}}
    @if($videoLocal)
    <div id="heroLocalWrap" data-src="{{ $videoLocal }}"
         style="position:absolute;inset:0;z-index:1;overflow:hidden;display:none;"></div>
    @endif

    {{-- Botão ▶ (só se houver vídeo) — agora troca o texto pelo vídeo no próprio hero --}}
    @if($videoEmbed || $videoLocal)
    <button class="hero-video-btn" id="heroPlayBtn" onclick="tocarVideoNoHero()">▶</button>
    <button class="hero-som-btn" id="heroSomBtn" onclick="toggleHeroSom()"
            style="display:{{ $videoEmbed ? 'flex' : 'none' }}">🔇</button>
    <button class="hero-video-btn" id="heroVoltarBtn" onclick="voltarTextoHero()" style="display:none;">✕</button>
    @endif

    {{-- Badge --}}
    @if($totalDisp <= 0)
        <span class="hero-badge badge-sold">Esgotado</span>
    @elseif($preco == 0)
        <span class="hero-badge badge-free">Gratuito</span>
    @elseif($evento->created_at->isCurrentWeek())
        <span class="hero-badge badge-new">Novo</span>
    @endif

    @if($temFotos && $fotos->count() > 1)
        <button class="hero-fotos-btn" onclick="abrirDrawer('drawer-galeria')">
            🖼 +{{ $fotos->count() }} fotos
        </button>
    @endif

    <div class="hero-content" id="heroContent">
        @if($evento->categoria)
            <div class="hero-category anim-fadeInUp anim-delay-1">{{ $catEmoji }} {{ $evento->categoria->nome }}</div>
        @endif
        <h1 class="hero-title anim-fadeInUp anim-delay-2">{{ e($evento->titulo) }}</h1>
        <div class="hero-pills anim-fadeInUp anim-delay-3">
            <span class="hero-pill">
                📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('d M Y') }}
                @if($evento->hora_inicio) · {{ substr($evento->hora_inicio,0,5) }}@endif
            </span>
            <span class="hero-pill">📍 {{ Str::limit($evento->localizacao,30) }}</span>
            @if($evento->online)<span class="hero-pill">🌐 Online</span>@endif
        </div>
    </div>
</div>

{{-- Modal vídeo fullscreen --}}
@if($videoEmbed || $videoLocal)
<div class="modal-video-fullscreen" id="modalVideoEvento"
     onclick="if(event.target===this) fecharVideoEvento()">
    <button class="modal-video-close" onclick="fecharVideoEvento()">✕</button>
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:12px;text-align:center;">{{ e($evento->titulo) }}</div>
    @if($videoLocal)
        {{-- sem src no HTML — JS define ao abrir --}}
        <video id="modalVideoLocal" data-src="{{ $videoLocal }}"
               controls loop playsinline
               style="width:92%;max-width:1200px;border-radius:20px;max-height:85vh;display:none;box-shadow:0 20px 60px rgba(0,0,0,.6);"></video>
    @else
        {{-- sem src no HTML — JS define ao abrir --}}
        <iframe id="modalVideoIframe"
                data-src="{{ str_replace(['mute=1','muted=1','controls=0'],['mute=0','muted=0','controls=1'], $videoEmbed) }}"
                src=""
                style="width:92%;max-width:1200px;aspect-ratio:16/9;max-height:85vh;border-radius:20px;border:none;box-shadow:0 20px 60px rgba(0,0,0,.6);"
                allow="autoplay; fullscreen" allowfullscreen></iframe>
    @endif
</div>
@endif

{{-- ═══ LAYOUT PRINCIPAL ═══ --}}
<div class="layout">

    {{-- ═══ COLUNA ESQUERDA ═══ --}}
    <div class="layout-left">

        {{-- CARDS HORIZONTAIS --}}
        <div class="cards-row">

            {{-- CARD SOBRE --}}
            <div class="card scroll-reveal">
                <div class="card-head">
                    <div class="card-title">📋 Sobre o Evento</div>
                </div>
                <div class="card-body">
                    <p class="sobre-text" id="sobreText" style="display:-webkit-box;-webkit-line-clamp:5;-webkit-box-orient:vertical;overflow:hidden;">
                        {!! nl2br(e($evento->descricao)) !!}
                    </p>
                    <button class="ver-mais-btn" onclick="toggleSobre()">
                        <span id="sobreBtn">Ver mais ↓</span>
                    </button>

                    {{-- Info rápida integrada --}}
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:5px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span style="font-size:15px;">📅</span>
                            <span>{{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('d M Y') }}</span>
                        </div>
                        @if($evento->hora_inicio)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span style="font-size:15px;">🕐</span>
                            <span>{{ substr($evento->hora_inicio,0,5) }}@if($evento->hora_fim) – {{ substr($evento->hora_fim,0,5) }}@endif</span>
                        </div>
                        @endif
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span style="font-size:15px;">📍</span>
                            <span>{{ e($evento->localizacao) }}</span>
                        </div>
                        @if($evento->municipio || $evento->provincia)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span style="font-size:15px;">🗺</span>
                            <span>{{ e($evento->municipio) }}@if($evento->municipio && $evento->provincia), @endif{{ e($evento->provincia) }}</span>
                        </div>
                        @endif
                        @if($evento->lotacao_maxima)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span style="font-size:15px;">👥</span>
                            <span>Lotação: {{ number_format($evento->lotacao_maxima) }} pessoas</span>
                        </div>
                        @endif
                        @if($evento->online)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--emerald);">
                            <span style="font-size:15px;">🌐</span>
                            <span>Evento Online</span>
                        </div>
                        @endif
                        @if($evento->link_externo)
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
                            <span style="font-size:15px;">🔗</span>
                            <a href="{{ e($evento->link_externo) }}" target="_blank" style="color:var(--cyan);text-decoration:none;">Site oficial</a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="quick-actions">
                    <button onclick="abrirDrawer('drawer-detalhes')" class="qa-btn">🔍 Detalhes</button>
                    <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="qa-btn">💬 Contactar</a>
                </div>
            </div>

            {{-- CARD BILHETES (compacto) --}}
            <div class="card scroll-reveal">
                <div class="card-head">
                    <div class="card-title">🎟 Bilhetes</div>
                    @if($totalDisp > 0)
                    <span style="font-size:10px;font-weight:700;color:var(--emerald);background:rgba(0,200,150,.1);border:1px solid rgba(0,200,150,.2);padding:2px 8px;border-radius:20px;">
                        <span id="contadorDisponiveis" data-target="{{ $totalDisp }}">0</span> disponíveis
                    </span>
                    @else
                    <span style="font-size:10px;font-weight:700;color:var(--rose);">Esgotado</span>
                    @endif
                </div>
                <div class="card-body">
                    <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:900;color:var(--gold);margin-bottom:4px;">
                        @if($preco == 0) Gratuito @else {{ number_format($preco,0,',','.') }} Kz @endif
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:14px;">preço mínimo por bilhete</div>

                    @foreach($evento->tiposIngresso->take(3) as $tipo)
                    @php $esg = $tipo->quantidade_disponivel <= 0; @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:10px;background:var(--card2);border:1px solid var(--border);margin-bottom:6px;{{ $esg ? 'opacity:.5;' : 'cursor:pointer;' }}"
                         @if(!$esg) onclick="abrirModalBanco('{{ addslashes(e($tipo->nome)) }}',{{ $tipo->preco }},{{ $tipo->id }},{{ $tipo->quantidade_disponivel }})" @endif>
                        <div>
                            <div style="font-size:12px;font-weight:700;color:var(--txt);">{{ e($tipo->nome) }}</div>
                            <div style="font-size:10px;color:var(--muted);">{{ $esg ? 'Esgotado' : $tipo->quantidade_disponivel.' disponíveis' }}</div>
                        </div>
                        <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:900;color:var(--gold);">
                            @if($tipo->preco == 0) Grátis @else {{ number_format($tipo->preco,0,',','.') }} Kz @endif
                        </div>
                    </div>
                    @endforeach

                    @if($totalDisp > 0)
                    <button class="buy-btn" style="margin-top:10px;" onclick="abrirDrawer('drawer-bilhetes-mobile')">
                        🛒 Comprar Bilhete
                    </button>
                    <div class="buy-notice" style="margin-top:10px;">⚡ Confirmação rápida e pagamento seguro via IBAN/Multicaixa.</div>
                    @else
                    <button class="buy-btn" disabled style="margin-top:10px;">Evento Esgotado</button>
                    @endif
                </div>
            </div>

            {{-- CARD ORGANIZADOR --}}
            <div class="card scroll-reveal">
                <div class="card-head">
                    <div class="card-title">👤 Organizador</div>
                </div>
                <div class="card-body">
                    @php $org = $evento->user; @endphp
                    @if($org)
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <div style="width:48px;height:48px;border-radius:50%;overflow:hidden;flex-shrink:0;background:linear-gradient(135deg,#0c3a4a,#1e6a7a);border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;color:var(--cyan);">
                            @if($org->avatar)
                                <img src="{{ asset('storage/'.$org->avatar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                            @else
                                {{ strtoupper(substr($org->name,0,2)) }}
                            @endif
                        </div>
                        <div>
                            <div style="font-size:14px;font-weight:700;color:var(--txt);">{{ e($org->name) }}</div>
                            <div style="font-size:11px;color:var(--muted);">{{ ucfirst($org->role) }}</div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span>📅</span>
                            <span>Membro desde {{ $org->created_at->translatedFormat('M Y') }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted2);">
                            <span>🎟</span>
                            <span>{{ $org->eventos()->count() }} evento(s) criado(s)</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:14px;">
                        <a href="{{ route('profile.show', $org->id) }}"
                           style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px;border-radius:10px;background:rgba(0,212,255,.06);border:1px solid var(--border2);color:var(--cyan);font-size:12px;font-weight:600;text-decoration:none;transition:all .2s;">
                            👁 Ver perfil
                        </a>
                        <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id]) }}"
                           style="flex:1;display:flex;align-items:center;justify-content:center;gap:5px;padding:8px;border-radius:10px;background:rgba(0,212,255,.06);border:1px solid var(--border2);color:var(--muted2);font-size:12px;font-weight:600;text-decoration:none;transition:all .2s;">
                            💬 Contactar
                        </a>
                    </div>
                    @else
                    <div style="font-size:13px;color:var(--muted);text-align:center;padding:16px 0;">Informação não disponível</div>
                    @endif
                </div>
            </div>

        </div>{{-- /cards-row --}}

{{-- META ESPECÍFICO DA CATEGORIA --}}
@if($temMeta && !empty($meta))
<div class="card scroll-reveal">
    <div class="card-head">
        <div class="card-title">
            @if(str_contains($catNome,'viag')) ✈️ Detalhes da Viagem
            @elseif(str_contains($catNome,'show') || str_contains($catNome,'musica') || str_contains($catNome,'música')) 🎤 Detalhes do Show
            @elseif(str_contains($catNome,'festiv')) 🎉 Detalhes do Festival
            @elseif(str_contains($catNome,'desporto') || !empty($meta['equipa_local'])) ⚽ Detalhes do Jogo
            @elseif(str_contains($catNome,'confer')) 🎙️ Conferência
            @elseif(str_contains($catNome,'workshop') || !empty($meta['instrutor'])) 📚 Workshop
            @elseif(str_contains($catNome,'cultura')) 🎭 Cultural
            @elseif(str_contains($catNome,'gastro') || !empty($meta['chef'])) 🍽️ Gastronomia
            @else ⚙️ Detalhes específicos
            @endif
        </div>
    </div>
    <div class="card-body">
        @php
            $temRotaNova = !empty($meta['partida_provincia']) || !empty($meta['destino_provincia']);
        @endphp
        @if($temRotaNova)
        <div class="rota-strip">
            <div class="rota-node">
                <div class="rota-lbl">Partida</div>
                <div class="rota-val">{{ e($meta['partida_bairro'] ?: $meta['partida_municipio'] ?: $meta['partida_provincia'] ?: '—') }}</div>
                @if(!empty($meta['partida_provincia']))<div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ e($meta['partida_provincia']) }}</div>@endif
            </div>
            <div class="rota-sep">✈️</div>
            <div class="rota-node">
                <div class="rota-lbl">Destino</div>
                <div class="rota-val">{{ e($meta['destino_bairro'] ?: $meta['destino_municipio'] ?: $meta['destino_provincia'] ?: '—') }}</div>
                @if(!empty($meta['destino_provincia']))<div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ e($meta['destino_provincia']) }}</div>@endif
            </div>
        </div>
        @elseif(!empty($meta['partida']) || !empty($meta['destino']))
        {{-- Compatibilidade com eventos antigos, criados antes da rota dupla --}}
        <div class="rota-strip">
            <div class="rota-node"><div class="rota-lbl">Partida</div><div class="rota-val">{{ e($meta['partida'] ?? '—') }}</div></div>
            <div class="rota-sep">✈️</div>
            <div class="rota-node"><div class="rota-lbl">Destino</div><div class="rota-val">{{ e($meta['destino'] ?? '—') }}</div></div>
        </div>
        @endif
        @if(!empty($meta['ida_volta']) && $meta['ida_volta']=="1")
        <div class="tag-row" style="margin-top:8px;"><span class="tag">🔁 Ida e volta</span></div>
        @endif
        <div class="meta-list">
            @if(!empty($meta['hora_partida']))<div class="meta-row"><div class="meta-row-icon">1️⃣</div><div><div class="meta-row-lbl">Hora de Partida</div><div class="meta-row-val">{{ e($meta['hora_partida']) }}</div></div></div>@endif
            @if(!empty($meta['hora_chegada']))<div class="meta-row"><div class="meta-row-icon">🏁</div><div><div class="meta-row-lbl">Chegada Prevista</div><div class="meta-row-val">{{ e($meta['hora_chegada']) }}</div></div></div>@endif
            @if(!empty($meta['motorista']))<div class="meta-row"><div class="meta-row-icon">👤</div><div><div class="meta-row-lbl">Motorista</div><div class="meta-row-val">{{ e($meta['motorista']) }}</div></div></div>@endif
            @if(!empty($meta['marca_veiculo']))<div class="meta-row"><div class="meta-row-icon">🚌</div><div><div class="meta-row-lbl">Veículo</div><div class="meta-row-val">{{ e($meta['marca_veiculo']) }}@if(!empty($meta['matricula'])) · {{ e($meta['matricula']) }}@endif</div></div></div>@endif
            @if(!empty($meta['palco']))<div class="meta-row"><div class="meta-row-icon">🎪</div><div><div class="meta-row-lbl">Palco</div><div class="meta-row-val">{{ e($meta['palco']) }}</div></div></div>@endif
            @if(!empty($meta['dresscode']))<div class="meta-row"><div class="meta-row-icon">👔</div><div><div class="meta-row-lbl">Dress Code</div><div class="meta-row-val">{{ e($meta['dresscode']) }}</div></div></div>@endif
            @if(!empty($meta['classificacao_etaria']))<div class="meta-row"><div class="meta-row-icon">🔞</div><div><div class="meta-row-lbl">Classificação</div><div class="meta-row-val">{{ e($meta['classificacao_etaria']) }}</div></div></div>@endif
            @if(!empty($meta['nivel']))<div class="meta-row"><div class="meta-row-icon">📊</div><div><div class="meta-row-lbl">Nível</div><div class="meta-row-val">{{ e($meta['nivel']) }}</div></div></div>@endif
            @if(!empty($meta['modalidade']))<div class="meta-row"><div class="meta-row-icon">🏆</div><div><div class="meta-row-lbl">Modalidade</div><div class="meta-row-val">{{ e($meta['modalidade']) }}</div></div></div>@endif
            @if(!empty($meta['dias_festival']))<div class="meta-row"><div class="meta-row-icon">📅</div><div><div class="meta-row-lbl">Duração do Festival</div><div class="meta-row-val">{{ e($meta['dias_festival']) }} dia(s)</div></div></div>@endif
            @if(!empty($meta['num_palcos']))<div class="meta-row"><div class="meta-row-icon">🎪</div><div><div class="meta-row-lbl">Número de Palcos</div><div class="meta-row-val">{{ e($meta['num_palcos']) }}</div></div></div>@endif
            @if(!empty($meta['camping']) && $meta['camping']=="1")<div class="meta-row"><div class="meta-row-icon">⛺</div><div><div class="meta-row-lbl">Camping</div><div class="meta-row-val">Disponível</div></div></div>@endif
            @if(!empty($meta['equipa_local']))<div class="meta-row"><div class="meta-row-icon">🏠</div><div><div class="meta-row-lbl">Equipa Casa</div><div class="meta-row-val">{{ e($meta['equipa_local']) }}</div></div></div>@endif
            @if(!empty($meta['equipa_visitante']))<div class="meta-row"><div class="meta-row-icon">✈️</div><div><div class="meta-row-lbl">Equipa Visitante</div><div class="meta-row-val">{{ e($meta['equipa_visitante']) }}</div></div></div>@endif
            @if(!empty($meta['arbitro']))<div class="meta-row"><div class="meta-row-icon">🧑‍⚖️</div><div><div class="meta-row-lbl">Árbitro</div><div class="meta-row-val">{{ e($meta['arbitro']) }}</div></div></div>@endif
            @if(!empty($meta['fase']))<div class="meta-row"><div class="meta-row-icon">🏅</div><div><div class="meta-row-lbl">Fase / Competição</div><div class="meta-row-val">{{ e($meta['fase']) }}</div></div></div>@endif
            @if(!empty($meta['tema']))<div class="meta-row"><div class="meta-row-icon">💡</div><div><div class="meta-row-lbl">Tema</div><div class="meta-row-val">{{ e($meta['tema']) }}</div></div></div>@endif
            @if(!empty($meta['requisitos']))<div class="meta-row"><div class="meta-row-icon">✅</div><div><div class="meta-row-lbl">Requisitos</div><div class="meta-row-val">{{ e($meta['requisitos']) }}</div></div></div>@endif
            @if(!empty($meta['instrutor']))<div class="meta-row"><div class="meta-row-icon">👨‍🏫</div><div><div class="meta-row-lbl">Instrutor</div><div class="meta-row-val">{{ e($meta['instrutor']) }}</div></div></div>@endif
            @if(!empty($meta['duracao_horas']))<div class="meta-row"><div class="meta-row-icon">⏱️</div><div><div class="meta-row-lbl">Duração</div><div class="meta-row-val">{{ e($meta['duracao_horas']) }}h</div></div></div>@endif
            @if(!empty($meta['max_alunos']))<div class="meta-row"><div class="meta-row-icon">👥</div><div><div class="meta-row-lbl">Máx. de Alunos</div><div class="meta-row-val">{{ e($meta['max_alunos']) }}</div></div></div>@endif
            @if(!empty($meta['materiais']))<div class="meta-row"><div class="meta-row-icon">🎒</div><div><div class="meta-row-lbl">Materiais Incluídos</div><div class="meta-row-val">{{ e($meta['materiais']) }}</div></div></div>@endif
            @if(!empty($meta['duracao_minutos']))<div class="meta-row"><div class="meta-row-icon">⏱️</div><div><div class="meta-row-lbl">Duração</div><div class="meta-row-val">{{ e($meta['duracao_minutos']) }} min</div></div></div>@endif
            @if(!empty($meta['certificado']) && $meta['certificado']=="1")<div class="meta-row"><div class="meta-row-icon">🎓</div><div><div class="meta-row-lbl">Certificado</div><div class="meta-row-val">Incluído</div></div></div>@endif
            @if(!empty($meta['chef']))<div class="meta-row"><div class="meta-row-icon">👨‍🍳</div><div><div class="meta-row-lbl">Chef</div><div class="meta-row-val">{{ e($meta['chef']) }}</div></div></div>@endif
            @if(!empty($meta['tipo_culinaria']))<div class="meta-row"><div class="meta-row-icon">🍽️</div><div><div class="meta-row-lbl">Culinária</div><div class="meta-row-val">{{ e($meta['tipo_culinaria']) }}</div></div></div>@endif
            @if(!empty($meta['preco_menu']))<div class="meta-row"><div class="meta-row-icon">💰</div><div><div class="meta-row-lbl">Preço do Menu</div><div class="meta-row-val">{{ number_format($meta['preco_menu'], 0, ',', '.') }} Kz</div></div></div>@endif
            @if(!empty($meta['bebidas_incluidas']) && $meta['bebidas_incluidas']=="1")<div class="meta-row"><div class="meta-row-icon">🍹</div><div><div class="meta-row-lbl">Bebidas</div><div class="meta-row-val">Incluídas</div></div></div>@endif
            @if(!empty($meta['idioma']))<div class="meta-row"><div class="meta-row-icon">🌐</div><div><div class="meta-row-lbl">Idioma</div><div class="meta-row-val">{{ e($meta['idioma']) }}</div></div></div>@endif
            @if(!empty($meta['ar_condicionado']) && $meta['ar_condicionado']=="1")<div class="meta-row"><div class="meta-row-icon">❄️</div><div><div class="meta-row-lbl">Conforto</div><div class="meta-row-val">Ar condicionado</div></div></div>@endif
            @if(!empty($meta['tipo_veiculo']))<div class="meta-row"><div class="meta-row-icon">🚐</div><div><div class="meta-row-lbl">Tipo de Veículo</div><div class="meta-row-val">{{ e($meta['tipo_veiculo']) }}</div></div></div>@endif
            @if(!empty($meta['contacto_motorista']))<div class="meta-row"><div class="meta-row-icon">📞</div><div><div class="meta-row-lbl">Contacto</div><div class="meta-row-val">{{ e($meta['contacto_motorista']) }}</div></div></div>@endif
            @if(!empty($meta['cancelamento_horas']))<div class="meta-row"><div class="meta-row-icon">↩️</div><div><div class="meta-row-lbl">Cancelamento</div><div class="meta-row-val">Até {{ e($meta['cancelamento_horas']) }}h antes</div></div></div>@endif
            @if(!empty($meta['hora_abertura_portas']))<div class="meta-row"><div class="meta-row-icon">🚪</div><div><div class="meta-row-lbl">Abertura de Portas</div><div class="meta-row-val">{{ e($meta['hora_abertura_portas']) }}</div></div></div>@endif
            @if(!empty($meta['politica_entrada']))<div class="meta-row"><div class="meta-row-icon">🎫</div><div><div class="meta-row-lbl">Entrada/Saída</div><div class="meta-row-val">{{ e($meta['politica_entrada']) }}</div></div></div>@endif
            @if(!empty($meta['nome_competicao']))<div class="meta-row"><div class="meta-row-icon">🏆</div><div><div class="meta-row-lbl">Competição/Liga</div><div class="meta-row-val">{{ e($meta['nome_competicao']) }}</div></div></div>@endif
            @if(!empty($meta['escalao']))<div class="meta-row"><div class="meta-row-icon">🎽</div><div><div class="meta-row-lbl">Escalão</div><div class="meta-row-val">{{ e($meta['escalao']) }}</div></div></div>@endif
            @if(!empty($meta['duracao_formato']))<div class="meta-row"><div class="meta-row-icon">⏱️</div><div><div class="meta-row-lbl">Formato</div><div class="meta-row-val">{{ e($meta['duracao_formato']) }}</div></div></div>@endif
            @if(!empty($meta['entidade_organizadora']))<div class="meta-row"><div class="meta-row-icon">🏛️</div><div><div class="meta-row-lbl">Organização</div><div class="meta-row-val">{{ e($meta['entidade_organizadora']) }}</div></div></div>@endif
            @if(!empty($meta['inclui_almoco']) && $meta['inclui_almoco']=="1")<div class="meta-row"><div class="meta-row-icon">🍱</div><div><div class="meta-row-lbl">Refeição</div><div class="meta-row-val">Almoço/coffee break incluído</div></div></div>@endif
            @if(!empty($meta['networking']) && $meta['networking']=="1")<div class="meta-row"><div class="meta-row-icon">🤝</div><div><div class="meta-row-lbl">Networking</div><div class="meta-row-val">Incluído</div></div></div>@endif
            @if(!empty($meta['pre_requisitos']))<div class="meta-row"><div class="meta-row-icon">📋</div><div><div class="meta-row-lbl">Pré-requisitos</div><div class="meta-row-val">{{ e($meta['pre_requisitos']) }}</div></div></div>@endif
            @if(!empty($meta['o_que_trazer']))<div class="meta-row"><div class="meta-row-icon">🎒</div><div><div class="meta-row-lbl">O que trazer</div><div class="meta-row-val">{{ e($meta['o_que_trazer']) }}</div></div></div>@endif
            @if(!empty($meta['genero_tipo']))<div class="meta-row"><div class="meta-row-icon">🎬</div><div><div class="meta-row-lbl">Género/Tipo</div><div class="meta-row-val">{{ e($meta['genero_tipo']) }}</div></div></div>@endif
            @if(!empty($meta['sala']))<div class="meta-row"><div class="meta-row-icon">🚪</div><div><div class="meta-row-lbl">Sala/Auditório</div><div class="meta-row-val">{{ e($meta['sala']) }}</div></div></div>@endif
            @if(!empty($meta['reserva_obrigatoria']) && $meta['reserva_obrigatoria']=="1")<div class="meta-row"><div class="meta-row-icon">📌</div><div><div class="meta-row-lbl">Reserva</div><div class="meta-row-val">Mesa obrigatória</div></div></div>@endif
        </div>

        @if(!empty($meta['link_transmissao']))
        <div style="margin-top:12px;"><a href="{{ $meta['link_transmissao'] }}" target="_blank" rel="noopener" class="tag" style="text-decoration:none;">📡 Ver transmissão em direto ↗</a></div>
        @endif

        @if(!empty($meta['comodidades']) && is_array($meta['comodidades']))
        @php
            $comodidadesLbl = ['agua'=>'💧 Água','wc'=>'🚻 Casas de banho','comida'=>'🍔 Comida','parque'=>'🅿️ Parqueamento','socorros'=>'🏥 Primeiros socorros'];
        @endphp
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">Comodidades no recinto</div><div class="tag-row">@foreach($meta['comodidades'] as $com)<span class="tag">{{ $comodidadesLbl[$com] ?? e($com) }}</span>@endforeach</div></div>
        @endif

        @if(!empty($meta['restricoes_alimentares']) && is_array($meta['restricoes_alimentares']))
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">Restrições alimentares disponíveis</div><div class="tag-row">@foreach($meta['restricoes_alimentares'] as $r)<span class="tag">{{ e($r) }}</span>@endforeach</div></div>
        @endif

        @if(!empty($meta['escudo_casa']) || !empty($meta['escudo_visitante']))
        <div class="rota-strip" style="margin-top:12px;">
            <div class="rota-node">
                @if(!empty($meta['escudo_casa']))<img src="{{ asset('storage/'.$meta['escudo_casa']) }}" alt="Equipa da casa" style="width:44px;height:44px;object-fit:cover;border-radius:50%;margin:0 auto 6px;display:block;">@endif
                <div class="rota-lbl">Casa</div><div class="rota-val">{{ e($meta['equipa_local'] ?? '—') }}</div>
            </div>
            <div class="rota-sep">×</div>
            <div class="rota-node">
                @if(!empty($meta['escudo_visitante']))<img src="{{ asset('storage/'.$meta['escudo_visitante']) }}" alt="Equipa visitante" style="width:44px;height:44px;object-fit:cover;border-radius:50%;margin:0 auto 6px;display:block;">@endif
                <div class="rota-lbl">Visitante</div><div class="rota-val">{{ e($meta['equipa_visitante'] ?? '—') }}</div>
            </div>
        </div>
        @endif

        @if((str_contains($catNome,'viag')) && !empty($tParagens))
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">📍 Paragens</div><div class="tag-row">@foreach($tParagens as $t)<span class="tag">{{ e($t) }}</span>@endforeach</div></div>
        @endif
        @if((str_contains($catNome,'show')||str_contains($catNome,'musica')||str_contains($catNome,'música')||str_contains($catNome,'festiv'))&&!empty($meta['lineup']))
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">🕐 Lineup</div><pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['lineup']) }}</pre></div>
        @endif
        @if(str_contains($catNome,'confer')&&!empty($meta['agenda']))
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">🗓️ Agenda / Programa</div><pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['agenda']) }}</pre></div>
        @endif
        @if((str_contains($catNome,'gastro')||!empty($meta['chef']))&&!empty($meta['menu']))
        <div style="margin-top:12px;"><div class="meta-row-lbl" style="margin-bottom:6px;">📋 Ementa</div><pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['menu']) }}</pre></div>
        @endif
    </div>
</div>
@endif

@if(count($tArtistas) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎤 Artistas / Atuações</div>
<div class="artist-list">@foreach($tArtistas as $art)
    @php $artNome = is_array($art) ? ($art['nome'] ?? '') : $art; $artFoto = is_array($art) ? ($art['foto'] ?? null) : null; @endphp
    <div class="artist-item"><div class="artist-avatar" @if($artFoto) style="background-image:url('{{ asset('storage/'.$artFoto) }}');background-size:cover;background-position:center;" @endif>@if(!$artFoto)✨@endif</div><div><div class="artist-name">{{ e($artNome) }}</div><div class="artist-role">Convidado Especial</div></div></div>
@endforeach</div>
@endif
@if(count($tPalestrantes) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎙️ Palestrantes / Facilitadores</div>
<div class="artist-list">@foreach($tPalestrantes as $pal)
    @php $palNome = is_array($pal) ? ($pal['nome'] ?? '') : $pal; $palFoto = is_array($pal) ? ($pal['foto'] ?? null) : null; @endphp
    <div class="artist-item"><div class="artist-avatar" @if($palFoto) style="background-image:url('{{ asset('storage/'.$palFoto) }}');background-size:cover;background-position:center;" @endif>@if(!$palFoto)👨‍🏫@endif</div><div><div class="artist-name">{{ e($palNome) }}</div><div class="artist-role">Orador</div></div></div>
@endforeach</div>
@endif
@if(count($tElenco) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎭 Elenco / Participantes</div>
<div class="artist-list">@foreach($tElenco as $elc)
    @php $elcNome = is_array($elc) ? ($elc['nome'] ?? '') : $elc; $elcFoto = is_array($elc) ? ($elc['foto'] ?? null) : null; @endphp
    <div class="artist-item"><div class="artist-avatar" @if($elcFoto) style="background-image:url('{{ asset('storage/'.$elcFoto) }}');background-size:cover;background-position:center;" @endif>@if(!$elcFoto)🎬@endif</div><div><div class="artist-name">{{ e($elcNome) }}</div><div class="artist-role">Ator / Participante</div></div></div>
@endforeach</div>
@endif

@if(!empty($meta['foto_instrutor']) || !empty($meta['foto_chef']))
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">{{ !empty($meta['foto_instrutor']) ? '👨‍🏫 Instrutor' : '👨‍🍳 Chef' }}</div>
<div class="artist-list">
    <div class="artist-item">
        <div class="artist-avatar" style="background-image:url('{{ asset('storage/'.($meta['foto_instrutor'] ?? $meta['foto_chef'])) }}');background-size:cover;background-position:center;"></div>
        <div><div class="artist-name">{{ e($meta['instrutor'] ?? $meta['chef'] ?? '') }}</div></div>
    </div>
</div>
@endif

    </div>{{-- /layout-left --}}
</div>{{-- /layout --}}

{{-- BARRA FIXA MOBILE --}}
<div class="bottom-bar show-mobile">
    <div>
        <div class="bottom-bar-price">{{ number_format($preco,0,',','.') }} <small style="font-size:10px;color:var(--muted)">KZ+</small></div>
        <div class="bottom-bar-label">Total inicial</div>
    </div>
    <button class="bottom-bar-btn" {{ $totalDisp<=0 ? 'disabled' : '' }} onclick="abrirDrawer('drawer-bilhetes-mobile')">
        🛒 Comprar
    </button>
</div>

{{-- DRAWER BILHETES MOBILE --}}
<div class="drawer-overlay" id="drawer-bilhetes-mobile" onclick="if(event.target===this) fecharDrawer('drawer-bilhetes-mobile')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">🎟️ Escolha seu Ingresso</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-bilhetes-mobile')">×</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($evento->tiposIngresso as $tipo)
            @php $esg = $tipo->quantidade_disponivel <= 0; @endphp
            <div class="ticket-type {{ $esg ? 'esgotado' : '' }}"
                 onclick="abrirModalBanco('{{ e($tipo->nome) }}',{{ $tipo->preco }},{{ $tipo->id }},{{ $tipo->quantidade_disponivel }})">
                <div>
                    <div class="ticket-type-name">{{ e($tipo->nome) }}</div>
                    <div class="ticket-type-avail">
                        @if($esg)
                            <span style="color:var(--rose)">Esgotado</span>
                        @else
                            {{ $tipo->quantidade_disponivel }} disponíveis
                        @endif
                    </div>
                </div>
                <div class="ticket-type-price">
                    @if($tipo->preco == 0) Gratuito @else {{ number_format($tipo->preco,0,',','.') }}<small>KZ</small> @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- DRAWER DETALHES --}}
<div class="drawer-overlay" id="drawer-detalhes" onclick="if(event.target===this) fecharDrawer('drawer-detalhes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">🔍 Informações Detalhadas</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-detalhes')">×</button>
        </div>
        <div class="drawer-info-row"><span class="drawer-info-lbl">Organizador</span><span class="drawer-info-val">{{ e(optional($evento->user)->name ?? 'Não informado') }}</span></div>
        <div class="drawer-info-row"><span class="drawer-info-lbl">Telefone</span><span class="drawer-info-val">{{ e($evento->telefone_contacto ?? 'Não informado') }}</span></div>
        <div class="drawer-info-row"><span class="drawer-info-lbl">Email</span><span class="drawer-info-val" style="word-break:break-all;">{{ e($evento->email_contacto ?? 'Não informado') }}</span></div>
        <div class="drawer-info-row"><span class="drawer-info-lbl">Abertura de portas</span><span class="drawer-info-val">{{ $evento->hora_inicio ? substr($evento->hora_inicio,0,5) : '—' }}</span></div>
        @if($evento->link_externo)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Website</span><span class="drawer-info-val"><a href="{{ e($evento->link_externo) }}" target="_blank" style="color:var(--cyan);">Visitar 🔗</a></span></div>
        @endif
        <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="contactar-btn">💬 Enviar Mensagem Direta</a>
    </div>
</div>

{{-- DRAWER GALERIA --}}
@if($temFotos)
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this) fecharDrawer('drawer-galeria')">
    <div class="drawer-box" style="max-width:540px;">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">🖼️ Galeria de Fotos ({{ $fotos->count() }})</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">×</button>
        </div>
        <div class="gallery-drawer-grid">
            @foreach($fotos as $ft)
            <a href="{{ asset('storage/'.$ft->caminho) }}" target="_blank">
                <img src="{{ asset('storage/'.$ft->caminho) }}" alt="Foto do evento" loading="lazy">
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- MODAL BANCÁRIO --}}
<div id="modal-banco-overlay" onclick="fecharModalBanco()"
     style="display:none;position:fixed;inset:0;z-index:980;background:rgba(4,6,13,.92);backdrop-filter:blur(14px);"></div>
<div id="modal-banco-center"
     style="display:none;position:fixed;inset:0;z-index:981;align-items:flex-end;justify-content:center;">
    <div class="modal-banco-box" onclick="event.stopPropagation()">
        <div class="modal-banco-drag"></div>
        <div class="modal-banco-head">
            <div class="modal-banco-title">🏦 Dados para Pagamento</div>
            <button class="modal-banco-x" onclick="fecharModalBanco()">✕</button>
        </div>
        <div class="modal-banco-body">
            <div style="background:var(--card2);border:1px solid var(--border2);border-radius:12px;padding:12px 14px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:13px;font-weight:800;color:var(--txt);" id="banco-tipo-nome">—</div>
                    <div style="font-size:11px;color:var(--muted);">Tipo de bilhete seleccionado</div>
                </div>
                <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:var(--gold);" id="banco-tipo-preco">—</div>
            </div>
            <div class="modal-banco-sub">📋 Copie os dados bancários, efectue o pagamento e clique em "Já paguei".</div>
            @forelse($contasBancarias as $conta)
            <div class="banco-card" style="animation-delay:{{ $loop->index * 0.08 }}s;">
                <div class="banco-header">
                    @if($conta->logo)<img src="{{ asset('images/bancos/'.$conta->logo) }}" class="banco-logo" alt="{{ $conta->nome_banco }}">
                    @else<div class="banco-logo-ph">🏦</div>@endif
                    <div>
                        <div class="banco-nome">{{ e($conta->nome_banco) }}</div>
                        <div class="banco-titular">{{ e($conta->titular) }}</div>
                    </div>
                </div>
                <div class="banco-row">
                    <div><div class="banco-lbl">IBAN</div><div class="banco-val" id="iban-{{ $conta->id }}">{{ e($conta->iban) }}</div></div>
                    <button class="copiar-btn" onclick="copiarTexto('iban-{{ $conta->id }}', this)">📋 Copiar IBAN</button>
                </div>
                @if($conta->numero_conta)
                <div class="banco-row">
                    <div><div class="banco-lbl">Nº Conta</div><div class="banco-val" id="conta-{{ $conta->id }}">{{ e($conta->numero_conta) }}</div></div>
                    <button class="copiar-btn" onclick="copiarTexto('conta-{{ $conta->id }}', this)">📋 Copiar</button>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">⚠️ Nenhuma conta bancária disponível.</div>
            @endforelse
            <div class="modal-banco-separator"></div>
            <button class="ja-paguei-btn" onclick="jaEfetueiPagamento()">
                ✅ Já efectuei o pagamento — Preencher dados e anexar comprovativo
            </button>
            <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:10px;line-height:1.6;">O staff valida o comprovativo em até 24h</div>
        </div>
    </div>
</div>

{{-- MODAL ALPINE COMPRA --}}
<div x-data="{
    modalAberto:false,ingressoNome:'',ingressoPreco:0,ingressoId:'',quantidade:1,
    inc(){this.quantidade++},dec(){if(this.quantidade>1)this.quantidade--},
    total(){return(this.quantidade*this.ingressoPreco).toLocaleString('pt-PT')},
    fecharModal(){this.modalAberto=false;document.body.style.overflow='';}
}"
@abrir-modal.window="ingressoNome=$event.detail.nome;ingressoPreco=$event.detail.preco;ingressoId=$event.detail.id;quantidade=1;modalAberto=true;document.body.style.overflow='hidden';">
    <div x-show="modalAberto" class="modal-overlay" x-cloak style="display:none;"></div>
    <div x-show="modalAberto" class="modal-center" x-cloak style="display:none;" @click="if($event.target===$el) fecharModal()">
        <div class="modal-box" x-show="modalAberto"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full">
            <div class="modal-drag"></div>
            <div class="modal-top">
                <div><div class="modal-secure-badge">Ambiente de Compra Seguro</div><div class="modal-top-title">Inscrição no Evento</div></div>
                <button type="button" class="modal-x" @click="fecharModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-ev-row">
                    @if($temCapa)<img class="modal-ev-thumb" src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">
                    @elseif($temFotos)<img class="modal-ev-thumb" src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="">
                    @else<div class="modal-ev-thumb-ph">{{ $catEmoji }}</div>@endif
                    <div><div class="modal-ev-name">{{ e($evento->titulo) }}</div><div class="modal-ev-sub">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</div></div>
                </div>
                <div class="modal-selected-type">
                    <div><div class="modal-selected-name" x-text="ingressoNome"></div><div class="modal-selected-sub">Tipo de ingresso escolhido</div></div>
                    <div class="modal-selected-price"><span x-text="ingressoPreco==0?'Gratuito':(ingressoPreco).toLocaleString('pt-PT')"></span><small class="modal-selected-kz" x-show="ingressoPreco>0">KZ</small></div>
                </div>
                <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                    <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                    <div class="fg" x-show="ingressoPreco>0">
                        <label class="fl">Quantidade de Bilhetes</label>
                        <div class="qty-wrapper"><span class="qty-label">Selecione a quantidade</span><div class="qty-controls"><button type="button" class="qty-control-btn" @click="dec()">-</button><span class="qty-value" x-text="quantidade"></span><input type="hidden" name="quantidade" :value="quantidade"><button type="button" class="qty-control-btn" @click="inc()">+</button></div></div>
                    </div>
                    <div class="total-row" x-show="ingressoPreco>0"><span class="total-label">Total a Pagar:</span><span class="total-value"><span x-text="total()"></span><small style="font-size:12px;color:var(--gold);">KZ</small></span></div>
                    <div class="fg"><label class="fl">Nome Completo</label><input type="text" name="nome_cliente" class="fi" placeholder="Seu nome completo" required></div>
                    <div class="fg"><label class="fl">Telefone / WhatsApp</label><input type="text" name="whatsapp" class="fi" placeholder="Ex: 923 000 000" required></div>
                    <div class="fg" x-show="ingressoPreco>0">
                        <label class="fl">Comprovativo de Pagamento</label>
                        <div class="upload-area"><input type="file" name="comprovativo" onchange="handleUpload(this)"><div class="upload-icon">📁</div><div class="upload-label">Clique para carregar o comprovativo</div><div class="upload-hint">Formatos: JPG, PNG (Máx: 2MB)</div></div>
                        <div class="upload-preview" id="upload-preview"><div class="upload-preview-name" id="upload-preview-name"></div></div>
                    </div>
                    <button type="submit" class="submit-btn"><span>Confirmar Inscrição 🚀</span></button>
                    <p class="submit-notice">Ao confirmar, os seus dados serão enviados para a organização do evento de forma segura.</p>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
function copiarTexto(elementId, btn) {
    var texto = document.getElementById(elementId)?.textContent?.trim();
    if (!texto) return;
    navigator.clipboard.writeText(texto).then(function() {
        var original = btn.innerHTML;
        btn.innerHTML = '✅ Copiado!';
        btn.style.color = '#00c896';
        setTimeout(function() { btn.innerHTML = original; btn.style.color = ''; }, 2000);
    });
}
function abrirModalBanco(nome, preco, id, disp) {
    if (parseInt(disp) <= 0) return;
    fecharDrawer('drawer-bilhetes-mobile');
    window._bilheteSeleccionado = {nome: nome, preco: parseFloat(preco), id: parseInt(id)};
    document.getElementById('banco-tipo-nome').textContent = nome;
    document.getElementById('banco-tipo-preco').textContent = Number(preco).toLocaleString('pt-PT') + ' Kz';
    document.getElementById('modal-banco-overlay').style.display = 'block';
    document.getElementById('modal-banco-center').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalBanco() {
    document.getElementById('modal-banco-overlay').style.display = 'none';
    document.getElementById('modal-banco-center').style.display = 'none';
    document.body.style.overflow = '';
}
function jaEfetueiPagamento() {
    fecharModalBanco();
    var b = window._bilheteSeleccionado;
    if (b) window.dispatchEvent(new CustomEvent('abrir-modal', {detail:{nome:b.nome,preco:b.preco,id:b.id}}));
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.drawer-overlay.open').forEach(function(d){d.classList.remove('open');});
        fecharModalBanco();
        fecharVideoEvento();
        document.body.style.overflow = '';
    }
});

// ── Teleporta o modal de vídeo para o <body> ──────────────
// Escapa de qualquer elemento "pai" no layout que possa confinar o
// position:fixed a um espaço menor que o ecrã (mesma técnica já usada
// no dropdown de estado do admin-eventos.blade.php).
document.addEventListener('DOMContentLoaded', function() {
    var modalVideo = document.getElementById('modalVideoEvento');
    if (modalVideo) document.body.appendChild(modalVideo);
});

// ── Scroll reveal ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var els = document.querySelectorAll('.scroll-reveal');
    if (!('IntersectionObserver' in window)) {
        els.forEach(function(el){ el.classList.add('is-visible'); });
        return;
    }
    var obs = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) { entry.target.classList.add('is-visible'); obs.unobserve(entry.target); }
        });
    }, { threshold: 0.15 });
    els.forEach(function(el){ obs.observe(el); });
});

// ── Contador de bilhetes disponíveis ──────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var contador = document.getElementById('contadorDisponiveis');
    if (!contador) return;
    var alvo = parseInt(contador.dataset.target, 10) || 0;
    if (alvo <= 0) { contador.textContent = '0'; return; }
    var inicio = null;
    function passo(ts) {
        if (!inicio) inicio = ts;
        var prog = Math.min((ts - inicio) / 1000, 1);
        contador.textContent = Math.max(1, Math.floor(prog * alvo));
        if (prog < 1) requestAnimationFrame(passo);
        else contador.textContent = alvo;
    }
    requestAnimationFrame(passo);
});

// ── VÍDEO NO HERO ─────────────────────────────────────────
var _heroVideoEl  = null;
var _heroSomActivo = false;
var _heroTimer    = null;

function heroHoverIn() {
    clearTimeout(_heroTimer);
    _heroTimer = setTimeout(function() {
        var wrap = document.getElementById('heroLocalWrap');
        if (wrap && wrap.dataset.src) {
            // Vídeo local — criar sem src até ter muted garantido
            if (!_heroVideoEl) {
                var v = document.createElement('video');
                v.loop = true;
                v.playsInline = true;
                v.muted  = true;
                v.volume = 0;
                v.style.cssText = 'width:100%;height:100%;object-fit:cover;pointer-events:none;';
                v.src = wrap.dataset.src;
                wrap.appendChild(v);
                _heroVideoEl = v;
            }
            wrap.style.display = 'block';
            _heroVideoEl.muted  = true;
            _heroVideoEl.volume = 0;
            _heroVideoEl.play().catch(function(){});
            var somBtn = document.getElementById('heroSomBtn');
            if (somBtn) somBtn.style.display = 'flex';
        }
        // Para URL externa o iframe já está visível — nada a fazer
    }, 300);
}

function heroHoverOut() {
    clearTimeout(_heroTimer);
    var wrap = document.getElementById('heroLocalWrap');
    if (wrap && _heroVideoEl) {
        _heroVideoEl.pause();
        wrap.style.display = 'none';
        _heroVideoEl.muted  = true;
        _heroVideoEl.volume = 0;
        _heroSomActivo = false;
        var somBtn = document.getElementById('heroSomBtn');
        if (somBtn) { somBtn.style.display = 'none'; somBtn.textContent = '🔇'; }
    }
}

function toggleHeroSom() {
    _heroSomActivo = !_heroSomActivo;
    var btn = document.getElementById('heroSomBtn');
    // Vídeo local
    if (_heroVideoEl) {
        if (_heroSomActivo) {
            _heroVideoEl.removeAttribute('muted');
            _heroVideoEl.muted  = false;
            _heroVideoEl.volume = 1;
        } else {
            _heroVideoEl.muted  = true;
            _heroVideoEl.volume = 0;
        }
    }
    // iframe YouTube
    var iframe = document.getElementById('heroIframe');
    if (iframe) {
        iframe.contentWindow.postMessage(
            JSON.stringify({event:'command', func: _heroSomActivo ? 'unMute' : 'mute', args:[]}), '*'
        );
    }
    if (btn) btn.textContent = _heroSomActivo ? '🔊' : '🔇';
}

// NOVO — troca o texto do hero pelo vídeo, no próprio hero (sem abrir modal).
// Reaproveita as mesmas camadas de vídeo já usadas na pré-visualização por
// hover (heroIframe / heroLocalWrap), só que agora com som e visível a sério.
function tocarVideoNoHero() {
    clearTimeout(_heroTimer);

    var content = document.getElementById('heroContent');
    if (content) content.classList.add('hero-content-escondido');

    var wrap = document.getElementById('heroLocalWrap');
    if (wrap && wrap.dataset.src) {
        if (!_heroVideoEl) {
            var v = document.createElement('video');
            v.loop = true;
            v.playsInline = true;
            v.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            v.src = wrap.dataset.src;
            wrap.appendChild(v);
            _heroVideoEl = v;
        }
        wrap.style.display = 'block';
        _heroVideoEl.controls = true;
        _heroVideoEl.muted  = false;
        _heroVideoEl.volume = 1;
        _heroVideoEl.play().catch(function(){});
    }

    var iframe = document.getElementById('heroIframe');
    if (iframe) {
        iframe.style.opacity = '1';
        iframe.style.pointerEvents = 'auto';
        iframe.contentWindow.postMessage(JSON.stringify({event:'command', func:'unMute', args:[]}), '*');
    }

    _heroSomActivo = true;
    var somBtn = document.getElementById('heroSomBtn');
    if (somBtn) somBtn.style.display = 'none';

    document.getElementById('heroPlayBtn').style.display   = 'none';
    document.getElementById('heroVoltarBtn').style.display = 'flex';
}

// NOVO — volta ao texto, silenciando e escondendo o vídeo outra vez.
function voltarTextoHero() {
    var content = document.getElementById('heroContent');
    if (content) content.classList.remove('hero-content-escondido');

    var wrap = document.getElementById('heroLocalWrap');
    if (wrap && _heroVideoEl) {
        _heroVideoEl.pause();
        _heroVideoEl.controls = false;
        _heroVideoEl.muted  = true;
        _heroVideoEl.volume = 0;
        wrap.style.display = 'none';
    }

    var iframe = document.getElementById('heroIframe');
    if (iframe) {
        iframe.style.opacity = '.55';
        iframe.style.pointerEvents = 'none';
        iframe.contentWindow.postMessage(JSON.stringify({event:'command', func:'mute', args:[]}), '*');
    }

    _heroSomActivo = false;
    document.getElementById('heroPlayBtn').style.display   = 'flex';
    document.getElementById('heroVoltarBtn').style.display = 'none';
}

function abrirVideoEvento() {
    var modal = document.getElementById('modalVideoEvento');
    if (!modal) return;
    // Vídeo local — definir src só ao abrir
    var videoEl = document.getElementById('modalVideoLocal');
    if (videoEl && videoEl.dataset.src) {
        videoEl.src = videoEl.dataset.src;
        videoEl.style.display = 'block';
        videoEl.muted  = false;
        videoEl.volume = 1;
        videoEl.play().catch(function(){});
    }
    // iframe — definir src só ao abrir
    var iframeEl = document.getElementById('modalVideoIframe');
    if (iframeEl && iframeEl.dataset.src && !iframeEl.src) {
        iframeEl.src = iframeEl.dataset.src;
    }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function fecharVideoEvento() {
    var modal = document.getElementById('modalVideoEvento');
    if (!modal) return;
    var videoEl  = document.getElementById('modalVideoLocal');
    var iframeEl = document.getElementById('modalVideoIframe');
    if (videoEl)  { videoEl.pause(); videoEl.src = ''; videoEl.style.display = 'none'; }
    if (iframeEl) { iframeEl.src = ''; }
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
</script>
@endsection
