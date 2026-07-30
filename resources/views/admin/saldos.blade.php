@extends('layouts.app')
@section('title', 'Pagamentos a Criadores')
@section('content')
<style>
:root{--bg:#06091a;--s1:#0c1228;--s2:#111830;--sky:#38bdf8;--green:#10b981;--amber:#f59e0b;--red:#f43f5e;--purple:#a78bfa;--b1:rgba(56,189,248,.10);--b2:rgba(56,189,248,.20);--t1:#fff;--t2:#c8d8f0;--t3:#7a90b0;--mono:'Space Mono',monospace;}
body{background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;}
.wrap{position:relative;z-index:1;padding:20px 8px 80px;max-width:1000px;margin:0 auto;}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-title{font-family:var(--mono);font-size:14px;font-weight:700;color:var(--purple);letter-spacing:.1em;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;transition:all .2s;}
.btn:hover{border-color:var(--sky);color:var(--t1);}
.btn.primary{background:linear-gradient(135deg,var(--sky),#0ea5e9);border:none;color:#000;}

/* Stats */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;}
.stat{background:var(--s1);border:1px solid var(--b2);border-radius:12px;padding:16px;text-align:center;}
.stat-val{font-family:var(--mono);font-size:22px;font-weight:700;margin-bottom:4px;}
.stat-lbl{font-size:11px;color:var(--t2);text-transform:uppercase;letter-spacing:.06em;}

/* Tabela criadores */
.panel{background:var(--s1);border:1px solid var(--b2);border-radius:16px;overflow:hidden;margin-bottom:16px;}
.panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.panel-title{font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t1);letter-spacing:.06em;text-transform:uppercase;}
.panel-body{padding:16px 18px;}
table{width:100%;border-collapse:collapse;}
th{font-family:var(--mono);font-size:11px;color:var(--t2);padding:8px 12px;text-align:left;border-bottom:1px solid var(--b1);letter-spacing:.04em;text-transform:uppercase;}
td{padding:12px;border-bottom:1px solid var(--b1);font-size:13px;color:var(--t1);}
tr:last-child td{border-bottom:none;}
tr:hover td{background:var(--b1);}

/* Dados bancários inline */
.banco-info{font-size:11px;color:var(--t3);margin-top:3px;}
.banco-iban{font-family:var(--mono);font-size:11px;color:var(--sky);}

/* Badge estado */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-pend{background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.25);color:var(--amber);}
.badge-pago{background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:var(--green);}

