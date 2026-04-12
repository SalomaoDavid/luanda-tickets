@extends('layouts.app')

@section('title', 'Centro de Comando — Luanda Tickets')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
:root {
    --bg:       #04070f;
    --s1:       #080d1a;
    --s2:       #0d1526;
    --s3:       #111e33;
    --s4:       #172340;
    --sky:      #38bdf8;
    --sky2:     #0ea5e9;
    --cyan:     #06b6d4;
    --green:    #10b981;
    --amber:    #f59e0b;
    --red:      #f43f5e;
    --purple:   #a78bfa;
    --b1:       rgba(56,189,248,0.07);
    --b2:       rgba(56,189,248,0.14);
    --b3:       rgba(56,189,248,0.28);
    --b4:       rgba(56,189,248,0.45);
    --t1:       #f0f6ff;
    --t2:       #94a3b8;
    --t3:       #475569;
    --mono:     'Space Mono', monospace;
    --sans:     'Outfit', sans-serif;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); font-family: var(--sans); color: var(--t1); min-height: 100vh; }

/* ── SCANLINES background ── */
.adm-bg {
    position: fixed; inset: 0; z-index: 0; pointer-events: none;
    background:
        radial-gradient(ellipse at 15% 20%, rgba(6,182,212,.08) 0%, transparent 45%),
        radial-gradient(ellipse at 85% 70%, rgba(167,139,250,.06) 0%, transparent 40%),
        radial-gradient(ellipse at 50% 100%, rgba(56,189,248,.05) 0%, transparent 50%);
}
.adm-grid {
    position: fixed; inset: 0; z-index: 0; pointer-events: none; opacity: .025;
    background-image:
        linear-gradient(rgba(56,189,248,1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(56,189,248,1) 1px, transparent 1px);
    background-size: 40px 40px;
}

.adm-wrap { position: relative; z-index: 1; max-width: 1400px; margin: 0 auto; padding: 24px 16px 80px; }
@media(min-width:768px) { .adm-wrap { padding: 32px 28px 80px; } }

/* ── TOPBAR ── */
.adm-topbar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; margin-bottom: 32px; flex-wrap: wrap;
}
.adm-brand { display: flex; align-items: center; gap: 12px; }
.adm-brand-dot {
    width: 8px; height: 8px; border-radius: 50%; background: var(--green);
    box-shadow: 0 0 10px var(--green);
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.4)} }
.adm-brand-title { font-family: var(--mono); font-size: 13px; font-weight: 700; color: var(--t1); letter-spacing: .05em; }
.adm-brand-sub { font-family: var(--mono); font-size: 10px; color: var(--t3); letter-spacing: .12em; margin-top: 2px; }

.adm-topbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.adm-time {
    font-family: var(--mono); font-size: 11px; color: var(--sky);
    background: var(--b1); border: 1px solid var(--b2);
    padding: 6px 12px; border-radius: 8px; letter-spacing: .08em;
}
.adm-pill {
    display: flex; align-items: center; gap: 6px;
    font-family: var(--mono); font-size: 10px; font-weight: 700;
    padding: 6px 12px; border-radius: 8px;
    background: var(--b1); border: 1px solid var(--b2); color: var(--t2);
    text-decoration: none; transition: all .2s;
}
.adm-pill:hover { border-color: var(--b3); color: var(--sky); }
.adm-pill.danger { background: rgba(244,63,94,.08); border-color: rgba(244,63,94,.25); color: var(--red); }
.adm-pill.danger:hover { background: rgba(244,63,94,.15); }
.adm-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; animation: pulse-dot 1.5s infinite; }

/* ── SECTION LABEL ── */
.sec-label {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--mono); font-size: 10px; font-weight: 700;
    color: var(--t3); letter-spacing: .18em; text-transform: uppercase;
    margin-bottom: 14px;
}
.sec-label::before { content:''; width: 16px; height: 1px; background: var(--b3); }
.sec-label::after  { content:''; flex: 1; height: 1px; background: var(--b2); }

