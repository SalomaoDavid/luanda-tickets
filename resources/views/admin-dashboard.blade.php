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
@media(min-width:768px){.adm-wrap{padding:28px 20px 80px;}}

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
@media(min-width:640px){.kpi-grid{grid-template-columns:repeat(4,1fr);gap:14px;}}
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
@media(min-width:768px){.sec-kpi-grid{grid-template-columns:repeat(6,1fr);}}
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
@media(min-width:1024px){.adm-cols{grid-template-columns:1fr 320px;gap:18px;}}

/* ── PANEL ── */
.adm-panel{background:var(--s1);border:1px solid var(--b2);border-radius:16px;overflow:hidden;margin-bottom:14px;}
.adm-panel:last-child{margin-bottom:0;}
.adm-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.adm-panel-title{display:flex;align-items:center;gap:8px;font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t1);letter-spacing:.06em;text-transform:uppercase;}
.adm-panel-title-dot{width:6px;height:6px;border-radius:50%;background:var(--sky);box-shadow:0 0 6px var(--sky);}
.adm-panel-body{padding:16px 18px;}

/* ── PERFORMANCE ── */
.perf-grid{display:grid;grid-template-columns:1fr;gap:10px;}
@media(min-width:640px){.perf-grid{grid-template-columns:repeat(2,1fr);}}
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

/* ══════════════════════════════════════════════
   NOVAS MELHORIAS
   ══════════════════════════════════════════════ */
[x-cloak]{display:none!important;}

/* 12: mensagens flash do controller */
/* mensagens de sucesso/erro agora tratadas pelo toast global do app.blade.php — nada a fazer aqui */

/* 6: última atualização + atualizar */
.adm-refresh-row{display:flex;align-items:center;justify-content:space-between;font-size:10.5px;color:var(--t3);margin-bottom:12px;}
.adm-refresh-row .dot{width:6px;height:6px;border-radius:50%;background:var(--green);display:inline-block;margin-right:5px;animation:pulse-r 2s infinite;}
.adm-refresh-btn{font-size:10px;color:var(--sky);background:none;border:1px solid var(--b2);border-radius:8px;padding:4px 10px;cursor:pointer;font-family:var(--mono);}
.adm-refresh-btn:hover{border-color:var(--b3);}
.adm-refresh-btn.loading{opacity:.6;pointer-events:none;}

/* 5: filtro de período */
.period-filters{display:flex;gap:6px;overflow-x:auto;scrollbar-width:none;margin-bottom:16px;}
.period-filters::-webkit-scrollbar{display:none;}
.pfbtn{flex-shrink:0;padding:7px 13px;border-radius:9px;font-size:11px;font-weight:700;border:1px solid var(--b2);background:var(--s2);color:var(--t2);cursor:pointer;text-decoration:none;font-family:var(--sans);}
.pfbtn.active{border-color:var(--sky);color:var(--sky);background:var(--b1);}

/* 8: badge de pendentes reposicionado */
.kpi-delta.warn{cursor:default;}

