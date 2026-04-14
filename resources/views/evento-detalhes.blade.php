@extends('layouts.app')
@section('title', $evento->titulo)
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

@php
    $fotos     = $evento->fotos;
    $temCapa   = $evento->imagem_capa;
    $temFotos  = $fotos->count() > 0;
    $preco     = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
    $totalDisp = $evento->tiposIngresso->sum('quantidade_disponivel');
    $meta      = [];
    $rawMeta   = $evento->meta ?? null;
    if (is_array($rawMeta))      $meta = $rawMeta;
    elseif (is_string($rawMeta)) $meta = json_decode($rawMeta, true) ?? [];
    $catNome  = strtolower(optional($evento->categoria)->nome ?? '');
    $catEmoji = optional($evento->categoria)->emoji ?? '🎟';
    $temMeta  = !empty(array_filter($meta));
    $parseTags = function($v) {
        return is_array($v) ? $v : (json_decode($v ?? '[]', true) ?? []);
    };
@endphp

<style>
[x-cloak]{display:none!important;}
*{box-sizing:border-box;margin:0;padding:0;}
:root{
    --bg:#050810;--s1:#090e1b;--s2:#0d1628;--s3:#152035;
    --c:#06b6d4;--c2:#0891b2;--gold:#f59e0b;--green:#10b981;--red:#f43f5e;
    --t1:#f0f6ff;--t2:#94a3b8;--t3:#475569;
    --b1:rgba(6,182,212,.08);--b2:rgba(6,182,212,.18);--b3:rgba(6,182,212,.35);
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--t1);min-height:100vh;}
.ed-page{max-width:900px;margin:0 auto;padding:0 0 140px;}

/* HERO */
.ed-hero{position:relative;height:320px;overflow:hidden;}
@media(min-width:768px){.ed-hero{height:500px;border-radius:0 0 40px 40px;}}
.ed-hero-img{width:100%;height:100%;object-fit:cover;}
.ed-hero-ph{width:100%;height:100%;background:linear-gradient(135deg,#050d1a,#0c2244,#071830);display:flex;align-items:center;justify-content:center;font-size:100px;}
.ed-hero-ov{position:absolute;inset:0;background:linear-gradient(to top,rgba(5,8,16,1) 0%,rgba(5,8,16,.65) 40%,rgba(5,8,16,.1) 75%,transparent 100%),linear-gradient(to right,rgba(5,8,16,.4),transparent 55%);}
.ed-hero-top{position:absolute;top:16px;left:16px;right:16px;display:flex;justify-content:space-between;align-items:flex-start;z-index:3;}
.ed-badge{font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:5px 12px;border-radius:20px;backdrop-filter:blur(8px);}
.badge-hot{background:rgba(244,63,94,.9);color:#fff;}
.badge-free{background:rgba(16,185,129,.9);color:#fff;}
.badge-new{background:rgba(6,182,212,.9);color:#000;}
.ed-fotos-btn{display:flex;align-items:center;gap:5px;background:rgba(0,0,0,.55);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.18);border-radius:10px;padding:6px 12px;font-size:11px;font-weight:700;color:#fff;cursor:pointer;transition:all .2s;}
.ed-fotos-btn:hover{background:rgba(6,182,212,.25);border-color:var(--c);}
.ed-hero-content{position:absolute;bottom:0;left:0;right:0;padding:20px 20px 28px;z-index:3;}
@media(min-width:768px){.ed-hero-content{padding:32px 36px 36px;}}
.ed-hero-cat{display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--c);margin-bottom:10px;background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.2);padding:4px 10px;border-radius:20px;}
.ed-hero-title{font-family:'Syne',sans-serif;font-size:26px;font-weight:900;color:#fff;line-height:1.1;letter-spacing:-.5px;margin-bottom:14px;}
@media(min-width:768px){.ed-hero-title{font-size:42px;}}
.ed-hero-pills{display:flex;flex-wrap:wrap;gap:8px;}
.ed-hero-pill{display:flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:rgba(255,255,255,.85);background:rgba(0,0,0,.5);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.12);padding:5px 11px;border-radius:20px;}

/* BODY */
.ed-body{display:flex;flex-direction:column;gap:14px;padding:14px;}
@media(min-width:768px){.ed-body{padding:20px 24px;gap:16px;}}

/* SECTION */
.ed-sec{background:var(--s1);border:1px solid var(--b2);border-radius:20px;overflow:hidden;}
.ed-sec-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.ed-sec-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--t1);display:flex;align-items:center;gap:7px;}
.ed-sec-body{padding:16px 18px;}
@media(min-width:768px){.ed-sec-body{padding:20px 22px;}}

/* CHIPS */
.ed-chips{display:flex;flex-wrap:wrap;gap:8px;padding:14px 18px;}
.ed-chip{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:10px;background:var(--s2);border:1px solid var(--b1);font-size:12px;font-weight:500;color:var(--t2);cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;}
.ed-chip:hover,.ed-chip.act{border-color:var(--b3);color:var(--c);background:var(--b1);}

/* DESCRICAO */
.ed-desc{font-size:14px;color:var(--t2);line-height:1.85;}
.ed-desc-more{display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:var(--c);cursor:pointer;border:none;background:none;padding:0;margin-top:10px;}

/* META GRID */
.meta-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;}
@media(min-width:480px){.meta-grid{grid-template-columns:repeat(3,1fr);}}
.mc{background:var(--s2);border:1px solid var(--b1);border-radius:12px;padding:12px 14px;transition:border-color .2s;}
.mc:hover{border-color:var(--b2);}
.mc.full{grid-column:1/-1;}
.mc-lbl{font-size:9px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:5px;}
.mc-val{font-size:13px;font-weight:600;color:var(--t1);line-height:1.4;}
.mc-val.pre{white-space:pre-line;font-size:12px;color:var(--t2);}
.mc-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:4px;}
.mc-tag{font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.22);color:var(--c);}