/* Modal pagar */
.modal-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.8);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:16px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#0c1220;border:1px solid var(--b2);border-radius:18px;padding:24px;max-width:440px;width:100%;}
.modal-title{font-size:16px;font-weight:800;color:var(--t1);margin-bottom:6px;}
.modal-sub{font-size:12px;color:var(--t3);margin-bottom:18px;}
.form-label{display:block;font-size:10px;font-weight:700;color:var(--t2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;}
.form-input{width:100%;background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:10px 12px;font-size:13px;color:var(--t1);outline:none;margin-bottom:14px;font-family:inherit;}
.form-input:focus{border-color:var(--sky);}
</style>

<div class="wrap">
    <div class="topbar">
        <div class="page-title">💰 Pagamentos a Criadores</div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.contas-bancarias') }}" class="btn">🏦 Gerir Contas Bancárias</a>
            <a href="{{ route('admin.dashboard') }}" class="btn">← Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#34d399;font-size:13px;">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- STATS --}}
    <div class="stats">
        <div class="stat">
            <div class="stat-val" style="color:var(--amber);">{{ number_format($totalPendente,0,',','.') }} Kz</div>
            <div class="stat-lbl">A pagar a criadores</div>
        </div>
        <div class="stat">
            <div class="stat-val" style="color:var(--green);">{{ number_format($totalPago,0,',','.') }} Kz</div>
            <div class="stat-lbl">Já pago</div>
        </div>
        <div class="stat">
            <div class="stat-val" style="color:var(--sky);">{{ number_format($totalAdmin,0,',','.') }} Kz</div>
            <div class="stat-lbl">Comissão Admin (10%)</div>
        </div>
    </div>

    {{-- POR CRIADOR --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Resumo por Criador</div>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Criador</th>
                        <th>Dados Bancários</th>
                        <th>Reservas</th>
                        <th>Total a pagar</th>
                        <th>Acção</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($porCriador as $item)
                    <tr>
                        <td>
                            <div style="font-weight:700;">{{ $item->criador->name }}</div>
                        </td>
                        <td>
                            @if($item->criador->dadosBancarios)
                            <div class="banco-iban">{{ $item->criador->dadosBancarios->iban }}</div>
                            <div class="banco-info">{{ $item->criador->dadosBancarios->nome_banco }} · {{ $item->criador->dadosBancarios->titular }}</div>
                            @else
                            <span style="font-size:11px;color:var(--red);">⚠ Sem dados bancários</span>
                            @endif
                        </td>
                        <td style="font-family:var(--mono);">{{ $item->num_reservas }}</td>
                        <td style="font-family:var(--mono);color:var(--amber);font-weight:700;">
                            {{ number_format($item->total_devido,0,',','.') }} Kz
                        </td>
                        <td>
                            <button onclick="abrirModalPagar({{ $item->criador_id }}, '{{ addslashes($item->criador->name) }}', '{{ number_format($item->total_devido,0,',','.') }}')"
                                    class="btn primary" style="font-size:11px;padding:6px 12px;">
                                💸 Marcar como pago
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t3);">Sem pagamentos pendentes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- HISTORIAL RECENTE --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Histórico Recente</div>
            <span style="font-size:10px;color:var(--t2);">{{ $saldosPendentes->total() }} registos</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Reserva</th>
                        <th>Criador</th>
                        <th>Evento</th>
                        <th>Total</th>
                        <th>Admin (10%)</th>
                        <th>Criador (90%)</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saldosPendentes as $s)
                    <tr>
                        <td style="font-family:var(--mono);font-size:11px;">#{{ $s->reserva_id }}</td>
                        <td>{{ $s->criador->name }}</td>
                        <td style="font-size:12px;">{{ Str::limit($s->evento->titulo,25) }}</td>
                        <td style="font-family:var(--mono);">{{ number_format($s->valor_total,0,',','.') }} Kz</td>
                        <td style="font-family:var(--mono);color:var(--sky);">{{ number_format($s->valor_admin,0,',','.') }} Kz</td>
                        <td style="font-family:var(--mono);color:var(--amber);">{{ number_format($s->valor_criador,0,',','.') }} Kz</td>
                        <td>
                            <span class="badge {{ $s->estado === 'pago' ? 'badge-pago' : 'badge-pend' }}">
                                {{ $s->estado === 'pago' ? '✅ Pago' : '⏳ Pendente' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--t3);">Sem registos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($saldosPendentes->hasPages())
        <div style="padding:12px 18px;">{{ $saldosPendentes->links() }}</div>
        @endif
    </div>
</div>

{{-- MODAL MARCAR PAGO --}}
<div class="modal-overlay" id="modal-pagar" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <div class="modal-title">💸 Registar Pagamento</div>
        <div class="modal-sub" id="modal-pagar-sub">A pagar ao criador...</div>
        <form method="POST" id="form-pagar" action="">
            @csrf
            <label class="form-label">Referência da transferência (opcional)</label>
            <input type="text" name="referencia" class="form-input" placeholder="Ex: REF-2026-001">
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="btn" onclick="document.getElementById('modal-pagar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn primary">✅ Confirmar Pagamento</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPagar(criadorId, nome, total) {
    document.getElementById('modal-pagar-sub').textContent = nome + ' — ' + total + ' Kz';
    document.getElementById('form-pagar').action = '/admin/saldos/' + criadorId + '/pagar';
    document.getElementById('modal-pagar').classList.add('open');
}
</script>
@endsection