/* 9: tooltip explicativo (HMAC e outros termos técnicos) */
.info-icon{display:inline-flex;align-items:center;justify-content:center;width:13px;height:13px;border-radius:50%;background:var(--b1);border:1px solid var(--b2);color:var(--sky);font-size:8.5px;cursor:pointer;margin-left:4px;position:relative;vertical-align:middle;}
.info-icon .tip{display:none;position:absolute;bottom:130%;left:50%;transform:translateX(-50%);width:190px;background:#000;color:#fff;font-size:10px;font-weight:400;padding:8px 10px;border-radius:8px;line-height:1.5;z-index:20;box-shadow:0 4px 14px rgba(0,0,0,.5);text-transform:none;letter-spacing:normal;}
.info-icon:hover .tip, .info-icon.open .tip{display:block;}

/* 7: pesquisa na tabela de vendas */
.tbl-search{display:flex;align-items:center;gap:8px;background:var(--s2);border:1px solid var(--b1);border-radius:10px;padding:8px 12px;margin-bottom:10px;}
.tbl-search input{flex:1;background:none;border:none;outline:none;color:var(--t1);font-size:12px;font-family:var(--sans);}
.tbl-empty-filtered{display:none;text-align:center;padding:20px;color:var(--t3);font-size:12px;}
.tbl-empty-filtered.show{display:block;}

/* 10: eliminar com loading + modal em vez de confirm() nativo */
.tbl-btn.loading{opacity:.5;pointer-events:none;}
.tbl-btn .mini-spin{display:none;width:10px;height:10px;border-radius:50%;border:2px solid rgba(244,63,94,.25);border-top-color:var(--red);animation:spin-mini .6s linear infinite;}
.tbl-btn.loading .mini-spin{display:inline-block;}
@keyframes spin-mini{to{transform:rotate(360deg)}}
.del-modal-ov{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:20px;}
.del-modal-ov.open{display:flex;}
.del-modal-box{background:var(--s1);border:1px solid rgba(244,63,94,.4);border-radius:16px;padding:20px;max-width:300px;width:100%;text-align:center;}
.del-modal-actions{display:flex;gap:8px;margin-top:14px;}
.del-mbtn{flex:1;padding:10px;border-radius:10px;font-size:12px;font-weight:700;border:none;cursor:pointer;}
.del-mbtn.cancel{background:var(--s3);color:var(--t2);}
.del-mbtn.confirm{background:var(--red);color:#fff;}

/* 3: aviso se o gráfico (CDN externo) não carregar */
.chart-fallback{display:none;height:240px;border-radius:10px;background:var(--s2);align-items:center;justify-content:center;flex-direction:column;gap:6px;font-size:11px;color:var(--t3);text-align:center;padding:14px;}
.chart-fallback.show{display:flex;}

/* 14: cartões de segurança do topo clicáveis */
a.sec-kpi{display:block;text-decoration:none;transition:border-color .2s,transform .2s;}
a.sec-kpi:hover{border-color:var(--b3);transform:translateY(-2px);}

/* 15: destaque de cor consistente nos atalhos rápidos */
.qlink.c-green{border-color:rgba(16,185,129,.25);} .qlink.c-green .qlink-label{color:var(--green);}
.qlink.c-amber{border-color:rgba(245,158,11,.25);} .qlink.c-amber .qlink-label{color:var(--amber);}
.qlink.c-red{border-color:rgba(244,63,94,.25);} .qlink.c-red .qlink-label{color:var(--red);}
.qlink.c-sky{border-color:rgba(56,189,248,.25);} .qlink.c-sky .qlink-label{color:var(--sky);}
</style>

<div class="adm-bg"></div>
<div class="adm-grid"></div>

<div class="adm-wrap" x-data="{ showModal:false, imgUrl:'', clienteNome:'', whatsapp:'' }">

    {{-- 6: última atualização + recarregar --}}
    <div class="adm-refresh-row">
        <span><span class="dot"></span>Dados carregados às {{ now()->format('H:i') }}</span>
        <button type="button" class="adm-refresh-btn" id="btnRefresh" onclick="recarregarDashboard()">🔄 ATUALIZAR</button>
    </div>

    {{-- 5: filtro de período — afeta as métricas financeiras e o desempenho por evento --}}
    @php $periodoAtual = $periodo ?? 'tudo'; @endphp
    <div class="period-filters">
        <a href="{{ request()->fullUrlWithQuery(['periodo' => 'hoje']) }}" class="pfbtn {{ $periodoAtual === 'hoje' ? 'active' : '' }}">Hoje</a>
        <a href="{{ request()->fullUrlWithQuery(['periodo' => '7dias']) }}" class="pfbtn {{ $periodoAtual === '7dias' ? 'active' : '' }}">7 dias</a>
        <a href="{{ request()->fullUrlWithQuery(['periodo' => '30dias']) }}" class="pfbtn {{ $periodoAtual === '30dias' ? 'active' : '' }}">30 dias</a>
        <a href="{{ request()->fullUrlWithQuery(['periodo' => 'mes']) }}" class="pfbtn {{ $periodoAtual === 'mes' ? 'active' : '' }}">Este mês</a>
        <a href="{{ request()->fullUrlWithQuery(['periodo' => 'tudo']) }}" class="pfbtn {{ $periodoAtual === 'tudo' ? 'active' : '' }}">Tudo</a>
    </div>

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
            {{-- 11: erro ao carregar já não desaparece em silêncio --}}
            <img :src="imgUrl" alt="Comprovativo" id="comprovativoImg" onerror="mostrarErroComprovativo(this)" onload="mostrarErroComprovativo(this, false)">
            <div id="comprovativoErro" style="display:none;text-align:center;padding:30px 0;color:var(--t3);font-size:12px;background:var(--s2);border-radius:12px;margin-bottom:16px;">
                📂 Sem comprovativo ou não foi possível carregar a imagem.
            </div>
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
    @php
        // 4: mostra o valor exato quando < 1000 Kz, em vez de arredondar sempre para "k"
        $fmtKz = function ($valor) {
            $valor = $valor ?? 0;
            return $valor >= 1000
                ? number_format($valor / 1000, 0, ',', '.') . 'k'
                : number_format($valor, 0, ',', '.');
        };
    @endphp
    <div class="sec-label">métricas financeiras</div>
    <div class="kpi-grid" style="margin-bottom:20px;">
        <div class="kpi-card" style="--accent-color:var(--sky)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">💰</div>
            <div class="kpi-label">Receita Total</div>
            <div class="kpi-value">{{ $fmtKz($receitaTotal ?? 0) }}</div>
            @php
                $periodoLbl = ['hoje'=>'hoje','7dias'=>'últimos 7 dias','30dias'=>'últimos 30 dias','mes'=>'este mês','tudo'=>'todas as vendas'][$periodoAtual] ?? 'todas as vendas';
            @endphp
            <div class="kpi-sub">Kz · {{ $periodoLbl }}</div>
        </div>
        <div class="kpi-card" style="--accent-color:var(--green)">
            <div class="kpi-glow"></div>
            <div class="kpi-delta up">✓ Recebido</div>
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">Lucro LT</div>
            <div class="kpi-value accent">{{ $fmtKz(($receitaTotal ?? 0)*.10) }}</div>
            <div class="kpi-sub">Kz · taxa 10%</div>
        </div>
        {{-- 8: aviso de pendentes agora no cartão certo — quem está pendente de receber é o organizador, não a Luanda Tickets --}}
        <div class="kpi-card" style="--accent-color:var(--amber)">
            <div class="kpi-glow"></div>
            @if(($pendentesCount ?? 0) > 0)
            <div class="kpi-delta warn">⚠ {{ $pendentesCount }} pendentes</div>
            @else
            <div class="kpi-delta up">✓ Em dia</div>
            @endif
            <div class="kpi-icon">🏦</div>
            <div class="kpi-label">Repasse</div>
            <div class="kpi-value">{{ $fmtKz(($receitaTotal ?? 0)*.90) }}</div>
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

    {{-- KPIs SEGURANÇA — agora clicáveis, levam à página de Análises --}}
    <div class="sec-label">segurança de bilhetes</div>
    <div class="sec-kpi-grid" style="margin-bottom:20px;">
        <a href="{{ route('admin.analises') }}" class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $totalBilhetes ?? 0 }}</div>
            <div class="sec-kpi-lbl">Total Bilhetes</div>
        </a>
        <a href="{{ route('admin.analises') }}" class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $bilhetesComHmac ?? 0 }}</div>
            <div class="sec-kpi-lbl">
                Com HMAC
                <span class="info-icon" onclick="event.preventDefault(); this.classList.toggle('open')">?
                    <span class="tip">HMAC é uma assinatura digital que impede que os bilhetes sejam falsificados ou alterados depois de emitidos.</span>
                </span>
            </div>
        </a>
        <a href="{{ route('admin.analises') }}" class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $bilhetesValidados ?? 0 }}</div>
            <div class="sec-kpi-lbl">Validados</div>
        </a>
        <a href="{{ route('admin.analises') }}" class="sec-kpi ok">
            <div class="sec-kpi-val">{{ $lotesEmitidos ?? 0 }}</div>
            <div class="sec-kpi-lbl">Lotes</div>
        </a>
        <a href="{{ route('admin.analises') }}" class="sec-kpi {{ ($bilhetesBloqueados ?? 0) > 0 ? 'danger' : 'ok' }}">
            <div class="sec-kpi-val">{{ $bilhetesBloqueados ?? 0 }}</div>
            <div class="sec-kpi-lbl">Bloqueados</div>
        </a>
        <a href="{{ route('admin.analises') }}" class="sec-kpi {{ ($tentativasInvalidas ?? 0) > 0 ? 'warn' : 'ok' }}">
            <div class="sec-kpi-val">{{ $tentativasInvalidas ?? 0 }}</div>
            <div class="sec-kpi-lbl">Tentativas</div>
        </a>
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
                        <div style="height:240px;" id="chartWrap"><canvas id="chartReceita"></canvas></div>
                        <div class="chart-fallback" id="chartFallback">
                            📡 Gráfico indisponível<br>
                            <span style="font-size:10px;">A biblioteca de gráficos não carregou (provavelmente sem internet). Os números continuam disponíveis na aba "📋 Vendas".</span>
                        </div>
                    </div>
                    <div class="adm-tab-panel" id="tab-vendas">
                        {{-- 7: pesquisa por cliente (filtra a página atual carregada) --}}
                        <div class="tbl-search">
                            🔍 <input type="text" id="vendasSearch" placeholder="Buscar por cliente..." oninput="filtrarVendas()">
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="adm-table" id="vendasTable">
                                <thead><tr>
                                    <th>Cliente</th><th>Evento</th>
                                    <th style="text-align:right;">Total</th>
                                    <th>Data</th><th>Ações</th>
                                </tr></thead>
                                <tbody>
                                    @forelse($vendasDetalhadas as $venda)
                                    <tr data-cliente="{{ strtolower($venda->nome_cliente) }}">
                                        <td><div class="td-name">{{ e($venda->nome_cliente) }}</div></td>
                                        <td><div class="td-event">{{ Str::limit(optional(optional($venda->tipoIngresso)->evento)->titulo??'—',22) }}</div></td>
                                        <td class="td-val">{{ number_format($venda->total,0,',','.') }} Kz</td>
                                        <td><div class="td-date">{{ $venda->updated_at->format('d/m/y H:i') }}</div></td>
                                        <td>
                                            <div class="td-acts">
                                                <button class="tbl-btn"
                                                    x-on:click="showModal=true;imgUrl='{{ asset('storage/'.($venda->comprovativo_path??'')) }}';clienteNome='{{ addslashes(e($venda->nome_cliente)) }}';whatsapp='{{ e($venda->whatsapp??'') }}'">📂</button>
                                                {{-- 2, 10: modal com nome do cliente + loading em vez de confirm() nativo --}}
                                                <form action="{{ route('reserva.eliminar',$venda->id) }}" method="POST" id="form-del-{{ $venda->id }}" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="tbl-btn danger" id="btn-del-{{ $venda->id }}"
                                                        onclick="confirmarEliminarReserva({{ $venda->id }}, '{{ addslashes(e($venda->nome_cliente)) }}', '{{ number_format($venda->total,0,',','.') }} Kz')">
                                                        🗑<span class="mini-spin"></span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t3);font-family:var(--mono);font-size:11px;">SEM REGISTOS</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="tbl-empty-filtered" id="vendasEmptyFiltered">🔍 Nenhum cliente encontrado nesta página.</div>
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
                        <a href="{{ route('admin.eventos.criar') }}" class="qlink c-sky"><div class="qlink-icon">➕</div><div class="qlink-label">Novo Evento</div></a>
                        <a href="{{ route('admin.reservas') }}" class="qlink c-green"><div class="qlink-icon">📋</div><div class="qlink-label">Reservas</div></a>
                        <a href="{{ route('admin.pagos') }}" class="qlink c-amber"><div class="qlink-icon">💳</div><div class="qlink-label">Pagamentos</div></a>
                        <a href="{{ route('admin.scanner') }}" class="qlink c-red"><div class="qlink-icon">📷</div><div class="qlink-label">Scanner</div></a>
                        @if($isAdmin)
                        <a href="{{ route('admin.usuarios.index') }}" class="qlink c-sky"><div class="qlink-icon">👥</div><div class="qlink-label">Utilizadores</div></a>
                        @endif
                        <a href="{{ route('admin.analises') }}" class="qlink" style="border-color:rgba(167,139,250,.2);"><div class="qlink-icon">🔬</div><div class="qlink-label" style="color:var(--purple);">Análises</div></a>
                        <a href="{{ route('admin.saldos') }}" class="qlink c-amber"><div class="qlink-icon">💰</div><div class="qlink-label">Saldos</div></a>
                        <a href="{{ route('admin.contas-bancarias') }}" class="qlink c-sky"><div class="qlink-icon">🏦</div><div class="qlink-label">Contas Bancárias</div></a>
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

