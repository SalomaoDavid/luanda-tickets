<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Histórico do Bilhete — Luanda Tickets</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a2e; background: white; }

.header { padding: 24px 32px 16px; border-bottom: 3px solid #0ea5e9; margin-bottom: 20px; }
.header-top { display: flex; justify-content: space-between; align-items: flex-start; }
.brand { font-size: 20px; font-weight: 900; color: #0ea5e9; }
.brand span { color: #1a1a2e; }
.report-title { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
.report-sub { font-size: 11px; color: #64748b; }
.header-meta { font-size: 10px; color: #64748b; text-align: right; }

.bilhete-info { margin: 0 32px 20px; padding: 16px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; }
.bilhete-info-title { font-size: 11px; font-weight: 700; color: #0ea5e9; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.info-row { display: flex; flex-direction: column; gap: 2px; }
.info-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; }
.info-val { font-size: 12px; font-weight: 600; color: #1a1a2e; font-family: monospace; }
.info-val.normal { font-family: Arial, sans-serif; }

.status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.status-ok  { background: #d1fae5; color: #065f46; }
.status-bad { background: #fee2e2; color: #991b1b; }
.status-used{ background: #dbeafe; color: #1d4ed8; }

.section { margin: 0 32px 24px; }
.section-title { font-size: 13px; font-weight: 700; color: #0ea5e9; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

table { width: 100%; border-collapse: collapse; font-size: 11px; }
thead tr { background: #0ea5e9; color: white; }
thead th { padding: 8px 10px; text-align: left; font-weight: 700; font-size: 10px; text-transform: uppercase; }
tbody tr:nth-child(even) { background: #f8fafc; }
tbody td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }

.badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 700; }
.badge-emitido           { background: #dbeafe; color: #1d4ed8; }
.badge-validado          { background: #d1fae5; color: #065f46; }
.badge-tentativa_invalida{ background: #fee2e2; color: #991b1b; }
.badge-hmac_invalido     { background: #fee2e2; color: #991b1b; }
.badge-bloqueado         { background: #fef3c7; color: #92400e; }
.badge-tentativa_reuso   { background: #ede9fe; color: #5b21b6; }

.aviso-bloqueado { margin: 0 32px 16px; padding: 12px 16px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; font-size: 12px; color: #991b1b; font-weight: 600; }

.footer { margin: 20px 32px 0; padding: 12px 0; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="brand">Luanda<span>Tickets</span></div>
            <div class="report-title">Histórico Individual do Bilhete</div>
            <div class="report-sub">Rastreabilidade completa para verificação e reclamações</div>
        </div>
        <div class="header-meta">
            <div><strong>Emitido em:</strong> {{ now()->format('d/m/Y H:i') }}</div>
            <div><strong>Por:</strong> {{ auth()->user()->name }}</div>
        </div>
    </div>
</div>

{{-- AVISO SE BLOQUEADO --}}
@if($bilhete->bloqueado)
<div class="aviso-bloqueado">
    ⚠️ ATENÇÃO: Este bilhete está BLOQUEADO por adulteração detectada ({{ $bilhete->tentativas_invalidas }} tentativa(s) inválida(s)).
</div>
@endif

{{-- INFORMAÇÕES DO BILHETE --}}
<div class="bilhete-info">
    <div class="bilhete-info-title">Identificação do Bilhete</div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-lbl">Código único</span>
            <span class="info-val">{{ $bilhete->codigo_unico }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Lote</span>
            <span class="info-val">{{ $bilhete->lote_id ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Evento</span>
            <span class="info-val normal">{{ optional($bilhete->evento)->titulo ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Tipo de Bilhete</span>
            <span class="info-val normal">{{ optional($bilhete->tipoIngresso)->nome ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Estado</span>
            <span class="info-val normal">
                @if($bilhete->bloqueado)
                    <span class="status-badge status-bad">🔒 Bloqueado</span>
                @elseif($bilhete->validado_em)
                    <span class="status-badge status-used">✅ Utilizado em {{ \Carbon\Carbon::parse($bilhete->validado_em)->format('d/m/Y H:i') }}</span>
                @else
                    <span class="status-badge status-ok">🎟 Válido — não utilizado</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Integridade HMAC</span>
            <span class="info-val normal">
                @if($bilhete->hmac_assinatura)
                    <span class="status-badge status-ok">✓ Assinado</span>
                @else
                    <span class="status-badge status-bad">✗ Sem assinatura</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Emitido em</span>
            <span class="info-val normal">{{ \Carbon\Carbon::parse($bilhete->created_at)->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Tentativas inválidas</span>
            <span class="info-val normal" style="{{ $bilhete->tentativas_invalidas > 0 ? 'color:#991b1b;font-weight:700;' : '' }}">
                {{ $bilhete->tentativas_invalidas }}
            </span>
        </div>
    </div>
</div>

{{-- HISTÓRICO COMPLETO --}}
<div class="section">
    <div class="section-title">Histórico Completo de Eventos</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Acção</th>
                <th>Executado por</th>
                <th>IP do Scanner</th>
                <th>Snapshot</th>
                <th>Data e Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historico as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><span class="badge badge-{{ $log->acao }}">{{ strtoupper($log->acao) }}</span></td>
                <td>{{ $log->scanner_nome ?? '—' }}</td>
                <td style="font-family:monospace;font-size:10px;">{{ $log->scanner_ip ?? '—' }}</td>
                <td style="font-size:10px;color:#64748b;max-width:150px;">
                    @if($log->snapshot)
                        @php $snap = json_decode($log->snapshot, true) ?? []; @endphp
                        @foreach(array_slice($snap, 0, 3) as $k => $v)
                            <span>{{ $k }}: {{ is_array($v) ? implode(',', $v) : $v }}</span><br>
                        @endforeach
                    @else —
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Sem histórico para este bilhete.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    <span>Luanda Tickets — Documento de rastreabilidade para fins legais</span>
    <span>{{ now()->format('d/m/Y H:i:s') }}</span>
</div>

</body>
</html>