/* ── KPI CARDS ── */
.kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 28px; }
@media(min-width:640px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; } }
@media(min-width:1024px) { .kpi-grid { grid-template-columns: repeat(4, 1fr); } }

.kpi-card {
    background: var(--s1); border: 1px solid var(--b2);
    border-radius: 16px; padding: 18px 16px;
    position: relative; overflow: hidden;
    transition: border-color .2s, transform .2s;
}
@media(min-width:768px) { .kpi-card { padding: 22px 20px; border-radius: 20px; } }
.kpi-card:hover { border-color: var(--b3); transform: translateY(-2px); }
.kpi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: var(--accent-color, var(--sky));
    opacity: .7;
}
.kpi-glow {
    position: absolute; top: -20px; right: -20px;
    width: 80px; height: 80px; border-radius: 50%;
    background: var(--accent-color, var(--sky));
    opacity: .04; filter: blur(20px); pointer-events: none;
}
.kpi-icon { font-size: 22px; margin-bottom: 10px; }
.kpi-label { font-family: var(--mono); font-size: 9px; font-weight: 700; letter-spacing: .15em; color: var(--t3); text-transform: uppercase; margin-bottom: 6px; }
.kpi-value { font-family: var(--mono); font-size: 20px; font-weight: 700; color: var(--t1); line-height: 1; }
@media(min-width:768px) { .kpi-value { font-size: 26px; } }
.kpi-value.accent { color: var(--accent-color, var(--sky)); }
.kpi-sub { font-size: 10px; color: var(--t3); margin-top: 5px; }
.kpi-delta {
    position: absolute; top: 16px; right: 16px;
    font-family: var(--mono); font-size: 9px; font-weight: 700;
    padding: 3px 7px; border-radius: 6px;
}
.kpi-delta.up   { background: rgba(16,185,129,.15); color: var(--green); border: 1px solid rgba(16,185,129,.25); }
.kpi-delta.warn { background: rgba(244,63,94,.12);  color: var(--red);   border: 1px solid rgba(244,63,94,.2); }

/* ── LAYOUT 2 COLUNAS ── */
.adm-cols { display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px; }
@media(min-width:1024px) { .adm-cols { flex-direction: row; gap: 20px; } }
.adm-col-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 16px; }
.adm-col-side  { width: 100%; display: flex; flex-direction: column; gap: 16px; }
@media(min-width:1024px) { .adm-col-side { width: 340px; flex-shrink: 0; } }

/* ── PANEL ── */
.adm-panel {
    background: var(--s1); border: 1px solid var(--b2);
    border-radius: 16px; overflow: hidden;
}
@media(min-width:768px) { .adm-panel { border-radius: 20px; } }
.adm-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 18px; border-bottom: 1px solid var(--b1);
    gap: 10px; flex-wrap: wrap;
}
@media(min-width:768px) { .adm-panel-head { padding: 18px 22px; } }
.adm-panel-title {
    font-family: var(--mono); font-size: 11px; font-weight: 700;
    color: var(--sky); letter-spacing: .12em; text-transform: uppercase;
    display: flex; align-items: center; gap: 8px;
}
.adm-panel-title-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--sky); }
.adm-panel-body { padding: 18px; }
@media(min-width:768px) { .adm-panel-body { padding: 22px; } }

/* ── CHART ── */
.chart-wrap { position: relative; width: 100%; }

/* ── PERFORMANCE CARDS ── */
.perf-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
@media(min-width:640px) { .perf-grid { grid-template-columns: repeat(2, 1fr); } }
@media(min-width:1200px) { .perf-grid { grid-template-columns: repeat(3, 1fr); } }

