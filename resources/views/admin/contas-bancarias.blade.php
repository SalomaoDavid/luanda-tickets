@extends('layouts.app')
@section('title', 'Contas Bancárias')
@section('content')
<style>
:root{--bg:#06091a;--s1:#0c1228;--s2:#111830;--sky:#38bdf8;--green:#10b981;--red:#f43f5e;--b1:rgba(56,189,248,.10);--b2:rgba(56,189,248,.20);--t1:#fff;--t2:#c8d8f0;--t3:#7a90b0;--mono:'Space Mono',monospace;}
body{background:var(--bg);color:var(--t1);font-family:'Outfit',sans-serif;}
.wrap{padding:20px 8px 80px;max-width:800px;margin:0 auto;}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;}
.page-title{font-family:var(--mono);font-size:14px;font-weight:700;color:#38bdf8;letter-spacing:.1em;}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;cursor:pointer;transition:all .2s;font-family:inherit;}
.btn:hover{border-color:var(--sky);color:var(--t1);}
.btn.primary{background:linear-gradient(135deg,var(--sky),#0ea5e9);border:none;color:#000;}
.btn.danger{background:rgba(244,63,94,.1);border-color:rgba(244,63,94,.3);color:var(--red);}
.panel{background:var(--s1);border:1px solid var(--b2);border-radius:16px;overflow:hidden;margin-bottom:16px;}
.panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--b1);}
.panel-title{font-family:var(--mono);font-size:12px;font-weight:700;color:var(--t1);text-transform:uppercase;letter-spacing:.06em;}
.panel-body{padding:18px;}
.form-group{margin-bottom:13px;}
.form-label{display:block;font-size:10px;font-weight:700;color:var(--t2);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;}
.form-input{width:100%;background:var(--s2);border:1px solid var(--b2);border-radius:10px;padding:10px 12px;font-size:13px;color:var(--t1);outline:none;font-family:inherit;transition:border-color .15s;}
.form-input:focus{border-color:var(--sky);}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
/* Conta card */
.conta-card{display:flex;align-items:center;gap:14px;padding:14px;background:var(--s2);border:1px solid var(--b1);border-radius:12px;margin-bottom:10px;}
.conta-card:last-child{margin-bottom:0;}
.conta-logo{width:44px;height:44px;border-radius:10px;object-fit:contain;background:#fff;padding:4px;flex-shrink:0;}
.conta-logo-ph{width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#0c1228,#1e3a5f);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.conta-nome{font-size:14px;font-weight:700;color:var(--t1);}
.conta-titular{font-size:11px;color:var(--t2);}
.conta-iban{font-family:var(--mono);font-size:11px;color:var(--sky);margin-top:2px;}
.conta-actions{margin-left:auto;display:flex;gap:6px;flex-shrink:0;}
.badge-activa{font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:var(--green);}
.badge-inactiva{font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;background:rgba(100,116,139,.12);border:1px solid rgba(100,116,139,.25);color:var(--t3);}
/* Modal editar */
.modal-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.8);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:16px;}
.modal-overlay.open{display:flex;}
.modal-box{background:#0c1220;border:1px solid var(--b2);border-radius:18px;padding:24px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto;}
.modal-title{font-size:16px;font-weight:800;color:var(--t1);margin-bottom:18px;}

/* ══════════════════════════════════════════════
   NOVAS MELHORIAS
   ══════════════════════════════════════════════ */
@media(max-width:640px){ .g2{grid-template-columns:1fr;} }

/* mensagens de erro agora tratadas pelo toast global do app.blade.php — nada a fazer aqui */

/* contador de contas ativas + aviso de última conta */
.active-counter{font-size:12px;color:var(--t2);margin-bottom:12px;font-family:var(--mono);}
.active-counter b{color:var(--green);}
.warn-last-active{display:none;background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.35);border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#fda4af;line-height:1.5;}
.warn-last-active.show{display:block;}

/* copiar IBAN em cada cartão */
.copy-btn-sm{background:rgba(56,189,248,.1);border:1px solid var(--b2);color:var(--sky);border-radius:6px;padding:2px 7px;font-size:9px;font-weight:700;cursor:pointer;margin-left:6px;font-family:inherit;}
.copy-btn-sm.copied{background:rgba(16,185,129,.15);border-color:rgba(16,185,129,.4);color:var(--green);}

/* preview do logo antes de submeter */
.logo-preview{display:none;margin-top:8px;width:52px;height:52px;border-radius:10px;object-fit:contain;background:#fff;padding:4px;}
.logo-preview.show{display:block;}

/* dica de formato do IBAN */
.field-hint{font-size:10px;color:var(--t3);margin-top:4px;}
.field-hint.warn{color:var(--amber);}
</style>

<div class="wrap">
    <div class="topbar">
        <div class="page-title">🏦 Contas Bancárias</div>
        <div style="display:flex;gap:8px;">
            <button class="btn primary" onclick="document.getElementById('modal-add').classList.add('open')">+ Adicionar Conta</button>
            <a href="{{ route('admin.saldos') }}" class="btn">← Pagamentos</a>
        </div>
    </div>

    @php $numAtivas = $contas->where('activa', true)->count(); @endphp
    <div class="active-counter">👁️ <b>{{ $numAtivas }}</b> conta(s) visível(eis) no modal de pagamento dos compradores agora</div>
    <div class="warn-last-active" id="warnUltimaConta">
        ⚠️ <b>Esta é a última conta ativa!</b> Se a eliminares ou desativares, os compradores deixam de ter forma nenhuma de pagar bilhetes até ativares outra conta.
    </div>

    {{-- LISTA DE CONTAS --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Contas Disponíveis ({{ $contas->count() }})</div>
        </div>
        <div class="panel-body">
            @forelse($contas as $conta)
            <div class="conta-card" data-id="{{ $conta->id }}" data-activa="{{ $conta->activa ? '1' : '0' }}">
                @if($conta->logo)
                    <img src="{{ asset('images/bancos/'.$conta->logo) }}" class="conta-logo" alt="{{ $conta->nome_banco }}">
                @else
                    <div class="conta-logo-ph">🏦</div>
                @endif
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="conta-nome">{{ $conta->nome_banco }}</div>
                        <span class="{{ $conta->activa ? 'badge-activa' : 'badge-inactiva' }}">
                            {{ $conta->activa ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div class="conta-titular">{{ $conta->titular }}</div>
                    <div class="conta-iban">
                        {{ $conta->iban }}
                        <button type="button" class="copy-btn-sm" onclick="copiarIbanConta(this, '{{ $conta->iban }}')">📋</button>
                    </div>
                    @if($conta->numero_conta)
                    <div style="font-size:11px;color:var(--t3);">Conta: {{ $conta->numero_conta }}</div>
                    @endif
                </div>
                <div class="conta-actions">
                    <button class="btn" style="font-size:11px;padding:5px 10px;"
                            onclick="abrirEditar({{ $conta->id }}, '{{ addslashes($conta->nome_banco) }}', '{{ addslashes($conta->titular) }}', '{{ $conta->iban }}', '{{ $conta->numero_conta }}', {{ $conta->activa ? 'true' : 'false' }}, {{ $conta->ordem }})">
                        ✏️
                    </button>
                    <form method="POST" action="{{ route('admin.contas-bancarias.destroy', $conta->id) }}"
                          onsubmit="return confirmarEliminarConta(event, {{ $conta->activa ? 'true' : 'false' }}, '{{ addslashes($conta->nome_banco) }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn danger" style="font-size:11px;padding:5px 10px;">🗑</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--t3);font-size:13px;">
                Nenhuma conta bancária registada ainda.
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL ADICIONAR --}}
<div class="modal-overlay" id="modal-add" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <div class="modal-title">🏦 Adicionar Conta Bancária</div>
        <form method="POST" action="{{ route('admin.contas-bancarias.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Nome do Banco</label>
                    <input type="text" name="nome_banco" class="form-input" placeholder="Ex: BFA" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Titular</label>
                    <input type="text" name="titular" class="form-input" placeholder="Nome do titular" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">IBAN</label>
                <input type="text" name="iban" class="form-input" placeholder="AO06 0006 0000 0000 0000 1014 3" pattern="^AO\d{2}[\d\s]{20,25}$" required>
                <div class="field-hint">Formato angolano: começa por "AO" + 23 dígitos</div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Número de Conta</label>
                    <input type="text" name="numero_conta" class="form-input" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label class="form-label">Ordem de exibição</label>
                    <input type="number" name="ordem" class="form-input" value="0" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Logo do banco (PNG/JPG · máx 512KB)</label>
                <input type="file" name="logo" class="form-input" accept="image/png,image/jpg,image/webp" onchange="previewLogo(this, 'preview-add')">
                <img class="logo-preview" id="preview-add" alt="Pré-visualização do logo">
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px;">
                <button type="button" class="btn" onclick="document.getElementById('modal-add').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn primary">+ Adicionar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal-overlay" id="modal-editar" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <div class="modal-title">✏️ Editar Conta Bancária</div>
        <form method="POST" id="form-editar" action="" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Nome do Banco</label>
                    <input type="text" name="nome_banco" id="edit-nome" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Titular</label>
                    <input type="text" name="titular" id="edit-titular" class="form-input" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">IBAN</label>
                <input type="text" name="iban" id="edit-iban" class="form-input" pattern="^AO\d{2}[\d\s]{20,25}$" required>
                <div class="field-hint">Formato angolano: começa por "AO" + 23 dígitos</div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Número de Conta</label>
                    <input type="text" name="numero_conta" id="edit-conta" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Ordem</label>
                    <input type="number" name="ordem" id="edit-ordem" class="form-input" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Novo Logo (opcional)</label>
                <input type="file" name="logo" class="form-input" accept="image/png,image/jpg,image/webp" onchange="previewLogo(this, 'preview-edit')">
                <img class="logo-preview" id="preview-edit" alt="Pré-visualização do logo">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" name="activa" id="edit-activa" value="1" style="width:16px;height:16px;" onchange="avisarSeUltimaAtiva(this)">
                <label for="edit-activa" style="font-size:13px;color:var(--t2);cursor:pointer;">Conta activa (visível no modal de pagamento)</label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:6px;">
                <button type="button" class="btn" onclick="document.getElementById('modal-editar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn primary">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
// Rota de edição — usa route() do Blade com id "0" como marcador e troca-se pelo id real,
// em vez de adivinhar o caminho completo à mão (a store/destroy já usavam route(), esta convenção segue a mesma)
const EDIT_URL_TEMPLATE = "{{ route('admin.contas-bancarias.update', 0) }}";

function abrirEditar(id, nome, titular, iban, conta, activa, ordem) {
    document.getElementById('form-editar').action = EDIT_URL_TEMPLATE.replace(/\/0$/, '/' + id);
    document.getElementById('edit-nome').value    = nome;
    document.getElementById('edit-titular').value = titular;
    document.getElementById('edit-iban').value    = iban;
    document.getElementById('edit-conta').value   = conta || '';
    document.getElementById('edit-ordem').value   = ordem;
    document.getElementById('edit-activa').checked = activa;
    document.getElementById('preview-edit').classList.remove('show');
    document.getElementById('modal-editar').classList.add('open');
}

// pré-visualização do logo antes de submeter (adicionar e editar)
function previewLogo(input, previewId) {
    const file = input.files[0];
    const img = document.getElementById(previewId);
    if (!file) { img.classList.remove('show'); return; }
    const reader = new FileReader();
    reader.onload = (e) => { img.src = e.target.result; img.classList.add('show'); };
    reader.readAsDataURL(file);
}

// avisa se estás a desativar a última conta ativa (o modal de pagamento ficaria sem nenhuma)
function contarContasAtivas() {
    return document.querySelectorAll('.conta-card[data-activa="1"]').length;
}
function avisarSeUltimaAtiva(checkbox) {
    const warnBox = document.getElementById('warnUltimaConta');
    if (!checkbox.checked && contarContasAtivas() <= 1) {
        warnBox.classList.add('show');
    } else {
        warnBox.classList.remove('show');
    }
}

// confirmação ao eliminar — reforça o aviso se for a última conta ativa
function confirmarEliminarConta(event, ehAtiva, nomeBanco) {
    if (ehAtiva && contarContasAtivas() <= 1) {
        return confirm('⚠️ "' + nomeBanco + '" é a ÚLTIMA conta ativa!\n\nSe a eliminares, os compradores deixam de ter forma nenhuma de pagar bilhetes até ativares outra conta.\n\nEliminar mesmo assim?');
    }
    return confirm('Eliminar a conta "' + nomeBanco + '"?');
}

// copiar IBAN de cada cartão
function copiarIbanConta(btn, iban) {
    navigator.clipboard.writeText(iban);
    const original = btn.textContent;
    btn.textContent = '✅';
    btn.classList.add('copied');
    setTimeout(() => { btn.textContent = original; btn.classList.remove('copied'); }, 1500);
}
</script>
@endsection