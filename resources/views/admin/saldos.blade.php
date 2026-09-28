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

/* ══════════════════════════════════════════════
   NOVAS MELHORIAS
   ══════════════════════════════════════════════ */

/* stats responsivo */
@media(max-width:640px){ .stats{grid-template-columns:1fr 1fr;} }

/* mensagens de erro agora tratadas pelo toast global do app.blade.php — nada a fazer aqui */

/* pesquisa + exportar */
.tools-row{display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;}
.search-box{flex:1;min-width:180px;display:flex;align-items:center;gap:8px;background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:8px 12px;}
.search-box input{flex:1;background:none;border:none;outline:none;color:var(--t1);font-size:12px;font-family:inherit;}
.exp-btn{padding:8px 13px;border-radius:10px;font-size:11.5px;font-weight:700;border:1px solid var(--b2);background:var(--s2);color:var(--sky);cursor:pointer;font-family:inherit;}
.empty-filtered{display:none;text-align:center;padding:20px;color:var(--t3);font-size:12px;}
.empty-filtered.show{display:block;}

/* botão desativado sem dados bancários */
.btn.disabled{opacity:.5;cursor:not-allowed;background:var(--s2);color:var(--t3);border:1px solid var(--b2);}

/* valor destacado no modal */
.valor-destaque{background:rgba(56,189,248,.08);border:1.5px solid var(--b2);border-radius:14px;padding:14px 16px;margin-bottom:16px;text-align:center;}
.valor-destaque-lbl{font-size:10px;font-weight:700;color:var(--t2);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;}
.valor-destaque-val{font-family:var(--mono);font-size:26px;font-weight:700;color:var(--sky);line-height:1;}
.valor-destaque-nome{font-size:12px;color:var(--t2);margin-top:6px;}

/* IBAN com copiar, dentro do modal */
.modal-iban-row{display:flex;align-items:center;gap:8px;background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:10px 12px;margin-bottom:14px;}
.modal-iban-val{font-family:var(--mono);font-size:12px;color:var(--sky);flex:1;word-break:break-all;}
.copy-btn{background:var(--b1);border:1px solid var(--b2);color:var(--sky);border-radius:7px;padding:5px 9px;font-size:10px;font-weight:700;cursor:pointer;flex-shrink:0;}
.copy-btn.copied{background:rgba(16,185,129,.15);border-color:rgba(16,185,129,.4);color:var(--green);}

/* aviso + checkbox de confirmação real antes de marcar como pago */
.confirm-box{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:12px;padding:12px 14px;margin-bottom:16px;}
.confirm-box-title{font-size:12px;font-weight:800;color:var(--amber);margin-bottom:4px;}
.confirm-box-text{font-size:11.5px;color:var(--t2);line-height:1.5;}
.confirm-checkbox-row{display:flex;align-items:flex-start;gap:8px;margin-top:10px;}
.confirm-checkbox-row input{margin-top:2px;}
.confirm-checkbox-row label{font-size:11.5px;color:var(--t1);line-height:1.4;}

