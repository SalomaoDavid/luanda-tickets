@extends('layouts.app')
@section('title', 'Dashboard — Luanda Tickets')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
:root{
    --bg:#06091a;--s1:#0c1228;--s2:#111830;--s3:#172040;
    --sky:#38bdf8;--sky2:#0ea5e9;--green:#10b981;--amber:#f59e0b;
    --red:#f43f5e;--purple:#a78bfa;--cyan:#06b6d4;
    --b1:rgba(56,189,248,.10);--b2:rgba(56,189,248,.20);--b3:rgba(56,189,248,.38);
    --t1:#ffffff;--t2:#c8d8f0;--t3:#7a90b0;
    --mono:'Space Mono',monospace;--sans:'Outfit',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);font-family:var(--sans);color:var(--t1);min-height:100vh;}

.adm-bg{position:fixed;inset:0;z-index:0;pointer-events:none;
    background:radial-gradient(ellipse at 15% 20%,rgba(6,182,212,.08),transparent 45%),
               radial-gradient(ellipse at 85% 70%,rgba(167,139,250,.06),transparent 40%);}
.adm-grid{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.022;
    background-image:linear-gradient(rgba(56,189,248,1) 1px,transparent 1px),linear-gradient(90deg,rgba(56,189,248,1) 1px,transparent 1px);
    background-size:40px 40px;}

.adm-wrap{position:relative;z-index:1;width:100%;padding:16px 8px 80px;}
@@media(min-width:768px){.adm-wrap{padding:28px 20px 80px;}}

/* ── TOPBAR ── */
.adm-topbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.adm-brand-dot{width:8px;height:8px;border-radius:50%;background:var(--sky);box-shadow:0 0 10px var(--sky);animation:pulse 2s infinite;margin-right:10px;}
@keyframes pulse{0%,100%{box-shadow:0 0 6px var(--sky)}50%{box-shadow:0 0 18px var(--sky)}}
.adm-brand-title{font-family:var(--mono);font-size:13px;font-weight:700;color:var(--sky);letter-spacing:.1em;}
.adm-brand-sub{font-family:var(--mono);font-size:11px;color:var(--t2);margin-top:2px;letter-spacing:.06em;}
.adm-topbar-right{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.adm-time{font-family:var(--mono);font-size:12px;color:var(--t2);letter-spacing:.04em;}
.adm-pill{
    display:inline-flex;align-items:center;gap:5px;
    padding:5px 11px;border-radius:20px;
    background:var(--b1);border:1px solid var(--b2);
    font-family:var(--mono);font-size:10px;color:var(--t2);
    text-decoration:none;transition:all .2s;cursor:pointer;white-space:nowrap;
}
.adm-pill:hover{border-color:var(--b3);color:var(--sky);}
.adm-pill.danger{border-color:rgba(244,63,94,.3);color:var(--red);background:rgba(244,63,94,.08);}
.adm-pill.danger .dot{width:5px;height:5px;border-radius:50%;background:var(--red);animation:pulse-r 1s infinite;}
@keyframes pulse-r{0%,100%{opacity:1}50%{opacity:.3}}
.adm-pill.accent{border-color:var(--b3);color:var(--sky);background:rgba(56,189,248,.08);}

/* ── ANÁLISE BTN ── */
.analise-btn{
    display:inline-flex;align-items:center;gap:7px;
    padding:8px 16px;border-radius:10px;
    background:linear-gradient(135deg,rgba(167,139,250,.15),rgba(124,58,237,.1));
    border:1px solid rgba(167,139,250,.3);
    color:var(--purple);font-family:var(--mono);font-size:10px;font-weight:700;
    text-decoration:none;transition:all .2s;letter-spacing:.05em;
}
.analise-btn:hover{background:rgba(167,139,250,.2);border-color:rgba(167,139,250,.5);}

/* ── SECTION LABEL ── */
.sec-label{font-family:var(--mono);font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--t2);margin-bottom:10px;}

