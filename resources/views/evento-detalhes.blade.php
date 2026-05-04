@extends('layouts.app')
@section('title', $evento->titulo)
@section('content')

@php
$fotos     = $evento->fotos;
$temCapa   = $evento->imagem_capa;
$temFotos  = $fotos->count() > 0;
$preco     = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
$totalDisp = $evento->tiposIngresso->sum('quantidade_disponivel');

$meta = [];
$rawMeta = $evento->meta ?? null;
if (is_array($rawMeta))      { $meta = $rawMeta; }
elseif (is_string($rawMeta)) { $meta = json_decode($rawMeta, true) ?? []; }

$catNome  = strtolower(optional($evento->categoria)->nome ?? '');
$catEmoji = optional($evento->categoria)->emoji ?? '🎟';
$temMeta  = !empty(array_filter($meta));

$tParagens     = isset($meta['paragens'])     ? (is_array($meta['paragens'])     ? $meta['paragens']     : (json_decode($meta['paragens'],     true) ?? [])) : [];
$tArtistas     = isset($meta['artistas'])     ? (is_array($meta['artistas'])     ? $meta['artistas']     : (json_decode($meta['artistas'],     true) ?? [])) : [];
$tPalestrantes = isset($meta['palestrantes']) ? (is_array($meta['palestrantes']) ? $meta['palestrantes'] : (json_decode($meta['palestrantes'],  true) ?? [])) : [];
$tElenco       = isset($meta['elenco'])       ? (is_array($meta['elenco'])       ? $meta['elenco']       : (json_decode($meta['elenco'],       true) ?? [])) : [];
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
.page{max-width:1080px;margin:0 auto;padding:24px 16px 120px;}

/* ─── HERO BANNER ─── */
.hero{
    position:relative;
    border-radius:24px;
    overflow:hidden;
    margin-bottom:28px;
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
    padding:28px 28px 32px;
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
    gap:20px;
}
.layout-left{display:flex;flex-direction:column;gap:20px;}
.layout-right{display:flex;flex-direction:column;gap:20px;}

