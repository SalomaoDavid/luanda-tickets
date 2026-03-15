@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<style>
main { max-width: 100% !important; padding: 0 !important; }
:root {
    --bg:#0d0d12;--s1:#131318;--s2:#1a1a22;--s3:#22222c;
    --b1:#ffffff0a;--b2:#ffffff16;--b3:#ffffff22;
    --acc:#a78bfa;--acc2:#7c3aed;--acc-bg:#a78bfa14;
    --green:#34d399;--red:#f87171;--amber:#facc15;
    --t1:#f0ecff;--t2:#9b92b3;--t3:#5a5270;
}
*{box-sizing:border-box;margin:0;padding:0;}
.editar-page{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--t1);}
.topbar{position:sticky;top:0;z-index:50;display:flex;align-items:center;gap:16px;padding:14px 32px;background:var(--s1);border-bottom:1px solid var(--b2);}
.logo{display:flex;align-items:center;gap:9px;}
.logo-k{width:28px;height:28px;border-radius:8px;background:var(--acc2);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:800;font-size:14px;color:#fff;}
.logo-n{font-family:'Syne',sans-serif;font-weight:700;font-size:15px;color:var(--t1);}
.tb-div{width:1px;height:16px;background:var(--b3);}
.back-link{font-size:13px;color:var(--t3);text-decoration:none;transition:color .15s;}
.back-link:hover{color:var(--t2);}
.page-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--t1);}
.tb-actions{margin-left:auto;display:flex;align-items:center;gap:8px;}
.btn-ghost{padding:7px 16px;border-radius:9px;border:1px solid var(--b2);background:transparent;font-size:13px;color:var(--t2);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;}
.btn-ghost:hover{background:var(--s2);color:var(--t1);}
.btn-save{padding:7px 20px;border-radius:9px;background:var(--green);border:none;font-size:13px;font-weight:600;color:#052e16;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:6px;transition:opacity .15s;}
.btn-save:hover{opacity:.85;}
.editar-layout{display:flex;align-items:flex-start;padding:32px;}
.form-col{flex:1;min-width:0;max-width:700px;}
.aside-col{width:260px;flex-shrink:0;padding-left:24px;position:sticky;top:80px;}
.form-card{background:var(--s1);border:1px solid var(--b2);border-radius:16px;padding:26px;margin-bottom:18px;}
.form-card:last-child{margin-bottom:0;}
.card-head{display:flex;align-items:flex-start;gap:14px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid var(--b1);}
.card-icon{width:36px;height:36px;border-radius:10px;background:var(--acc-bg);border:1px solid var(--acc2);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;}
.card-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:var(--t1);margin-bottom:2px;}
.card-sub{font-size:12.5px;color:var(--t3);}
.field{margin-bottom:18px;}
.field:last-child{margin-bottom:0;}
.field label{display:block;font-size:11px;font-weight:700;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;margin-bottom:7px;}
.req{color:var(--acc);}
.field input[type=text],
.field input[type=date],
.field input[type=time],
.field input[type=number],
.field input[type=url],
.field textarea,
.field select{width:100%;padding:10px 13px;background:var(--s2);border:1px solid var(--b2);border-radius:10px;font-size:13.5px;color:var(--t1);font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s,background .15s;-webkit-appearance:none;appearance:none;}
.field input:focus,.field textarea:focus,.field select:focus{border-color:var(--acc);background:var(--s3);}
.field input::placeholder,.field textarea::placeholder{color:var(--t3);}
.field select option{background:var(--s2);}
.field textarea{resize:vertical;min-height:95px;line-height:1.7;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.error-msg{font-size:12px;color:var(--red);margin-top:5px;}
.alert-error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);color:var(--red);border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;}
.alert-success{background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.3);color:var(--green);border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;}
.tog-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--b1);}
.tog-row:last-child{border-bottom:none;padding-bottom:0;}
.tog-info .tog-lbl{font-size:13.5px;color:var(--t1);}
.tog-info .tog-desc{font-size:12px;color:var(--t3);margin-top:2px;}
.tog-btn{width:36px;height:20px;border-radius:999px;background:var(--t3);border:none;cursor:pointer;position:relative;flex-shrink:0;transition:background .2s;margin-left:16px;}
.tog-btn.on{background:var(--acc);}
.tog-btn::after{content:'';width:14px;height:14px;border-radius:50%;background:#fff;position:absolute;top:3px;left:3px;transition:left .18s;}
.tog-btn.on::after{left:19px;}
.status-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:9px;}
.status-tile{padding:13px 10px;border-radius:10px;border:1px solid var(--b2);background:var(--s2);cursor:pointer;text-align:center;transition:all .18s;}
.status-tile:hover{border-color:var(--b3);}
.status-tile.sel-rascunho{border-color:var(--amber);background:rgba(250,204,21,.08);}
.status-tile.sel-publicado{border-color:var(--green);background:rgba(52,211,153,.08);}
.status-tile.sel-encerrado{border-color:var(--red);background:rgba(248,113,113,.08);}
.status-em{font-size:20px;display:block;margin-bottom:5px;}
.status-nm{font-size:12px;font-weight:600;font-family:'Syne',sans-serif;}
.aside-lbl{font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:11px;}
.info-card{background:var(--s1);border:1px solid var(--b2);border-radius:15px;padding:18px;margin-bottom:14px;}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--b1);font-size:13px;}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-label{color:var(--t3);}
.info-val{color:var(--t1);font-weight:500;text-align:right;}
.danger-card{background:rgba(248,113,113,.06);border:1px solid rgba(248,113,113,.2);border-radius:15px;padding:18px;}
.danger-title{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--red);margin-bottom:8px;}
.danger-sub{font-size:12px;color:var(--t3);margin-bottom:14px;line-height:1.55;}
.btn-danger{width:100%;padding:9px;border-radius:9px;background:transparent;border:1px solid rgba(248,113,113,.4);color:var(--red);font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;}
.btn-danger:hover{background:rgba(248,113,113,.1);}
</style>