/* ── KPI GRID ── */
.kpi-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:20px;}
@@media(min-width:640px){.kpi-grid{grid-template-columns:repeat(4,1fr);gap:14px;}}
.kpi-card{
    position:relative;background:var(--s1);border:1px solid var(--b2);
    border-radius:16px;padding:16px;overflow:hidden;
    transition:transform .2s,border-color .2s;
}
.kpi-card:hover{transform:translateY(-2px);border-color:var(--b3);}
.kpi-glow{position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:var(--accent-color,var(--sky));opacity:.08;filter:blur(20px);}
.kpi-icon{font-size:20px;margin-bottom:8px;}
.kpi-label{font-size:12px;font-weight:700;color:var(--t2);letter-spacing:.04em;text-transform:uppercase;margin-bottom:4px;}
.kpi-value{font-family:var(--mono);font-size:22px;font-weight:700;color:var(--t1);line-height:1;margin-bottom:3px;}
.kpi-value.accent{color:var(--accent-color,var(--sky));}
.kpi-sub{font-size:12px;color:var(--t2);}
.kpi-delta{font-family:var(--mono);font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;margin-bottom:6px;display:inline-block;}
.kpi-delta.up{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.2);}
.kpi-delta.warn{background:rgba(245,158,11,.12);color:var(--amber);border:1px solid rgba(245,158,11,.2);}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.kpi-card{animation:fadeUp .4s ease both;}
.kpi-card:nth-child(1){animation-delay:.05s;}
.kpi-card:nth-child(2){animation-delay:.10s;}
.kpi-card:nth-child(3){animation-delay:.15s;}
.kpi-card:nth-child(4){animation-delay:.20s;}

/* ── SEGURANÇA KPIs ── */
.sec-kpi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px;}
@@media(min-width:768px){.sec-kpi-grid{grid-template-columns:repeat(6,1fr);}}
.sec-kpi{
    background:var(--s1);border:1px solid var(--b1);border-radius:12px;
    padding:12px;text-align:center;
}
.sec-kpi-val{font-family:var(--mono);font-size:18px;font-weight:700;color:var(--t1);margin-bottom:3px;}
.sec-kpi-lbl{font-size:11px;color:var(--t2);letter-spacing:.04em;text-transform:uppercase;}
.sec-kpi.ok .sec-kpi-val{color:var(--green);}
.sec-kpi.warn .sec-kpi-val{color:var(--amber);}
.sec-kpi.danger .sec-kpi-val{color:var(--red);}

/* ── LAYOUT COLS ── */
.adm-cols{display:grid;grid-template-columns:1fr;gap:14px;}
@@media(min-width:1024px){.adm-cols{grid-template-columns:1fr 320px;gap:18px;}}

/* ── PANEL ── */
.adm-panel{background:var(--s1);border:1px solid var(--b2);border-radius:16px;overflow:hidden;margin-bottom:14px;}
.adm-panel:last-child{margin-bottom:0;}
.adm-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.adm-panel-title{display:flex;align-items:center;gap:8px;font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t1);letter-spacing:.06em;text-transform:uppercase;}
.adm-panel-title-dot{width:6px;height:6px;border-radius:50%;background:var(--sky);box-shadow:0 0 6px var(--sky);}
.adm-panel-body{padding:16px 18px;}