.perf-card {
    background: var(--s2); border: 1px solid var(--b1);
    border-radius: 14px; padding: 16px;
    transition: border-color .2s;
}
.perf-card:hover { border-color: var(--b3); }
.perf-card-name {
    font-size: 12px; font-weight: 700; color: var(--t1);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid var(--b1);
}
.perf-row { margin-bottom: 10px; }
.perf-row-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.perf-row-label { font-family: var(--mono); font-size: 9px; font-weight: 700; color: var(--t3); letter-spacing: .1em; }
.perf-row-val   { font-family: var(--mono); font-size: 10px; font-weight: 700; }
.perf-bar { height: 3px; background: var(--b2); border-radius: 2px; overflow: hidden; }
.perf-bar-fill { height: 100%; border-radius: 2px; transition: width 1.2s cubic-bezier(.4,0,.2,1); }
.perf-total {
    display: flex; justify-content: space-between; align-items: center;
    padding-top: 10px; margin-top: 4px; border-top: 1px solid var(--b1);
}
.perf-total-label { font-family: var(--mono); font-size: 8px; font-weight: 700; color: var(--t3); letter-spacing: .12em; text-transform: uppercase; }
.perf-total-val   { font-family: var(--mono); font-size: 13px; font-weight: 700; color: var(--t1); }

/* ── TABELA VENDAS ── */
.adm-table { width: 100%; border-collapse: collapse; }
.adm-table th {
    font-family: var(--mono); font-size: 9px; font-weight: 700;
    color: var(--t3); text-transform: uppercase; letter-spacing: .12em;
    padding: 0 12px 12px; text-align: left; border-bottom: 1px solid var(--b2);
}
.adm-table th:last-child { text-align: center; }
.adm-table td { padding: 12px; border-bottom: 1px solid var(--b1); vertical-align: middle; }
.adm-table tr:last-child td { border-bottom: none; }
.adm-table tr:hover td { background: var(--b1); }
.td-name  { font-size: 13px; font-weight: 700; color: var(--t1); }
.td-event { font-size: 11px; color: var(--t2); font-weight: 500; }
.td-val   { font-family: var(--mono); font-size: 12px; font-weight: 700; color: var(--sky); text-align: right; }
.td-date  { font-family: var(--mono); font-size: 10px; color: var(--t3); }
.td-acts  { display: flex; align-items: center; justify-content: center; gap: 6px; }

.tbl-btn {
    width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--b2);
    background: var(--b1); display: flex; align-items: center; justify-content: center;
    font-size: 13px; cursor: pointer; transition: all .2s;
}
.tbl-btn:hover { border-color: var(--b3); }
.tbl-btn.danger:hover { background: rgba(244,63,94,.1); border-color: rgba(244,63,94,.3); }

/* ── QUICK LINKS ── */
.qlink-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
.qlink {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 6px; padding: 16px 10px; border-radius: 12px;
    background: var(--s2); border: 1px solid var(--b1);
    text-decoration: none; transition: all .2s; text-align: center;
}
.qlink:hover { border-color: var(--b3); background: var(--s3); transform: translateY(-2px); }
.qlink-icon  { font-size: 22px; }
.qlink-label { font-family: var(--mono); font-size: 9px; font-weight: 700; color: var(--t3); letter-spacing: .1em; text-transform: uppercase; }
.qlink:hover .qlink-label { color: var(--sky); }

/* ── SISTEMA STATUS ── */
.sys-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0; border-bottom: 1px solid var(--b1); gap: 8px;
}
.sys-row:last-child { border-bottom: none; padding-bottom: 0; }
.sys-label { font-size: 12px; color: var(--t2); }
.sys-badge {
    font-family: var(--mono); font-size: 9px; font-weight: 700;
    padding: 3px 8px; border-radius: 6px; letter-spacing: .08em;
}
.sys-badge.ok   { background: rgba(16,185,129,.12); color: var(--green); border: 1px solid rgba(16,185,129,.2); }
.sys-badge.warn { background: rgba(245,158,11,.12); color: var(--amber); border: 1px solid rgba(245,158,11,.2); }
.sys-badge.err  { background: rgba(244,63,94,.12);  color: var(--red);   border: 1px solid rgba(244,63,94,.2); }
.sys-bar { flex: 1; max-width: 80px; height: 3px; background: var(--b2); border-radius: 2px; overflow: hidden; margin: 0 10px; }
.sys-bar-fill { height: 100%; border-radius: 2px; }

