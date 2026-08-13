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
$videoPreview  = $evento->video_preview ?? null;
// Detecta automaticamente: URL externa ou ficheiro local
$vUrl = $evento->video_preview ?? null;
$embedUrl = null;
$videoLocal = null;
if ($vUrl) {
    if (str_starts_with($vUrl, 'http')) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $vUrl, $m)) {
            $embedUrl = "https://www.youtube.com/embed/{$m[1]}?autoplay=1&mute=1&loop=1&playlist={$m[1]}&controls=0&playsinline=1&modestbranding=1&rel=0";
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrl, $m)) {
            $embedUrl = "https://player.vimeo.com/video/{$m[1]}?autoplay=1&muted=1&loop=1&controls=0";
        }
    } else {
        // Ficheiro local — tag <video>
        $videoLocal = asset('storage/'.$vUrl);
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
@endverbatim
</style>
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
<div class="hero anim-fadeInUp">
    @if($temCapa)
        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @elseif($temFotos)
        <img src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @else
        <div class="hero-ph">{{ $catEmoji }}</div>
    @endif
    <div class="hero-gradient"></div>

    @if($videoEmbed)
    <iframe id="heroVideoIframe" src="{{ $videoEmbed }}"
        style="position:absolute;inset:0;width:300%;height:300%;top:50%;left:50%;transform:translate(-50%,-50%);border:none;pointer-events:none;z-index:1;opacity:.65;"
        allow="autoplay" loading="lazy"></iframe>
    <button id="heroPlayBtn" onclick="abrirVideoEvento()" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:64px;height:64px;border-radius:50%;z-index:10;background:rgba(0,0,0,.55);backdrop-filter:blur(8px);border:2px solid rgba(255,255,255,.7);color:#fff;font-size:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;">▶</button>
    <button id="heroSomBtn" onclick="toggleHeroSom()" style="position:absolute;bottom:18px;right:18px;z-index:10;width:32px;height:32px;border-radius:50%;background:rgba(0,0,0,.6);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:14px;cursor:pointer;">🔇</button>
    @endif

    {{-- Badge --}}
    @if($totalDisp <= 0)
        <span class="hero-badge badge-sold">Esgotado</span>
    @elseif($preco == 0)
        <span class="hero-badge badge-free">Gratuito</span>
    @elseif($evento->created_at->isCurrentWeek())
        <span class="hero-badge badge-new">Novo</span>
    @endif

    {{-- Galeria btn --}}
    @if($temFotos && $fotos->count() > 1)
        <button class="hero-fotos-btn" onclick="abrirDrawer('drawer-galeria')">
            🖼 +{{ $fotos->count() }} fotos
        </button>
    @endif

    <div class="hero-content">
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
            @if(str_contains($catNome,'viagem') || !empty($meta['partida'])) ✈️ Detalhes da Viagem
            @elseif(str_contains($catNome,'show') || str_contains($catNome,'musica') || str_contains($catNome,'música')) 🎤 Detalhes do Show
            @elseif(str_contains($catNome,'festival')) 🎉 Detalhes do Festival
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

        {{-- Rota de viagem - Ativa por Categoria OU se houver dados de Partida/Destino --}}
        @if(str_contains($catNome, 'viagem') || !empty($meta['partida']) || !empty($meta['destino']))
            @if(!empty($meta['partida']) || !empty($meta['destino']))
            <div class="rota-strip">
                <div class="rota-node">
                    <div class="rota-lbl">Partida</div>
                    <div class="rota-val">{{ e($meta['partida'] ?? '—') }}</div>
                </div>
                <div class="rota-sep">✈️</div>
                <div class="rota-node">
                    <div class="rota-lbl">Destino</div>
                    <div class="rota-val">{{ e($meta['destino'] ?? '—') }}</div>
                </div>
            </div>
            @endif
        @endif

        <div class="meta-list">
            {{-- Campos de VIAGEM --}}
            @if(str_contains($catNome, 'viagem') || !empty($meta['motorista']) || !empty($meta['hora_partida']))
                @if(!empty($meta['hora_partida']))
                <div class="meta-row"><div class="meta-row-icon">1️⃣</div><div><div class="meta-row-lbl">Hora de Partida</div><div class="meta-row-val">{{ e($meta['hora_partida']) }}</div></div></div>
                @endif
                @if(!empty($meta['hora_chegada']))
                <div class="meta-row"><div class="meta-row-icon">🏁</div><div><div class="meta-row-lbl">Chegada Prevista</div><div class="meta-row-val">{{ e($meta['hora_chegada']) }}</div></div></div>
                @endif
                @if(!empty($meta['motorista']))
                <div class="meta-row"><div class="meta-row-icon">👤</div><div><div class="meta-row-lbl">Motorista</div><div class="meta-row-val">{{ e($meta['motorista']) }}</div></div></div>
                @endif
                @if(!empty($meta['marca_veiculo']))
                <div class="meta-row"><div class="meta-row-icon">🚌</div><div><div class="meta-row-lbl">Veículo</div><div class="meta-row-val">{{ e($meta['marca_veiculo']) }}@if(!empty($meta['matricula'])) · {{ e($meta['matricula']) }}@endif</div></div></div>
                @endif
            @endif

            {{-- Campos de SHOWS e FESTIVAIS --}}
            @if(str_contains($catNome,'show') || str_contains($catNome,'musica') || str_contains($catNome,'música') || str_contains($catNome,'festival') || !empty($meta['palco']))
                @if(!empty($meta['palco']))
                <div class="meta-row"><div class="meta-row-icon">🎪</div><div><div class="meta-row-lbl">Palco</div><div class="meta-row-val">{{ e($meta['palco']) }}</div></div></div>
                @endif
            @endif

            {{-- Campos Gerais --}}
            @if(!empty($meta['dresscode']))
            <div class="meta-row"><div class="meta-row-icon">👔</div><div><div class="meta-row-lbl">Dress Code</div><div class="meta-row-val">{{ e($meta['dresscode']) }}</div></div></div>
            @endif

            @if(!empty($meta['classificacao_etaria']))
            <div class="meta-row"><div class="meta-row-icon">🔞</div><div><div class="meta-row-lbl">Classificação</div><div class="meta-row-val">{{ e($meta['classificacao_etaria']) }}</div></div></div>
            @endif

            @if(!empty($meta['nivel']))
            <div class="meta-row"><div class="meta-row-icon">📊</div><div><div class="meta-row-lbl">Nível</div><div class="meta-row-val">{{ e($meta['nivel']) }}</div></div></div>
            @endif

            {{-- Campos do DESPORTO --}}
            @if(str_contains($catNome, 'desporto') || !empty($meta['equipa_local']))
                @if(!empty($meta['modalidade']))
                <div class="meta-row"><div class="meta-row-icon">🏆</div><div><div class="meta-row-lbl">Modalidade</div><div class="meta-row-val">{{ e($meta['modalidade']) }}</div></div></div>
                @endif
                @if(!empty($meta['equipa_local']))
                <div class="meta-row"><div class="meta-row-icon">🏠</div><div><div class="meta-row-lbl">Equipa Casa</div><div class="meta-row-val">{{ e($meta['equipa_local']) }}</div></div></div>
                @endif
                @if(!empty($meta['equipa_visitante']))
                <div class="meta-row"><div class="meta-row-icon">✈️</div><div><div class="meta-row-lbl">Equipa Visitante</div><div class="meta-row-val">{{ e($meta['equipa_visitante']) }}</div></div></div>
                @endif
            @endif

            {{-- Campos de CONFERÊNCIAS e WORKSHOPS --}}
            @if(str_contains($catNome, 'confer') || str_contains($catNome, 'workshop') || !empty($meta['tema']))
                @if(!empty($meta['tema']))
                <div class="meta-row"><div class="meta-row-icon">💡</div><div><div class="meta-row-lbl">Tema</div><div class="meta-row-val">{{ e($meta['tema']) }}</div></div></div>
                @endif
                @if(!empty($meta['instrutor']))
                <div class="meta-row"><div class="meta-row-icon">👨‍🏫</div><div><div class="meta-row-lbl">Instrutor</div><div class="meta-row-val">{{ e($meta['instrutor']) }}</div></div></div>
                @endif
                @if(!empty($meta['certificado']) && $meta['certificado'] == "1")
                <div class="meta-row"><div class="meta-row-icon">🎓</div><div><div class="meta-row-lbl">Certificado</div><div class="meta-row-val">Incluído</div></div></div>
                @endif
            @endif

            {{-- Campos de GASTRONOMIA --}}
            @if(str_contains($catNome, 'gastro') || !empty($meta['chef']))
                @if(!empty($meta['chef']))
                <div class="meta-row"><div class="meta-row-icon">👨‍🍳</div><div><div class="meta-row-lbl">Chef</div><div class="meta-row-val">{{ e($meta['chef']) }}</div></div></div>
                @endif
                @if(!empty($meta['tipo_culinaria']))
                <div class="meta-row"><div class="meta-row-icon">🍽️</div><div><div class="meta-row-lbl">Culinária</div><div class="meta-row-val">{{ e($meta['tipo_culinaria']) }}</div></div></div>
                @endif
            @endif

            @if(!empty($meta['idioma']))
            <div class="meta-row"><div class="meta-row-icon">🌐</div><div><div class="meta-row-lbl">Idioma</div><div class="meta-row-val">{{ e($meta['idioma']) }}</div></div></div>
            @endif
            
            @if(!empty($meta['ar_condicionado']) && $meta['ar_condicionado'] == "1")
            <div class="meta-row"><div class="meta-row-icon">❄️</div><div><div class="meta-row-lbl">Conforto</div><div class="meta-row-val">Ar condicionado</div></div></div>
            @endif
        </div>

        {{-- Tags de Paragens - Exclusivo de Viagem --}}
        @if((str_contains($catNome, 'viagem') || !empty($meta['partida'])) && !empty($tParagens))
        <div style="margin-top:12px;">
            <div class="meta-row-lbl" style="margin-bottom:6px;">📍 Paragens</div>
            <div class="tag-row">
                @foreach($tParagens as $t)
                <span class="tag">{{ e($t) }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Lineup - Exclusivo de Shows/Festivais --}}
        @if((str_contains($catNome,'show') || str_contains($catNome,'musica') || str_contains($catNome,'música') || str_contains($catNome,'festival')) && !empty($meta['lineup']))
        <div style="margin-top:12px;">
            <div class="meta-row-lbl" style="margin-bottom:6px;">🕐 Lineup</div>
            <pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['lineup']) }}</pre>
        </div>
        @endif

        {{-- Ementa - Exclusivo de Gastronomia --}}
        @if((str_contains($catNome, 'gastro') || !empty($meta['chef'])) && !empty($meta['menu']))
        <div style="margin-top:12px;">
            <div class="meta-row-lbl" style="margin-bottom:6px;">📋 Ementa</div>
            <pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['menu']) }}</pre>
        </div>
        @endif
    </div>