<div class="editar-page">

    <div class="topbar">
        <div class="logo">
            <div class="logo-k">LT</div>
            <span class="logo-n">Luanda Tickets</span>
        </div>
        <div class="tb-div"></div>
        <a href="{{ route('admin.eventos') }}" class="back-link">← Eventos</a>
        <div class="tb-div"></div>
        <span class="page-title">Editar evento</span>
        <div class="tb-actions">
            <a href="{{ route('admin.eventos') }}" class="btn-ghost">Cancelar</a>
            <button type="submit" form="form-editar" class="btn-save">💾 Guardar alterações</button>
        </div>
    </div>

    <div class="editar-layout">
        <div class="form-col">

            @if($errors->any())
                <div class="alert-error">
                    <strong>Corrige os seguintes erros:</strong>
                    <ul style="margin-top:6px;padding-left:16px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form id="form-editar"
                  method="POST"
                  action="{{ route('admin.eventos.atualizar', $evento->id) }}">
                @csrf
                @method('PUT')

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">📝</div>
                        <div><div class="card-title">Informações básicas</div><div class="card-sub">Dados principais do evento</div></div>
                    </div>
                    <div class="field">
                        <label>Nome do evento <span class="req">*</span></label>
                        <input type="text" name="titulo" required maxlength="255"
                               value="{{ old('titulo', $evento->titulo) }}"
                               placeholder="Nome do evento">
                        @error('titulo')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Descrição <span class="req">*</span></label>
                        <textarea name="descricao" rows="4" required
                                  placeholder="Descrição do evento">{{ old('descricao', $evento->descricao) }}</textarea>
                        @error('descricao')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Link externo</label>
                        <input type="url" name="link_externo"
                               value="{{ old('link_externo', $evento->link_externo) }}"
                               placeholder="https://...">
                    </div>
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">📅</div>
                        <div><div class="card-title">Data e hora</div><div class="card-sub">Quando é que o evento acontece?</div></div>
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>Data de início <span class="req">*</span></label>
                            <input type="date" name="data_evento" required
                                   value="{{ old('data_evento', \Carbon\Carbon::parse($evento->data_evento)->format('Y-m-d')) }}">
                            @error('data_evento')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Data de fim</label>
                            <input type="date" name="data_fim"
                                   value="{{ old('data_fim', $evento->data_fim ? \Carbon\Carbon::parse($evento->data_fim)->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>Hora de início <span class="req">*</span></label>
                            <input type="time" name="hora_inicio" required
                                   value="{{ old('hora_inicio', $evento->hora_inicio) }}">
                            @error('hora_inicio')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Hora de fim</label>
                            <input type="time" name="hora_fim"
                                   value="{{ old('hora_fim', $evento->hora_fim) }}">
                        </div>
                    </div>
                    <div class="tog-row">
                        <div class="tog-info">
                            <div class="tog-lbl">Múltiplos dias</div>
                            <div class="tog-desc">Cada dia com programação separada</div>
                        </div>
                        <button type="button"
                                class="tog-btn {{ old('multiplos_dias', $evento->multiplos_dias) ? 'on' : '' }}"
                                onclick="toggleField(this,'multiplos_dias')"></button>
                        <input type="hidden" name="multiplos_dias" id="multiplos_dias"
                               value="{{ old('multiplos_dias', $evento->multiplos_dias ? 1 : 0) }}">
                    </div>
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">📍</div>
                        <div><div class="card-title">Localização</div><div class="card-sub">Onde vai acontecer o evento?</div></div>
                    </div>
                    <div class="field">
                        <label>Nome do local <span class="req">*</span></label>
                        <input type="text" name="localizacao" required
                               value="{{ old('localizacao', $evento->localizacao) }}"
                               placeholder="Ex: Cine Karl Marx">
                        @error('localizacao')<div class="error-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>Município</label>
                            <select name="municipio">
                                @foreach(['Luanda','Talatona','Viana','Cacuaco','Cazenga','Kilamba Kiaxi','Belas','Icolo e Bengo'] as $m)
                                    <option value="{{ $m }}" {{ old('municipio', $evento->municipio) === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Província</label>
                            <select name="provincia">
                                @foreach(['Luanda','Benguela','Huíla','Huambo','Cabinda','Namibe','Malanje','Uíge'] as $p)
                                    <option value="{{ $p }}" {{ old('provincia', $evento->provincia) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="tog-row">
                        <div class="tog-info">
                            <div class="tog-lbl">Evento online</div>
                            <div class="tog-desc">Realizado via streaming ou plataforma virtual</div>
                        </div>
                        <button type="button"
                                class="tog-btn {{ old('online', $evento->online) ? 'on' : '' }}"
                                onclick="toggleField(this,'online')"></button>
                        <input type="hidden" name="online" id="online"
                               value="{{ old('online', $evento->online ? 1 : 0) }}">
                    </div>
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">📊</div>
                        <div><div class="card-title">Capacidade</div><div class="card-sub">Limites gerais de participação</div></div>
                    </div>
                    <div class="grid2">
                        <div class="field">
                            <label>Lotação máxima <span class="req">*</span></label>
                            <input type="number" name="lotacao_maxima" required min="1"
                                   value="{{ old('lotacao_maxima', $evento->lotacao_maxima) }}"
                                   placeholder="500">
                            @error('lotacao_maxima')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Ingressos por pessoa</label>
                            <input type="number" name="ingressos_por_pessoa" min="1" max="10"
                                   value="{{ old('ingressos_por_pessoa', $evento->ingressos_por_pessoa ?? 1) }}">
                        </div>
                    </div>
                    <div class="tog-row">
                        <div class="tog-info">
                            <div class="tog-lbl">Lista de espera</div>
                            <div class="tog-desc">Aceitar inscrições após esgotamento</div>
                        </div>
                        <button type="button"
                                class="tog-btn {{ old('lista_espera', $evento->lista_espera) ? 'on' : '' }}"
                                onclick="toggleField(this,'lista_espera')"></button>
                        <input type="hidden" name="lista_espera" id="lista_espera"
                               value="{{ old('lista_espera', $evento->lista_espera ? 1 : 0) }}">
                    </div>
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">🔒</div>
                        <div><div class="card-title">Visibilidade e acesso</div><div class="card-sub">Controla quem vê e participa</div></div>
                    </div>
                    @foreach([
                        ['privado',               'Evento privado',                 'Visível apenas com link direto',          false],
                        ['aprovacao_manual',       'Aprovação manual de inscrições', 'Confirmas cada pedido manualmente',      false],
                        ['permitir_comentarios',   'Permitir comentários',           'Participantes podem comentar na página', true],
                        ['participantes_publicos', 'Lista de participantes pública', 'Outros utilizadores podem ver quem vai', true],
                    ] as [$field, $label, $desc, $default])
                        <div class="tog-row">
                            <div class="tog-info">
                                <div class="tog-lbl">{{ $label }}</div>
                                <div class="tog-desc">{{ $desc }}</div>
                            </div>
                            <button type="button"
                                    class="tog-btn {{ old($field, $evento->$field ?? $default) ? 'on' : '' }}"
                                    onclick="toggleField(this,'{{ $field }}')"></button>
                            <input type="hidden" name="{{ $field }}" id="{{ $field }}"
                                   value="{{ old($field, ($evento->$field ?? $default) ? 1 : 0) }}">
                        </div>
                    @endforeach
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">🔔</div>
                        <div><div class="card-title">Notificações</div><div class="card-sub">Alertas sobre o evento</div></div>
                    </div>
                    @foreach([
                        ['notif_nova_inscricao', 'Nova inscrição',     'Alerta quando alguém se inscrever',  true],
                        ['notif_lembrete_24h',   'Lembrete 24h antes', 'Envio automático aos participantes', true],
                        ['notif_resumo_semanal', 'Resumo semanal',     'Relatório de inscrições por semana', false],
                    ] as [$field, $label, $desc, $default])
                        <div class="tog-row">
                            <div class="tog-info">
                                <div class="tog-lbl">{{ $label }}</div>
                                <div class="tog-desc">{{ $desc }}</div>
                            </div>
                            <button type="button"
                                    class="tog-btn {{ old($field, $evento->$field ?? $default) ? 'on' : '' }}"
                                    onclick="toggleField(this,'{{ $field }}')"></button>
                            <input type="hidden" name="{{ $field }}" id="{{ $field }}"
                                   value="{{ old($field, ($evento->$field ?? $default) ? 1 : 0) }}">
                        </div>
                    @endforeach
                </div>

                <div class="form-card">
                    <div class="card-head">
                        <div class="card-icon">📡</div>
                        <div><div class="card-title">Estado do evento</div><div class="card-sub">Controla a visibilidade pública</div></div>
                    </div>
                    <div class="status-grid">
                        <div class="status-tile {{ old('status', $evento->status) === 'rascunho'  ? 'sel-rascunho'  : '' }}"
                             id="st-rascunho" onclick="selectStatus('rascunho')">
                            <span class="status-em">📝</span>
                            <span class="status-nm" style="color:var(--amber);">Rascunho</span>
                        </div>
                        <div class="status-tile {{ old('status', $evento->status) === 'publicado' ? 'sel-publicado' : '' }}"
                             id="st-publicado" onclick="selectStatus('publicado')">
                            <span class="status-em">✅</span>
                            <span class="status-nm" style="color:var(--green);">Publicado</span>
                        </div>
                        <div class="status-tile {{ old('status', $evento->status) === 'encerrado' ? 'sel-encerrado' : '' }}"
                             id="st-encerrado" onclick="selectStatus('encerrado')">
                            <span class="status-em">🔒</span>
                            <span class="status-nm" style="color:var(--red);">Encerrado</span>
                        </div>
                    </div>
                    <input type="hidden" name="status" id="inp-status"
                           value="{{ old('status', $evento->status) }}">
                </div>

            </form>
        </div>

        <div class="aside-col">
            <div class="aside-lbl">Informações do evento</div>
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Criado por</span>
                    <span class="info-val">{{ $evento->user->name ?? '—' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Criado em</span>
                    <span class="info-val">{{ $evento->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Última edição</span>
                    <span class="info-val">{{ $evento->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado actual</span>
                    <span class="info-val" style="color:{{ $evento->status === 'publicado' ? 'var(--green)' : ($evento->status === 'encerrado' ? 'var(--red)' : 'var(--amber)') }}">
                        {{ ucfirst($evento->status) }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lotação</span>
                    <span class="info-val">{{ $evento->lotacao_maxima }} pessoas</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Categoria</span>
                    <span class="info-val">{{ $evento->categoria->nome ?? '—' }}</span>
                </div>
            </div>

            <div class="aside-lbl">Zona de perigo</div>
            <div class="danger-card">
                <div class="danger-title">Eliminar evento</div>
                <div class="danger-sub">Esta acção é irreversível. Todos os ingressos e reservas associados serão eliminados.</div>
                <form method="POST" action="{{ route('admin.eventos.eliminar', $evento->id) }}"
                      onsubmit="return confirm('Tens a certeza que queres eliminar este evento?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">🗑️ Eliminar evento</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleField(btn, field) {
    btn.classList.toggle('on');
    const el = document.getElementById(field);
    if (el) el.value = btn.classList.contains('on') ? 1 : 0;
}

function selectStatus(status) {
    ['rascunho','publicado','encerrado'].forEach(s => {
        document.getElementById('st-' + s).className =
            'status-tile' + (s === status ? ' sel-' + s : '');
    });
    document.getElementById('inp-status').value = status;
}
</script>
@endsection