/* ─── CARD BASE ─── */
.card{
    background:var(--card);
    border:1px solid var(--border2);
    border-radius:20px;
    overflow:hidden;
}
.card-head{
    padding:18px 22px 14px;
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
}
.card-title{
    font-family:'Syne',sans-serif;
    font-size:14px;font-weight:800;color:var(--txt);
    display:flex;align-items:center;gap:8px;
}
.card-body{padding:18px 22px;}

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
    display:flex;gap:8px;flex-wrap:wrap;
    padding:14px 22px;border-top:1px solid var(--border);
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
    .hero{height:380px;}
    .hero-title{font-size:36px;}
    .layout{grid-template-columns:1fr 340px;}
    .hide-mobile{display:block;}
    .show-mobile{display:none;}
    .modal-center{align-items:center;padding:20px;}
    .modal-box{border-radius:28px;max-height:90vh;}
    .modal-drag{display:none;}
    .drawer-overlay.open{align-items:center;justify-content:center;}
    .drawer-box{border-radius:24px;max-width:480px;max-height:82vh;}
    .drawer-handle{display:none;}
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
<div class="hero">
    @if($temCapa)
        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @elseif($temFotos)
        <img src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @else
        <div class="hero-ph">{{ $catEmoji }}</div>
    @endif
    <div class="hero-gradient"></div>

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
            <div class="hero-category">{{ $catEmoji }} {{ $evento->categoria->nome }}</div>
        @endif
        <h1 class="hero-title">{{ e($evento->titulo) }}</h1>
        <div class="hero-pills">
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

        {{-- SOBRE --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title">📋 Sobre o Evento</div>
            </div>
            <div class="card-body">
                <p class="sobre-text" id="sobreText" style="display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;">
                    {!! nl2br(e($evento->descricao)) !!}
                </p>
                <button class="ver-mais-btn" onclick="toggleSobre()">
                    <span id="sobreBtn">Ver mais ↓</span>
                </button>
            </div>
        </div>

        {{-- INFO RÁPIDA --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title">📌 Informações</div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item-icon">📅</div>
                        <div class="info-item-lbl">Data</div>
                        <div class="info-item-val">{{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">🕐</div>
                        <div class="info-item-lbl">Hora</div>
                        <div class="info-item-val">{{ $evento->hora_inicio ? substr($evento->hora_inicio,0,5) : '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">📍</div>
                        <div class="info-item-lbl">Local</div>
                        <div class="info-item-val">{{ Str::limit($evento->localizacao,22) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item-icon">👥</div>
                        <div class="info-item-lbl">Lotação</div>
                        <div class="info-item-val">{{ $evento->lotacao_maxima ? number_format($evento->lotacao_maxima) : '—' }}</div>
                    </div>
                    @if($evento->municipio)
                    <div class="info-item">
                        <div class="info-item-icon">🏙</div>
                        <div class="info-item-lbl">Município</div>
                        <div class="info-item-val">{{ e($evento->municipio) }}</div>
                    </div>
                    @endif
                    @if($evento->provincia)
                    <div class="info-item">
                        <div class="info-item-icon">🗺</div>
                        <div class="info-item-lbl">Província</div>
                        <div class="info-item-val">{{ e($evento->provincia) }}</div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="quick-actions">
                <button onclick="abrirDrawer('drawer-detalhes')" class="qa-btn">🔍 Mais detalhes</button>
                <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="qa-btn">💬 Contactar</a>
                @if($evento->link_externo)
                <a href="{{ e($evento->link_externo) }}" target="_blank" rel="noopener noreferrer" class="qa-btn">🔗 Site</a>
                @endif
            </div>
        </div>

        {{-- META ESPECÍFICO DA CATEGORIA --}}
        @if($temMeta)
        <div class="card">
            <div class="card-head">
                <div class="card-title">
                    @if(str_contains($catNome,'viagem')) ✈️ Detalhes da Viagem
                    @elseif(str_contains($catNome,'show') || str_contains($catNome,'musica') || str_contains($catNome,'música')) 🎤 Detalhes do Show
                    @elseif(str_contains($catNome,'festival')) 🎉 Detalhes do Festival
                    @elseif(str_contains($catNome,'desporto')) ⚽ Detalhes do Jogo
                    @elseif(str_contains($catNome,'confer')) 🎙️ Conferência
                    @elseif(str_contains($catNome,'workshop')) 📚 Workshop
                    @elseif(str_contains($catNome,'cultura')) 🎭 Cultural
                    @elseif(str_contains($catNome,'gastro')) 🍽️ Gastronomia
                    @else ⚙️ Detalhes específicos
                    @endif
                </div>
            </div>
            <div class="card-body">

                {{-- Rota de viagem --}}
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

                <div class="meta-list">
                    @if(!empty($meta['hora_partida']))
                    <div class="meta-row"><div class="meta-row-icon">🕐</div><div><div class="meta-row-lbl">Hora de Partida</div><div class="meta-row-val">{{ e($meta['hora_partida']) }}</div></div></div>
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
                    @if(!empty($meta['palco']))
                    <div class="meta-row"><div class="meta-row-icon">🎪</div><div><div class="meta-row-lbl">Palco</div><div class="meta-row-val">{{ e($meta['palco']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['dresscode']))
                    <div class="meta-row"><div class="meta-row-icon">👔</div><div><div class="meta-row-lbl">Dress Code</div><div class="meta-row-val">{{ e($meta['dresscode']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['modalidade']))
                    <div class="meta-row"><div class="meta-row-icon">🏆</div><div><div class="meta-row-lbl">Modalidade</div><div class="meta-row-val">{{ e($meta['modalidade']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['equipa_local']))
                    <div class="meta-row"><div class="meta-row-icon">🏠</div><div><div class="meta-row-lbl">Equipa Casa</div><div class="meta-row-val">{{ e($meta['equipa_local']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['equipa_visitante']))
                    <div class="meta-row"><div class="meta-row-icon">✈️</div><div><div class="meta-row-lbl">Equipa Visitante</div><div class="meta-row-val">{{ e($meta['equipa_visitante']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['tema']))
                    <div class="meta-row"><div class="meta-row-icon">💡</div><div><div class="meta-row-lbl">Tema</div><div class="meta-row-val">{{ e($meta['tema']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['instrutor']))
                    <div class="meta-row"><div class="meta-row-icon">👨‍🏫</div><div><div class="meta-row-lbl">Instrutor</div><div class="meta-row-val">{{ e($meta['instrutor']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['nivel']))
                    <div class="meta-row"><div class="meta-row-icon">📊</div><div><div class="meta-row-lbl">Nível</div><div class="meta-row-val">{{ e($meta['nivel']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['chef']))
                    <div class="meta-row"><div class="meta-row-icon">👨‍🍳</div><div><div class="meta-row-lbl">Chef</div><div class="meta-row-val">{{ e($meta['chef']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['tipo_culinaria']))
                    <div class="meta-row"><div class="meta-row-icon">🍽️</div><div><div class="meta-row-lbl">Culinária</div><div class="meta-row-val">{{ e($meta['tipo_culinaria']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['classificacao_etaria']))
                    <div class="meta-row"><div class="meta-row-icon">🔞</div><div><div class="meta-row-lbl">Classificação</div><div class="meta-row-val">{{ e($meta['classificacao_etaria']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['certificado']))
                    <div class="meta-row"><div class="meta-row-icon">🎓</div><div><div class="meta-row-lbl">Certificado</div><div class="meta-row-val">Incluído</div></div></div>
                    @endif
                    @if(!empty($meta['idioma']))
                    <div class="meta-row"><div class="meta-row-icon">🌐</div><div><div class="meta-row-lbl">Idioma</div><div class="meta-row-val">{{ e($meta['idioma']) }}</div></div></div>
                    @endif
                    @if(!empty($meta['ar_condicionado']))
                    <div class="meta-row"><div class="meta-row-icon">❄️</div><div><div class="meta-row-lbl">Conforto</div><div class="meta-row-val">Ar condicionado</div></div></div>
                    @endif
                </div>

                {{-- Tags --}}
                @if(!empty($tParagens))
                <div style="margin-top:12px;">
                    <div class="meta-row-lbl" style="margin-bottom:6px;">📍 Paragens</div>
                    <div class="tag-row">
                        @foreach($tParagens as $t)
                        <span class="tag">{{ e($t) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($meta['lineup']))
                <div style="margin-top:12px;">
                    <div class="meta-row-lbl" style="margin-bottom:6px;">🕐 Lineup</div>
                    <pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['lineup']) }}</pre>
                </div>
                @endif

                @if(!empty($meta['menu']))
                <div style="margin-top:12px;">
                    <div class="meta-row-lbl" style="margin-bottom:6px;">📋 Ementa</div>
                    <pre style="font-family:inherit;font-size:12px;color:var(--muted2);white-space:pre-line;line-height:1.7;">{{ e($meta['menu']) }}</pre>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ARTISTAS --}}
        @if(!empty($tArtistas) || !empty($tElenco) || !empty($tPalestrantes))
        <div class="card">
            <div class="card-head">
                <div class="card-title">
                    @if(!empty($tArtistas)) 🎤 Artistas
                    @elseif(!empty($tElenco)) 🎭 Elenco
                    @else 🎙️ Palestrantes
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="artist-list">
                    @if(!empty($tArtistas))
                        @foreach($tArtistas as $ar)
                        <div class="artist-item">
                            <div class="artist-avatar">{{ strtoupper(substr($ar,0,2)) }}</div>
                            <div><div class="artist-name">{{ e($ar) }}</div><div class="artist-role">Artista</div></div>
                        </div>
                        @endforeach
                    @elseif(!empty($tElenco))
                        @foreach($tElenco as $mb)
                        <div class="artist-item">
                            <div class="artist-avatar">{{ strtoupper(substr($mb,0,2)) }}</div>
                            <div><div class="artist-name">{{ e($mb) }}</div></div>
                        </div>
                        @endforeach
                    @else
                        @foreach($tPalestrantes as $pl)
                        <div class="artist-item">
                            <div class="artist-avatar">{{ strtoupper(substr($pl,0,2)) }}</div>
                            <div><div class="artist-name">{{ e($pl) }}</div><div class="artist-role">Palestrante</div></div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- GALERIA --}}
        @if($temFotos || $temCapa)
        <div class="card">
            <div class="card-head">
                <div class="card-title">🖼 Galeria de Fotos</div>
            </div>
            <div class="card-body">
                <div class="gallery-grid">
                    @if($temCapa)
                    <div class="gallery-item"><img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="Capa" loading="lazy"></div>
                    @endif
                    @foreach($fotos->take($temCapa ? 8 : 9) as $foto)
                    <div class="gallery-item"><img src="{{ asset('storage/'.e($foto->caminho)) }}" alt="Foto" loading="lazy"></div>
                    @endforeach
                </div>
                @if($fotos->count() > 8)
                <button class="gallery-more" onclick="abrirDrawer('drawer-galeria')">Ver todas as fotos (+{{ $fotos->count() }})</button>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- /layout-left --}}

    {{-- ═══ COLUNA DIREITA (sticky - só desktop) ═══ --}}
    <div class="layout-right hide-mobile">
        <div class="ticket-card">
            <div class="ticket-head">
                <div class="ticket-head-price">
                    @if($preco == 0) Gratuito
                    @else {{ number_format($preco,0,',','.') }} Kz
                    @endif
                </div>
                <div class="ticket-head-label">por bilhete · preço mínimo</div>
                @if($totalDisp > 0)
                <div class="ticket-head-avail">
                    <div class="ticket-head-avail-dot"></div>
                    {{ $totalDisp }} bilhetes disponíveis
                </div>
                @else
                <div style="color:var(--rose);font-size:12px;font-weight:600;margin-top:8px;">Esgotado</div>
                @endif
            </div>
            <div class="ticket-body">

                @forelse($evento->tiposIngresso as $tipo)
                <div class="ticket-type {{ $tipo->quantidade_disponivel <= 0 ? 'esgotado' : '' }}"
                onclick="selecionarTipo('{{ addslashes(e($tipo->nome)) }}',{{ $tipo->preco }},{{ $tipo->id }},{{ $tipo->quantidade_disponivel }})">
                    <div>
                        <div class="ticket-type-name">{{ e($tipo->nome) }}</div>
                        <div class="ticket-type-avail">
                            @if($tipo->quantidade_disponivel > 0)
                                {{ $tipo->quantidade_disponivel }} disponíveis
                            @else
                                Esgotado
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="ticket-type-price">{{ number_format($tipo->preco,0,',','.') }} Kz</div>
                        <small style="font-size:10px;color:var(--muted);">por bilhete</small>
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:var(--muted);text-align:center;padding:16px 0;">Sem bilhetes disponíveis.</p>
                @endforelse

                <div style="margin-top:12px;">
                    @if($totalDisp > 0)
                    <button class="buy-btn" onclick="abrirDrawer('drawer-bilhetes-mobile')">
                        🎟 Comprar Bilhete
                    </button>
                    @else
                    <button class="buy-btn" disabled>Evento Esgotado</button>
                    @endif
                </div>
                <div class="buy-notice">🔒 Pagamento seguro · Bilhete emitido em até 24h</div>
            </div>

            <div class="quick-actions">
                <button onclick="abrirDrawer('drawer-detalhes')" class="qa-btn">ℹ️ Detalhes</button>
                <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="qa-btn">💬 Contactar</a>
            </div>
        </div>
    </div>

</div>{{-- /layout --}}

{{-- ═══ BOTTOM BAR MOBILE ═══ --}}
<div class="bottom-bar show-mobile">
    <div style="flex:1;">
        <div class="bottom-bar-price">
            @if($preco==0)Gratuito@else{{ number_format($preco,0,',','.') }} Kz@endif
        </div>
        <div class="bottom-bar-label">por bilhete</div>
    </div>
    <button class="bottom-bar-btn" onclick="abrirDrawer('drawer-bilhetes-mobile')" {{ $totalDisp<=0?'disabled':'' }}>
        🎟 {{ $totalDisp>0?'Comprar':'Esgotado' }}
    </button>
</div>

{{-- ═══ MODAL DE COMPRA ═══ --}}
<div x-show="modalAberto" x-cloak>
    <div class="modal-overlay"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-on:click="fecharModal()">
    </div>
    <div class="modal-center">
        <div class="modal-box"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4">

            <div class="modal-drag"></div>

            <div class="modal-top">
                <div>
                    <div class="modal-secure-badge">Reserva segura</div>
                    <div class="modal-top-title">Finalizar compra</div>
                </div>
                <button class="modal-x" x-on:click="fecharModal()">✕</button>
            </div>

            <div class="modal-body">

                @if($errors->any())
                <div class="form-errs">
                    <strong style="display:block;color:#fff;margin-bottom:5px;">Erros:</strong>
                    <ul style="margin:0;padding-left:16px;">
                        @foreach($errors->all() as $err)
                        <li>{{ e($err) }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Preview evento --}}
                <div class="modal-ev-row">
                    @if($temCapa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="" class="modal-ev-thumb" loading="lazy">
                    @else
                        <div class="modal-ev-thumb-ph">{{ $catEmoji }}</div>
                    @endif
                    <div>
                        <div class="modal-ev-name">{{ e($evento->titulo) }}</div>
                        <div class="modal-ev-sub">
                            📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}
                            @if($evento->hora_inicio) · {{ substr($evento->hora_inicio,0,5) }}@endif
                            · 📍 {{ Str::limit($evento->localizacao,20) }}
                        </div>
                    </div>
                </div>

                {{-- Tipo selecionado --}}
                <div class="modal-selected-type">
                    <div>
                        <div class="modal-selected-name" x-text="ingressoNome"></div>
                        <div class="modal-selected-sub">Tipo de bilhete selecionado</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="modal-selected-price" x-text="Number(ingressoPreco).toLocaleString('pt-PT')+' Kz'"></div>
                        <div class="modal-selected-kz">por bilhete</div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_ingresso_id" x-bind:value="ingressoId">
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                    <input type="hidden" name="quantidade" x-bind:value="quantidade">

                    <div class="fg">
                        <label class="fl">Nome do titular</label>
                        <input type="text" name="nome_cliente" class="fi" placeholder="Nome completo" required autocomplete="name" value="{{ old('nome_cliente') }}">
                    </div>

                    <div class="fg">
                        <label class="fl">WhatsApp</label>
                        <input type="tel" name="whatsapp" class="fi" placeholder="+244 9XX XXX XXX" required autocomplete="tel" value="{{ old('whatsapp') }}">
                    </div>

                    <div class="fg">
                        <label class="fl">Quantidade de bilhetes</label>
                        <div class="qty-wrapper">
                            <span class="qty-label">Bilhetes</span>
                            <div class="qty-controls">
                                <button type="button" class="qty-control-btn" x-on:click="dec()">−</button>
                                <span class="qty-value" x-text="quantidade"></span>
                                <button type="button" class="qty-control-btn" x-on:click="inc()">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="total-row">
                        <span class="total-label">Total a pagar</span>
                        <span class="total-value" x-text="total()+' Kz'"></span>
                    </div>

                    <div class="fg">
                        <label class="fl">Comprovativo de pagamento</label>
                        <div class="upload-area">
                            <input type="file" name="comprovativo" required accept=".jpg,.jpeg,.png,.pdf" onchange="handleUpload(this)">
                            <div class="upload-icon">📎</div>
                            <div class="upload-label">Clica para anexar</div>
                            <div class="upload-hint">JPG, PNG ou PDF · máx. 5 MB</div>
                        </div>
                        <div class="upload-preview" id="upload-preview">
                            <span>✅</span>
                            <span class="upload-preview-name" id="upload-preview-name"></span>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">✅ Confirmar Reserva</button>
                    <div class="submit-notice">🔒 Reserva segura · O staff valida o comprovativo em até 24h e emite o bilhete digital</div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- DRAWER BILHETES MOBILE --}}
<div class="drawer-overlay" id="drawer-bilhetes-mobile" onclick="if(event.target===this)fecharDrawer('drawer-bilhetes-mobile')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">🎟 Bilhetes</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-bilhetes-mobile')">✕</button>
        </div>
        @forelse($evento->tiposIngresso as $tipo)
        <div class="ticket-type {{ $tipo->quantidade_disponivel <= 0 ? 'esgotado' : '' }}" style="margin-bottom:10px;"
            onclick="selecionarTipoMobile('{{ addslashes(e($tipo->nome)) }}',{{ $tipo->preco }},{{ $tipo->id }},{{ $tipo->quantidade_disponivel }})">
            <div>
                <div class="ticket-type-name">{{ e($tipo->nome) }}</div>
                <div class="ticket-type-avail">
                    @if($tipo->quantidade_disponivel > 0) {{ $tipo->quantidade_disponivel }} disponíveis
                    @else Esgotado @endif
                </div>
            </div>
            <div>
                <div class="ticket-type-price">{{ number_format($tipo->preco,0,',','.') }} Kz</div>
                <small style="font-size:10px;color:var(--muted);">por bilhete</small>
            </div>
        </div>
        @empty
        <p style="text-align:center;color:var(--muted);padding:20px 0;font-size:13px;">Sem bilhetes disponíveis.</p>
        @endforelse
    </div>
</div>

{{-- DRAWER DETALHES --}}
<div class="drawer-overlay" id="drawer-detalhes" onclick="if(event.target===this)fecharDrawer('drawer-detalhes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">ℹ️ Detalhes completos</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-detalhes')">✕</button>
        </div>
        <div class="drawer-info-row"><span class="drawer-info-lbl">Data</span><span class="drawer-info-val">{{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span></div>
        @if($evento->hora_inicio)
        <div class="drawer-info-row">
            <span class="drawer-info-lbl">Hora</span>
            <span class="drawer-info-val">{{ substr($evento->hora_inicio,0,5) }}@if($evento->hora_fim) – {{ substr($evento->hora_fim,0,5) }}@endif</span>
        </div>
        @endif
        @if($evento->data_fim && $evento->data_fim !== $evento->data_evento)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Termina</span><span class="drawer-info-val">{{ \Carbon\Carbon::parse($evento->data_fim)->format('d/m/Y') }}</span></div>
        @endif
        <div class="drawer-info-row"><span class="drawer-info-lbl">Local</span><span class="drawer-info-val">{{ e($evento->localizacao) }}</span></div>
        @if($evento->municipio)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Município</span><span class="drawer-info-val">{{ e($evento->municipio) }}</span></div>
        @endif
        @if($evento->provincia)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Província</span><span class="drawer-info-val">{{ e($evento->provincia) }}</span></div>
        @endif
        @if($evento->lotacao_maxima)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Lotação</span><span class="drawer-info-val">{{ number_format($evento->lotacao_maxima,0,',','.') }} pessoas</span></div>
        @endif
        <div class="drawer-info-row"><span class="drawer-info-lbl">Formato</span><span class="drawer-info-val">{{ $evento->online ? '🌐 Online' : '📍 Presencial' }}</span></div>
        @if($evento->categoria)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Categoria</span><span class="drawer-info-val">{{ $catEmoji }} {{ e($evento->categoria->nome) }}</span></div>
        @endif
        @if($evento->subcategoria)
        <div class="drawer-info-row"><span class="drawer-info-lbl">Subcategoria</span><span class="drawer-info-val">{{ e($evento->subcategoria->nome) }}</span></div>
        @endif
        <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="contactar-btn">💬 Contactar organizador</a>
    </div>
</div>

{{-- DRAWER GALERIA --}}
@if($temFotos)
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this)fecharDrawer('drawer-galeria')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title-row">
            <div class="drawer-title">🖼️ Galeria</div>
            <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">✕</button>
        </div>
        <div class="gallery-drawer-grid">
            @foreach($fotos as $foto)
            <img src="{{ asset('storage/'.e($foto->caminho)) }}" alt="Foto" loading="lazy">
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
function abrirDrawer(id){
    var el=document.getElementById(id);
    if(el){el.classList.add('open');document.body.style.overflow='hidden';}
}
function fecharDrawer(id){
    var el=document.getElementById(id);
    if(el){el.classList.remove('open');document.body.style.overflow='';}
}
function toggleSobre(){
    var p=document.getElementById('sobreText');
    var btn=document.getElementById('sobreBtn');
    var aberto=btn.textContent.includes('menos');
    p.style.webkitLineClamp=aberto?'4':'unset';
    p.style.overflow=aberto?'hidden':'visible';
    btn.textContent=aberto?'Ver mais ↓':'Ver menos ↑';
}
function handleUpload(input){
    var file=input.files[0];
    var prev=document.getElementById('upload-preview');
    var name=document.getElementById('upload-preview-name');
    if(file){name.textContent=file.name;prev.style.display='flex';}
    else{prev.style.display='none';}
}
document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){
        document.querySelectorAll('.drawer-overlay.open').forEach(function(d){d.classList.remove('open');});
        document.body.style.overflow='';
    }
});

function selecionarTipo(nome, preco, id, disp) {
    if (disp <= 0) return;
    window.dispatchEvent(new CustomEvent('abrir-modal', { detail: { nome, preco, id } }));
}
function selecionarTipoMobile(nome, preco, id, disp) {
    if (disp <= 0) return;
    fecharDrawer('drawer-bilhetes-mobile');
    window.dispatchEvent(new CustomEvent('abrir-modal', { detail: { nome, preco, id } }));
}
</script>

</div>{{-- /x-data --}}
@endif
@endsection