</div>
@endif


{{-- Artistas / Atuações --}}
@if(count($tArtistas) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎤 Artistas / Atuações</div>
<div class="artist-list">
    @foreach($tArtistas as $art)
    <div class="artist-item">
        <div class="artist-avatar">✨</div>
        <div>
            <div class="artist-name">{{ e($art) }}</div>
            <div class="artist-role">Convidado Especial</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Palestrantes / Facilitadores --}}
@if(count($tPalestrantes) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎙️ Palestrantes / Facilitadores</div>
<div class="artist-list">
    @foreach($tPalestrantes as $pal)
    <div class="artist-item">
        <div class="artist-avatar">👨‍🏫</div>
        <div>
            <div class="artist-name">{{ e($pal) }}</div>
            <div class="artist-role">Orador</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Elenco / Elenco Principal --}}
@if(count($tElenco) > 0)
<div class="divider"></div>
<div class="fl" style="margin-bottom:8px;">🎭 Elenco / Participantes</div>
<div class="artist-list">
    @foreach($tElenco as $elc)
    <div class="artist-item">
        <div class="artist-avatar">🎬</div>
        <div>
            <div class="artist-name">{{ e($elc) }}</div>
            <div class="artist-role">Ator / Participante</div>
        </div>
    </div>
    @endforeach
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
    <button class="bottom-bar-btn" @if($totalDisp<=0) disabled @endif onclick="abrirDrawer('drawer-bilhetes-mobile')">
        🛒 Comprar
    </button>