/* loading no botão de confirmar */
.btn.loading{opacity:.7;pointer-events:none;}
.mini-spin{display:none;width:11px;height:11px;border-radius:50%;border:2px solid rgba(0,0,0,.25);border-top-color:#000;animation:spin .6s linear infinite;margin-right:4px;}
.btn.loading .mini-spin{display:inline-block;}
@keyframes spin{to{transform:rotate(360deg)}}

.char-count{font-size:10px;color:var(--t3);text-align:right;margin-top:-10px;margin-bottom:14px;}
</style>

<div class="wrap">
    <div class="topbar">
        <div class="page-title">💰 Pagamentos a Criadores</div>
        <div style="display:flex;gap:8px;">
            <a href="{{ route('admin.contas-bancarias') }}" class="btn">🏦 Gerir Contas Bancárias</a>
            <a href="{{ route('admin.dashboard') }}" class="btn">← Dashboard</a>
        </div>
    </div>

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
        <div class="panel-body" style="padding-bottom:0;">
            <div class="tools-row">
                <div class="search-box">
                    🔍 <input type="text" id="criadorSearch" placeholder="Buscar por nome do criador..." oninput="filtrarCriadores()">
                </div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table id="criadoresTable">
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
                    <tr data-nome="{{ strtolower($item->criador->name) }}">
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
                            {{-- botão desativado quando não há dados bancários — não faz sentido "marcar como pago" uma transferência para uma conta que não existe --}}
                            @if($item->criador->dadosBancarios)
                            <button onclick="abrirModalPagar({{ $item->criador_id }}, '{{ addslashes($item->criador->name) }}', '{{ number_format($item->total_devido,0,',','.') }}', '{{ addslashes($item->criador->dadosBancarios->iban) }}', '{{ addslashes($item->criador->dadosBancarios->nome_banco) }}')"
                                    class="btn primary" style="font-size:11px;padding:6px 12px;">
                                💸 Marcar como pago
                            </button>
                            @else
                            <button type="button" class="btn disabled" style="font-size:11px;padding:6px 12px;"
                                    onclick="alert('Este criador não tem dados bancários registados — não é possível confirmar um pagamento sem saber para onde foi transferido.')">
                                💸 Marcar como pago
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--t3);">Sem pagamentos pendentes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="empty-filtered" id="criadoresEmptyFiltered">🔍 Nenhum criador encontrado.</div>
        </div>
    </div>

    {{-- HISTORIAL RECENTE --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Histórico Recente</div>
            <span style="font-size:10px;color:var(--t2);">{{ $saldosPendentes->total() }} registos</span>
        </div>
        <div class="panel-body" style="padding-bottom:0;">
            <div class="tools-row">
                <button type="button" class="exp-btn" onclick="exportarHistoricoCsv()">⬇ Exportar CSV (visíveis)</button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table id="historicoTable">
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
        <div style="padding:12px 18px;">{{ $saldosPendentes->links('paginacao.tema') }}</div>
        @endif
    </div>
</div>

{{-- MODAL MARCAR PAGO --}}
<div class="modal-overlay" id="modal-pagar" onclick="if(event.target===this)fecharModalPagar()">
    <div class="modal-box">
        <div class="modal-title">💸 Registar Pagamento</div>

        {{-- valor bem destacado — é a informação mais importante a conferir --}}
        <div class="valor-destaque">
            <div class="valor-destaque-lbl">Valor a transferir</div>
            <div class="valor-destaque-val" id="modal-valor">—</div>
            <div class="valor-destaque-nome">para <span id="modal-nome">—</span></div>
        </div>

        <div class="modal-iban-row">
            <span class="modal-iban-val" id="modal-iban">—</span>
            <button type="button" class="copy-btn" onclick="copiarIbanModal(this)">📋 Copiar</button>
        </div>

        <div class="confirm-box">
            <div class="confirm-box-title">⚠️ Antes de continuares</div>
            <div class="confirm-box-text">Isto só regista que o pagamento foi feito — <b>não transfere dinheiro nenhum</b>. Confirma que já concluíste a transferência bancária real antes de marcares como pago.</div>
            <div class="confirm-checkbox-row">
                <input type="checkbox" id="confirmPagoCheck" onchange="document.getElementById('btnConfirmarPagamento').disabled = !this.checked">
                <label for="confirmPagoCheck">Já efetuei a transferência bancária para esta conta</label>
            </div>
        </div>

        <form method="POST" id="form-pagar" action="">
            @csrf
            <label class="form-label">Referência da transferência (opcional)</label>
            <input type="text" name="referencia" id="referenciaInput" class="form-input" placeholder="Ex: REF-2026-001" maxlength="100" oninput="atualizarContadorRef()">
            <div class="char-count"><span id="refCount">0</span>/100</div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="btn" onclick="fecharModalPagar()">Cancelar</button>
                <button type="submit" class="btn primary" id="btnConfirmarPagamento" disabled>
                    <span class="mini-spin"></span>✅ Confirmar Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalPagar(criadorId, nome, total, iban, banco) {
    document.getElementById('modal-valor').textContent = total + ' Kz';
    document.getElementById('modal-nome').textContent = nome;
    document.getElementById('modal-iban').textContent = (banco ? banco + ' · ' : '') + iban;
    document.getElementById('form-pagar').action = '/admin/saldos/' + criadorId + '/pagar';
    document.getElementById('confirmPagoCheck').checked = false;
    document.getElementById('btnConfirmarPagamento').disabled = true;
    document.getElementById('referenciaInput').value = '';
    document.getElementById('refCount').textContent = '0';
    document.getElementById('modal-pagar').classList.add('open');
}
function fecharModalPagar() {
    document.getElementById('modal-pagar').classList.remove('open');
}
function copiarIbanModal(btn) {
    const texto = document.getElementById('modal-iban').textContent;
    navigator.clipboard.writeText(texto.includes('·') ? texto.split('·')[1].trim() : texto);
    const original = btn.textContent;
    btn.textContent = '✅ Copiado!';
    btn.classList.add('copied');
    setTimeout(() => { btn.textContent = original; btn.classList.remove('copied'); }, 1800);
}
function atualizarContadorRef() {
    document.getElementById('refCount').textContent = document.getElementById('referenciaInput').value.length;
}

// protege contra duplo clique — o formulário continua a submeter normalmente,
// só mostra um estado de carregamento enquanto o servidor responde
document.getElementById('form-pagar').addEventListener('submit', function () {
    const btn = document.getElementById('btnConfirmarPagamento');
    btn.classList.add('loading');
    btn.disabled = true;
});

// pesquisa por criador (filtra a página atual carregada)
function filtrarCriadores() {
    const q = document.getElementById('criadorSearch').value.toLowerCase();
    let visiveis = 0;
    document.querySelectorAll('#criadoresTable tbody tr').forEach(tr => {
        if (!tr.dataset.nome) return;
        const visivel = tr.dataset.nome.includes(q);
        tr.style.display = visivel ? '' : 'none';
        if (visivel) visiveis++;
    });
    document.getElementById('criadoresEmptyFiltered').classList.toggle('show', visiveis === 0);
}

// exportar histórico visível para CSV
function exportarHistoricoCsv() {
    const linhas = ['Reserva,Criador,Evento,Total,Admin,Criador,Estado'];
    document.querySelectorAll('#historicoTable tbody tr').forEach(tr => {
        const cols = tr.querySelectorAll('td');
        if (cols.length < 7) return;
        const vals = Array.from(cols).map(td => td.textContent.trim().replace(/"/g, "'"));
        linhas.push(vals.map(v => `"${v}"`).join(','));
    });
    const blob = new Blob([linhas.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'pagamentos_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
}
</script>
@endsection