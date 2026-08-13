{{-- resources/views/livewire/evento-form.blade.php --}}
<div>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#0a0d14;--s1:#0f1320;--s2:#151a2a;--s3:#1c2236;
    --acc:#7c3aed;--acc2:#a78bfa;--acc-bg:rgba(124,58,237,.1);
    --green:#34d399;--red:#f87171;--amber:#fbbf24;--sky:#38bdf8;
    --t1:#f0ecff;--t2:#9b92b3;--t3:#5a5270;
    --b1:rgba(255,255,255,.06);--b2:rgba(255,255,255,.12);--b3:rgba(255,255,255,.2);
}
*{box-sizing:border-box;}
.evf{font-family:'DM Sans',sans-serif;color:var(--t1);min-height:100vh;}

/* ══ NAVBAR CATEGORIAS (TOPO FIXO) ══ */
.cat-navbar{
    position:sticky;top:64px;z-index:50;
    background:var(--s1);
    border-bottom:2px solid var(--b1);
    padding:0 16px;
    overflow-x:auto;scrollbar-width:none;
    display:flex;align-items:stretch;gap:0;
}
.cat-navbar::-webkit-scrollbar{display:none;}
.cat-nav-btn{
    display:flex;align-items:center;gap:7px;
    padding:14px 18px;
    font-size:13px;font-weight:700;
    color:var(--t3);
    background:none;border:none;border-bottom:3px solid transparent;
    cursor:pointer;transition:all .2s;white-space:nowrap;
    font-family:'DM Sans',sans-serif;
    margin-bottom:-2px;
}
.cat-nav-btn:hover{color:var(--t2);background:var(--b1);}
.cat-nav-btn.active{
    color:var(--acc2);
    border-bottom-color:var(--acc2);
    background:var(--acc-bg);
}
.cat-nav-btn .cat-emoji{font-size:18px;}
.cat-nav-btn .cat-label{font-size:12px;}