</div>

{{-- ═══ DRAWER: BILHETES MOBILE / COMPRA RAPIDA ═══ --}}
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
                     onclick="abrirModalBanco('{{ e($tipo->nome) }}', {{ $tipo->preco }}, {{ $tipo->id }}, {{ $tipo->quantidade_disponivel }})">
                    <div>
                        <div class="ticket-type-name">{{ e($tipo->nome) }}</div>
                        <div class="ticket-type-avail">
                            @if($esg) <span style="color:var(--rose)">Esgotado</span> @else {{ $tipo->quantidade_disponivel }} disponíveis @endif
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

{{-- ═══ DRAWER: MAIS DETALHES ═══ --}}
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
            <div class="drawer-info-row"><span class="drawer-info-lbl">Website</span><span class="drawer-info-val"><a href="{{ e($evento->link_externo) }}" target="_blank" style="color:var(--cyan);">Visitar Link externos 🔗</a></span></div>
        @endif
        <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="contactar-btn">💬 Enviar Mensagem Direta</a>
    </div>
</div>

{{-- ═══ DRAWER: GALERIA COMPLETA ═══ --}}
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

{{-- ═══ MODAL BANCÁRIO (JS puro) ═══ --}}
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
                    @if($conta->logo)
                        <img src="{{ asset('images/bancos/'.$conta->logo) }}" class="banco-logo" alt="{{ $conta->nome_banco }}">
                    @else
                        <div class="banco-logo-ph">🏦</div>
                    @endif
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