/* ROTA VIAGEM */
.rota-strip{display:flex;align-items:center;background:var(--s2);border:1px solid var(--b1);border-radius:12px;overflow:hidden;margin-bottom:12px;}
.rota-node{flex:1;padding:12px 14px;text-align:center;}
.rota-node-lbl{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:4px;}
.rota-node-val{font-size:13px;font-weight:700;color:var(--t1);}
.rota-arrow{flex-shrink:0;width:40px;display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--c);border-left:1px solid var(--b1);border-right:1px solid var(--b1);}

/* BILHETES */
.ed-ticket{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px;border-radius:14px;background:var(--s2);border:1px solid var(--b1);margin-bottom:10px;transition:border-color .2s;position:relative;overflow:hidden;}
.ed-ticket::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,var(--c),var(--c2));}
.ed-ticket:last-child{margin-bottom:0;}
.ed-ticket:hover{border-color:var(--b2);}
.ed-ticket-nome{font-size:14px;font-weight:700;color:var(--t1);margin-bottom:3px;}
.ed-ticket-disp{font-size:11px;font-weight:600;}
.ed-ticket-disp.ok{color:var(--t3);}
.ed-ticket-disp.esg{color:var(--red);}
.ed-ticket-right{text-align:right;flex-shrink:0;}
.ed-ticket-preco{font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:var(--gold);margin-bottom:6px;}
.ed-ticket-btn{background:linear-gradient(135deg,var(--c),var(--c2));color:#000;padding:8px 18px;border-radius:10px;font-size:12px;font-weight:800;border:none;cursor:pointer;transition:all .2s;}
.ed-ticket-btn:hover{transform:translateY(-1px);box-shadow:0 4px 14px rgba(6,182,212,.4);}
.ed-ticket-btn:disabled{background:var(--s3);color:var(--t3);cursor:not-allowed;transform:none;}

/* BOTTOM BAR */
.ed-bottom{position:fixed;bottom:0;left:0;right:0;z-index:200;padding:12px 16px;background:rgba(9,14,27,.97);backdrop-filter:blur(20px);border-top:1px solid var(--b2);display:flex;align-items:center;gap:10px;}
@media(min-width:768px){.ed-bottom{display:none;}}
.ed-bottom-preco{font-family:'Syne',sans-serif;font-size:20px;font-weight:900;color:#fff;}
.ed-bottom-lbl{font-size:10px;color:var(--t3);}
.ed-bottom-btn{flex:1;padding:13px;border-radius:14px;background:linear-gradient(135deg,var(--c),var(--c2));color:#000;font-size:14px;font-weight:800;border:none;cursor:pointer;font-family:'Syne',sans-serif;}
.ed-bottom-info{padding:12px 14px;border-radius:14px;border:1px solid var(--b2);background:var(--s2);color:var(--t2);font-size:13px;font-weight:600;cursor:pointer;}

/* DRAWERS */
.drawer-overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);}
.drawer-overlay.open{display:flex;align-items:flex-end;}
@media(min-width:640px){.drawer-overlay.open{align-items:center;justify-content:center;}}
.drawer-box{width:100%;background:var(--s1);border:1px solid var(--b2);border-radius:24px 24px 0 0;padding:20px 20px 40px;max-height:88vh;overflow-y:auto;scrollbar-width:none;}
.drawer-box::-webkit-scrollbar{display:none;}
@media(min-width:640px){.drawer-box{border-radius:24px;max-width:480px;max-height:82vh;}}
.drawer-handle{width:36px;height:4px;border-radius:2px;background:var(--b2);margin:0 auto 18px;}
@media(min-width:640px){.drawer-handle{display:none;}}
.drawer-title{font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:var(--t1);margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;}
.drawer-close{background:none;border:none;font-size:20px;color:var(--t3);cursor:pointer;transition:color .2s;}
.drawer-close:hover{color:var(--red);}
.gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;}
.gallery-grid img{width:100%;height:90px;object-fit:cover;border-radius:10px;cursor:pointer;}
@media(min-width:640px){.gallery-grid img{height:110px;}}
.ed-info-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--b1);font-size:13px;gap:8px;}
.ed-info-row:last-child{border-bottom:none;}
.ed-info-lbl{color:var(--t3);font-weight:500;flex-shrink:0;}
.ed-info-val{color:var(--t1);font-weight:600;text-align:right;}
.contactar-btn{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:18px;padding:13px;border-radius:13px;background:rgba(6,182,212,.1);border:1px solid var(--b2);color:var(--c);font-weight:700;font-size:14px;text-decoration:none;transition:all .2s;}
.contactar-btn:hover{background:rgba(6,182,212,.18);}

