@extends('layouts.app')
@section('title', 'Análise do Sistema — Luanda Tickets')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --bg:#06091a;--s1:#0c1228;--s2:#111830;--s3:#172040;
    --sky:#38bdf8;--green:#10b981;--amber:#f59e0b;--red:#f43f5e;--purple:#a78bfa;
    --b1:rgba(56,189,248,.10);--b2:rgba(56,189,248,.20);--b3:rgba(56,189,248,.38);
    --t1:#ffffff;--t2:#c8d8f0;--t3:#7a90b0;
    --mono:'Space Mono',monospace;--sans:'Outfit',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{background:var(--bg);font-family:var(--sans);color:var(--t1);min-height:100vh;}
.adm-bg{position:fixed;inset:0;z-index:0;pointer-events:none;background:radial-gradient(ellipse at 20% 30%,rgba(167,139,250,.07),transparent 45%),radial-gradient(ellipse at 80% 60%,rgba(6,182,212,.06),transparent 40%);}
.an-wrap{position:relative;z-index:1;width:100%;padding:16px 8px 80px;}
@@media(min-width:768px){.an-wrap{padding:28px 20px 80px;}}
.an-topbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.an-title{font-family:var(--mono);font-size:14px;font-weight:700;color:var(--purple);letter-spacing:.1em;}
.an-sub{font-family:var(--mono);font-size:11px;color:var(--t2);margin-top:2px;}
.top-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;transition:all .2s;}
.top-btn:hover{border-color:var(--b3);color:var(--t1);}
.top-btn.purple{border-color:rgba(167,139,250,.3);color:var(--purple);background:rgba(167,139,250,.06);}
.top-btn.purple:hover{background:rgba(167,139,250,.12);}
.sec-label{font-family:var(--mono);font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--t2);margin-bottom:10px;}
.st-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px;}
@@media(min-width:640px){.st-grid{grid-template-columns:repeat(6,1fr);}}
.st-card{background:var(--s1);border:1px solid var(--b2);border-radius:12px;padding:14px;text-align:center;}
.st-val{font-family:var(--mono);font-size:20px;font-weight:700;color:var(--t1);margin-bottom:4px;}
.st-lbl{font-size:11px;color:var(--t2);letter-spacing:.04em;text-transform:uppercase;}
.st-card.ok .st-val{color:var(--green);}
.st-card.warn .st-val{color:var(--amber);}
.st-card.bad .st-val{color:var(--red);}
.an-panel{background:var(--s1);border:1px solid var(--b2);border-radius:16px;overflow:hidden;margin-bottom:16px;}
.an-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.an-panel-title{display:flex;align-items:center;gap:8px;font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t1);letter-spacing:.06em;text-transform:uppercase;}
.an-panel-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;}
.an-panel-head-badge{font-family:var(--mono);font-size:10px;color:var(--t2);}
.an-panel-body{padding:16px 18px;}
.an-table{width:100%;border-collapse:collapse;}
.an-table th{font-family:var(--mono);font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--t2);padding:8px 12px;text-align:left;border-bottom:1px solid var(--b1);}
.an-table td{padding:10px 12px;border-bottom:1px solid var(--b1);vertical-align:middle;font-size:13px;color:var(--t1);}
.an-table tr:last-child td{border-bottom:none;}
.an-table tr:hover td{background:var(--b1);}
.acao-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-family:var(--mono);font-size:10px;font-weight:700;letter-spacing:.03em;}
.acao-emitido{background:rgba(56,189,248,.12);border:1px solid rgba(56,189,248,.25);color:var(--sky);}
.acao-validado{background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:var(--green);}
.acao-tentativa_invalida{background:rgba(244,63,94,.12);border:1px solid rgba(244,63,94,.25);color:var(--red);}
.acao-hmac_invalido{background:rgba(244,63,94,.12);border:1px solid rgba(244,63,94,.25);color:var(--red);}
.acao-bloqueado{background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);color:var(--amber);}
.acao-tentativa_reuso{background:rgba(167,139,250,.12);border:1px solid rgba(167,139,250,.25);color:var(--purple);}
.acao-bloqueado_reuso{background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);color:var(--amber);}
.acao-acesso_negado{background:rgba(244,63,94,.12);border:1px solid rgba(244,63,94,.25);color:var(--red);}
.acao-eliminado_pelo_utilizador{background:rgba(167,139,250,.12);border:1px solid rgba(167,139,250,.25);color:var(--purple);}
.top-ev-row{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--b1);}
.top-ev-row:last-child{border-bottom:none;}
.top-ev-rank{font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t3);width:26px;flex-shrink:0;}
.top-ev-name{flex:1;min-width:0;font-size:13px;font-weight:600;color:var(--t1);}
.top-ev-bar-wrap{width:100%;height:3px;border-radius:999px;background:var(--b1);margin-top:5px;overflow:hidden;}
.top-ev-bar{height:100%;border-radius:999px;background:var(--sky);}
.top-ev-stat{text-align:right;flex-shrink:0;}
.top-ev-stat-val{font-family:var(--mono);font-size:13px;font-weight:700;}
.top-ev-stat-lbl{font-size:11px;color:var(--t2);}
.an-cols{display:grid;grid-template-columns:1fr;gap:16px;}
@@media(min-width:1024px){.an-cols{grid-template-columns:1fr 1fr;}}
.lote-row{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--s2);border-radius:10px;border:1px solid var(--b1);margin-bottom:6px;}
.lote-row:last-child{margin-bottom:0;}
.lote-id{font-family:var(--mono);font-size:11px;font-weight:700;color:var(--sky);}
.lote-meta{font-size:12px;color:var(--t2);margin-top:2px;}
.lote-qty{font-family:var(--mono);font-size:14px;font-weight:700;color:var(--green);}
.lote-val{font-size:11px;color:var(--t2);margin-top:1px;}
.lote-date{font-family:var(--mono);font-size:10px;color:var(--t3);margin-top:1px;}
.blk-row{padding:12px 14px;background:rgba(244,63,94,.05);border:1px solid rgba(244,63,94,.15);border-radius:10px;margin-bottom:6px;}
.blk-row:last-child{margin-bottom:0;}
.blk-code{font-family:var(--mono);font-size:11px;color:var(--red);margin-bottom:4px;}
.blk-meta{font-size:12px;color:var(--t2);}
.blk-tent{display:inline-flex;align-items:center;gap:4px;margin-top:6px;font-family:var(--mono);font-size:10px;color:var(--amber);background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);padding:3px 8px;border-radius:20px;}
</style>