/* ══ TOPBAR ACÇÕES ══ */
.ev-topbar{
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;
    background:var(--s1);border-bottom:1px solid var(--b1);
    flex-wrap:wrap;
}
.ev-topbar-info{}
.ev-topbar-cat{
    display:inline-flex;align-items:center;gap:5px;
    font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
    color:var(--acc2);background:var(--acc-bg);border:1px solid rgba(167,139,250,.2);
    padding:3px 10px;border-radius:20px;margin-bottom:4px;
}
.ev-topbar-title{font-family:'Syne',sans-serif;font-size:16px;font-weight:900;color:var(--t1);}
.ev-topbar-sub{font-size:11px;color:var(--t3);}
.ev-topbar-actions{margin-left:auto;display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
.btn-g{padding:8px 14px;border-radius:9px;border:1px solid var(--b2);background:transparent;font-size:12px;color:var(--t2);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-g:hover{background:var(--s2);color:var(--t1);}
.btn-p{padding:8px 16px;border-radius:9px;background:var(--acc);border:none;font-size:12px;font-weight:700;color:#fff;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:opacity .15s;}
.btn-p:hover{opacity:.85;}
.btn-pub{padding:8px 16px;border-radius:9px;background:var(--green);border:none;font-size:12px;font-weight:700;color:#052e16;cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:opacity .15s;}
.btn-pub:hover{opacity:.85;}

/* ══ STEPS ══ */
.ev-steps{
    display:flex;align-items:center;
    padding:0 16px;background:var(--s1);
    border-bottom:1px solid var(--b1);
    overflow-x:auto;scrollbar-width:none;
}
.ev-steps::-webkit-scrollbar{display:none;}
.ev-step-item{display:flex;align-items:center;gap:6px;padding:11px 0;cursor:pointer;flex-shrink:0;}
.ev-step-n{width:22px;height:22px;border-radius:50%;border:1.5px solid var(--t3);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--t3);transition:all .2s;}
.ev-step-n.active{border-color:var(--acc2);color:var(--acc2);background:var(--acc-bg);}
.ev-step-n.done{border-color:var(--green);color:var(--green);background:rgba(52,211,153,.1);}
.ev-step-lbl{font-size:11px;font-weight:600;color:var(--t3);white-space:nowrap;transition:color .2s;}
.ev-step-lbl.active{color:var(--acc2);}
.ev-step-lbl.done{color:var(--green);}
.ev-step-line{flex:1;height:1px;background:var(--b1);margin:0 8px;min-width:12px;transition:background .2s;}
.ev-step-line.done{background:var(--green);}

/* ══ PROGRESS ══ */
.ev-prog{display:flex;align-items:center;gap:12px;padding:6px 16px;background:var(--s1);border-bottom:1px solid var(--b1);}
.ev-prog-track{flex:1;height:3px;border-radius:999px;background:var(--b1);overflow:hidden;}
.ev-prog-fill{height:100%;border-radius:999px;background:var(--acc2);transition:width .4s;}
.ev-prog-lbl{font-size:11px;color:var(--t3);white-space:nowrap;}

/* ══ LAYOUT ══ */
.ev-body{max-width:700px;margin:0 auto;padding:20px 16px 100px;}

/* ══ CARD ══ */
.ev-card{background:var(--s1);border:1px solid var(--b2);border-radius:16px;padding:20px;margin-bottom:14px;}
.ev-card-head{display:flex;align-items:flex-start;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--b1);}
.ev-card-icon{width:34px;height:34px;border-radius:9px;background:var(--acc-bg);border:1px solid rgba(124,58,237,.3);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.ev-card-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:var(--t1);margin-bottom:2px;}
.ev-card-sub{font-size:11px;color:var(--t3);}

/* ══ CAMPOS ESPECÍFICOS (banner colorido) ══ */
.cat-specific-banner{
    display:flex;align-items:center;gap:10px;
    padding:12px 16px;margin-bottom:16px;
    background:var(--acc-bg);
    border:1px solid rgba(167,139,250,.25);
    border-radius:12px;
}
.cat-specific-banner-icon{font-size:22px;}
.cat-specific-banner-title{font-family:'Syne',sans-serif;font-size:13px;font-weight:800;color:var(--acc2);}
.cat-specific-banner-sub{font-size:11px;color:var(--t3);margin-top:1px;}

/* ══ FIELDS ══ */
.fld{margin-bottom:14px;}
.fld:last-child{margin-bottom:0;}
.fld label{display:block;font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;}
.fld input[type=text],
.fld input[type=date],
.fld input[type=time],
.fld input[type=number],
.fld input[type=url],
.fld textarea,
.fld select{
    width:100%;padding:10px 13px;
    background:var(--s2);border:1px solid var(--b2);
    border-radius:10px;font-size:13px;color:var(--t1);
    font-family:'DM Sans',sans-serif;outline:none;
    transition:border-color .15s;-webkit-appearance:none;
}
.fld input:focus,.fld textarea:focus,.fld select:focus{border-color:var(--acc2);background:var(--s3);}
.fld input::placeholder,.fld textarea::placeholder{color:var(--t3);}
.fld select option{background:var(--s2);}
.fld textarea{resize:vertical;min-height:90px;line-height:1.7;}
.fld-err{font-size:11px;color:var(--red);margin-top:4px;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;}

/* ══ TOGGLE ══ */
.tog-row{display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid var(--b1);}
.tog-row:last-child{border-bottom:none;padding-bottom:0;}
.tog-lbl{font-size:13px;color:var(--t1);}
.tog-desc{font-size:11px;color:var(--t3);margin-top:1px;}
.tog-sw{width:38px;height:21px;border-radius:999px;background:var(--t3);border:none;cursor:pointer;position:relative;flex-shrink:0;transition:background .2s;margin-left:12px;}
.tog-sw.on{background:var(--acc2);}
.tog-sw::after{content:'';width:15px;height:15px;border-radius:50%;background:#fff;position:absolute;top:3px;left:3px;transition:left .18s;}
.tog-sw.on::after{left:20px;}

/* ══ SUBCATEGORIAS ══ */
.sub-chips{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
.sub-chip{padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid var(--b2);background:var(--s2);color:var(--t3);cursor:pointer;transition:all .15s;}
.sub-chip:hover{border-color:var(--acc2);color:var(--acc2);}
.sub-chip.active{background:var(--acc);border-color:var(--acc);color:#fff;}

/* ══ LOCALIZAÇÃO ══ */
.loc-wrap{position:relative;}
.loc-arrow{position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--t3);font-size:11px;}
.novo-bairro-btn{width:100%;padding:8px;border-radius:8px;background:var(--acc-bg);border:1px dashed rgba(124,58,237,.3);color:var(--acc2);font-size:12px;font-weight:600;cursor:pointer;margin-top:6px;transition:all .2s;}
.novo-bairro-btn:hover{background:rgba(124,58,237,.15);}
.novo-bairro-row{display:flex;gap:8px;margin-top:8px;}
.novo-bairro-row input{flex:1;padding:9px 12px;background:var(--s2);border:1px solid rgba(124,58,237,.3);border-radius:9px;font-size:13px;color:var(--t1);outline:none;font-family:inherit;}
.novo-bairro-row button{padding:9px 14px;border-radius:9px;background:var(--acc);border:none;color:#fff;font-size:12px;font-weight:700;cursor:pointer;}
.loc-preview{padding:10px 14px;background:var(--acc-bg);border:1px solid rgba(124,58,237,.2);border-radius:10px;font-size:12px;color:var(--acc2);margin-top:8px;}

/* ══ BILHETES ══ */
.tk-row{background:var(--s2);border:1px solid var(--b2);border-radius:12px;padding:14px;margin-bottom:10px;position:relative;}
.tk-row.bloqueado{opacity:.6;pointer-events:none;}
.tk-bloqueado-tag{position:absolute;top:8px;right:8px;font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:3px 8px;border-radius:20px;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);color:var(--amber);}
.tk-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;align-items:end;}
.tk-rm{position:absolute;top:10px;right:10px;width:26px;height:26px;border-radius:6px;background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.2);color:var(--red);font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.tk-rm:hover{background:rgba(248,113,113,.2);}
.add-tk{width:100%;padding:9px;border-radius:9px;border:1.5px dashed var(--b2);background:transparent;font-size:12px;color:var(--t3);cursor:pointer;margin-top:10px;font-family:'DM Sans',sans-serif;transition:all .15s;}
.add-tk:hover{border-color:var(--acc2);color:var(--acc2);}
.taxa-note{font-size:11px;color:var(--t3);margin-top:10px;padding:9px 13px;background:var(--s2);border-radius:8px;border:1px solid var(--b1);}

/* ══ AVISO PUBLICADO ══ */
.aviso-pub{display:flex;align-items:flex-start;gap:10px;padding:12px 14px;margin-bottom:14px;background:rgba(251,191,36,.07);border:1px solid rgba(251,191,36,.2);border-radius:12px;}
.aviso-pub-icon{font-size:18px;flex-shrink:0;}
.aviso-pub-txt{font-size:12px;color:var(--amber);line-height:1.6;}
.aviso-pub-title{font-weight:700;margin-bottom:2px;}

/* ══ UPLOAD ══ */
.upload-zone{border:1.5px dashed var(--b2);border-radius:10px;padding:24px 20px;text-align:center;cursor:pointer;transition:all .2s;display:block;}
.upload-zone:hover{border-color:var(--acc2);background:var(--acc-bg);}
.upload-zone p{font-size:13px;color:var(--t2);}
.upload-zone small{font-size:11px;color:var(--t3);}

/* ══ STATUS ══ */
.status-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.status-tile{padding:14px 10px;border-radius:12px;border:1.5px solid var(--b2);background:var(--s2);cursor:pointer;text-align:center;transition:all .18s;}
.status-tile:hover{border-color:var(--b3);}
.status-tile.s-rascunho{border-color:var(--amber);background:rgba(251,191,36,.08);}
.status-tile.s-publicado{border-color:var(--green);background:rgba(52,211,153,.08);}
.status-tile.s-encerrado{border-color:var(--red);background:rgba(248,113,113,.08);}
.status-em{font-size:20px;display:block;margin-bottom:5px;}
.status-nm{font-size:11px;font-weight:700;font-family:'Syne',sans-serif;}

/* ══ STEP PANELS ══ */
.sp{display:none;}
.sp.active{display:block;}

/* ══ ERROS ══ */
.alert-err{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);color:var(--red);border-radius:10px;padding:12px 16px;margin-bottom:14px;font-size:13px;}

/* ══ REVIEW ══ */
.rev-item{display:flex;align-items:center;gap:12px;padding:12px;background:var(--s2);border-radius:11px;border:1px solid var(--b2);margin-bottom:8px;}
.rev-icon{width:30px;height:30px;border-radius:8px;background:var(--acc-bg);border:1px solid rgba(124,58,237,.3);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.rev-body{flex:1;min-width:0;}
.rev-title{font-size:12px;font-weight:700;color:var(--t1);}
.rev-val{font-size:11px;color:var(--t3);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.rev-edit{font-size:11px;color:var(--acc2);cursor:pointer;background:none;border:none;white-space:nowrap;}

/* ══ NAV BTNS ══ */
.nav-btns{display:flex;gap:10px;margin-top:20px;}
.nav-back{flex:1;padding:12px;border-radius:12px;background:var(--s2);border:1px solid var(--b2);color:var(--t2);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;}
.nav-back:hover{border-color:var(--b3);color:var(--t1);}
.nav-next{flex:2;padding:12px;border-radius:12px;background:var(--acc);border:none;color:#fff;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s;}
.nav-next:hover{opacity:.85;}
.nav-save{flex:2;padding:12px;border-radius:12px;background:var(--green);border:none;color:#052e16;font-size:13px;font-weight:800;cursor:pointer;transition:opacity .15s;}
.nav-save:hover{opacity:.85;}
</style>

<div class="evf">

    {{-- ══════════════════════════════════════
         NAVBAR DE CATEGORIAS (sempre visível)
    ══════════════════════════════════════ --}}
    <div class="cat-navbar">
        @foreach($this->categorias as $cat)
        <button
            type="button"
            class="cat-nav-btn {{ $categoria_id == $cat->id ? 'active' : '' }}"
            wire:click="selectCategoria({{ $cat->id }}, '{{ addslashes($cat->nome) }}', '{{ $cat->emoji }}')">
            <span class="cat-emoji">{{ $cat->emoji }}</span>
            <span class="cat-label">{{ $cat->nome }}</span>
        </button>
        @endforeach
    </div>

    {{-- TOPBAR COM ACÇÕES --}}
    <div class="ev-topbar">
        <div class="ev-topbar-info">
            <div class="ev-topbar-cat">{{ $catEmoji }} {{ ucfirst($catNome) ?: 'Categoria' }}</div>
            <div class="ev-topbar-title">{{ $editando ? 'Editar Evento' : 'Criar Evento' }}</div>
            <div class="ev-topbar-sub">Passo {{ $step }} de {{ $totalSteps }}</div>
        </div>
        <div class="ev-topbar-actions">
            <a href="{{ route('admin.eventos') }}" class="btn-g">← Cancelar</a>
            @if($step < $totalSteps)
                <button type="button" wire:click="proximoStep" class="btn-p">
                    Continuar →
                </button>
            @else
                <button type="button" wire:click="salvar" class="btn-pub">
                    <span wire:loading.remove wire:target="salvar">🚀 {{ $editando ? 'Guardar' : 'Criar' }}</span>
                    <span wire:loading wire:target="salvar">⏳ A guardar...</span>
                </button>
            @endif
        </div>
    </div>

    {{-- STEPS --}}
    <div class="ev-steps">
        @foreach([1=>'Informações',2=>'Local & Detalhes',3=>'Capa',4=>'Bilhetes',5=>'Publicar'] as $n => $lbl)
        <div class="ev-step-item" wire:click="irParaStep({{ $n }})">
            <div class="ev-step-n {{ $step === $n ? 'active' : ($step > $n ? 'done' : '') }}">
                {{ $step > $n ? '✓' : $n }}
            </div>
            <span class="ev-step-lbl {{ $step === $n ? 'active' : ($step > $n ? 'done' : '') }}">{{ $lbl }}</span>
        </div>
        @if($n < 5)<div class="ev-step-line {{ $step > $n ? 'done' : '' }}"></div>@endif
        @endforeach
    </div>

    {{-- PROGRESS --}}
    <div class="ev-prog">
        <div class="ev-prog-track">
            <div class="ev-prog-fill" style="width:{{ ($step / $totalSteps) * 100 }}%"></div>
        </div>
        <span class="ev-prog-lbl">{{ round(($step / $totalSteps) * 100) }}%</span>
    </div>

    {{-- BODY --}}
    <div class="ev-body">

        @if($errors->any())
        <div class="alert-err">
            @foreach($errors->all() as $err)
            <div>• {{ $err }}</div>
            @endforeach
        </div>
        @endif

        {{-- ════════════════════════════════
             PASSO 1 — INFORMAÇÕES COMUNS
        ════════════════════════════════ --}}
        <div class="sp {{ $step === 1 ? 'active' : '' }}">

            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">📝</div>
                    <div>
                        <div class="ev-card-title">Informações básicas</div>
                        <div class="ev-card-sub">Nome, descrição e datas — válido para qualquer categoria</div>
                    </div>
                </div>

                <div class="fld">
                    <label>Nome do evento <span style="color:var(--red)">*</span></label>
                    <input type="text" wire:model="titulo" placeholder="Ex: Grande Noite de Kuduro" maxlength="255">
                    @error('titulo')<div class="fld-err">{{ $message }}</div>@enderror
                </div>

                <div class="fld">
                    <label>Descrição <span style="color:var(--red)">*</span></label>
                    <textarea wire:model="descricao" rows="4" placeholder="Descreve o evento, artistas, programa..."></textarea>
                    @error('descricao')<div class="fld-err">{{ $message }}</div>@enderror
                </div>

                <div class="fld">
                    <label>Link externo (opcional)</label>
                    <input type="url" wire:model="link_externo" placeholder="https://...">
                </div>

                <div class="g2">
                    <div class="fld">
                        <label>Data de início <span style="color:var(--red)">*</span></label>
                        <input type="date" wire:model="data_evento">
                        @error('data_evento')<div class="fld-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="fld">
                        <label>Data de fim</label>
                        <input type="date" wire:model="data_fim">
                    </div>
                </div>

                <div class="g2">
                    <div class="fld">
                        <label>Hora de início <span style="color:var(--red)">*</span></label>
                        <input type="time" wire:model="hora_inicio">
                        @error('hora_inicio')<div class="fld-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="fld">
                        <label>Hora de fim</label>
                        <input type="time" wire:model="hora_fim">
                    </div>
                </div>

                <div class="tog-row">
                    <div>
                        <div class="tog-lbl">Múltiplos dias</div>
                        <div class="tog-desc">Cada dia com programação separada</div>
                    </div>
                    <button type="button" class="tog-sw {{ $multiplos_dias ? 'on' : '' }}" wire:click="$toggle('multiplos_dias')"></button>
                </div>
                <div class="tog-row">
                    <div>
                        <div class="tog-lbl">Evento online</div>
                        <div class="tog-desc">Realizado via streaming ou plataforma virtual</div>
                    </div>
                    <button type="button" class="tog-sw {{ $online ? 'on' : '' }}" wire:click="$toggle('online')"></button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════
             PASSO 2 — LOCAL + CAMPOS ESPECÍFICOS
        ════════════════════════════════ --}}
        <div class="sp {{ $step === 2 ? 'active' : '' }}">

            {{-- LOCAL (comum a todos) --}}
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">📍</div>
                    <div>
                        <div class="ev-card-title">Localização</div>
                        <div class="ev-card-sub">Província → Município → Bairro</div>
                    </div>
                </div>

                <div class="fld">
                    <label>Província</label>
                    <div class="loc-wrap">
                        <select wire:model.live="provincia">
                            <option value="">Selecciona a província</option>
                            @foreach(array_keys($this->localizacoes) as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </select>
                        <span class="loc-arrow">▾</span>
                    </div>
                </div>

                @if($provincia)
                <div class="fld">
                    <label>Município</label>
                    <div class="loc-wrap">
                        <select wire:model.live="municipio">
                            <option value="">Selecciona o município</option>
                            @foreach($this->municipios as $mun)
                            <option value="{{ $mun }}">{{ $mun }}</option>
                            @endforeach
                        </select>
                        <span class="loc-arrow">▾</span>
                    </div>
                </div>
                @endif

                @if($municipio)
                <div class="fld">
                    <label>Bairro</label>
                    <div class="loc-wrap">
                        <select wire:model.live="bairro">
                            <option value="">Selecciona o bairro</option>
                            @foreach($this->bairros as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        <span class="loc-arrow">▾</span>
                    </div>
                    @if(!$novo_bairro)
                    <button type="button" class="novo-bairro-btn" wire:click="$set('novo_bairro', true)">
                        + Bairro não está na lista? Adicionar
                    </button>
                    @else
                    <div class="novo-bairro-row">
                        <input type="text" wire:model="novo_bairro_nome" placeholder="Nome do bairro...">
                        <button type="button" wire:click="adicionarNovoBairro">✓</button>
                    </div>
                    @endif
                </div>
                @endif

                <div class="fld">
                    <label>Local específico <span style="color:var(--red)">*</span></label>
                    <input type="text" wire:model="localizacao" placeholder="Ex: Cine Karl Marx, Estádio, Terminal...">
                    @error('localizacao')<div class="fld-err">{{ $message }}</div>@enderror
                </div>

                @if($localizacao)
                <div class="loc-preview">📍 {{ $localizacao }}</div>
                @endif
            </div>

            {{-- SUBCATEGORIAS --}}
            @if(!empty($this->subcategorias))
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">🏷️</div>
                    <div>
                        <div class="ev-card-title">Subcategoria</div>
                        <div class="ev-card-sub">Refina a categoria do teu evento</div>
                    </div>
                </div>
                <div class="sub-chips">
                    @foreach($this->subcategorias as $sub)
                    <button type="button"
                        class="sub-chip {{ $subcategoria_id == $sub['id'] ? 'active' : '' }}"
                        wire:click="$set('subcategoria_id', {{ $sub['id'] }})">
                        {{ $sub['nome'] }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══════════════════════════════════════
                 CAMPOS ESPECÍFICOS POR CATEGORIA
                 Aparecem neste passo, após a localização
            ══════════════════════════════════════ --}}

            @php $c = $catNome; @endphp

            {{-- ✈️ VIAGEM --}}
            @if(str_contains($c,'viagem'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">✈️</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes da Viagem</div>
                        <div class="cat-specific-banner-sub">Veículo, rota, motorista e paragens</div>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Local de Partida</label><input type="text" wire:model="meta.partida" placeholder="Ex: Terminal Rodoviário de Luanda"></div>
                    <div class="fld"><label>Destino Final</label><input type="text" wire:model="meta.destino" placeholder="Ex: Benguela Centro"></div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Hora de Partida</label><input type="time" wire:model="meta.hora_partida"></div>
                    <div class="fld"><label>Chegada Prevista</label><input type="time" wire:model="meta.hora_chegada"></div>
                </div>
                <div class="g3">
                    <div class="fld"><label>Matrícula</label><input type="text" wire:model="meta.matricula" placeholder="LD-00-00-AA"></div>
                    <div class="fld"><label>Marca / Modelo</label><input type="text" wire:model="meta.marca_veiculo" placeholder="Ex: Toyota Hiace"></div>
                    <div class="fld"><label>Motorista</label><input type="text" wire:model="meta.motorista" placeholder="Nome completo"></div>
                </div>
                <div class="fld"><label>Paragens (separa com vírgula)</label><input type="text" wire:model="meta.paragens_texto" placeholder="Ex: Viana, Km 30, Catumbela"></div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Ar condicionado</div></div>
                    <button type="button" class="tog-sw {{ !empty($meta['ar_condicionado']) ? 'on' : '' }}" wire:click="$set('meta.ar_condicionado', {{ empty($meta['ar_condicionado']) ? 1 : 0 }})"></button>
                </div>
            </div>
            @endif

            {{-- 🎤 SHOW / MÚSICA / FESTA --}}
            @if(str_contains($c,'show') || str_contains($c,'musica') || str_contains($c,'música') || str_contains($c,'festa'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">🎤</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes do Show</div>
                        <div class="cat-specific-banner-sub">Artistas, lineup e dress code</div>
                    </div>
                </div>
                <div class="fld"><label>Artistas (separa com vírgula)</label><input type="text" wire:model="meta.artistas_texto" placeholder="Ex: Anselmo Ralph, Yola Semedo, Gerilson Insrael"></div>
                <div class="g2">
                    <div class="fld"><label>Nome do Palco</label><input type="text" wire:model="meta.palco" placeholder="Palco Principal"></div>
                    <div class="fld">
                        <label>Dress Code</label>
                        <select wire:model="meta.dresscode">
                            <option value="">Sem dress code</option>
                            @foreach(['Casual','Smart Casual','Formal','Black Tie','Temático'] as $dc)
                            <option value="{{ $dc }}">{{ $dc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fld">
                    <label>Lineup / Horário do Programa</label>
                    <textarea wire:model="meta.lineup" rows="4" placeholder="22:00 — DJ Alfa (abertura)&#10;23:30 — Anselmo Ralph (headliner)&#10;01:00 — Encerramento"></textarea>
                </div>
            </div>
            @endif

            {{-- 🎉 FESTIVAL --}}
            @if(str_contains($c,'festival') || str_contains($c,'festiv'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">🎉</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes do Festival</div>
                        <div class="cat-specific-banner-sub">Lineup, palcos e camping</div>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Número de Dias</label><input type="number" wire:model="meta.dias_festival" min="1" placeholder="3"></div>
                    <div class="fld"><label>Número de Palcos</label><input type="number" wire:model="meta.num_palcos" min="1" placeholder="2"></div>
                </div>
                <div class="fld"><label>Artistas / Headliners (separa com vírgula)</label><input type="text" wire:model="meta.artistas_texto" placeholder="Ex: Artista A, Artista B, Artista C"></div>
                <div class="fld"><label>Lineup por Dia</label><textarea wire:model="meta.lineup" rows="4" placeholder="Dia 1: Artista A, Artista B&#10;Dia 2: Artista C, Artista D"></textarea></div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Camping disponível</div><div class="tog-desc">Área de campismo no recinto</div></div>
                    <button type="button" class="tog-sw {{ !empty($meta['camping']) ? 'on' : '' }}" wire:click="$set('meta.camping', {{ empty($meta['camping']) ? 1 : 0 }})"></button>
                </div>
            </div>
            @endif

            {{-- ⚽ DESPORTO --}}
            @if(str_contains($c,'desporto'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">⚽</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes do Jogo</div>
                        <div class="cat-specific-banner-sub">Equipas, modalidade e fase</div>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Equipa da Casa</label><input type="text" wire:model="meta.equipa_local" placeholder="Ex: Petro de Luanda"></div>
                    <div class="fld"><label>Equipa Visitante</label><input type="text" wire:model="meta.equipa_visitante" placeholder="Ex: 1º de Agosto"></div>
                </div>
                <div class="g2">
                    <div class="fld">
                        <label>Modalidade</label>
                        <select wire:model="meta.modalidade">
                            @foreach(['Futebol','Basquetebol','Voleibol','Atletismo','Natação','Boxe','MMA','Ténis','Rugby','Ciclismo','Outro'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fld"><label>Árbitro</label><input type="text" wire:model="meta.arbitro" placeholder="Nome do árbitro"></div>
                </div>
                <div class="fld"><label>Fase / Competição</label><input type="text" wire:model="meta.fase" placeholder="Ex: Final do Campeonato Nacional"></div>
            </div>
            @endif

            {{-- 🎙️ CONFERÊNCIA --}}
            @if(str_contains($c,'confer'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">🎙️</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes da Conferência</div>
                        <div class="cat-specific-banner-sub">Palestrantes, agenda e requisitos</div>
                    </div>
                </div>
                <div class="fld"><label>Tema Principal</label><input type="text" wire:model="meta.tema" placeholder="Ex: Inovação e Tecnologia em Angola"></div>
                <div class="fld"><label>Palestrantes (separa com vírgula)</label><input type="text" wire:model="meta.palestrantes_texto" placeholder="Ex: Dr. João Silva, Eng. Maria Costa"></div>
                <div class="fld"><label>Agenda / Programa</label><textarea wire:model="meta.agenda" rows="4" placeholder="09:00 — Abertura&#10;09:30 — Palestra 1&#10;11:00 — Coffee Break"></textarea></div>
                <div class="g2">
                    <div class="fld">
                        <label>Idioma</label>
                        <select wire:model="meta.idioma">
                            @foreach(['Português','Inglês','Francês','Bilíngue PT/EN'] as $lang)
                            <option value="{{ $lang }}">{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fld"><label>Requisitos</label><input type="text" wire:model="meta.requisitos" placeholder="Ex: Profissionais de TI"></div>
                </div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Certificado incluído</div><div class="tog-desc">Emitir certificado aos participantes</div></div>
                    <button type="button" class="tog-sw {{ !empty($meta['certificado']) ? 'on' : '' }}" wire:click="$set('meta.certificado', {{ empty($meta['certificado']) ? 1 : 0 }})"></button>
                </div>
            </div>
            @endif

            {{-- 📚 WORKSHOP --}}
            @if(str_contains($c,'workshop'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">📚</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes do Workshop</div>
                        <div class="cat-specific-banner-sub">Instrutor, nível e materiais</div>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Instrutor / Formador</label><input type="text" wire:model="meta.instrutor" placeholder="Nome do instrutor"></div>
                    <div class="fld">
                        <label>Nível</label>
                        <select wire:model="meta.nivel">
                            @foreach(['Iniciante','Intermédio','Avançado','Todos os níveis'] as $n)
                            <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Duração (horas)</label><input type="number" wire:model="meta.duracao_horas" min="1" placeholder="4"></div>
                    <div class="fld"><label>Máx. de Alunos</label><input type="number" wire:model="meta.max_alunos" min="1" placeholder="20"></div>
                </div>
                <div class="fld"><label>Materiais Incluídos</label><input type="text" wire:model="meta.materiais" placeholder="Ex: Apostila, caneta, certificado digital"></div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Certificado incluído</div></div>
                    <button type="button" class="tog-sw {{ !empty($meta['certificado']) ? 'on' : '' }}" wire:click="$set('meta.certificado', {{ empty($meta['certificado']) ? 1 : 0 }})"></button>
                </div>
            </div>
            @endif

            {{-- 🎭 CULTURA --}}
            @if(str_contains($c,'cultura'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">🎭</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes Culturais</div>
                        <div class="cat-specific-banner-sub">Elenco, classificação e duração</div>
                    </div>
                </div>
                <div class="fld"><label>Artistas / Elenco (separa com vírgula)</label><input type="text" wire:model="meta.elenco_texto" placeholder="Ex: Actor A, Actriz B, Músico C"></div>
                <div class="g3">
                    <div class="fld">
                        <label>Classificação Etária</label>
                        <select wire:model="meta.classificacao_etaria">
                            @foreach(['Livre','+6','+12','+16','+18'] as $ce)
                            <option value="{{ $ce }}">{{ $ce }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fld"><label>Duração (min)</label><input type="number" wire:model="meta.duracao_minutos" min="1" placeholder="90"></div>
                    <div class="fld">
                        <label>Idioma</label>
                        <select wire:model="meta.idioma">
                            @foreach(['Português','Inglês','Kimbundu','Kikongo','Umbundo','Outro'] as $lang)
                            <option value="{{ $lang }}">{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endif

            {{-- 🍽️ GASTRONOMIA --}}
            @if(str_contains($c,'gastro'))
            <div class="ev-card">
                <div class="cat-specific-banner">
                    <div class="cat-specific-banner-icon">🍽️</div>
                    <div>
                        <div class="cat-specific-banner-title">Detalhes do Evento Gastronómico</div>
                        <div class="cat-specific-banner-sub">Chef, menu e dress code</div>
                    </div>
                </div>
                <div class="g2">
                    <div class="fld"><label>Chef / Responsável</label><input type="text" wire:model="meta.chef" placeholder="Nome do chef"></div>
                    <div class="fld"><label>Tipo de Culinária</label><input type="text" wire:model="meta.tipo_culinaria" placeholder="Ex: Culinária Angolana Tradicional"></div>
                </div>
                <div class="fld"><label>Menu / Ementa</label><textarea wire:model="meta.menu" rows="4" placeholder="Entrada: Muamba de galinha&#10;Prato: Calulu de peixe&#10;Sobremesa: Cocada amarela"></textarea></div>
                <div class="g2">
                    <div class="fld">
                        <label>Dress Code</label>
                        <select wire:model="meta.dresscode">
                            <option value="">Sem dress code</option>
                            @foreach(['Casual','Smart Casual','Formal','Black Tie'] as $dc)
                            <option value="{{ $dc }}">{{ $dc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fld"><label>Preço Fixo do Menu (Kz)</label><input type="number" wire:model="meta.preco_menu" min="0" placeholder="5000"></div>
                </div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Bebidas incluídas</div><div class="tog-desc">O preço inclui bebidas</div></div>
                    <button type="button" class="tog-sw {{ !empty($meta['bebidas_incluidas']) ? 'on' : '' }}" wire:click="$set('meta.bebidas_incluidas', {{ empty($meta['bebidas_incluidas']) ? 1 : 0 }})"></button>
                </div>
            </div>
            @endif

        </div>

            {{-- ════════════════════════════════
             PASSO 3 — CAPA E GALERIA
        ════════════════════════════════ --}}
        <div class="sp {{ $step === 3 ? 'active' : '' }}">
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">🖼️</div>
                    <div>
                        <div class="ev-card-title">Imagem de capa</div>
                        <div class="ev-card-sub">1200 × 630 px recomendado · PNG, JPG, WEBP · máx. 2 MB</div>
                    </div>
                </div>
                <label class="upload-zone">
                    <input type="file" wire:model="imagem_capa" accept="image/*" style="display:none;">
                    @if($imagem_capa)
                        <p style="color:var(--green);">✅ {{ $imagem_capa->getClientOriginalName() }}</p>
                    @elseif($editando && $eventoId)
                        <p>🔄 Clica para substituir a imagem actual</p>
                        <small>PNG, JPG, WEBP · máx. 2 MB</small>
                    @else
                        <p>📷 Arrasta ou clica para seleccionar</p>
                        <small>PNG, JPG, WEBP · máx. 2 MB</small>
                    @endif
                </label>
                <div wire:loading wire:target="imagem_capa" style="font-size:12px;color:var(--acc2);margin-top:8px;">⏳ A carregar imagem...</div>
            </div>

            {{-- VÍDEO DE PRÉ-VISUALIZAÇÃO --}}
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">🎬</div>
                    <div>
                        <div class="ev-card-title">Vídeo de pré-visualização</div>
                        <div class="ev-card-sub">Máx. 60 segundos · Link externo ou ficheiro local</div>
                    </div>
                </div>

                {{-- Toggle link / upload — usa @if em vez de ternária no style --}}
                <div style="display:flex;gap:8px;margin-bottom:16px;">
                    @if($video_tipo === 'link')
                    <button type="button"
                        style="flex:1;padding:9px;border-radius:10px;font-size:12px;font-weight:700;border:1.5px solid var(--acc2);background:var(--acc-bg);color:var(--acc2);cursor:pointer;"
                        wire:click="$set('video_tipo','link')">
                        🔗 Link externo
                    </button>
                    <button type="button"
                        style="flex:1;padding:9px;border-radius:10px;font-size:12px;font-weight:700;border:1.5px solid var(--b2);background:var(--s2);color:var(--t3);cursor:pointer;"
                        wire:click="$set('video_tipo','upload')">
                        📁 Upload de ficheiro
                    </button>
                    @else
                    <button type="button"
                        style="flex:1;padding:9px;border-radius:10px;font-size:12px;font-weight:700;border:1.5px solid var(--b2);background:var(--s2);color:var(--t3);cursor:pointer;"
                        wire:click="$set('video_tipo','link')">
                        🔗 Link externo
                    </button>
                    <button type="button"
                        style="flex:1;padding:9px;border-radius:10px;font-size:12px;font-weight:700;border:1.5px solid var(--acc2);background:var(--acc-bg);color:var(--acc2);cursor:pointer;"
                        wire:click="$set('video_tipo','upload')">
                        📁 Upload de ficheiro
                    </button>
                    @endif
                </div>

                {{-- OPÇÃO: Link externo --}}
                @if($video_tipo === 'link')
                <div class="fld">
                    <label>Link do vídeo (YouTube, Vimeo, TikTok...)</label>
                    <input type="url" wire:model="video_preview" placeholder="https://youtube.com/watch?v=...">
                    @error('video_preview')<div class="fld-err">{{ $message }}</div>@enderror
                </div>
                <div style="font-size:11px;color:var(--t3);padding:10px 13px;background:var(--s2);border-radius:8px;border:1px solid var(--b1);line-height:1.7;">
                    💡 Cole o link do YouTube, Vimeo, TikTok ou qualquer plataforma de vídeo.<br>
                    <span style="color:var(--amber);">⚠️ Recomendado: máximo 60 segundos.</span>
                </div>
                @if(!empty($video_preview) && str_starts_with($video_preview, 'http'))
                @php
                    $prevEmbed = null;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $video_preview, $ym)) {
                        $prevEmbed = "https://www.youtube.com/embed/{$ym[1]}?controls=1";
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $video_preview, $vm)) {
                        $prevEmbed = "https://player.vimeo.com/video/{$vm[1]}";
                    }
                @endphp
                @if($prevEmbed)
                <div style="margin-top:14px;border-radius:12px;overflow:hidden;aspect-ratio:16/9;">
                    <iframe src="{{ $prevEmbed }}" style="width:100%;height:100%;border:none;" allow="fullscreen" allowfullscreen></iframe>
                </div>
                @else
                <div style="margin-top:10px;padding:10px 13px;background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);border-radius:8px;font-size:12px;color:var(--green);">
                    ✅ Link guardado: {{ Str::limit($video_preview, 50) }}
                </div>
                @endif
                @endif
                @endif

                {{-- OPÇÃO: Upload de ficheiro --}}
                @if($video_tipo === 'upload')
                <div class="fld">
                    <label>Ficheiro de vídeo</label>
                    <label class="upload-zone">
                        <input type="file" wire:model="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime" style="display:none;">
                        @if($video_file)
                            <p style="color:var(--green);">✅ {{ $video_file->getClientOriginalName() }}</p>
                            <small>{{ round($video_file->getSize() / 1024 / 1024, 1) }} MB</small>
                        @elseif($editando && !empty($video_preview) && !str_starts_with($video_preview, 'http'))
                            <p>🔄 Clica para substituir o vídeo actual</p>
                            <small>MP4, WebM, OGG, MOV · máx. 100 MB</small>
                        @else
                            <p>🎬 Arrasta ou clica para seleccionar</p>
                            <small>MP4, WebM, OGG, MOV · máx. 100 MB</small>
                        @endif
                    </label>
                    <div wire:loading wire:target="video_file" style="font-size:12px;color:var(--acc2);margin-top:8px;">⏳ A carregar vídeo...</div>
                    @error('video_file')<div class="fld-err">{{ $message }}</div>@enderror
                </div>
                @if($editando && !empty($video_preview) && !str_starts_with($video_preview, 'http') && !$video_file)
                <div style="margin-top:10px;border-radius:12px;overflow:hidden;background:#000;">
                    <video src="{{ asset('storage/'.$video_preview) }}" controls style="width:100%;max-height:200px;display:block;"></video>
                </div>
                @endif
                <div style="font-size:11px;color:var(--t3);margin-top:10px;padding:10px 13px;background:var(--s2);border-radius:8px;border:1px solid var(--b1);line-height:1.7;">
                    💡 O vídeo é guardado no servidor — funciona sem dependências externas.<br>
                    🎬 Nos círculos usa <code>&lt;video&gt;</code> tag nativa — mais rápido que iframe.<br>
                    <span style="color:var(--amber);">⚠️ Recomendado: máximo 60 segundos · formato MP4.</span>
                </div>
                @endif
            </div>

            @if(!$editando)
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">🗂️</div>
                    <div>
                        <div class="ev-card-title">Galeria de fotos</div>
                        <div class="ev-card-sub">Fotos adicionais para a página do evento</div>
                    </div>
                </div>
                <label class="upload-zone">
                    <input type="file" wire:model="galeria" accept="image/*" multiple style="display:none;">
                    <p>🗂️ Clica para seleccionar várias fotos</p>
                    <small>PNG, JPG, WEBP · máx. 5 MB por foto</small>
                </label>
                @if(!empty($galeria))
                <p style="font-size:12px;color:var(--green);margin-top:8px;">✅ {{ count($galeria) }} foto(s) seleccionada(s)</p>
                @endif
            </div>
            @endif
        </div>

        {{-- ════════════════════════════════
             PASSO 4 — BILHETES
        ════════════════════════════════ --}}
        <div class="sp {{ $step === 4 ? 'active' : '' }}">

            @if($editando && $statusOriginal === 'publicado')
            <div class="aviso-pub">
                <div class="aviso-pub-icon">⚠️</div>
                <div class="aviso-pub-txt">
                    <div class="aviso-pub-title">Bilhetes bloqueados — evento publicado</div>
                    Os bilhetes existentes são imutáveis por segurança. Podes apenas adicionar novos tipos.
                </div>
            </div>
            @endif

            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">🎟️</div>
                    <div>
                        <div class="ev-card-title">Tipos de bilhete</div>
                        <div class="ev-card-sub">O preço final inclui automaticamente 20% de taxa de serviço</div>
                    </div>
                </div>

                @foreach($ingressos as $i => $ingresso)
                <div class="tk-row {{ !empty($ingresso['bloqueado']) ? 'bloqueado' : '' }}">
                    @if(!empty($ingresso['bloqueado']))<div class="tk-bloqueado-tag">🔒 Bloqueado</div>@endif
                    <div class="tk-grid" style="padding-right:{{ empty($ingresso['bloqueado']) ? '34px' : '0' }}">
                        <div class="fld" style="margin:0">
                            <label>Tipo de bilhete</label>
                            <input type="text" wire:model="ingressos.{{ $i }}.nome" placeholder="Ex: Geral, VIP, Camarote..." {{ !empty($ingresso['bloqueado']) ? 'readonly' : '' }}>
                        </div>
                        <div class="fld" style="margin:0">
                            <label>Preço base (Kz)</label>
                            <input type="number" wire:model="ingressos.{{ $i }}.preco" placeholder="0" min="0" {{ !empty($ingresso['bloqueado']) ? 'readonly' : '' }}>
                        </div>
                        <div class="fld" style="margin:0">
                            <label>Quantidade</label>
                            <input type="number" wire:model="ingressos.{{ $i }}.quantidade" placeholder="100" min="1" {{ !empty($ingresso['bloqueado']) ? 'readonly' : '' }}>
                        </div>
                    </div>
                    @if(empty($ingresso['bloqueado']))
                    <button type="button" class="tk-rm" wire:click="removerIngresso({{ $i }})">✕</button>
                    @endif
                </div>
                @endforeach

                <button type="button" class="add-tk" wire:click="adicionarIngresso">+ Adicionar tipo de bilhete</button>
                <div class="taxa-note">💡 Taxa de 20% incluída automaticamente. Ex: 5.000 Kz → preço final 6.000 Kz</div>
            </div>

            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">📊</div>
                    <div><div class="ev-card-title">Capacidade</div></div>
                </div>
                <div class="g2">
                    <div class="fld">
                        <label>Lotação máxima <span style="color:var(--red)">*</span></label>
                        <input type="number" wire:model="lotacao_maxima" min="1" placeholder="500">
                        @error('lotacao_maxima')<div class="fld-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="fld">
                        <label>Bilhetes por pessoa</label>
                        <input type="number" wire:model="ingressos_por_pessoa" min="1" max="10">
                    </div>
                </div>
                <div class="tog-row">
                    <div><div class="tog-lbl">Lista de espera</div><div class="tog-desc">Aceitar inscrições após esgotamento</div></div>
                    <button type="button" class="tog-sw {{ $lista_espera ? 'on' : '' }}" wire:click="$toggle('lista_espera')"></button>
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════
             PASSO 5 — PUBLICAR
        ════════════════════════════════ --}}
        <div class="sp {{ $step === 5 ? 'active' : '' }}">

            {{-- REVISÃO --}}
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">✅</div>
                    <div><div class="ev-card-title">Revisão final</div><div class="ev-card-sub">Verifica antes de guardar</div></div>
                </div>
                <div class="rev-item">
                    <div class="rev-icon">{{ $catEmoji }}</div>
                    <div class="rev-body"><div class="rev-title">Categoria</div><div class="rev-val">{{ ucfirst($catNome) ?: '—' }}</div></div>
                    <button class="rev-edit" wire:click="$set('step', 1)">Alterar</button>
                </div>
                <div class="rev-item">
                    <div class="rev-icon">📝</div>
                    <div class="rev-body"><div class="rev-title">Evento</div><div class="rev-val">{{ $titulo ?: '—' }}{{ $data_evento ? ' · '.$data_evento : '' }}</div></div>
                    <button class="rev-edit" wire:click="$set('step', 1)">Editar →</button>
                </div>
                <div class="rev-item">
                    <div class="rev-icon">📍</div>
                    <div class="rev-body"><div class="rev-title">Localização</div><div class="rev-val">{{ $localizacao ?: '—' }}</div></div>
                    <button class="rev-edit" wire:click="$set('step', 2)">Editar →</button>
                </div>
                <div class="rev-item">
                    <div class="rev-icon">🎟️</div>
                    <div class="rev-body"><div class="rev-title">Bilhetes</div><div class="rev-val">{{ count(array_filter($ingressos, fn($i) => !empty($i['nome']))) }} tipo(s) configurado(s)</div></div>
                    <button class="rev-edit" wire:click="$set('step', 4)">Editar →</button>
                </div>
            </div>

            {{-- DEFINIÇÕES --}}
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">⚙️</div>
                    <div><div class="ev-card-title">Definições de visibilidade</div></div>
                </div>
                @foreach([
                    ['privado','Evento privado','Visível apenas com link directo'],
                    ['aprovacao_manual','Aprovação manual','Confirmas cada pedido manualmente'],
                    ['permitir_comentarios','Permitir comentários','Participantes podem comentar'],
                    ['participantes_publicos','Lista pública','Outros utilizadores vêem quem vai'],
                    ['notif_nova_inscricao','Notif. nova inscrição','Alerta quando alguém se inscrever'],
                    ['notif_lembrete_24h','Lembrete 24h antes','Envio automático aos participantes'],
                    ['notif_resumo_semanal','Resumo semanal','Relatório de inscrições por semana'],
                ] as [$field, $lbl, $desc])
                <div class="tog-row">
                    <div><div class="tog-lbl">{{ $lbl }}</div><div class="tog-desc">{{ $desc }}</div></div>
                    <button type="button" class="tog-sw {{ $this->$field ? 'on' : '' }}" wire:click="$toggle('{{ $field }}')"></button>
                </div>
                @endforeach
            </div>

            {{-- STATUS --}}
            <div class="ev-card">
                <div class="ev-card-head">
                    <div class="ev-card-icon">📡</div>
                    <div>
                        <div class="ev-card-title">Estado do evento</div>
                        @if($status === 'publicado')
                        <div class="ev-card-sub" style="color:var(--amber);">⚠️ Após publicar, os bilhetes ficam imutáveis</div>
                        @endif
                    </div>
                </div>
                <div class="status-grid">
                    @foreach(['rascunho'=>['📝','Rascunho','#fbbf24'],'publicado'=>['✅','Publicado','var(--green)'],'encerrado'=>['🔒','Encerrado','var(--red)']] as $st=>[$em,$nm,$cor])
                    <div class="status-tile {{ $status === $st ? 's-'.$st : '' }}" wire:click="$set('status', '{{ $st }}')">
                        <span class="status-em">{{ $em }}</span>
                        <span class="status-nm" style="color:{{ $cor }}">{{ $nm }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- TERMOS --}}
            <div class="ev-card">
                <div class="tog-row" style="border-bottom:none;padding-bottom:0;">
                    <div>
                        <div class="tog-lbl">Aceito os termos de publicação</div>
                        <div class="tog-desc">O evento respeita as diretrizes da plataforma Luanda Tickets</div>
                    </div>
                    <button type="button" class="tog-sw {{ $termos ? 'on' : '' }}" wire:click="$toggle('termos')"></button>
                </div>
                @error('termos')<div class="fld-err" style="margin-top:8px;">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- BOTÕES DE NAVEGAÇÃO --}}
        <div class="nav-btns">
            @if($step > 1)
            <button type="button" class="nav-back" wire:click="anteriorStep">← Anterior</button>
            @endif
            @if($step < $totalSteps)
            <button type="button" class="nav-next" wire:click="proximoStep">Continuar →</button>
            @else
            <button type="button" class="nav-save" wire:click="salvar">
                <span wire:loading.remove wire:target="salvar">🚀 {{ $editando ? 'Guardar Alterações' : 'Criar Evento' }}</span>
                <span wire:loading wire:target="salvar">⏳ A guardar...</span>
            </button>
            @endif
        </div>

    </div>
</div>
</div>