{{-- 2, 10: modal de confirmação personalizado para eliminar reserva --}}
<div class="del-modal-ov" id="delModalOv" onclick="if(event.target===this) fecharModalDelReserva()">
    <div class="del-modal-box">
        <div style="font-size:26px;margin-bottom:8px;">🗑️</div>
        <div style="font-size:14px;font-weight:800;margin-bottom:6px;">Eliminar esta reserva?</div>
        <div style="font-size:12px;color:var(--t2);line-height:1.5;" id="delModalText"></div>
        <div class="del-modal-actions">
            <button type="button" class="del-mbtn cancel" onclick="fecharModalDelReserva()">Cancelar</button>
            <button type="button" class="del-mbtn confirm" id="delModalConfirmBtn">Eliminar</button>
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

// 3: Gráfico — com aviso claro se o Chart.js (CDN) não tiver carregado
if (typeof Chart === 'undefined') {
    const wrap = document.getElementById('chartWrap');
    const fallback = document.getElementById('chartFallback');
    if (wrap) wrap.style.display = 'none';
    if (fallback) fallback.classList.add('show');
} else {
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
}

// 6: recarregar dashboard (não há endpoint de dados ao vivo — recarrega a página inteira)
function recarregarDashboard() {
    const btn = document.getElementById('btnRefresh');
    btn.classList.add('loading');
    btn.textContent = '⏳ A ATUALIZAR...';
    location.reload();
}