/* ── MODAL COMPROVATIVO ── */
.adm-modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(4,7,15,.92); backdrop-filter: blur(12px);
    display: flex; align-items: center; justify-content: center; padding: 20px;
}
.adm-modal-box {
    background: var(--s1); border: 1px solid var(--b3);
    border-radius: 20px; width: 100%; max-width: 500px;
    overflow: hidden;
}
.adm-modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--b2);
}
.adm-modal-title { font-family: var(--mono); font-size: 11px; font-weight: 700; color: var(--sky); letter-spacing: .12em; text-transform: uppercase; }
.adm-modal-close { width: 30px; height: 30px; border-radius: 8px; background: var(--b1); border: 1px solid var(--b2); color: var(--t3); font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
.adm-modal-close:hover { background: rgba(244,63,94,.15); border-color: rgba(244,63,94,.3); color: var(--red); }

/* ── TABS ── */
.adm-tabs { display: flex; gap: 4px; padding: 4px; background: var(--s2); border-radius: 10px; margin-bottom: 18px; }
.adm-tab {
    flex: 1; padding: 8px 12px; border-radius: 7px; border: none;
    font-family: var(--mono); font-size: 10px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase; cursor: pointer;
    background: transparent; color: var(--t3); transition: all .2s;
}
.adm-tab.active { background: var(--s3); color: var(--sky); border: 1px solid var(--b2); }
.adm-tab:hover:not(.active) { color: var(--t2); }
.adm-tab-panel { display: none; }
.adm-tab-panel.active { display: block; }

@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
.kpi-card  { animation: fadeUp .4s ease both; }
.kpi-card:nth-child(1) { animation-delay: .05s; }
.kpi-card:nth-child(2) { animation-delay: .10s; }
.kpi-card:nth-child(3) { animation-delay: .15s; }
.kpi-card:nth-child(4) { animation-delay: .20s; }
</style>

<div class="adm-bg"></div>
<div class="adm-grid"></div>

<div class="adm-wrap" x-data="{ showModal: false, imgUrl: '', clienteNome: '', whatsapp: '' }">

    {{-- ── TOPBAR ── --}}
    <div class="adm-topbar">
        <div class="adm-brand">
            <div class="adm-brand-dot"></div>
            <div>
                <div class="adm-brand-title">LT // COMMAND CENTER</div>
                <div class="adm-brand-sub">SUPER ADMIN · {{ strtoupper(auth()->user()->name) }}</div>
            </div>
        </div>
        <div class="adm-topbar-right">
            <div class="adm-time" id="adm-clock">--:--:--</div>
            @if(($pendentesCount ?? 0) > 0)
            <a href="{{ route('admin.reservas') }}" class="adm-pill danger">
                <span class="dot"></span>
                {{ $pendentesCount }} PENDENTES
            </a>
            @endif
            <a href="{{ route('admin.eventos.criar') }}" class="adm-pill">+ EVENTO</a>
            <a href="{{ route('admin.usuarios.index') }}" class="adm-pill">👥 USERS</a>
            <a href="{{ route('noticias.sincronizar') }}" class="adm-pill">🔄 RSS</a>
        </div>
    </div>

    {{-- ── KPI CARDS ── --}}
    <div class="sec-label">métricas globais</div>
    <div class="kpi-grid" style="margin-bottom:28px;">

        <div class="kpi-card" style="--accent-color: var(--sky)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">💰</div>
            <div class="kpi-label">Receita Total Bruta</div>
            <div class="kpi-value">{{ number_format(($receitaTotal ?? 0) / 1000, 0, ',', '.') }}k</div>
            <div class="kpi-sub">Kz · todas as vendas confirmadas</div>
        </div>

        <div class="kpi-card" style="--accent-color: var(--green)">
            <div class="kpi-glow"></div>
            @if(($pendentesCount ?? 0) > 0)
            <div class="kpi-delta warn">⚠ {{ $pendentesCount }} pendentes</div>
            @else
            <div class="kpi-delta up">✓ Em dia</div>
            @endif
            <div class="kpi-icon">📈</div>
            <div class="kpi-label">Lucro Luanda Tickets</div>
            <div class="kpi-value accent">{{ number_format(($receitaTotal ?? 0) * 0.10 / 1000, 0, ',', '.') }}k</div>
            <div class="kpi-sub">Kz · taxa de 10% sobre vendas</div>
        </div>

        <div class="kpi-card" style="--accent-color: var(--amber)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">🏦</div>
            <div class="kpi-label">Repasse Organizadores</div>
            <div class="kpi-value">{{ number_format(($receitaTotal ?? 0) * 0.90 / 1000, 0, ',', '.') }}k</div>
            <div class="kpi-sub">Kz · líquido a pagar</div>
        </div>

        <div class="kpi-card" style="--accent-color: var(--purple)">
            <div class="kpi-glow"></div>
            <div class="kpi-icon">🎟</div>
            <div class="kpi-label">Eventos Ativos</div>
            <div class="kpi-value">{{ $eventosAtivos ?? 0 }}</div>
            <div class="kpi-sub">publicados e em curso</div>
        </div>
    </div>

    {{-- ── LAYOUT PRINCIPAL ── --}}
    <div class="adm-cols">

        {{-- COLUNA PRINCIPAL --}}
        <div class="adm-col-main">

            {{-- PERFORMANCE POR EVENTO --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title">
                        <div class="adm-panel-title-dot"></div>
                        Desempenho por Evento
                    </div>
                    <span style="font-family:var(--mono);font-size:9px;color:var(--t3);">{{ collect($eventosPerformance ?? [])->count() }} EVENTOS</span>
                </div>
                <div class="adm-panel-body">
                    @if(collect($eventosPerformance ?? [])->isEmpty())
                    <div style="text-align:center;padding:30px 0;color:var(--t3);font-size:13px;">Nenhum evento com vendas ainda.</div>
                    @else
                    <div class="perf-grid">
                        @foreach($eventosPerformance ?? [] as $perf)
                        <div class="perf-card">
                            <div class="perf-card-name" title="{{ $perf->titulo }}">{{ $perf->titulo }}</div>
                            <div class="perf-row">
                                <div class="perf-row-top">
                                    <span class="perf-row-label">NORMAL · {{ $perf->qtd_normal }}</span>
                                    <span class="perf-row-val" style="color:var(--sky)">{{ number_format($perf->total_normal/1000,1,',','.') }}k Kz</span>
                                </div>
                                <div class="perf-bar">
                                    <div class="perf-bar-fill" style="width:{{ min($perf->perc_vendas,100) }}%;background:var(--sky);"></div>
                                </div>
                            </div>
                            <div class="perf-row">
                                <div class="perf-row-top">
                                    <span class="perf-row-label">VIP · {{ $perf->qtd_vip }}</span>
                                    <span class="perf-row-val" style="color:var(--purple)">{{ number_format($perf->total_vip/1000,1,',','.') }}k Kz</span>
                                </div>
                                <div class="perf-bar">
                                    <div class="perf-bar-fill" style="width:{{ min($perf->perc_vip_vendas,100) }}%;background:var(--purple);"></div>
                                </div>
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

            {{-- GRÁFICO + VENDAS --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title">
                        <div class="adm-panel-title-dot"></div>
                        Análise & Registos
                    </div>
                </div>
                <div class="adm-panel-body">

                    {{-- TABS --}}
                    <div class="adm-tabs">
                        <button class="adm-tab active" onclick="switchTab('grafico', this)">📊 Gráfico</button>
                        <button class="adm-tab" onclick="switchTab('vendas', this)">📋 Vendas</button>
                    </div>

                    {{-- TAB GRÁFICO --}}
                    <div class="adm-tab-panel active" id="tab-grafico">
                        <div class="chart-wrap" style="height:260px;">
                            <canvas id="chartReceitaEventos"></canvas>
                        </div>
                    </div>

                    {{-- TAB VENDAS --}}
                    <div class="adm-tab-panel" id="tab-vendas">
                        <div style="overflow-x:auto;">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Evento</th>
                                        <th style="text-align:right;">Total</th>
                                        <th style="text-align:left;">Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vendasDetalhadas as $venda)
                                    <tr>
                                        <td>
                                            <div class="td-name">{{ $venda->nome_cliente }}</div>
                                        </td>
                                        <td>
                                            <div class="td-event">{{ Str::limit($venda->tipoIngresso->evento->titulo ?? 'N/A', 22) }}</div>
                                        </td>
                                        <td class="td-val">{{ number_format($venda->total, 0, ',', '.') }} Kz</td>
                                        <td>
                                            <div class="td-date">{{ $venda->updated_at->format('d/m/y H:i') }}</div>
                                        </td>
                                        <td>
                                            <div class="td-acts">
                                                <button class="tbl-btn"
                                                    @click="showModal=true; imgUrl='{{ asset('storage/' . ($venda->comprovativo_path ?? '')) }}'; clienteNome='{{ addslashes($venda->nome_cliente) }}'; whatsapp='{{ $venda->whatsapp }}'"
                                                    title="Ver comprovativo">📂</button>
                                                <form action="{{ route('reserva.eliminar', $venda->id) }}" method="POST" onsubmit="return confirm('Apagar este registo?')" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="tbl-btn danger" title="Eliminar">🗑</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--t3);font-family:var(--mono);font-size:11px;">SEM REGISTOS</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA LATERAL --}}
        <div class="adm-col-side">

            {{-- AÇÕES RÁPIDAS --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Ações Rápidas</div>
                </div>
                <div class="adm-panel-body">
                    <div class="qlink-grid">
                        <a href="{{ route('admin.eventos.criar') }}" class="qlink">
                            <div class="qlink-icon">➕</div>
                            <div class="qlink-label">Novo Evento</div>
                        </a>
                        <a href="{{ route('admin.reservas') }}" class="qlink">
                            <div class="qlink-icon">📋</div>
                            <div class="qlink-label">Reservas</div>
                        </a>
                        <a href="{{ route('admin.pagos') }}" class="qlink">
                            <div class="qlink-icon">💳</div>
                            <div class="qlink-label">Pagamentos</div>
                        </a>
                        <a href="{{ route('admin.usuarios.index') }}" class="qlink">
                            <div class="qlink-icon">👥</div>
                            <div class="qlink-label">Utilizadores</div>
                        </a>
                        <a href="{{ route('admin.eventos') }}" class="qlink">
                            <div class="qlink-icon">🎭</div>
                            <div class="qlink-label">Eventos</div>
                        </a>
                        <a href="{{ route('admin.scanner') }}" class="qlink">
                            <div class="qlink-icon">📸</div>
                            <div class="qlink-label">Scanner QR</div>
                        </a>
                        <a href="{{ route('noticias.sincronizar') }}" class="qlink">
                            <div class="qlink-icon">📡</div>
                            <div class="qlink-label">Sincronizar RSS</div>
                        </a>
                        <a href="{{ route('home') }}" class="qlink">
                            <div class="qlink-icon">🌐</div>
                            <div class="qlink-label">Ver Site</div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ESTADO DO SISTEMA --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Estado do Sistema</div>
                    <span style="font-family:var(--mono);font-size:9px;color:var(--green);">● ONLINE</span>
                </div>
                <div class="adm-panel-body">
                    <div class="sys-row">
                        <span class="sys-label">Base de Dados</span>
                        <div class="sys-bar"><div class="sys-bar-fill" style="width:32%;background:var(--green)"></div></div>
                        <span class="sys-badge ok">OK</span>
                    </div>
                    <div class="sys-row">
                        <span class="sys-label">Storage</span>
                        <div class="sys-bar"><div class="sys-bar-fill" style="width:58%;background:var(--amber)"></div></div>
                        <span class="sys-badge warn">58%</span>
                    </div>
                    <div class="sys-row">
                        <span class="sys-label">Feed RSS</span>
                        <div class="sys-bar"><div class="sys-bar-fill" style="width:100%;background:var(--green)"></div></div>
                        <span class="sys-badge ok">ATIVO</span>
                    </div>
                    <div class="sys-row">
                        <span class="sys-label">Notificações</span>
                        <div class="sys-bar"><div class="sys-bar-fill" style="width:100%;background:var(--green)"></div></div>
                        <span class="sys-badge ok">OK</span>
                    </div>
                    <div class="sys-row">
                        <span class="sys-label">Reservas Pendentes</span>
                        <div class="sys-bar">
                            <div class="sys-bar-fill" style="width:{{ ($pendentesCount??0) > 0 ? '100' : '0' }}%;background:var(--red)"></div>
                        </div>
                        @if(($pendentesCount??0) > 0)
                        <span class="sys-badge err">{{ $pendentesCount }}</span>
                        @else
                        <span class="sys-badge ok">ZERO</span>
                        @endif
                    </div>
                    <div class="sys-row">
                        <span class="sys-label">Ambiente</span>
                        <div class="sys-bar"><div class="sys-bar-fill" style="width:100%;background:var(--sky)"></div></div>
                        <span class="sys-badge ok">{{ strtoupper(app()->environment()) }}</span>
                    </div>
                </div>
            </div>

            {{-- RESUMO FINANCEIRO --}}
            <div class="adm-panel">
                <div class="adm-panel-head">
                    <div class="adm-panel-title"><div class="adm-panel-title-dot"></div>Resumo Financeiro</div>
                </div>
                <div class="adm-panel-body">
                    @php
                        $total = $receitaTotal ?? 0;
                        $lucro = $total * 0.10;
                        $repasse = $total * 0.90;
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <div style="background:var(--s2);border:1px solid var(--b2);border-radius:12px;padding:14px;">
                            <div style="font-family:var(--mono);font-size:9px;color:var(--t3);letter-spacing:.12em;margin-bottom:6px;">RECEITA BRUTA</div>
                            <div style="font-family:var(--mono);font-size:22px;font-weight:700;color:var(--t1);">{{ number_format($total,0,',','.') }}</div>
                            <div style="font-size:11px;color:var(--t3);margin-top:2px;">Kwanzas</div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div style="background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);border-radius:10px;padding:12px;">
                                <div style="font-family:var(--mono);font-size:8px;color:var(--green);letter-spacing:.12em;margin-bottom:5px;">LT · TAXA</div>
                                <div style="font-family:var(--mono);font-size:15px;font-weight:700;color:var(--green);">{{ number_format($lucro/1000,1,',','.') }}k</div>
                                <div style="font-size:9px;color:var(--t3);">10% das vendas</div>
                            </div>
                            <div style="background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.2);border-radius:10px;padding:12px;">
                                <div style="font-family:var(--mono);font-size:8px;color:var(--amber);letter-spacing:.12em;margin-bottom:5px;">REPASSE</div>
                                <div style="font-family:var(--mono);font-size:15px;font-weight:700;color:var(--amber);">{{ number_format($repasse/1000,1,',','.') }}k</div>
                                <div style="font-size:9px;color:var(--t3);">90% organizadores</div>
                            </div>
                        </div>
                        <a href="{{ route('admin.pagos') }}"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--sky);font-family:var(--mono);font-size:10px;font-weight:700;letter-spacing:.08em;text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--b2)'" onmouseout="this.style.background='var(--b1)'">
                            VER TODOS OS PAGAMENTOS →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── MODAL COMPROVATIVO ── --}}
    <div x-show="showModal" x-cloak class="adm-modal-overlay" @click.away="showModal=false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="adm-modal-box"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="adm-modal-head">
                <div class="adm-modal-title">📂 Comprovativo de Pagamento</div>
                <button class="adm-modal-close" @click="showModal=false">✕</button>
            </div>
            <div style="padding:16px;">
                <img :src="imgUrl"
                     onerror="this.src='https://placehold.co/480x300/0d1526/38bdf8?text=Erro+ao+carregar'"
                     style="width:100%;border-radius:10px;border:1px solid var(--b2);max-height:300px;object-fit:contain;background:var(--s2);">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px;">
                    <div style="background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:12px;">
                        <div style="font-family:var(--mono);font-size:8px;color:var(--t3);letter-spacing:.12em;margin-bottom:4px;">CLIENTE</div>
                        <div style="font-size:13px;font-weight:700;color:var(--t1);" x-text="clienteNome"></div>
                    </div>
                    <div style="background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:12px;">
                        <div style="font-family:var(--mono);font-size:8px;color:var(--t3);letter-spacing:.12em;margin-bottom:4px;">WHATSAPP</div>
                        <div style="font-size:13px;font-weight:700;color:var(--green);" x-text="whatsapp"></div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;margin-top:10px;">
                    <a :href="'https://wa.me/' + whatsapp.replace(/\D/g,'')"
                       target="_blank"
                       style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:10px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:var(--green);font-family:var(--mono);font-size:10px;font-weight:700;text-decoration:none;">
                        💬 WHATSAPP
                    </a>
                    <a :href="imgUrl" target="_blank"
                       style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--sky);font-family:var(--mono);font-size:10px;font-weight:700;text-decoration:none;">
                        🖼 ORIGINAL
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Relógio ──────────────────────────────────────────────────
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const s = String(now.getSeconds()).padStart(2,'0');
    const el = document.getElementById('adm-clock');
    if (el) el.textContent = h + ':' + m + ':' + s;
}
updateClock();
setInterval(updateClock, 1000);