{{-- ═══ MODAL ALPINE COM LÓGICA DE INICIALIZAÇÃO ═══ --}}
<div x-data="{
    modalAberto: false,
    ingressoNome: '',
    ingressoPreco: 0,
    ingressoId: '',
    quantidade: 1,
    
    inc() { this.quantidade++ },
    dec() { if(this.quantidade > 1) this.quantidade-- },
    total() { return (this.quantidade * this.ingressoPreco).toLocaleString('pt-PT') },
    
    fecharModal() {
        this.modalAberto = false;
        document.body.style.overflow = '';
    }
}"
@abrir-modal.window="
    ingressoNome = $event.detail.nome;
    ingressoPreco = $event.detail.preco;
    ingressoId = $event.detail.id;
    quantidade = 1;
    modalAberto = true;
    document.body.style.overflow = 'hidden';
">

    {{-- Overlay Escuro --}}
    <div x-show="modalAberto" class="modal-overlay" x-cloak style="display:none;"></div>

    {{-- Container do Centro - Fecha ao clicar fora --}}
    <div x-show="modalAberto" class="modal-center" x-cloak style="display:none;" @click="if($event.target === $el) fecharModal()">
        
        <div class="modal-box" 
             x-show="modalAberto" 
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="transform translate-y-full" 
             x-transition:enter-end="transform translate-y-0" 
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="transform translate-y-0" 
             x-transition:leave-end="transform translate-y-full">
            
            <div class="modal-drag"></div>
            <div class="modal-top">
                <div>
                    <div class="modal-secure-badge">Ambiente de Compra Seguro</div>
                    <div class="modal-top-title">Inscrição no Evento</div>
                </div>
                <button type="button" class="modal-x" @click="fecharModal()">×</button>
            </div>

            <div class="modal-body">
                {{-- Resumo do Evento --}}
                <div class="modal-ev-row">
                    @if($temCapa)
                        <img class="modal-ev-thumb" src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">
                    @elseif($temFotos)
                        <img class="modal-ev-thumb" src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="">
                    @else
                        <div class="modal-ev-thumb-ph">{{ $catEmoji }}</div>
                    @endif
                    <div>
                        <div class="modal-ev-name">{{ e($evento->titulo) }}</div>
                        <div class="modal-ev-sub">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- Tipo de Ingresso Selecionado --}}
                <div class="modal-selected-type">
                    <div>
                        <div class="modal-selected-name" x-text="ingressoNome"></div>
                        <div class="modal-selected-sub">Tipo de ingresso escolhido</div>
                    </div>
                    <div class="modal-selected-price">
                        <span x-text="ingressoPreco == 0 ? 'Gratuito' : (ingressoPreco).toLocaleString('pt-PT')"></span>
                        <small class="modal-selected-kz" x-show="ingressoPreco > 0">KZ</small>
                    </div>
                </div>

                {{-- FORMULÁRIO DE ENVIO --}}
                <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                    <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">

                    {{-- QUANTIDADE --}}
                    <div class="fg" x-show="ingressoPreco > 0">
                        <label class="fl">Quantidade de Bilhetes</label>
                        <div class="qty-wrapper">
                            <span class="qty-label">Selecione a quantidade</span>
                            <div class="qty-controls">
                                <button type="button" class="qty-control-btn" @click="dec()">-</button>
                                <span class="qty-value" x-text="quantidade"></span>
                                <input type="hidden" name="quantidade" :value="quantidade">
                                <button type="button" class="qty-control-btn" @click="inc()">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- TOTAL DINÂMICO --}}
                    <div class="total-row" x-show="ingressoPreco > 0">
                        <span class="total-label">Total a Pagar:</span>
                        <span class="total-value">
                            <span x-text="total()"></span> 
                            <small style="font-size:12px; color:var(--gold);">KZ</small>
                        </span>
                    </div>

                    {{-- CAMPOS DO CLIENTE --}}
                    <div class="fg">
                        <label class="fl">Nome Completo</label>
                        <input type="text" name="nome_cliente" class="fi" placeholder="Seu nome completo" required>
                    </div>

                    <div class="fg">
                        <label class="fl">Telefone / WhatsApp</label>
                        <input type="text" name="whatsapp" class="fi" placeholder="Ex: 923 000 000" required>
                    </div>

                    {{-- UPLOAD DE COMPROVATIVO --}}
                    <div class="fg" x-show="ingressoPreco > 0">
                        <label class="fl">Comprovativo de Pagamento</label>
                        <div class="upload-area">
                            <input type="file" name="comprovativo" onchange="handleUpload(this)">
                            <div class="upload-icon">📁</div>
                            <div class="upload-label">Clique para carregar o comprovativo</div>
                            <div class="upload-hint">Formatos: JPG, PNG (Máx: 2MB)</div>
                        </div>
                        
                        {{-- Preview Dinâmico --}}
                        <div class="upload-preview" id="upload-preview">
                            <div class="upload-preview-name" id="upload-preview-name"></div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span>Confirmar Inscrição 🚀</span>
                    </button>
                    <p class="submit-notice">Ao confirmar, os seus dados serão enviados para a organização do evento de forma segura.</p>
                </form>

            </div>
        </div>
    </div>