/* ── PERFORMANCE ── */
.perf-grid{display:grid;grid-template-columns:1fr;gap:10px;}
@@media(min-width:640px){.perf-grid{grid-template-columns:repeat(2,1fr);}}
.perf-card{background:var(--s2);border:1px solid var(--b1);border-radius:12px;padding:14px;}
.perf-card-name{font-size:12px;font-weight:700;color:var(--t1);margin-bottom:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.perf-row{margin-bottom:8px;}
.perf-row-top{display:flex;justify-content:space-between;margin-bottom:4px;}
.perf-row-label{font-family:var(--mono);font-size:11px;color:var(--t2);}
.perf-row-val{font-family:var(--mono);font-size:9px;font-weight:700;}
.perf-bar{height:3px;border-radius:999px;background:var(--b1);}
.perf-bar-fill{height:100%;border-radius:999px;transition:width .8s;}
.perf-total{display:flex;justify-content:space-between;padding-top:8px;border-top:1px solid var(--b1);}
.perf-total-label{font-size:12px;color:var(--t2);}
.perf-total-val{font-family:var(--mono);font-size:11px;font-weight:700;color:var(--green);}

/* ── TABS ── */
.adm-tabs{display:flex;gap:6px;margin-bottom:14px;border-bottom:1px solid var(--b1);padding-bottom:10px;}
.adm-tab{padding:6px 14px;border-radius:8px;background:transparent;border:1px solid transparent;font-size:12px;font-weight:600;color:var(--t3);cursor:pointer;font-family:var(--sans);transition:all .15s;}
.adm-tab:hover{color:var(--t2);}
.adm-tab.active{background:var(--b1);border-color:var(--b2);color:var(--sky);}
.adm-tab-panel{display:none;}
.adm-tab-panel.active{display:block;}

/* ── TABLE ── */
.adm-table{width:100%;border-collapse:collapse;font-size:12px;}
.adm-table th{font-family:var(--mono);font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);padding:8px 12px;text-align:left;border-bottom:1px solid var(--b1);}
.adm-table td{padding:10px 12px;border-bottom:1px solid var(--b1);vertical-align:middle;font-size:13px;color:var(--t1);}
.adm-table tr:last-child td{border-bottom:none;}
.adm-table tr:hover td{background:var(--b1);}
.td-name{font-weight:600;color:var(--t1);}
.td-event{font-size:11px;color:var(--t2);}
.td-val{font-family:var(--mono);font-size:11px;color:var(--green);text-align:right;}
.td-date{font-family:var(--mono);font-size:10px;color:var(--t3);}
.td-acts{display:flex;gap:5px;}
.tbl-btn{width:28px;height:28px;border-radius:7px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.tbl-btn:hover{border-color:var(--b3);color:var(--sky);}
.tbl-btn.danger:hover{background:rgba(244,63,94,.1);border-color:rgba(244,63,94,.3);color:var(--red);}

/* ── QUICK LINKS ── */
.qlink-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;}
.qlink{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:14px 10px;border-radius:12px;background:var(--s2);border:1px solid var(--b1);text-decoration:none;transition:all .2s;}
.qlink:hover{border-color:var(--b2);background:var(--s3);}
.qlink-icon{font-size:20px;}
.qlink-label{font-size:12px;font-weight:700;color:var(--t1);text-align:center;letter-spacing:.02em;}

/* ── EVENTOS STATUS ── */
.ev-status-list{display:flex;flex-direction:column;gap:8px;}
.ev-status-row{display:flex;align-items:center;gap:10px;}
.ev-status-lbl{font-size:12px;color:var(--t1);width:90px;flex-shrink:0;}
.ev-status-bar-wrap{flex:1;height:6px;border-radius:999px;background:var(--b1);overflow:hidden;}
.ev-status-bar{height:100%;border-radius:999px;}
.ev-status-count{font-family:var(--mono);font-size:10px;color:var(--t3);width:24px;text-align:right;flex-shrink:0;}