// ── Tabs ─────────────────────────────────────────────────────
function switchTab(id, btn) {
    document.querySelectorAll('.adm-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.adm-tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + id).classList.add('active');
}

// ── Chart.js ─────────────────────────────────────────────────
const labels = {!! json_encode(collect($eventosPerformance ?? [])->pluck('titulo')->map(fn($t) => strlen($t) > 16 ? substr($t,0,14).'…' : $t)) !!};
const valores = {!! json_encode(collect($eventosPerformance ?? [])->pluck('total_geral')) !!};

const ctx = document.getElementById('chartReceitaEventos');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Normal (Kz)',
                    data: {!! json_encode(collect($eventosPerformance ?? [])->pluck('total_normal')) !!},
                    backgroundColor: 'rgba(56,189,248,0.65)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'VIP (Kz)',
                    data: {!! json_encode(collect($eventosPerformance ?? [])->pluck('total_vip')) !!},
                    backgroundColor: 'rgba(167,139,250,0.65)',
                    borderRadius: 6,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: { color: '#64748b', font: { size: 10 }, boxWidth: 10 }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    grid: { display: false },
                    ticks: { color: '#475569', font: { size: 9 } }
                },
                y: {
                    stacked: false,
                    grid: { color: 'rgba(56,189,248,0.06)' },
                    ticks: {
                        color: '#475569', font: { size: 9 },
                        callback: v => (v/1000).toFixed(0) + 'k'
                    }
                }
            }
        }
    });
}
</script>
@endsection