</div>

@if(!empty($videoEmbedSom))
<div id="modal-video-evento" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.95);align-items:center;justify-content:center;flex-direction:column;">
    <button onclick="fecharVideoEvento()" style="position:absolute;top:16px;right:16px;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:20px;cursor:pointer;">✕</button>
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:10px;">{{ e($evento->titulo) }}</div>
    <iframe id="modal-video-iframe" data-src="{{ $videoEmbedSom }}" src=""
        style="width:90vw;max-width:800px;height:50vw;max-height:450px;border-radius:16px;border:none;"
        allow="autoplay; fullscreen" allowfullscreen></iframe>
</div>
@endif

<script>
var _heroSomActivo = false;

// Fix 1: usar postMessage em vez de mudar src — não reinicia o vídeo
function toggleHeroSom() {
    var iframe = document.getElementById('heroVideoIframe');
    var btn    = document.getElementById('heroSomBtn');
    if (!iframe || !btn) return;
    _heroSomActivo = !_heroSomActivo;
    var cmd = _heroSomActivo ? 'unMute' : 'mute';
    iframe.contentWindow.postMessage(
        JSON.stringify({event:'command', func:cmd, args:[]}), '*'
    );
    btn.textContent = _heroSomActivo ? '🔊' : '🔇';
}

