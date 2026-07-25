<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório de Auditoria — Luanda Tickets</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a2e; background: white; }

.header { padding: 24px 32px 16px; border-bottom: 3px solid #0ea5e9; margin-bottom: 20px; }
.header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
.brand { font-size: 20px; font-weight: 900; color: #0ea5e9; letter-spacing: -0.5px; }
.brand span { color: #1a1a2e; }
.header-meta { font-size: 10px; color: #64748b; text-align: right; }
.report-title { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
.report-sub { font-size: 11px; color: #64748b; }

.stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; margin: 0 32px 20px; }
.stat-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; }
.stat-val { font-size: 18px; font-weight: 700; color: #0ea5e9; margin-bottom: 2px; }
.stat-val.ok { color: #10b981; }
.stat-val.warn { color: #f59e0b; }
.stat-val.bad { color: #f43f5e; }
.stat-lbl { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }

.section { margin: 0 32px 24px; }
.section-title { font-size: 13px; font-weight: 700; color: #0ea5e9; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

table { width: 100%; border-collapse: collapse; font-size: 11px; }
thead tr { background: #0ea5e9; color: white; }
thead th { padding: 8px 10px; text-align: left; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
tbody tr:nth-child(even) { background: #f8fafc; }
tbody tr:hover { background: #f0f9ff; }
tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; color: #1a1a2e; }

.badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 700; }
.badge-emitido           { background: #dbeafe; color: #1d4ed8; }
.badge-validado          { background: #d1fae5; color: #065f46; }
.badge-tentativa_invalida{ background: #fee2e2; color: #991b1b; }
.badge-hmac_invalido     { background: #fee2e2; color: #991b1b; }
.badge-bloqueado         { background: #fef3c7; color: #92400e; }
.badge-tentativa_reuso   { background: #ede9fe; color: #5b21b6; }

.footer { margin: 20px 32px 0; padding: 12px 0; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; }

.page-break { page-break-before: always; }
</style>
</head>
<body>

<div class="header">
    <div class="header-top">
        <div>
            <div class="brand">Luanda<span>Tickets</span></div>
            <div class="report-title">Relatório Completo de Auditoria</div>
            <div class="report-sub">Logs de segurança e rastreabilidade de bilhetes</div>
        </div>
        <div class="header-meta">
            <div><strong>Data de emissão:</strong> {{ now()->format('d/m/Y H:i') }}</div>
            <div><strong>Emitido por:</strong> {{ auth()->user()->name }}</div>
            <div><strong>Total de registos:</strong> {{ $auditoria->count() }}</div>
        </div>
    </div>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-val ok">{{ $stats['total'] }}</div>
        <div class="stat-lbl">Total Bilhetes</div>
    </div>
    <div class="stat-box">
        <div class="stat-val ok">{{ $stats['com_hmac'] }}</div>
        <div class="stat-lbl">Com HMAC</div>
    </div>
    <div class="stat-box">
        <div class="stat-val ok">{{ $stats['validados'] }}</div>
        <div class="stat-lbl">Validados</div>
    </div>
    <div class="stat-box">
        <div class="stat-val ok">{{ $stats['lotes'] }}</div>
        <div class="stat-lbl">Lotes</div>
    </div>
    <div class="stat-box">
        <div class="stat-val {{ $stats['bloqueados'] > 0 ? 'bad' : 'ok' }}">{{ $stats['bloqueados'] }}</div>
        <div class="stat-lbl">Bloqueados</div>
    </div>
    <div class="stat-box">
        <div class="stat-val {{ $stats['tentativas'] > 0 ? 'warn' : 'ok' }}">{{ $stats['tentativas'] }}</div>
        <div class="stat-lbl">Tentativas</div>
    </div>
</div>

{{-- AUDITORIA COMPLETA --}}
<div class="section">
    <div class="section-title">Log completo de auditoria</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Acção</th>
                <th>Código do Bilhete</th>
                <th>Lote</th>
                <th>Scanner / IP</th>
                <th>Data e Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($auditoria as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><span class="badge badge-{{ $log->acao }}">{{ strtoupper($log->acao) }}</span></td>
                <td style="font-family:monospace;font-size:10px;">{{ $log->codigo_unico }}</td>
                <td style="font-family:monospace;font-size:10px;color:#0ea5e9;">{{ $log->lote_id ?? '—' }}</td>
                <td>{{ $log->scanner_nome ?? $log->scanner_ip ?? '—' }}</td>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">Sem registos de auditoria.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- BILHETES BLOQUEADOS --}}
@if($bilhetesBloqueados->count() > 0)
<div class="section">
    <div class="section-title" style="color:#f43f5e;">Bilhetes Bloqueados por Adulteração</div>
    <table>
        <thead>
            <tr>
                <th>Código do Bilhete</th>
                <th>Evento</th>
                <th>Lote</th>
                <th>Tentativas</th>
                <th>Bloqueado em</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bilhetesBloqueados as $blk)
            <tr>
                <td style="font-family:monospace;font-size:10px;">{{ $blk->codigo_unico }}</td>
                <td>{{ $blk->evento ?? '—' }}</td>
                <td style="font-family:monospace;font-size:10px;">{{ $blk->lote_id ?? '—' }}</td>
                <td style="color:#f43f5e;font-weight:700;">{{ $blk->tentativas_invalidas }}x</td>
                <td>{{ \Carbon\Carbon::parse($blk->updated_at)->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    <span>Luanda Tickets — Relatório gerado automaticamente</span>
    <span>{{ now()->format('d/m/Y H:i:s') }}</span>
</div>

</body>
</html>