// 7: pesquisa na tabela de vendas (filtra a página atual carregada)
function filtrarVendas() {
    const q = document.getElementById('vendasSearch').value.toLowerCase();
    let visiveis = 0;
    document.querySelectorAll('#vendasTable tbody tr').forEach(tr => {
        if (!tr.dataset.cliente) return; // ignora a linha "SEM REGISTOS"
        const visivel = tr.dataset.cliente.includes(q);
        tr.style.display = visivel ? '' : 'none';
        if (visivel) visiveis++;
    });
    document.getElementById('vendasEmptyFiltered').classList.toggle('show', visiveis === 0 && q.length > 0);
}

// 2, 10: eliminar reserva com nome do cliente + loading, em vez de confirm() nativo
function confirmarEliminarReserva(id, nomeCliente, valor) {
    document.getElementById('delModalText').innerHTML =
        `A reserva de <b>${nomeCliente}</b> (${valor}) será apagada permanentemente.`;
    const confirmBtn = document.getElementById('delModalConfirmBtn');
    confirmBtn.onclick = function () {
        fecharModalDelReserva();
        const btn = document.getElementById('btn-del-' + id);
        btn.classList.add('loading');
        document.getElementById('form-del-' + id).submit();
    };
    document.getElementById('delModalOv').classList.add('open');
}
function fecharModalDelReserva() {
    document.getElementById('delModalOv').classList.remove('open');
}

// 11: comprovativo — mostra aviso claro em vez de desaparecer em silêncio
function mostrarErroComprovativo(img, comErro = true) {
    document.getElementById('comprovativoErro').style.display = comErro ? 'block' : 'none';
    img.style.display = comErro ? 'none' : 'block';
}
</script>
@endsection