/* MODAL */
.modal-bg{position:fixed;inset:0;z-index:990;background:rgba(5,8,16,.9);backdrop-filter:blur(14px);}
.modal-wrap{position:fixed;inset:0;z-index:991;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;}
@media(min-width:640px){.modal-wrap{align-items:center;padding:20px;}}
.modal-box{pointer-events:auto;width:100%;max-width:500px;background:var(--s1);border:1px solid var(--b3);border-radius:28px 28px 0 0;max-height:96vh;overflow-y:auto;scrollbar-width:none;}
.modal-box::-webkit-scrollbar{display:none;}
@media(min-width:640px){.modal-box{border-radius:28px;max-height:90vh;}}
.modal-handle-bar{width:36px;height:4px;border-radius:2px;background:var(--b2);margin:14px auto 0;display:block;}
@media(min-width:640px){.modal-handle-bar{display:none;}}
.modal-header{position:sticky;top:0;z-index:5;background:var(--s1);padding:14px 20px;border-bottom:1px solid var(--b1);display:flex;align-items:center;justify-content:space-between;}
.modal-secure{display:inline-flex;align-items:center;gap:5px;font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--green);background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);padding:3px 9px;border-radius:20px;margin-bottom:6px;}
.modal-secure::before{content:'●';font-size:7px;animation:blink 1.5s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.modal-h-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:var(--t1);}
.modal-close{width:34px;height:34px;border-radius:10px;background:var(--s2);border:1px solid var(--b1);color:var(--t3);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.modal-close:hover{background:rgba(244,63,94,.12);border-color:rgba(244,63,94,.3);color:var(--red);}
.modal-content{padding:18px 20px 28px;}

/* modal evento preview */
.modal-ev{display:flex;gap:12px;align-items:center;padding:12px 14px;background:var(--s2);border:1px solid var(--b1);border-radius:14px;margin-bottom:16px;}
.modal-ev-img{width:50px;height:50px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid var(--b2);}
.modal-ev-ph{width:50px;height:50px;border-radius:10px;background:linear-gradient(135deg,#0c1a2e,#1e3a5f);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;border:1px solid var(--b2);}
.modal-ev-name{font-family:'Syne',sans-serif;font-size:13px;font-weight:800;color:var(--t1);margin-bottom:3px;}
.modal-ev-meta{font-size:11px;color:var(--t3);}

/* modal meta */
.modal-meta{background:rgba(6,182,212,.04);border:1px solid rgba(6,182,212,.14);border-radius:14px;padding:14px;margin-bottom:16px;}
.modal-meta-lbl{font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--c);margin-bottom:10px;}
.modal-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.mmc{background:var(--s2);border:1px solid var(--b1);border-radius:10px;padding:9px 11px;}
.mmc.full{grid-column:1/-1;}
.mmc-lbl{font-size:9px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:3px;}
.mmc-val{font-size:12px;font-weight:600;color:var(--t1);}
.mmc-tags{display:flex;flex-wrap:wrap;gap:4px;margin-top:3px;}
.mmc-tag{font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.2);color:var(--c);}

/* modal tipo */
.modal-tipo{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:var(--s2);border:1px solid var(--b2);border-radius:14px;margin-bottom:16px;position:relative;overflow:hidden;}
.modal-tipo::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,var(--c),var(--c2));}
.modal-tipo-nome{font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--t1);}
.modal-tipo-sub{font-size:11px;color:var(--t3);margin-top:2px;}
.modal-tipo-preco{font-family:'Syne',sans-serif;font-size:20px;font-weight:900;color:var(--gold);}
.modal-tipo-kz{font-size:10px;color:var(--t3);}

/* form */
.mf-group{margin-bottom:14px;}
.mf-label{display:block;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t2);margin-bottom:6px;}
.mf-input{width:100%;background:var(--s2);border:1.5px solid var(--b1);border-radius:12px;padding:12px 14px;font-size:14px;color:var(--t1);outline:none;transition:border-color .2s;font-family:'Inter',sans-serif;}
.mf-input:focus{border-color:var(--c);background:var(--s3);}
.mf-input::placeholder{color:var(--t3);}

/* qty stepper */
.qty-row{display:flex;align-items:center;justify-content:space-between;background:var(--s2);border:1.5px solid var(--b1);border-radius:12px;padding:10px 14px;}
.qty-lbl{font-size:13px;color:var(--t2);}
.qty-ctrl{display:flex;align-items:center;gap:12px;}
.qty-btn{width:32px;height:32px;border-radius:9px;background:var(--s3);border:1px solid var(--b2);color:var(--t1);font-size:18px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;line-height:1;}
.qty-btn:hover{border-color:var(--c);color:var(--c);}
.qty-num{font-family:'Syne',sans-serif;font-size:18px;font-weight:900;color:#fff;min-width:24px;text-align:center;}

/* total */
.modal-total{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.18);border-radius:12px;margin-bottom:16px;}
.modal-total-lbl{font-size:12px;color:var(--t2);}
.modal-total-val{font-family:'Syne',sans-serif;font-size:22px;font-weight:900;color:var(--gold);}

/* upload */
.upload-zone{position:relative;border:1.5px dashed rgba(6,182,212,.25);border-radius:12px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;}
.upload-zone:hover{border-color:var(--c);background:rgba(6,182,212,.04);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-icon{font-size:22px;margin-bottom:6px;}
.upload-txt{font-size:13px;color:var(--t2);}
.upload-sub{font-size:11px;color:var(--t3);margin-top:3px;}
.upload-ok{display:none;align-items:center;gap:8px;margin-top:10px;padding:9px 12px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:9px;}
.upload-ok-name{font-size:12px;color:var(--green);font-weight:600;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

/* erros */
.form-errors{background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.22);color:#fca5a5;padding:12px 14px;border-radius:12px;margin-bottom:14px;font-size:13px;}

/* submit */
.modal-submit{width:100%;padding:15px;border-radius:16px;background:linear-gradient(135deg,var(--c),var(--c2));color:#000;font-size:15px;font-weight:800;border:none;cursor:pointer;font-family:'Syne',sans-serif;box-shadow:0 4px 24px rgba(6,182,212,.35);display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;margin-top:18px;}
.modal-submit:hover{transform:translateY(-1px);box-shadow:0 6px 30px rgba(6,182,212,.45);}
.modal-notice{font-size:11px;color:var(--t3);text-align:center;margin-top:12px;line-height:1.6;}
</style>

<div x-data="{
    modalAberto:false, ingressoNome:'', ingressoPreco:0, ingressoId:'', quantidade:1,
    abrirModal(nome,preco,id){ this.ingressoNome=nome; this.ingressoPreco=preco; this.ingressoId=id; this.quantidade=1; this.modalAberto=true; document.body.style.overflow='hidden'; },
    fecharModal(){ this.modalAberto=false; document.body.style.overflow=''; },
    inc(){ if(this.quantidade<10) this.quantidade++; },
    dec(){ if(this.quantidade>1) this.quantidade--; },
    total(){ return (this.ingressoPreco*this.quantidade).toLocaleString('pt-PT'); }
}" class="ed-page">

{{-- HERO --}}
<div class="ed-hero">
    @if($temCapa) <img class="ed-hero-img" src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @elseif($temFotos) <img class="ed-hero-img" src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
    @else <div class="ed-hero-ph">{{ $catEmoji }}</div>
    @endif
    <div class="ed-hero-ov"></div>
    <div class="ed-hero-top">
        <div>
            @if($totalDisp<=0)<span class="ed-badge badge-hot">🔥 Esgotado</span>
            @elseif($preco==0)<span class="ed-badge badge-free">✅ Gratuito</span>
            @elseif($evento->created_at->isCurrentWeek())<span class="ed-badge badge-new">✨ Novo</span>
            @endif
        </div>
        @if($temFotos && $fotos->count()>1)
        <button class="ed-fotos-btn" onclick="abrirDrawer('drawer-galeria')">🖼 +{{ $fotos->count() }} fotos</button>
        @endif
    </div>
    <div class="ed-hero-content">
        @if($evento->categoria)<div class="ed-hero-cat">{{ $catEmoji }} {{ $evento->categoria->nome }}</div>@endif
        <h1 class="ed-hero-title">{{ e($evento->titulo) }}</h1>
        <div class="ed-hero-pills">
            <span class="ed-hero-pill">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}@if($evento->hora_inicio) · {{ substr($evento->hora_inicio,0,5) }}@endif</span>
            <span class="ed-hero-pill">📍 {{ Str::limit($evento->localizacao,28) }}</span>
            @if($evento->online)<span class="ed-hero-pill">🌐 Online</span>@endif
            @if($evento->lotacao_maxima)<span class="ed-hero-pill">👥 {{ number_format($evento->lotacao_maxima) }} pessoas</span>@endif
        </div>
    </div>
</div>

<div class="ed-body">

    {{-- CHIPS --}}
    <div class="ed-sec" style="border-radius:16px;">
        <div class="ed-chips">
            <span class="ed-chip act" onclick="abrirDrawer('drawer-detalhes')">ℹ️ Detalhes</span>
            @if($temFotos)<span class="ed-chip act" onclick="abrirDrawer('drawer-galeria')">🖼 Galeria</span>@endif
            <a href="{{ route('mensagens.index', ['user_id' => (int)$evento->user_id, 'evento_id' => (int)$evento->id]) }}" class="ed-chip act">💬 Contactar</a>
            @if($evento->link_externo)<a href="{{ e($evento->link_externo) }}" target="_blank" rel="noopener noreferrer" class="ed-chip act">🔗 Site oficial</a>@endif
        </div>
    </div>

    {{-- SOBRE --}}
    <div class="ed-sec">
        <div class="ed-sec-head"><div class="ed-sec-title">📋 Sobre o evento</div></div>
        <div class="ed-sec-body">
            <div class="ed-desc" id="descTexto" style="display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;">{!! nl2br(e($evento->descricao)) !!}</div>
            <button class="ed-desc-more" onclick="toggleDesc()"><span id="descToggleText">Ver mais ↓</span></button>
        </div>
    </div>

    {{-- META (campos específicos da categoria) --}}
    @if($temMeta)
    <div class="ed-sec">
        <div class="ed-sec-head">
            <div class="ed-sec-title">
                @if(str_contains($catNome,'viagem'))✈️ Detalhes da Viagem
                @elseif(str_contains($catNome,'show')||str_contains($catNome,'m'.'usi'))🎤 Detalhes do Show
                @elseif(str_contains($catNome,'festival'))🎉 Detalhes do Festival
                @elseif(str_contains($catNome,'desporto'))⚽ Detalhes do Jogo
                @elseif(str_contains($catNome,'confer'))🎙️ Detalhes da Conferência
                @elseif(str_contains($catNome,'workshop'))📚 Detalhes do Workshop
                @elseif(str_contains($catNome,'cultura'))🎭 Detalhes Culturais
                @elseif(str_contains($catNome,'gastro'))🍽️ Detalhes do Evento
                @else ⚙️ Informações adicionais
                @endif
            </div>
        </div>
        <div class="ed-sec-body">

            @if(!empty($meta['partida'])||!empty($meta['destino']))
            <div class="rota-strip">
                <div class="rota-node"><div class="rota-node-lbl">Partida</div><div class="rota-node-val">{{ e($meta['partida']??'—') }}</div></div>
                <div class="rota-arrow">✈️</div>
                <div class="rota-node"><div class="rota-node-lbl">Destino</div><div class="rota-node-val">{{ e($meta['destino']??'—') }}</div></div>
            </div>
            @endif

            <div class="meta-grid">
                @if(!empty($meta['hora_partida']))<div class="mc"><div class="mc-lbl">🕐 Hora de partida</div><div class="mc-val">{{ e($meta['hora_partida']) }}</div></div>@endif
                @if(!empty($meta['hora_chegada']))<div class="mc"><div class="mc-lbl">🏁 Chegada prevista</div><div class="mc-val">{{ e($meta['hora_chegada']) }}</div></div>@endif
                @if(!empty($meta['motorista']))<div class="mc"><div class="mc-lbl">👤 Motorista</div><div class="mc-val">{{ e($meta['motorista']) }}</div></div>@endif
                @if(!empty($meta['marca_veiculo']))<div class="mc"><div class="mc-lbl">🚌 Veículo</div><div class="mc-val">{{ e($meta['marca_veiculo']) }}@if(!empty($meta['matricula'])) · {{ e($meta['matricula']) }}@endif</div></div>@endif
                @if(!empty($meta['ar_condicionado']))<div class="mc"><div class="mc-lbl">❄️ Conforto</div><div class="mc-val">Ar condicionado</div></div>@endif
                @php $paragens=$parseTags($meta['paragens']??null); @endphp
                @if(!empty($paragens))
                <div class="mc full">
                    <div class="mc-lbl">📍 Paragens</div>
                    <div class="mc-tags">
                        @foreach($paragens as $p)
                        <span class="mc-tag">{{ e($p) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @php $artistas=$parseTags($meta['artistas']??null); @endphp
                @if(!empty($artistas))
                <div class="mc full">
                    <div class="mc-lbl">🎤 Artistas</div>
                    <div class="mc-tags">
                        @foreach($artistas as $a)
                            <span class="mc-tag">{{ e($a) }}</span>
                        @endforeach
                        </div>
                    </div>
                    @endif
                @if(!empty($meta['palco']))<div class="mc"><div class="mc-lbl">🎪 Palco</div><div class="mc-val">{{ e($meta['palco']) }}</div></div>@endif
                @if(!empty($meta['dresscode']))<div class="mc"><div class="mc-lbl">👔 Dress code</div><div class="mc-val">{{ e($meta['dresscode']) }}</div></div>@endif
                @if(!empty($meta['lineup']))<div class="mc full"><div class="mc-lbl">🕐 Lineup</div><div class="mc-val pre">{{ e($meta['lineup']) }}</div></div>@endif
                @if(!empty($meta['dias_festival']))<div class="mc"><div class="mc-lbl">📅 Dias</div><div class="mc-val">{{ e($meta['dias_festival']) }} dia(s)</div></div>@endif
                @if(!empty($meta['num_palcos']))<div class="mc"><div class="mc-lbl">🎪 Palcos</div><div class="mc-val">{{ e($meta['num_palcos']) }}</div></div>@endif
                @if(!empty($meta['camping']))<div class="mc"><div class="mc-lbl">⛺ Camping</div><div class="mc-val">Disponível</div></div>@endif
                @if(!empty($meta['equipa_local']))<div class="mc"><div class="mc-lbl">🏠 Casa</div><div class="mc-val">{{ e($meta['equipa_local']) }}</div></div>@endif
                @if(!empty($meta['equipa_visitante']))<div class="mc"><div class="mc-lbl">✈️ Visitante</div><div class="mc-val">{{ e($meta['equipa_visitante']) }}</div></div>@endif
                @if(!empty($meta['modalidade']))<div class="mc"><div class="mc-lbl">🏆 Modalidade</div><div class="mc-val">{{ e($meta['modalidade']) }}</div></div>@endif
                @if(!empty($meta['fase']))<div class="mc full"><div class="mc-lbl">📋 Fase</div><div class="mc-val">{{ e($meta['fase']) }}</div></div>@endif
                @if(!empty($meta['arbitro']))<div class="mc"><div class="mc-lbl">🟡 Árbitro</div><div class="mc-val">{{ e($meta['arbitro']) }}</div></div>@endif
                @if(!empty($meta['tema']))<div class="mc full"><div class="mc-lbl">💡 Tema</div><div class="mc-val">{{ e($meta['tema']) }}</div></div>@endif
                @php $pals=$parseTags($meta['palestrantes']??null); @endphp
                @if(!empty($pals))
                <div class="mc full">
                    <div class="mc-lbl">🎙️ Palestrantes</div>
                    <div class="mc-tags">
                        @foreach($pals as $p)
                            <span class="mc-tag">{{ e($p) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if(!empty($meta['instrutor']))<div class="mc"><div class="mc-lbl">👨‍🏫 Instrutor</div><div class="mc-val">{{ e($meta['instrutor']) }}</div></div>@endif
                @if(!empty($meta['nivel']))<div class="mc"><div class="mc-lbl">📊 Nível</div><div class="mc-val">{{ e($meta['nivel']) }}</div></div>@endif
                @if(!empty($meta['duracao_horas']))<div class="mc"><div class="mc-lbl">⏱️ Duração</div><div class="mc-val">{{ e($meta['duracao_horas']) }}h</div></div>@endif
                @if(!empty($meta['max_alunos']))<div class="mc"><div class="mc-lbl">👥 Máx. alunos</div><div class="mc-val">{{ e($meta['max_alunos']) }}</div></div>@endif
                @if(!empty($meta['materiais']))<div class="mc full"><div class="mc-lbl">📦 Materiais</div><div class="mc-val">{{ e($meta['materiais']) }}</div></div>@endif
                @if(!empty($meta['idioma']))<div class="mc"><div class="mc-lbl">🌐 Idioma</div><div class="mc-val">{{ e($meta['idioma']) }}</div></div>@endif
                @if(!empty($meta['certificado']))<div class="mc"><div class="mc-lbl">🎓 Certificado</div><div class="mc-val">Incluído</div></div>@endif
                @if(!empty($meta['chef']))<div class="mc"><div class="mc-lbl">👨‍🍳 Chef</div><div class="mc-val">{{ e($meta['chef']) }}</div></div>@endif
                @if(!empty($meta['tipo_culinaria']))<div class="mc"><div class="mc-lbl">🍽️ Culinária</div><div class="mc-val">{{ e($meta['tipo_culinaria']) }}</div></div>@endif
                @if(!empty($meta['menu']))<div class="mc full"><div class="mc-lbl">📋 Ementa</div><div class="mc-val pre">{{ e($meta['menu']) }}</div></div>@endif
                @if(!empty($meta['preco_menu']))<div class="mc"><div class="mc-lbl">💰 Preço menu</div><div class="mc-val">{{ number_format($meta['preco_menu'],0,',','.') }} Kz</div></div>@endif
                @if(!empty($meta['bebidas_incluidas']))<div class="mc"><div class="mc-lbl">🍷 Bebidas</div><div class="mc-val">Incluídas</div></div>@endif
                @php $elenco=$parseTags($meta['elenco']??null); @endphp
                @if(!empty($elenco))
                <div class="mc full">
                    <div class="mc-lbl">🎭 Elenco</div>
                     <div class="mc-tags">
                        @foreach($elenco as $el)
                        <span class="mc-tag">{{ e($el) }}</span>
                            @endforeach
                     </div>
                    </div>
                    @endif
                @if(!empty($meta['classificacao_etaria']))<div class="mc"><div class="mc-lbl">🔞 Classificação</div><div class="mc-val">{{ e($meta['classificacao_etaria']) }}</div></div>@endif
                @if(!empty($meta['duracao_minutos']))<div class="mc"><div class="mc-lbl">⏱️ Duração</div><div class="mc-val">{{ e($meta['duracao_minutos']) }} min</div></div>@endif
            </div>
        </div>
    </div>
    @endif

    {{-- BILHETES --}}
    <div class="ed-sec">
        <div class="ed-sec-head">
            <div class="ed-sec-title">🎟️ Bilhetes disponíveis</div>
            @if($totalDisp>0)<span style="font-size:11px;color:var(--green);font-weight:600;">{{ $totalDisp }} disponíveis</span>
            @else<span style="font-size:11px;color:var(--red);font-weight:600;">Esgotado</span>@endif
        </div>
        <div class="ed-sec-body">
            @forelse($evento->tiposIngresso as $tipo)
            <div class="ed-ticket">
                <div style="flex:1;min-width:0;">
                    <div class="ed-ticket-nome">{{ e($tipo->nome) }}</div>
                    <div class="ed-ticket-disp {{ $tipo->quantidade_disponivel>0?'ok':'esg' }}">
                        @if($tipo->quantidade_disponivel>0){{ $tipo->quantidade_disponivel }} disponíveis@else Esgotado@endif
                    </div>
                </div>
                <div class="ed-ticket-right">
                    <div class="ed-ticket-preco">{{ number_format($tipo->preco,0,',','.') }} Kz</div>
                    <button class="ed-ticket-btn" @click="abrirModal('{{ addslashes(e($tipo->nome)) }}',{{ $tipo->preco }},{{ $tipo->id }})" {{ $tipo->quantidade_disponivel<=0?'disabled':'' }}>
                        {{ $tipo->quantidade_disponivel>0?'Comprar':'Esgotado' }}
                    </button>
                </div>
            </div>
            @empty
            <p style="font-size:14px;color:var(--t3);text-align:center;padding:20px 0;">Sem bilhetes disponíveis.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- BOTTOM BAR MOBILE --}}
<div class="ed-bottom">
    <div style="flex:1;">
        <div class="ed-bottom-preco">@if($preco==0)Gratuito@else{{ number_format($preco,0,',','.') }} Kz@endif</div>
        <div class="ed-bottom-lbl">por bilhete</div>
    </div>
    <button class="ed-bottom-info" onclick="abrirDrawer('drawer-detalhes')">ℹ️</button>
    <button class="ed-bottom-btn" onclick="abrirDrawer('drawer-bilhetes')" {{ $totalDisp<=0?'disabled':'' }}>
        🎟 {{ $totalDisp>0?'Comprar':'Esgotado' }}
    </button>
</div>

{{-- MODAL COMPRA --}}
<div x-show="modalAberto" x-cloak>
    <div class="modal-bg" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="fecharModal()"></div>
    <div class="modal-wrap">
        <div class="modal-box" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">
            <div class="modal-handle-bar"></div>
            <div class="modal-header">
                <div>
                    <div class="modal-secure">Reserva segura</div>
                    <div class="modal-h-title">Finalizar compra</div>
                </div>
                <button class="modal-close" @click="fecharModal()">✕</button>
            </div>
            <div class="modal-content">
                @if($errors->any())
                <div class="form-errors"><strong style="display:block;color:#fff;margin-bottom:5px;">Erros encontrados:</strong><ul style="margin:0;padding-left:16px;">
@foreach($errors->all() as $error)
<li>{{ e($error) }}</li>
@endforeach
</ul></div>
                @endif

                <div class="modal-ev">
                    @if($temCapa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="" class="modal-ev-img" loading="lazy">
                    @else<div class="modal-ev-ph">{{ $catEmoji }}</div>@endif
                    <div>
                        <div class="modal-ev-name">{{ e($evento->titulo) }}</div>
                        <div class="modal-ev-meta">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}@if($evento->hora_inicio) · {{ substr($evento->hora_inicio,0,5) }}@endif · 📍 {{ Str::limit($evento->localizacao,22) }}</div>
                    </div>
                </div>

                @if($temMeta)
                <div class="modal-meta">
                    <div class="modal-meta-lbl">@if(str_contains($catNome,'viagem'))✈️ Viagem@elseif(str_contains($catNome,'show')||str_contains($catNome,'music'))🎤 Show@elseif(str_contains($catNome,'festival'))🎉 Festival@elseif(str_contains($catNome,'desporto'))⚽ Jogo@elseif(str_contains($catNome,'confer'))🎙️ Conferência@elseif(str_contains($catNome,'workshop'))📚 Workshop@else⚙️ Detalhes@endif</div>
                    <div class="modal-meta-grid">
                        @if(!empty($meta['partida']))<div class="mmc"><div class="mmc-lbl">Partida</div><div class="mmc-val">{{ e($meta['partida']) }}</div></div>@endif
                        @if(!empty($meta['destino']))<div class="mmc"><div class="mmc-lbl">Destino</div><div class="mmc-val">{{ e($meta['destino']) }}</div></div>@endif
                        @if(!empty($meta['hora_partida']))<div class="mmc"><div class="mmc-lbl">Hora</div><div class="mmc-val">{{ e($meta['hora_partida']) }}</div></div>@endif
                        @if(!empty($meta['motorista']))<div class="mmc"><div class="mmc-lbl">Motorista</div><div class="mmc-val">{{ e($meta['motorista']) }}</div></div>@endif
                        @if(!empty($meta['marca_veiculo']))<div class="mmc"><div class="mmc-lbl">Veículo</div><div class="mmc-val">{{ e($meta['marca_veiculo']) }}</div></div>@endif
                        @php $art=$parseTags($meta['artistas']??null); @endphp
                        @if(!empty($art))
                        <div class="mmc full">
                            <div class="mmc-lbl">Artistas</div>
                            <div class="mmc-tags">
                                @foreach($art as $a)
                                <span class="mmc-tag">{{ e($a) }}</span>
                                @endforeach
                                </div>
                            </div>
                            @endif
                        @if(!empty($meta['dresscode']))<div class="mmc"><div class="mmc-lbl">Dress code</div><div class="mmc-val">{{ e($meta['dresscode']) }}</div></div>@endif
                        @if(!empty($meta['equipa_local']))<div class="mmc"><div class="mmc-lbl">Casa</div><div class="mmc-val">{{ e($meta['equipa_local']) }}</div></div>@endif
                        @if(!empty($meta['equipa_visitante']))<div class="mmc"><div class="mmc-lbl">Visitante</div><div class="mmc-val">{{ e($meta['equipa_visitante']) }}</div></div>@endif
                        @if(!empty($meta['instrutor']))<div class="mmc"><div class="mmc-lbl">Instrutor</div><div class="mmc-val">{{ e($meta['instrutor']) }}</div></div>@endif
                        @if(!empty($meta['chef']))<div class="mmc"><div class="mmc-lbl">Chef</div><div class="mmc-val">{{ e($meta['chef']) }}</div></div>@endif
                    </div>
                </div>
                @endif

                <div class="modal-tipo">
                    <div><div class="modal-tipo-nome" x-text="ingressoNome"></div><div class="modal-tipo-sub">Tipo selecionado</div></div>
                    <div style="text-align:right;"><div class="modal-tipo-preco" x-text="Number(ingressoPreco).toLocaleString('pt-PT')+' Kz'"></div><div class="modal-tipo-kz">por bilhete</div></div>
                </div>

                <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                    <input type="hidden" name="quantidade" :value="quantidade">

                    <div class="mf-group">
                        <label class="mf-label">Nome do titular</label>
                        <input type="text" name="nome_cliente" class="mf-input" placeholder="Nome completo" required autocomplete="name" value="{{ old('nome_cliente') }}">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">WhatsApp</label>
                        <input type="tel" name="whatsapp" class="mf-input" placeholder="+244 9XX XXX XXX" required autocomplete="tel" value="{{ old('whatsapp') }}">
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Quantidade</label>
                        <div class="qty-row">
                            <span class="qty-lbl">Bilhetes</span>
                            <div class="qty-ctrl">
                                <button type="button" class="qty-btn" @click="dec()">−</button>
                                <span class="qty-num" x-text="quantidade"></span>
                                <button type="button" class="qty-btn" @click="inc()">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-total">
                        <span class="modal-total-lbl">Total a pagar</span>
                        <span class="modal-total-val" x-text="total()+' Kz'"></span>
                    </div>
                    <div class="mf-group">
                        <label class="mf-label">Comprovativo de pagamento</label>
                        <div class="upload-zone">
                            <input type="file" name="comprovativo" required accept=".jpg,.jpeg,.png,.pdf" onchange="handleUpload(this)">
                            <div class="upload-icon">📎</div>
                            <div class="upload-txt">Clica para anexar</div>
                            <div class="upload-sub">JPG, PNG ou PDF · máx. 5 MB</div>
                        </div>
                        <div class="upload-ok" id="upload-ok"><span>✅</span><span class="upload-ok-name" id="upload-ok-name"></span></div>
                    </div>
                    <button type="submit" class="modal-submit">✅ Confirmar Reserva</button>
                    <div class="modal-notice">🔒 Reserva segura · Bilhete emitido em até 24h após validação</div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- DRAWERS --}}
<div class="drawer-overlay" id="drawer-bilhetes" onclick="if(event.target===this)fecharDrawer('drawer-bilhetes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">🎟️ Bilhetes <button class="drawer-close" onclick="fecharDrawer('drawer-bilhetes')">✕</button></div>
        @forelse($evento->tiposIngresso as $tipo)
        <div class="ed-ticket" style="margin-bottom:10px;">
            <div style="flex:1;min-width:0;"><div class="ed-ticket-nome">{{ e($tipo->nome) }}</div><div class="ed-ticket-disp {{ $tipo->quantidade_disponivel>0?'ok':'esg' }}">@if($tipo->quantidade_disponivel>0){{ $tipo->quantidade_disponivel }} disponíveis@else Esgotado@endif</div></div>
            <div class="ed-ticket-right">
                <div class="ed-ticket-preco">{{ number_format($tipo->preco,0,',','.') }} Kz</div>
                <button onclick="fecharDrawer('drawer-bilhetes')" @click="abrirModal('{{ addslashes(e($tipo->nome)) }}',{{ $tipo->preco }},{{ $tipo->id }})" class="ed-ticket-btn" {{ $tipo->quantidade_disponivel<=0?'disabled':'' }}>{{ $tipo->quantidade_disponivel>0?'Comprar':'Esgotado' }}</button>
            </div>
        </div>
        @empty
<p style="text-align:center;color:var(--t3);padding:20px 0;font-size:13px;">Sem bilhetes disponíveis.</p>
@endforelse
    </div>
</div>

<div class="drawer-overlay" id="drawer-detalhes" onclick="if(event.target===this)fecharDrawer('drawer-detalhes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">ℹ️ Detalhes <button class="drawer-close" onclick="fecharDrawer('drawer-detalhes')">✕</button></div>
        <div class="ed-info-row"><span class="ed-info-lbl">Data</span><span class="ed-info-val">{{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span></div>
        @if($evento->hora_inicio)<div class="ed-info-row"><span class="ed-info-lbl">Hora</span><span class="ed-info-val">{{ substr($evento->hora_inicio,0,5) }}@if($evento->hora_fim) – {{ substr($evento->hora_fim,0,5) }}@endif</span></div>@endif
        @if($evento->data_fim&&$evento->data_fim!==$evento->data_evento)<div class="ed-info-row"><span class="ed-info-lbl">Termina</span><span class="ed-info-val">{{ \Carbon\Carbon::parse($evento->data_fim)->format('d/m/Y') }}</span></div>@endif
        <div class="ed-info-row"><span class="ed-info-lbl">Local</span><span class="ed-info-val">{{ e($evento->localizacao) }}</span></div>
        @if($evento->municipio)<div class="ed-info-row"><span class="ed-info-lbl">Município</span><span class="ed-info-val">{{ e($evento->municipio) }}</span></div>@endif
        @if($evento->provincia)<div class="ed-info-row"><span class="ed-info-lbl">Província</span><span class="ed-info-val">{{ e($evento->provincia) }}</span></div>@endif
        @if($evento->lotacao_maxima)<div class="ed-info-row"><span class="ed-info-lbl">Lotação</span><span class="ed-info-val">{{ number_format($evento->lotacao_maxima,0,',','.') }}</span></div>@endif
        <div class="ed-info-row"><span class="ed-info-lbl">Formato</span><span class="ed-info-val">{{ $evento->online?'🌐 Online':'📍 Presencial' }}</span></div>
        @if($evento->categoria)<div class="ed-info-row"><span class="ed-info-lbl">Categoria</span><span class="ed-info-val">{{ $catEmoji }} {{ e($evento->categoria->nome) }}</span></div>@endif
        @if($evento->subcategoria)<div class="ed-info-row"><span class="ed-info-lbl">Subcategoria</span><span class="ed-info-val">{{ e($evento->subcategoria->nome) }}</span></div>@endif
        <a href="{{ route('mensagens.index', ['user_id'=>(int)$evento->user_id,'evento_id'=>(int)$evento->id]) }}" class="contactar-btn">💬 Contactar organizador</a>
    </div>
</div>

@if($temFotos)
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this)fecharDrawer('drawer-galeria')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">🖼️ Galeria <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">✕</button></div>
        <div class="gallery-grid">@foreach($fotos as $foto)
<img src="{{ asset('storage/'.e($foto->caminho)) }}" alt="Foto do evento" loading="lazy">
@endforeach</div>
    </div>
</div>
@endif

</div>

<script>
function abrirDrawer(id){document.getElementById(id).classList.add('open');document.body.style.overflow='hidden';}
function fecharDrawer(id){document.getElementById(id).classList.remove('open');document.body.style.overflow='';}
let descAberta=false;
function toggleDesc(){
    descAberta=!descAberta;
    const el=document.getElementById('descTexto');
    const btn=document.getElementById('descToggleText');
    el.style.webkitLineClamp=descAberta?'unset':'4';
    el.style.overflow=descAberta?'visible':'hidden';
    btn.textContent=descAberta?'Ver menos ↑':'Ver mais ↓';
}
function handleUpload(input){
    const file=input.files[0];
    const ok=document.getElementById('upload-ok');
    const name=document.getElementById('upload-ok-name');
    if(file){name.textContent=file.name;ok.style.display='flex';}
    else{ok.style.display='none';}
}
document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){document.querySelectorAll('.drawer-overlay.open').forEach(d=>d.classList.remove('open'));document.body.style.overflow='';}
});
</script>

@endsection