function abrirVideoEvento() {
    var modal  = document.getElementById('modal-video-evento');
    var iframe = document.getElementById('modal-video-iframe');
    if (!modal || !iframe) return;
    if (iframe.dataset.src && !iframe.src) iframe.src = iframe.dataset.src;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Fix 2: esconder botão ▶ quando modal abre
    var playBtn = document.getElementById('heroPlayBtn');
    if (playBtn) playBtn.style.display = 'none';
}

function fecharVideoEvento() {
    var modal  = document.getElementById('modal-video-evento');
    var iframe = document.getElementById('modal-video-iframe');
    if (modal)  modal.style.display = 'none';
    if (iframe) iframe.src = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fecharVideoEvento();
});


// ── abrirDrawer, fecharDrawer, toggleSobre e handleUpload já existem
//    globalmente em app.blade.php (window.*) — não redeclarar aqui.
// ── Funções exclusivas desta página (fluxo de pagamento bancário) ──
function copiarTexto(elementId, btn) {
    const texto = document.getElementById(elementId)?.textContent?.trim();
    if (!texto) return;
    navigator.clipboard.writeText(texto).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML = '✅ Copiado!';
        btn.style.color = '#00c896';
        setTimeout(() => { btn.innerHTML = original; btn.style.color = ''; }, 2000);
    });
}
function abrirModalBanco(nome, preco, id, disp) {
    if (parseInt(disp) <= 0) return;
    fecharDrawer('drawer-bilhetes-mobile');
    window._bilheteSeleccionado = {nome, preco: parseFloat(preco), id: parseInt(id)};
    const nomeEl  = document.getElementById('banco-tipo-nome');
    const precoEl = document.getElementById('banco-tipo-preco');
    if (nomeEl)  nomeEl.textContent  = nome;
    if (precoEl) precoEl.textContent = Number(preco).toLocaleString('pt-PT') + ' Kz';
    const overlay = document.getElementById('modal-banco-overlay');
    const center  = document.getElementById('modal-banco-center');
    if (overlay) overlay.style.display = 'block';
    if (center)  center.style.display  = 'flex';
    document.body.style.overflow = 'hidden';
}
function fecharModalBanco() {
    const overlay = document.getElementById('modal-banco-overlay');
    const center  = document.getElementById('modal-banco-center');
    if (overlay) overlay.style.display = 'none';
    if (center)  center.style.display  = 'none';
    document.body.style.overflow = '';
}
function jaEfetueiPagamento() {
    fecharModalBanco();
    const b = window._bilheteSeleccionado;
    if (b) window.dispatchEvent(new CustomEvent('abrir-modal', {detail:{nome:b.nome,preco:b.preco,id:b.id}}));
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.drawer-overlay.open').forEach(function(d){d.classList.remove('open');});
        document.body.style.overflow = '';
    }
});

// ── Scroll-reveal dos cards (animação, sem alterar lógica) ──
document.addEventListener('DOMContentLoaded', function () {
    var els = document.querySelectorAll('.scroll-reveal');
    if (!('IntersectionObserver' in window)) {
        els.forEach(function (el) { el.classList.add('is-visible'); });
        return;
    }
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    els.forEach(function (el) { obs.observe(el); });
});

// ── Contador de bilhetes disponíveis: conta de 1 até ao valor real ──
document.addEventListener('DOMContentLoaded', function () {
    var contador = document.getElementById('contadorDisponiveis');
    if (!contador) return;
    var alvo = parseInt(contador.dataset.target, 10) || 0;
    if (alvo <= 0) { contador.textContent = '0'; return; }
    var duracao = 1000;
    var inicio = null;
    function passo(ts) {
        if (!inicio) inicio = ts;
        var progresso = Math.min((ts - inicio) / duracao, 1);
        contador.textContent = Math.max(1, Math.floor(progresso * alvo));
        if (progresso < 1) requestAnimationFrame(passo);
        else contador.textContent = alvo;
    }
    requestAnimationFrame(passo);
});
</script>
@endsection