<div class="adm-bg"></div>

<div class="an-wrap">

    <div class="an-topbar">
        <div>
            <div class="an-title">🔬 ANÁLISE DO SISTEMA</div>
            <div class="an-sub">LUANDA TICKETS · LOGS DE SEGURANÇA E AUDITORIA</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <a href="{{ route('admin.dashboard') }}" class="top-btn">← Dashboard</a>
            <a href="{{ route('admin.analises.pdf') }}" target="_blank" class="top-btn purple">🖨️ Relatório PDF Completo</a>
        </div>
    </div>

    <div class="sec-label">resumo de segurança</div>
    <div class="st-grid">
        <div class="st-card ok"><div class="st-val">{{ $statsSeguranca['total'] }}</div><div class="st-lbl">Total Bilhetes</div></div>
        <div class="st-card ok"><div class="st-val">{{ $statsSeguranca['com_hmac'] }}</div><div class="st-lbl">Com HMAC</div></div>
        <div class="st-card ok"><div class="st-val">{{ $statsSeguranca['validados'] }}</div><div class="st-lbl">Validados</div></div>
        <div class="st-card ok"><div class="st-val">{{ $statsSeguranca['lotes'] }}</div><div class="st-lbl">Lotes</div></div>
        <div class="st-card {{ $statsSeguranca['bloqueados'] > 0 ? 'bad' : 'ok' }}"><div class="st-val">{{ $statsSeguranca['bloqueados'] }}</div><div class="st-lbl">Bloqueados</div></div>
        <div class="st-card {{ $statsSeguranca['tentativas'] > 0 ? 'warn' : 'ok' }}"><div class="st-val">{{ $statsSeguranca['tentativas'] }}</div><div class="st-lbl">Tentativas</div></div>
    </div>

    <div class="sec-label">top eventos por bilhetes</div>
    <div class="an-panel">
        <div class="an-panel-head">
            <div class="an-panel-title"><div class="an-panel-dot" style="background:var(--sky);box-shadow:0 0 6px var(--sky);"></div>Ranking de Eventos</div>
            <span class="an-panel-head-badge">{{ $topEventos->count() }} EVENTOS</span>
        </div>
        <div class="an-panel-body">
            @php $maxBilhetes = $topEventos->max('total_bilhetes') ?: 1; @endphp
            @forelse($topEventos as $i => $ev)
            <div class="top-ev-row">
                <div class="top-ev-rank">#{{ $i + 1 }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="top-ev-name">{{ Str::limit($ev->titulo, 40) }}</div>
                    <div class="top-ev-bar-wrap"><div class="top-ev-bar" style="width:{{ ($ev->total_bilhetes / $maxBilhetes) * 100 }}%"></div></div>
                </div>
                <div style="display:flex;gap:18px;margin-left:12px;">
                    <div class="top-ev-stat"><div class="top-ev-stat-val" style="color:var(--sky)">{{ $ev->total_bilhetes }}</div><div class="top-ev-stat-lbl">emitidos</div></div>
                    <div class="top-ev-stat"><div class="top-ev-stat-val" style="color:var(--green)">{{ $ev->validados }}</div><div class="top-ev-stat-lbl">validados</div></div>
                </div>
            </div>
            @empty
            <p style="text-align:center;color:var(--t2);padding:24px 0;font-size:13px;">Sem dados ainda.</p>
            @endforelse
        </div>
    </div>

    <div class="an-cols">

        {{-- LOG DE AUDITORIA --}}
        <div>
            <div class="sec-label">auditoria recente</div>
            <div class="an-panel">
                <div class="an-panel-head">
                    <div class="an-panel-title"><div class="an-panel-dot" style="background:var(--purple);box-shadow:0 0 6px var(--purple);"></div>Log de Auditoria</div>
                    <span class="an-panel-head-badge">{{ $auditoria->count() }} REGISTOS</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="an-table">
                        <thead><tr><th>Acção</th><th>Código</th><th>Scanner</th><th>Data</th><th></th></tr></thead>
                        <tbody>
                            @forelse($auditoria as $log)
                            <tr>
                                <td>
                                    <span class="acao-badge acao-{{ $log->acao }}">
                                        @if($log->acao === 'emitido') 📤 emitido
                                        @elseif($log->acao === 'validado') ✅ validado
                                        @elseif($log->acao === 'tentativa_invalida') ⚠️ inválida
                                        @elseif($log->acao === 'hmac_invalido') 🚫 adulterado
                                        @elseif($log->acao === 'bloqueado') 🔒 bloqueado
                                        @elseif($log->acao === 'bloqueado_reuso') 🔒 bloq. reuso
                                        @elseif($log->acao === 'tentativa_reuso') ♻️ reuso
                                        @elseif($log->acao === 'acesso_negado') 🚫 acesso negado
                                        @elseif($log->acao === 'eliminado_pelo_utilizador') 🗑 eliminado
                                        @else {{ $log->acao }}
                                        @endif
                                    </span>
                                </td>
                                <td style="font-family:var(--mono);font-size:10px;color:var(--t2);">{{ substr($log->codigo_unico, 0, 13) }}…</td>
                                <td style="font-size:12px;color:var(--t2);">{{ $log->scanner_nome ?? $log->scanner_ip ?? '—' }}</td>
                                <td style="font-family:var(--mono);font-size:10px;color:var(--t2);">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.analises.bilhete.pdf', $log->codigo_unico) }}" target="_blank"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:4px 9px;border-radius:7px;background:rgba(167,139,250,.1);border:1px solid rgba(167,139,250,.25);color:var(--purple);font-size:10px;font-weight:700;text-decoration:none;">
                                        🖨️ PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t2);">Sem registos de auditoria.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA --}}
        <div>
            <div class="sec-label">bilhetes bloqueados</div>
            <div class="an-panel">
                <div class="an-panel-head">
                    <div class="an-panel-title"><div class="an-panel-dot" style="background:var(--red);box-shadow:0 0 6px var(--red);"></div>Bilhetes Bloqueados</div>
                    <span class="an-panel-head-badge" style="color:{{ $bilhetesBloqueados->count() > 0 ? 'var(--red)' : 'var(--green)' }};">{{ $bilhetesBloqueados->count() > 0 ? $bilhetesBloqueados->count().' BLOQUEADO(S)' : '✓ LIMPO' }}</span>
                </div>
                <div class="an-panel-body">
                    @forelse($bilhetesBloqueados as $blk)
                    <div class="blk-row">
                        <div class="blk-code">{{ substr($blk->codigo_unico, 0, 20) }}…</div>
                        <div class="blk-meta">{{ Str::limit($blk->evento ?? '—', 30) }} · Lote: {{ $blk->lote_id ?? '—' }}</div>
                        <div class="blk-tent">⚠ {{ $blk->tentativas_invalidas }} tentativa(s)</div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:20px;color:var(--green);font-size:13px;">✅ Nenhum bilhete bloqueado</div>
                    @endforelse
                </div>
            </div>

            <div class="sec-label">lotes emitidos</div>
            <div class="an-panel">
                <div class="an-panel-head">
                    <div class="an-panel-title"><div class="an-panel-dot" style="background:var(--green);box-shadow:0 0 6px var(--green);"></div>Lotes Emitidos</div>
                    <span class="an-panel-head-badge">{{ $lotes->count() }} LOTES</span>
                </div>
                <div class="an-panel-body" style="max-height:380px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--b2) transparent;">
                    @forelse($lotes as $lote)
                    <div class="lote-row">
                        <div style="min-width:0;">
                            <div class="lote-id">{{ $lote->lote_id }}</div>
                            <div class="lote-meta">{{ Str::limit($lote->evento ?? '—', 24) }} · {{ $lote->cliente ?? 'Convidado' }}</div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div class="lote-qty">{{ $lote->quantidade }}x</div>
                            <div class="lote-val">{{ number_format($lote->total, 0, ',', '.') }} Kz</div>
                            <div class="lote-date">{{ \Carbon\Carbon::parse($lote->emitido_em)->format('d/m H:i') }}</div>
                        </div>
                    </div>
                    @empty
                    <p style="text-align:center;color:var(--t2);padding:20px 0;font-size:13px;">Sem lotes emitidos ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- BILHETES ELIMINADOS PELO UTILIZADOR --}}
    <div class="sec-label" style="margin-top:8px;">bilhetes eliminados pelos utilizadores</div>
    <div class="an-panel">
        <div class="an-panel-head">
            <div class="an-panel-title">
                <div class="an-panel-dot" style="background:var(--purple);box-shadow:0 0 6px var(--purple);"></div>
                Bilhetes Eliminados (Soft Delete)
            </div>
            <span class="an-panel-head-badge" style="color:var(--purple);">
                {{ $bilhetesEliminados->count() }} REGISTO(S)
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="an-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Evento</th>
                        <th>Tipo</th>
                        <th>Utilizador</th>
                        <th>Utilizado em</th>
                        <th>Eliminado em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bilhetesEliminados as $elim)
                    <tr>
                        <td style="font-family:var(--mono);font-size:10px;color:var(--purple);">{{ substr($elim->codigo_unico, 0, 16) }}…</td>
                        <td style="font-size:12px;color:var(--t2);">{{ Str::limit(optional($elim->evento)->titulo ?? '—', 28) }}</td>
                        <td style="font-size:12px;color:var(--t2);">{{ optional($elim->tipoIngresso)->nome ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--t2);">{{ optional(optional($elim->pedido)->user)->name ?? '—' }}</td>
                        <td style="font-family:var(--mono);font-size:10px;color:var(--green);">{{ $elim->validado_em ? $elim->validado_em->format('d/m/Y H:i') : '—' }}</td>
                        <td style="font-family:var(--mono);font-size:10px;color:var(--amber);">{{ $elim->deleted_at ? $elim->deleted_at->format('d/m/Y H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--t2);">✅ Nenhum bilhete eliminado ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection