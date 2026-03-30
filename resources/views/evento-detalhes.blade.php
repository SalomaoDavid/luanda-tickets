@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
[x-cloak]{display:none!important;}
*{box-sizing:border-box;}

:root{
  --bg:#06090f;--s1:#0b1120;--s2:#101828;--s3:#1a2540;
  --cyan:#06b6d4;--cyan2:#0891b2;--gold:#f59e0b;
  --green:#10b981;--red:#f43f5e;--text:#e8edf5;
  --muted:#64748b;--muted2:#94a3b8;
  --border:rgba(6,182,212,.13);--border2:rgba(6,182,212,.28);
}

body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);}

@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

.evd-wrap{max-width:860px;margin:0 auto;padding:0 0 120px;}

/* HERO */
.evd-hero{position:relative;height:280px;overflow:hidden;}
@media(min-width:768px){.evd-hero{height:420px;border-radius:0 0 32px 32px;}}
.evd-hero-img{width:100%;height:100%;object-fit:cover;}
.evd-hero-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:90px;background:linear-gradient(135deg,#0b1a2e,#1a3a5f);}
.evd-hero-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(6,9,15,.95) 0%,rgba(6,9,15,.4) 50%,rgba(6,9,15,.1) 100%);}
.evd-hero-top{position:absolute;top:14px;left:14px;right:14px;display:flex;justify-content:space-between;align-items:flex-start;z-index:2;}
.evd-badge{font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:4px 10px;border-radius:20px;}
.badge-hot{background:rgba(244,63,94,.9);color:#fff;}
.badge-free{background:rgba(16,185,129,.9);color:#fff;}
.badge-new{background:rgba(6,182,212,.9);color:#000;}
.evd-fotos-btn{display:flex;align-items:center;gap:6px;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.2);border-radius:10px;padding:6px 12px;font-size:11px;font-weight:700;color:#fff;cursor:pointer;transition:all .2s;}
.evd-fotos-btn:hover{background:rgba(6,182,212,.3);border-color:var(--cyan);}
.evd-hero-bottom{position:absolute;bottom:0;left:0;right:0;z-index:2;padding:20px 20px 24px;}
@media(min-width:768px){.evd-hero-bottom{padding:28px 32px 32px;}}
.evd-hero-cat{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--cyan);margin-bottom:8px;}
.evd-hero-title{font-family:'Syne',sans-serif;font-size:24px;font-weight:900;color:#fff;line-height:1.15;letter-spacing:-.5px;}
@media(min-width:768px){.evd-hero-title{font-size:36px;}}

/* MAIN CARD */
.evd-card{background:var(--s1);border:1px solid var(--border2);border-radius:0 0 24px 24px;padding:20px;position:relative;z-index:3;}
@media(min-width:768px){.evd-card{border-radius:24px;margin:16px;padding:28px;}}

.evd-chips{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:18px;}
.evd-chip{display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:9px;background:var(--s2);border:1px solid var(--border);font-size:12px;font-weight:500;color:var(--muted2);cursor:pointer;transition:all .2s;white-space:nowrap;text-decoration:none;}
.evd-chip:hover,.evd-chip.action{border-color:var(--border2);color:var(--cyan);background:rgba(6,182,212,.06);}
.evd-desc{font-size:14px;color:var(--muted2);line-height:1.8;margin-bottom:16px;}
.evd-desc-toggle{display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;color:var(--cyan);cursor:pointer;border:none;background:none;padding:0;margin-bottom:18px;}
.evd-cta-row{display:flex;align-items:center;justify-content:space-between;padding:18px 0 0;border-top:1px solid var(--border);gap:12px;flex-wrap:wrap;}
.evd-preco{font-family:'Syne',sans-serif;font-size:28px;font-weight:900;color:#fff;line-height:1;}
.evd-preco small{font-size:13px;color:var(--muted);font-weight:400;font-family:'DM Sans',sans-serif;}
.evd-preco.free{color:var(--green);font-size:18px;}
.evd-cta-btn{flex:1;min-width:160px;padding:14px 24px;border-radius:14px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-size:14px;font-weight:800;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 20px rgba(6,182,212,.35);font-family:'Syne',sans-serif;}
.evd-cta-btn:hover{transform:translateY(-1px);}
.evd-cta-btn:disabled{opacity:.4;cursor:not-allowed;transform:none;}

/* BILHETES */
.evd-section{background:var(--s1);border:1px solid var(--border);border-radius:20px;padding:20px;margin:12px 0 0;}
@media(min-width:768px){.evd-section{margin:12px 16px 0;padding:24px;}}
.evd-section-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:#fff;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.ticket-card{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px;border-radius:14px;border:1px solid var(--border);background:var(--s2);margin-bottom:8px;transition:border-color .2s;}
.ticket-card:hover{border-color:var(--border2);}
.ticket-card:last-child{margin-bottom:0;}
.ticket-nome{font-size:14px;font-weight:700;color:#fff;margin-bottom:3px;}
.ticket-restam{font-size:11px;color:var(--red);font-weight:700;}
.ticket-restam.ok{color:var(--muted2);}
.ticket-preco{font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:var(--gold);text-align:right;margin-bottom:7px;}
.buy-btn{background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;padding:8px 16px;border-radius:10px;font-size:12px;font-weight:800;border:none;cursor:pointer;transition:all .2s;white-space:nowrap;}
.buy-btn:hover{transform:translateY(-1px);}
.buy-btn:disabled{background:var(--s3);color:var(--muted);cursor:not-allowed;transform:none;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);font-size:13px;gap:8px;}
.info-row:last-child{border-bottom:none;}
.info-lbl{color:var(--muted);font-weight:500;flex-shrink:0;}
.info-val{color:var(--text);font-weight:600;text-align:right;}

/* BOTTOM BAR */
.evd-bottom-bar{position:fixed;bottom:0;left:0;right:0;z-index:200;padding:12px 16px;background:rgba(11,17,32,.97);backdrop-filter:blur(20px);border-top:1px solid var(--border2);display:flex;align-items:center;gap:10px;}
@media(min-width:768px){.evd-bottom-bar{display:none;}}
.evd-bottom-preco-val{font-family:'Syne',sans-serif;font-size:20px;font-weight:900;color:#fff;}
.evd-bottom-preco-lbl{font-size:10px;color:var(--muted);}
.evd-bottom-btn{flex:1;padding:13px;border-radius:14px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-size:14px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(6,182,212,.4);font-family:'Syne',sans-serif;}
.evd-bottom-btn:disabled{opacity:.4;}
.evd-bottom-outline{padding:13px 14px;border-radius:14px;border:1px solid var(--border2);background:var(--s2);color:var(--muted2);font-size:13px;font-weight:600;cursor:pointer;}

/* DRAWERS */
.drawer-overlay{display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);}
.drawer-overlay.open{display:flex;align-items:flex-end;}
@media(min-width:640px){.drawer-overlay.open{align-items:center;justify-content:center;}}
.drawer-box{width:100%;background:var(--s1);border:1px solid var(--border2);border-radius:24px 24px 0 0;padding:20px 20px 40px;max-height:88vh;overflow-y:auto;}
@media(min-width:640px){.drawer-box{border-radius:24px;max-width:480px;max-height:82vh;}}
.drawer-handle{width:36px;height:4px;border-radius:2px;background:var(--border2);margin:0 auto 18px;}
@media(min-width:640px){.drawer-handle{display:none;}}
.drawer-title{font-family:'Syne',sans-serif;font-size:17px;font-weight:800;color:#fff;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;}
.drawer-close{background:none;border:none;font-size:20px;color:var(--muted);cursor:pointer;transition:color .2s;}
.drawer-close:hover{color:var(--red);}
.gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;}
.gallery-grid img{width:100%;height:90px;object-fit:cover;border-radius:10px;cursor:pointer;}
@media(min-width:640px){.gallery-grid img{height:110px;}}
.contactar-btn{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:18px;padding:13px;border-radius:13px;background:rgba(6,182,212,.12);border:1px solid var(--border2);color:var(--cyan);font-weight:700;font-size:14px;text-decoration:none;transition:all .2s;}
.contactar-btn:hover{background:rgba(6,182,212,.2);}

/* MODAL COMPRA */
.modal-overlay{position:fixed;inset:0;z-index:998;background:rgba(0,0,0,.8);backdrop-filter:blur(8px);}
.modal-wrap{position:fixed;inset:0;z-index:999;display:flex;align-items:flex-end;justify-content:center;pointer-events:none;}
@media(min-width:640px){.modal-wrap{align-items:center;padding:16px;}}
.modal-inner{pointer-events:auto;width:100%;max-width:440px;background:var(--s1);border:1px solid var(--border2);border-radius:24px 24px 0 0;padding:24px 22px 36px;max-height:94vh;overflow-y:auto;position:relative;}
@media(min-width:640px){.modal-inner{border-radius:24px;padding:28px;}}
.modal-handle{width:36px;height:4px;border-radius:2px;background:var(--border2);margin:0 auto 18px;}
@media(min-width:640px){.modal-handle{display:none;}}
.modal-glow{position:absolute;top:-40px;right:-40px;width:120px;height:120px;border-radius:50%;background:rgba(6,182,212,.15);filter:blur(40px);pointer-events:none;}
.modal-title{font-family:'Syne',sans-serif;font-size:20px;font-weight:900;color:#fff;}
.modal-subtitle{font-size:10px;color:var(--muted);font-weight:600;letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;}
.modal-close-btn{width:32px;height:32px;border-radius:10px;background:var(--s2);border:1px solid var(--border);color:var(--muted);font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.modal-close-btn:hover{background:rgba(244,63,94,.15);border-color:var(--red);color:var(--red);}
.modal-summary{background:var(--s2);border:1px solid var(--border2);border-radius:14px;padding:14px 16px;margin:18px 0;}
.modal-summary-nome{font-size:11px;font-weight:700;color:var(--cyan);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;}
.modal-summary-preco{font-family:'Syne',sans-serif;font-size:26px;font-weight:900;color:var(--gold);line-height:1;}
.modal-summary-kz{font-size:13px;color:var(--muted);margin-left:4px;}
.form-group{margin-bottom:14px;}
.form-label{font-size:10px;font-weight:700;color:var(--muted2);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;display:block;}
.form-input{width:100%;background:var(--s2);border:1.5px solid var(--border);border-radius:12px;padding:12px 14px;font-size:14px;color:var(--text);outline:none;transition:border-color .2s;}
.form-input:focus{border-color:var(--cyan);}
.form-input::placeholder{color:var(--muted);}
.form-input[type=file]{padding:10px 14px;font-size:12px;color:var(--muted2);}
.form-input[type=file]::file-selector-button{background:var(--s3);border:1px solid var(--border2);color:var(--cyan);padding:4px 10px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;margin-right:8px;}
.qty-row{display:flex;align-items:center;justify-content:space-between;background:var(--s2);border:1.5px solid var(--border);border-radius:12px;padding:11px 14px;}
.qty-label{font-size:13px;color:var(--muted2);font-weight:500;}
.qty-input{width:64px;background:var(--s3);border:1px solid var(--border2);border-radius:9px;padding:6px 8px;text-align:center;font-size:14px;font-weight:700;color:#fff;outline:none;}
.form-errors{background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.3);color:#fca5a5;padding:12px 14px;border-radius:12px;margin-bottom:14px;font-size:13px;}
.submit-btn{width:100%;padding:15px;border-radius:14px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-size:15px;font-weight:800;border:none;cursor:pointer;transition:all .2s;font-family:'Syne',sans-serif;box-shadow:0 4px 20px rgba(6,182,212,.35);margin-top:18px;}
.submit-btn:hover{transform:translateY(-1px);}
.submit-notice{font-size:11px;color:var(--muted);text-align:center;margin-top:10px;line-height:1.5;}
</style>

<div x-data="{
    modalAberto: false,
    ingressoNome: '',
    ingressoPreco: 0,
    ingressoId: '',
    quantidade: 1,
    abrirModal(nome, preco, id) {
        this.ingressoNome = nome;
        this.ingressoPreco = preco;
        this.ingressoId = id;
        this.quantidade = 1;
        this.modalAberto = true;
        document.body.style.overflow = 'hidden';
    },
    fecharModal() {
        this.modalAberto = false;
        document.body.style.overflow = '';
    }
}" class="evd-wrap">

@php
    $fotos     = $evento->fotos;
    $temCapa   = $evento->imagem_capa;
    $temFotos  = $fotos->count() > 0;
    $preco     = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
    $totalDisp = $evento->tiposIngresso->sum('quantidade_disponivel');
@endphp

{{-- HERO --}}
<div class="evd-hero">
    @if($temCapa)
        <img class="evd-hero-img" src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
    @elseif($temFotos)
        <img class="evd-hero-img" src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ $evento->titulo }}" loading="lazy">
    @else
        <div class="evd-hero-placeholder">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
    @endif
    <div class="evd-hero-overlay"></div>

    <div class="evd-hero-top">
        <div>
            @if($totalDisp <= 0) <span class="evd-badge badge-hot">Esgotado</span>
            @elseif($preco == 0) <span class="evd-badge badge-free">Gratuito</span>
            @elseif($evento->created_at->isCurrentWeek()) <span class="evd-badge badge-new">✨ Novo</span>
            @endif
        </div>
        @if($temFotos && $fotos->count() > 1)
        <button class="evd-fotos-btn" onclick="abrirDrawer('drawer-galeria')">🖼 +{{ $fotos->count() }} fotos</button>
        @endif
    </div>

    <div class="evd-hero-bottom">
        @if($evento->categoria)
        <div class="evd-hero-cat">{{ $evento->categoria->emoji }} {{ $evento->categoria->nome }}</div>
        @endif
        <div class="evd-hero-title">{{ $evento->titulo }}</div>
    </div>
</div>

{{-- MAIN CARD --}}
<div class="evd-card">
    <div class="evd-chips">
        <div class="evd-chip">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}@if($evento->hora_inicio) · {{ \Illuminate\Support\Str::substr($evento->hora_inicio,0,5) }}@endif</div>
        <div class="evd-chip">📍 {{ Str::limit($evento->localizacao, 24) }}</div>
        @if($evento->online)<div class="evd-chip">🌐 Online</div>@endif
        <div class="evd-chip action" onclick="abrirDrawer('drawer-detalhes')">ℹ️ Detalhes</div>
        @if($temFotos)<div class="evd-chip action" onclick="abrirDrawer('drawer-galeria')">🖼 Galeria</div>@endif
        <a href="{{ route('mensagens.index', ['user_id' => $evento->user_id, 'evento_id' => $evento->id]) }}" class="evd-chip action">💬 Contactar</a>
    </div>

    <div class="evd-desc" id="descTexto" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
        {!! nl2br(e($evento->descricao)) !!}
    </div>
    <button class="evd-desc-toggle" onclick="toggleDesc()"><span id="descToggleText">Ver mais ↓</span></button>
    @if($evento->link_externo)
    <a href="{{ $evento->link_externo }}" target="_blank" style="display:inline-flex;align-items:center;gap:5px;margin-left:14px;font-size:12px;font-weight:700;color:var(--cyan);text-decoration:none;">🔗 Mais info</a>
    @endif

    <div class="evd-cta-row hidden md:flex">
        <div class="evd-preco {{ $preco == 0 ? 'free' : '' }}">
            @if($preco == 0) ✅ Entrada Gratuita
            @else {{ number_format($preco, 0, ',', '.') }} <small>Kz / bilhete</small>
            @endif
        </div>
        <button class="evd-cta-btn" onclick="abrirDrawer('drawer-bilhetes')" {{ $totalDisp <= 0 ? 'disabled' : '' }}>
            🎟 {{ $totalDisp > 0 ? 'Comprar Bilhete' : 'Esgotado' }}
        </button>
    </div>
</div>

{{-- BILHETES DESKTOP --}}
<div class="evd-section hidden md:block">
    <div class="evd-section-title">🎟️ Bilhetes disponíveis</div>
    @forelse($evento->tiposIngresso as $tipo)
    <div class="ticket-card">
        <div style="flex:1;min-width:0;">
            <div class="ticket-nome">{{ $tipo->nome }}</div>
            <div class="ticket-restam {{ $tipo->quantidade_disponivel > 0 ? 'ok' : '' }}">
                @if($tipo->quantidade_disponivel > 0) {{ $tipo->quantidade_disponivel }} disponíveis
                @else Esgotado @endif
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;">
            <div class="ticket-preco">{{ number_format($tipo->preco, 0, ',', '.') }} Kz</div>
            <button @click="abrirModal('{{ $tipo->nome }}', {{ $tipo->preco }}, {{ $tipo->id }})"
                    class="buy-btn" {{ $tipo->quantidade_disponivel <= 0 ? 'disabled' : '' }}>
                {{ $tipo->quantidade_disponivel > 0 ? 'Comprar' : 'Esgotado' }}
            </button>
        </div>
    </div>
    @empty
    <p style="font-size:14px;color:var(--muted);text-align:center;padding:16px 0;">Sem bilhetes disponíveis.</p>
    @endforelse
</div>

{{-- MODAL COMPRA --}}
<div x-show="modalAberto" x-cloak>
    <div class="modal-overlay"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="fecharModal()"></div>

    <div class="modal-wrap">
        <div class="modal-inner"
             x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">

            <div class="modal-glow"></div>
            <div class="modal-handle"></div>

            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;position:relative;z-index:1;">
                <div>
                    <div class="modal-title">Finalizar compra</div>
                    <div class="modal-subtitle">Luanda Tickets · Reserva segura</div>
                </div>
                <button class="modal-close-btn" @click="fecharModal()">✕</button>
            </div>

            @if($errors->any())
            <div class="form-errors" style="margin-top:14px;position:relative;z-index:1;">
                <strong style="display:block;color:#fff;margin-bottom:5px;">Ops! Algo correu mal:</strong>
                <ul style="margin:0;padding-left:16px;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="modal-summary" style="position:relative;z-index:1;">
                <div class="modal-summary-nome" x-text="ingressoNome"></div>
                <div class="modal-summary-preco">
                    <span x-text="(Number(ingressoPreco) * Number(quantidade)).toLocaleString('pt-PT')"></span>
                    <span class="modal-summary-kz">Kz</span>
                </div>
            </div>

            <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data" style="position:relative;z-index:1;">
                @csrf
                <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                <div class="form-group">
                    <label class="form-label">Nome do titular</label>
                    <input type="text" name="nome_cliente" class="form-input" placeholder="Nome completo" required value="{{ old('nome_cliente') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" class="form-input" placeholder="+244 9XX XXX XXX" required value="{{ old('whatsapp') }}">
                </div>
                <div class="form-group">
                    <div class="qty-row">
                        <span class="qty-label">Quantidade de bilhetes</span>
                        <input type="number" name="quantidade" min="1" x-model="quantidade" class="qty-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Comprovativo de pagamento</label>
                    <input type="file" name="comprovativo" class="form-input" required accept=".jpg,.jpeg,.png,.pdf">
                </div>
                <button type="submit" class="submit-btn">✅ Confirmar Reserva</button>
                <div class="submit-notice">Após confirmar, o staff irá validar o comprovativo<br>e emitir o bilhete digital em até 24h.</div>
            </form>
        </div>
    </div>
</div>

{{-- BOTTOM BAR MOBILE --}}
<div class="evd-bottom-bar">
    <div style="flex:1;">
        <div class="evd-bottom-preco-val">@if($preco == 0) Gratuito @else {{ number_format($preco, 0, ',', '.') }} Kz @endif</div>
        <div class="evd-bottom-preco-lbl">por bilhete</div>
    </div>
    <button class="evd-bottom-outline" onclick="abrirDrawer('drawer-detalhes')">ℹ️</button>
    <button class="evd-bottom-btn" onclick="abrirDrawer('drawer-bilhetes')" {{ $totalDisp <= 0 ? 'disabled' : '' }}>
        🎟 {{ $totalDisp > 0 ? 'Comprar' : 'Esgotado' }}
    </button>
</div>

{{-- DRAWER BILHETES --}}
<div class="drawer-overlay" id="drawer-bilhetes" onclick="if(event.target===this) fecharDrawer('drawer-bilhetes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">🎟️ Bilhetes <button class="drawer-close" onclick="fecharDrawer('drawer-bilhetes')">✕</button></div>
        @forelse($evento->tiposIngresso as $tipo)
        <div class="ticket-card">
            <div style="flex:1;min-width:0;">
                <div class="ticket-nome">{{ $tipo->nome }}</div>
                <div class="ticket-restam {{ $tipo->quantidade_disponivel > 0 ? 'ok' : '' }}">
                    @if($tipo->quantidade_disponivel > 0) {{ $tipo->quantidade_disponivel }} disponíveis @else Esgotado @endif
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div class="ticket-preco">{{ number_format($tipo->preco, 0, ',', '.') }} Kz</div>
                <button onclick="fecharDrawer('drawer-bilhetes')"
                        @click="abrirModal('{{ $tipo->nome }}', {{ $tipo->preco }}, {{ $tipo->id }})"
                        class="buy-btn" {{ $tipo->quantidade_disponivel <= 0 ? 'disabled' : '' }}>
                    {{ $tipo->quantidade_disponivel > 0 ? 'Comprar' : 'Esgotado' }}
                </button>
            </div>
        </div>
        @empty
        <p style="font-size:14px;color:var(--muted);text-align:center;padding:20px 0;">Sem bilhetes disponíveis.</p>
        @endforelse
    </div>
</div>

{{-- DRAWER DETALHES --}}
<div class="drawer-overlay" id="drawer-detalhes" onclick="if(event.target===this) fecharDrawer('drawer-detalhes')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">ℹ️ Detalhes <button class="drawer-close" onclick="fecharDrawer('drawer-detalhes')">✕</button></div>
        <div class="info-row"><span class="info-lbl">Data</span><span class="info-val">{{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span></div>
        @if($evento->hora_inicio)
        <div class="info-row"><span class="info-lbl">Hora</span><span class="info-val">{{ \Illuminate\Support\Str::substr($evento->hora_inicio,0,5) }}@if($evento->hora_fim) – {{ \Illuminate\Support\Str::substr($evento->hora_fim,0,5) }}@endif</span></div>
        @endif
        @if($evento->data_fim && $evento->data_fim !== $evento->data_evento)
        <div class="info-row"><span class="info-lbl">Termina</span><span class="info-val">{{ \Carbon\Carbon::parse($evento->data_fim)->format('d/m/Y') }}</span></div>
        @endif
        <div class="info-row"><span class="info-lbl">Local</span><span class="info-val">{{ $evento->localizacao }}</span></div>
        @if($evento->municipio)<div class="info-row"><span class="info-lbl">Município</span><span class="info-val">{{ $evento->municipio }}</span></div>@endif
        @if($evento->provincia)<div class="info-row"><span class="info-lbl">Província</span><span class="info-val">{{ $evento->provincia }}</span></div>@endif
        @if($evento->lotacao_maxima)<div class="info-row"><span class="info-lbl">Lotação</span><span class="info-val">{{ number_format($evento->lotacao_maxima,0,',','.') }} pessoas</span></div>@endif
        <div class="info-row"><span class="info-lbl">Formato</span><span class="info-val">{{ $evento->online ? '🌐 Online' : '📍 Presencial' }}</span></div>
        @if($evento->categoria)<div class="info-row"><span class="info-lbl">Categoria</span><span class="info-val">{{ $evento->categoria->emoji }} {{ $evento->categoria->nome }}</span></div>@endif
        @if($evento->subcategoria)<div class="info-row"><span class="info-lbl">Subcategoria</span><span class="info-val">{{ $evento->subcategoria->nome }}</span></div>@endif
        <a href="{{ route('mensagens.index', ['user_id' => $evento->user_id, 'evento_id' => $evento->id]) }}" class="contactar-btn">💬 Contactar organizador</a>
    </div>
</div>

{{-- DRAWER GALERIA --}}
@if($temFotos)
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this) fecharDrawer('drawer-galeria')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">🖼️ Galeria <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">✕</button></div>
        <div class="gallery-grid">
            @foreach($fotos as $foto)<img src="{{ asset('storage/'.$foto->caminho) }}" alt="Foto" loading="lazy">@endforeach
        </div>
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
    el.style.webkitLineClamp=descAberta?'unset':'3';
    el.style.overflow=descAberta?'visible':'hidden';
    btn.textContent=descAberta?'Ver menos ↑':'Ver mais ↓';
}
document.addEventListener('keydown',e=>{
    if(e.key==='Escape'){document.querySelectorAll('.drawer-overlay.open').forEach(d=>d.classList.remove('open'));document.body.style.overflow='';}
});
</script>

@endsection