/* ── MODAL COMPROVATIVO ── */
.modal-overlay{position:fixed;inset:0;z-index:900;background:rgba(4,7,15,.9);backdrop-filter:blur(10px);display:flex;align-items:center;justify-content:center;padding:20px;}
.modal-box{background:var(--s1);border:1px solid var(--b3);border-radius:20px;padding:24px;max-width:480px;width:100%;}
.modal-box img{width:100%;border-radius:12px;margin-bottom:16px;max-height:300px;object-fit:contain;background:var(--s2);}
.modal-close{width:32px;height:32px;border-radius:8px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.modal-close:hover{background:rgba(244,63,94,.12);color:var(--red);}
</style>

<div class="adm-bg"></div>
<div class="adm-grid"></div>

<div class="adm-wrap" x-data="{ showModal:false, imgUrl:'', clienteNome:'', whatsapp:'' }">

    {{-- MODAL COMPROVATIVO --}}
    <div class="modal-overlay" x-show="showModal" x-cloak x-on:click.self="showModal=false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="modal-box" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <div style="font-family:var(--mono);font-size:11px;color:var(--t3);margin-bottom:2px;">COMPROVATIVO</div>
                    <div style="font-size:14px;font-weight:700;color:var(--t1);" x-text="clienteNome"></div>
                    <div style="font-family:var(--mono);font-size:11px;color:var(--t3);" x-text="whatsapp"></div>
                </div>
                <button class="modal-close" x-on:click="showModal=false">✕</button>
            </div>
            <img :src="imgUrl" alt="Comprovativo" onerror="this.style.display='none'">
            <a :href="imgUrl" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;">
                🔗 Abrir em novo separador
            </a>
        </div>
    </div>

    {{-- TOPBAR --}}
    <div class="adm-topbar">
        <div style="display:flex;align-items:center;">
            <div class="adm-brand-dot"></div>
            <div>
                <div class="adm-brand-title">LT // COMMAND CENTER</div>
                <div class="adm-brand-sub">{{ auth()->user()->role === 'admin' ? 'SUPER ADMIN' : 'CRIADOR' }} · {{ strtoupper(auth()->user()->name) }}</div>
            </div>
        </div>
        <div class="adm-topbar-right">
            <div class="adm-time" id="adm-clock">--:--:--</div>
            @if(($pendentesCount ?? 0) > 0)
            <a href="{{ route('admin.reservas') }}" class="adm-pill danger">
                <span class="dot"></span>{{ $pendentesCount }} PENDENTES
            </a>
            @endif
            <a href="{{ route('admin.eventos.criar') }}" class="adm-pill">+ EVENTO</a>
            @if($isAdmin)
            <a href="{{ route('admin.usuarios.index') }}" class="adm-pill">👥 USERS</a>
            @endif
            <a href="{{ route('admin.analises') }}" class="analise-btn">🔬 Análise do Sistema</a>
        </div>
    </div>

    {{-- KPIs FINANCEIROS --}}
    <div class="sec-label">métricas financeiras</div>
    <div class="kpi-grid" style="margin-bottom:20px;">
        <div class="kpi-card" style="--accent-color:var(--sky)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">💰</div>
            <div class="kpi-label">Receita Total</div>
            <div class="kpi-value">{{ number_format(($receitaTotal ?? 0)/1000,0,',','.') }}k</div>
            <div class="kpi-sub">Kz · todas as vendas</div>
        </div>
        <div class="kpi-card" style="--accent-color:var(--green)">
            <div class="kpi-glow"></div>
            @if(($pendentesCount ?? 0) > 0)
            <div class="kpi-delta warn">⚠ {{ $pendentesCount }} pendentes</div>
            @else
            <div class="kpi-delta up">✓ Em dia</div>
            @endif
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">Lucro LT</div>
            <div class="kpi-value accent">{{ number_format(($receitaTotal ?? 0)*.10/1000,0,',','.') }}k</div>
            <div class="kpi-sub">Kz · taxa 10%</div>
        </div>
        <div class="kpi-card" style="--accent-color:var(--amber)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">🏦</div>
            <div class="kpi-label">Repasse</div>
            <div class="kpi-value">{{ number_format(($receitaTotal ?? 0)*.90/1000,0,',','.') }}k</div>
            <div class="kpi-sub">Kz · líquido organizadores</div>
        </div>
        <div class="kpi-card" style="--accent-color:var(--purple)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">🎟</div>
            <div class="kpi-label">Eventos Activos</div>
            <div class="kpi-value">{{ $eventosAtivos ?? 0 }}</div>
            <div class="kpi-sub">publicados e em curso</div>
        </div>
    </div>

    {{-- KPIs SEGURANÇA --}}
    <div class="sec-label">segurança de bilhetes</div>
    <div class="sec-kpi-grid" style="margin-bottom:20px;">
        <div class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $totalBilhetes ?? 0 }}</div>
            <div class="sec-kpi-lbl">Total Bilhetes</div>
        </div>
        <div class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $bilhetesComHmac ?? 0 }}</div>
            <div class="sec-kpi-lbl">Com HMAC</div>
        </div>
        <div class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $bilhetesValidados ?? 0 }}</div>
            <div class="sec-kpi-lbl">Validados</div>
        </div>
        <div class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $lotesEmitidos ?? 0 }}</div>
            <div class="sec-kpi-lbl">Lotes</div>
        </div>
        <div class="sec-kpi {{ ($bilhetesBloqueados ?? 0) > 0 ? 'danger' : 'ok' }}">
            <div class="sec-kpi-val">{{ $bilhetesBloqueados ?? 0 }}</div>
            <div class="sec-kpi-lbl">Bloqueados</div>
        </div>
        <div class="sec-kpi {{ ($tentativasInvalidas ?? 0) > 0 ? 'warn' : 'ok' }}">
            <div class="sec-kpi-val">{{ $tentativasInvalidas ?? 0 }}</div>
            <div class="sec-kpi-lbl">Tentativas</div>
        </div>
    </div>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="adm-cols">
        <div>
            {{-- PERFORMANCE POR EVENTO --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title">
                        <div class="adm-panel-title-dot"></div>Desempenho por Evento
                    </div>
                    <span style="font-family:var(--mono);font-size:9px;color:var(--t3);">{{ collect($eventosPerformance??[])->count() }} EVENTOS</span>
                </div>
                <div class="adm-panel-body">
                    @if(collect($eventosPerformance??[])->isEmpty())
                    <div style="text-align:center;padding:30px 0;color:var(--t3);font-size:13px;">Nenhum evento com vendas ainda.</div>
                    @else
                    <div class="perf-grid">
                        @foreach($eventosPerformance??[] as $perf)
                        <div class="perf-card">
                            <div class="perf-card-name" title="{{ $perf->titulo }}">{{ $perf->titulo }}</div>
                            <div class="perf-row">
                                <div class="perf-row-top">
                                    <span class="perf-row-label">NORMAL · {{ $perf->qtd_normal }}</span>
                                    <span class="perf-row-val" style="color:var(--sky)">{{ number_format($perf->total_normal/1000,1,',','.') }}k Kz</span>
                                </div>
                                <div class="perf-bar"><div class="perf-bar-fill" style="width:{{ min($perf->perc_vendas,100) }}%;background:var(--sky);"></div></div>
                            </div>
                            <div class="perf-row">
                                <div class="perf-row-top">
                                    <span class="perf-row-label">VIP · {{ $perf->qtd_vip }}</span>
                                    <span class="perf-row-val" style="color:var(--purple)">{{ number_format($perf->total_vip/1000,1,',','.') }}k Kz</span>
                                </div>
                                <div class="perf-bar"><div class="perf-bar-fill" style="width:{{ min($perf->perc_vip_vendas,100) }}%;background:var(--purple);"></div></div>
                            </div>
                            <div class="perf-total">
                                <span class="perf-total-label">Total Arrecadado</span>
                                <span class="perf-total-val">{{ number_format($perf->total_geral/1000,1,',','.') }}k Kz</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- ANÁLISE & VENDAS --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Análise & Registos</div>
                </div>
                <div class="adm-panel-body">
                    <div class="adm-tabs">
                        <button class="adm-tab active" onclick="switchTab('grafico',this)">📊 Gráfico</button>
                        <button class="adm-tab" onclick="switchTab('vendas',this)">📋 Vendas</button>
                    </div>
                    <div class="adm-tab-panel active" id="tab-grafico">
                        <div style="height:240px;"><canvas id="chartReceita"></canvas></div>
                    </div>
                    <div class="adm-tab-panel" id="tab-vendas">
                        <div style="overflow-x:auto;">
                            <table class="adm-table">
                                <thead><tr>
                                    <th>Cliente</th><th>Evento</th>
                                    <th style="text-align:right;">Total</th>
                                    <th>Data</th><th>Ações</th>
                                </tr></thead>
                                <tbody>
                                    @forelse($vendasDetalhadas as $venda)
                                    <tr>
                                        <td><div class="td-name">{{ e($venda->nome_cliente) }}</div></td>
                                        <td><div class="td-event">{{ Str::limit(optional(optional($venda->tipoIngresso)->evento)->titulo??'—',22) }}</div></td>
                                        <td class="td-val">{{ number_format($venda->total,0,',','.') }} Kz</td>
                                        <td><div class="td-date">{{ $venda->updated_at->format('d/m/y H:i') }}</div></td>
                                        <td>
                                            <div class="td-acts">
                                                <button class="tbl-btn"
                                                    x-on:click="showModal=true;imgUrl='{{ asset('storage/'.($venda->comprovativo_path??'')) }}';clienteNome='{{ addslashes(e($venda->nome_cliente)) }}';whatsapp='{{ e($venda->whatsapp??'') }}'">📂</button>
                                                <form action="{{ route('reserva.eliminar',$venda->id) }}" method="POST" onsubmit="return confirm('Apagar?')" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="tbl-btn danger">🗑</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t3);font-family:var(--mono);font-size:11px;">SEM REGISTOS</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA LATERAL --}}
        <div>
            {{-- AÇÕES RÁPIDAS --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Ações Rápidas</div>
                </div>
                <div class="adm-panel-body">
                    <div class="qlink-grid">
                        <a href="{{ route('admin.eventos.criar') }}" class="qlink"><div class="qlink-icon">➕</div><div class="qlink-label">Novo Evento</div></a>
                        <a href="{{ route('admin.reservas') }}" class="qlink"><div class="qlink-icon">📋</div><div class="qlink-label">Reservas</div></a>
                        <a href="{{ route('admin.pagos') }}" class="qlink"><div class="qlink-icon">💳</div><div class="qlink-label">Pagamentos</div></a>
                        <a href="{{ route('admin.scanner') }}" class="qlink"><div class="qlink-icon">📷</div><div class="qlink-label">Scanner</div></a>
                        @if($isAdmin)
                        <a href="{{ route('admin.usuarios.index') }}" class="qlink"><div class="qlink-icon">👥</div><div class="qlink-label">Utilizadores</div></a>
                        @endif
                        <a href="{{ route('admin.analises') }}" class="qlink" style="border-color:rgba(167,139,250,.2);"><div class="qlink-icon">🔬</div><div class="qlink-label" style="color:var(--purple);">Análises</div></a>
                    </div>
                </div>
            </div>

            {{-- EVENTOS POR STATUS --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Eventos por Estado</div>
                </div>
                <div class="adm-panel-body">
                    @php
                    $totalEvs = array_sum($eventosPorStatus->toArray() ?: [1]);
                    $statusCfg = ['publicado'=>['✅','var(--green)'],'rascunho'=>['📝','var(--amber)'],'encerrado'=>['🔒','var(--red)']];
                    @endphp
                    <div class="ev-status-list">
                        @foreach($statusCfg as $st=>[$em,$cor])
                        @php $cnt = $eventosPorStatus->get($st,0); @endphp
                        <div class="ev-status-row">
                            <span class="ev-status-lbl">{{ $em }} {{ ucfirst($st) }}</span>
                            <div class="ev-status-bar-wrap">
                                <div class="ev-status-bar" style="width:{{ $totalEvs > 0 ? ($cnt/$totalEvs)*100 : 0 }}%;background:{{ $cor }};"></div>
                            </div>
                            <span class="ev-status-count">{{ $cnt }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ESTADO DE SEGURANÇA --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot" style="background:var(--green);box-shadow:0 0 6px var(--green);"></div>Segurança</div>
                    <a href="{{ route('admin.analises') }}" style="font-family:var(--mono);font-size:9px;color:var(--purple);text-decoration:none;">VER TUDO →</a>
                </div>
                <div class="adm-panel-body">
                    @php
                    $total = $totalBilhetes ?? 1;
                    $percHmac = $total > 0 ? round((($bilhetesComHmac??0)/$total)*100) : 0;
                    @endphp
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                            <span style="font-size:11px;color:var(--t2);">Integridade HMAC</span>
                            <span style="font-family:var(--mono);font-size:10px;color:var(--green);">{{ $percHmac }}%</span>
                        </div>
                        <div style="height:4px;border-radius:999px;background:var(--b1);overflow:hidden;">
                            <div style="height:100%;width:{{ $percHmac }}%;background:var(--green);border-radius:999px;"></div>
                        </div>
                    </div>
                    @if(($bilhetesBloqueados??0) > 0)
                    <div style="padding:10px 12px;background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:9px;font-size:12px;color:var(--red);">
                        ⚠️ {{ $bilhetesBloqueados }} bilhete(s) bloqueado(s) por adulteração
                    </div>
                    @endif
                    @if(($tentativasInvalidas??0) > 0)
                    <div style="padding:10px 12px;margin-top:8px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:9px;font-size:12px;color:var(--amber);">
                        🔍 {{ $tentativasInvalidas }} tentativa(s) inválida(s) detectada(s)
                    </div>
                    @endif
                    @if(($bilhetesBloqueados??0) === 0 && ($tentativasInvalidas??0) === 0)
                    <div style="padding:10px 12px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:9px;font-size:12px;color:var(--green);">
                        ✅ Sistema seguro — sem incidentes
                    </div>
                    @endif
                </div>
            </div>

            {{-- UTILIZADORES (só admin) --}}
            @if($isAdmin)
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Utilizadores</div>
                </div>
                <div class="adm-panel-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <span style="font-size:12px;color:var(--t2);">Total registados</span>
                        <span style="font-family:var(--mono);font-size:18px;font-weight:700;color:var(--t1);">{{ $totalUtilizadores ?? 0 }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <span style="font-size:12px;color:var(--t2);">Novos (30 dias)</span>
                        <span style="font-family:var(--mono);font-size:16px;font-weight:700;color:var(--green);">+{{ $novosUtilizadores ?? 0 }}</span>
                    </div>
                    <a href="{{ route('admin.usuarios.index') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;border-radius:9px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;transition:all .2s;">
                        👥 Gerir utilizadores →
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Relógio
(function tick(){
    const d=new Date();
    const el=document.getElementById('adm-clock');
    if(el) el.textContent=d.toLocaleTimeString('pt-PT');
    setTimeout(tick,1000);
})();

// Tab switch
function switchTab(id,btn){
    document.querySelectorAll('.adm-tab-panel').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('.adm-tab').forEach(b=>b.classList.remove('active'));
    document.getElementById('tab-'+id).classList.add('active');
    btn.classList.add('active');
}

// Gráfico
const ctx=document.getElementById('chartReceita');
if(ctx){
    new Chart(ctx,{
        type:'bar',
        data:{
            labels:['Normal','VIP'],
            datasets:[{
                label:'Receita (Kz)',
                data:[{{ $valorTotalNormal ?? 0 }},{{ $valorTotalVip ?? 0 }}],
                backgroundColor:['rgba(56,189,248,.5)','rgba(167,139,250,.5)'],
                borderColor:['rgba(56,189,248,1)','rgba(167,139,250,1)'],
                borderWidth:1,borderRadius:6,
            }]
        },
        options:{
            responsive:true,maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:v=>v.raw.toLocaleString('pt-PT')+' Kz'}}},
            scales:{
                x:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#475569',font:{family:'Space Mono',size:10}}},
                y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#475569',font:{family:'Space Mono',size:10},callback:v=>v>=1000?Math.round(v/1000)+'k':v}}
            }
        }
    });
}
</script>
@endsection