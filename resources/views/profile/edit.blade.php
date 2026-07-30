@extends('layouts.app')
@section('title', 'Editar Perfil — Luanda Bilhetes')
@section('content')

@php
$user   = auth()->user();
$handle = strtolower(preg_replace('/\s+/', '.', trim($user->name)));
@endphp

<style>
*,*::before,*::after{box-sizing:border-box;}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}

/* ── COVER (igual ao show) ── */
.p-cover-wrap{position:relative;height:140px;overflow:visible;margin:-40px -16px 0;}
@@media(min-width:768px){.p-cover-wrap{height:220px;margin:-40px -40px 0;overflow:hidden;}}
.p-cover-bg{width:100%;height:100%;position:relative;background:linear-gradient(135deg,#050d1a,#091828,#0c1f3a,#071220);}
.p-cover-bg img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;}
.p-cover-glow{position:absolute;inset:0;z-index:1;pointer-events:none;background:radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.22),transparent 55%);}
.p-cover-fade{position:absolute;bottom:0;left:0;right:0;height:100px;z-index:2;background:linear-gradient(to top,#06090f,transparent);}
.cover-edit-overlay{position:absolute;inset:0;z-index:3;display:flex;align-items:center;justify-content:center;}
.cover-edit-btn{display:flex;align-items:center;gap:6px;background:rgba(6,9,15,.75);backdrop-filter:blur(8px);border:1px solid rgba(6,182,212,.3);border-radius:10px;padding:7px 12px;font-size:12px;font-weight:600;color:#e2e8f0;cursor:pointer;transition:all .2s;}
.cover-edit-btn:hover{border-color:rgba(6,182,212,.6);color:#06b6d4;}

/* ── HEADER (igual ao show) ── */
.p-header{position:relative;z-index:3;}
.p-top{display:flex;align-items:flex-start;gap:12px;margin-top:8px;padding-bottom:14px;border-bottom:1px solid rgba(6,182,212,.15);flex-wrap:wrap;}
@@media(min-width:768px){.p-top{margin-top:-55px;gap:20px;padding-bottom:20px;align-items:flex-end;}}
.p-ava{width:78px;height:78px;border-radius:50%;background:linear-gradient(135deg,#0c3a4a,#1e6a7a);border:3px solid #06090f;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:#06b6d4;overflow:hidden;box-shadow:0 8px 28px rgba(6,182,212,.3);position:relative;flex-shrink:0;cursor:pointer;}
@@media(min-width:768px){.p-ava{width:110px;height:110px;font-size:40px;}}
.p-ava img{width:100%;height:100%;object-fit:cover;}
.p-ava-edit-icon{position:absolute;inset:0;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;font-size:22px;opacity:0;transition:opacity .2s;}
.p-ava:hover .p-ava-edit-icon{opacity:1;}
.p-info{flex:1;min-width:0;width:100%;padding-bottom:6px;}
@@media(min-width:640px){.p-info{width:auto;}}
.p-name-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:3px;}
.p-name{font-size:17px;font-weight:800;color:#fff;}
@@media(min-width:768px){.p-name{font-size:24px;}}
.p-handle{font-size:11px;color:#94a3b8;}
.p-badge{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:2px 7px;border-radius:20px;}
.badge-admin{background:rgba(244,63,94,.2);border:1px solid rgba(244,63,94,.4);color:#f87171;}
.badge-creator{background:rgba(6,182,212,.2);border:1px solid rgba(6,182,212,.4);color:#22d3ee;}
.badge-user{background:rgba(148,163,184,.15);border:1px solid rgba(148,163,184,.3);color:#94a3b8;}
.p-actions{display:flex;gap:8px;align-items:center;flex-shrink:0;width:100%;padding-bottom:6px;}
@@media(min-width:640px){.p-actions{width:auto;}}
.btn-back{padding:8px 14px;border-radius:11px;font-size:12px;font-weight:600;background:#1e293b;border:1px solid #334155;color:#e2e8f0;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}

/* ── STATS ── */
.dash-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin:16px 0 20px;}
@@media(min-width:768px){.dash-stats{grid-template-columns:repeat(4,1fr);gap:13px;}}
.dstat{background:#0d1526;border:1px solid rgba(6,182,212,.15);border-radius:12px;padding:12px 14px;transition:all .2s;animation:fadeUp .4s ease both;}
.dstat:hover{border-color:rgba(6,182,212,.25);transform:translateY(-1px);}
.dstat-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}
.dstat-icon{font-size:17px;}
.dstat-trend{font-size:9px;font-weight:700;padding:2px 6px;border-radius:20px;}
.trend-up{background:rgba(16,185,129,.15);color:#10b981;}
.trend-neu{background:rgba(255,255,255,.06);color:#64748b;}
.dstat-num{font-size:20px;font-weight:800;line-height:1;margin-bottom:3px;color:#fff;}
.dstat-lbl{font-size:10px;color:#64748b;}

/* ── SECÇÕES RECOLHÍVEIS ── */
.section{background:#0d1526;border:1px solid rgba(6,182,212,.15);border-radius:14px;margin-bottom:14px;overflow:hidden;animation:fadeUp .4s ease both;}
.section-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid transparent;cursor:pointer;transition:background .2s;}
.section.open .section-head{border-bottom-color:rgba(6,182,212,.1);}
.section-head:hover{background:rgba(6,182,212,.04);}
.section-head-left{display:flex;align-items:center;gap:10px;flex:1;}
.section-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.section-icon.cyan{background:rgba(6,182,212,.12);}
.section-icon.orange{background:rgba(249,115,22,.12);}
.section-icon.purple{background:rgba(139,92,246,.12);}
.section-icon.red{background:rgba(244,63,94,.12);}
.section-icon.green{background:rgba(16,185,129,.12);}
.section-title-txt{font-size:13px;font-weight:700;color:#fff;}
.section-sub{font-size:10px;color:#64748b;margin-top:1px;}
.section-chevron{color:#64748b;font-size:14px;transition:transform .2s;margin-left:8px;}
.section.open .section-chevron{transform:rotate(180deg);}
.section-badge{font-size:9px;font-weight:700;padding:2px 8px;border-radius:20px;background:rgba(244,63,94,.15);color:#f87171;border:1px solid rgba(244,63,94,.25);}
.section-body{padding:0;max-height:0;overflow:hidden;transition:max-height .3s ease,padding .3s ease;}
.section.open .section-body{padding:16px;max-height:2000px;}

/* ── FORM ── */
.form-grid{display:grid;grid-template-columns:1fr;gap:12px;}
@@media(min-width:640px){.form-grid{grid-template-columns:1fr 1fr;}}
.form-group{display:flex;flex-direction:column;gap:5px;}
.form-group.full{grid-column:1/-1;}
.form-label{font-size:10px;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;}
.form-input{background:#111827;border:1px solid rgba(6,182,212,.15);border-radius:10px;padding:10px 12px;color:#eef0f6;font-size:13px;font-family:inherit;transition:all .2s;outline:none;width:100%;}
.form-input:focus{border-color:#06b6d4;box-shadow:0 0 0 3px rgba(6,182,212,.1);}
.form-input::placeholder{color:#64748b;}
textarea.form-input{resize:vertical;min-height:80px;line-height:1.6;}
select.form-input{cursor:pointer;-webkit-appearance:none;}
.form-error{font-size:11px;color:#f43f5e;margin-top:2px;}

/* ── TOGGLE ── */
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid rgba(6,182,212,.08);}
.toggle-row:last-child{border-bottom:none;}
.toggle-info{flex:1;padding-right:12px;}
.toggle-lbl{font-size:12px;font-weight:500;color:#eef0f6;margin-bottom:2px;}
.toggle-desc{font-size:10px;color:#64748b;}
.toggle{position:relative;width:42px;height:23px;flex-shrink:0;}
.toggle input{opacity:0;width:0;height:0;}
.toggle-track{position:absolute;inset:0;border-radius:12px;background:#162032;cursor:pointer;transition:all .25s;border:1px solid rgba(6,182,212,.15);}
.toggle input:checked + .toggle-track{background:#06b6d4;border-color:#06b6d4;}
.toggle-thumb{position:absolute;top:3px;left:3px;width:15px;height:15px;border-radius:50%;background:#fff;transition:transform .25s;box-shadow:0 1px 4px rgba(0,0,0,.3);pointer-events:none;}
.toggle input:checked ~ .toggle-thumb{transform:translateX(19px);}

/* ── SEGURANÇA ── */
.security-item{display:flex;align-items:center;gap:10px;padding:12px 0;border-bottom:1px solid rgba(6,182,212,.08);flex-wrap:wrap;}
.security-item:last-child{border-bottom:none;}
.security-icon{width:34px;height:34px;border-radius:9px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:15px;}
.security-info{flex:1;min-width:0;}
.security-lbl{font-size:12px;font-weight:600;color:#fff;margin-bottom:2px;}
.security-desc{font-size:10px;color:#64748b;}
.security-btn{padding:6px 12px;border-radius:8px;font-size:11px;font-weight:600;border:1px solid rgba(6,182,212,.2);background:rgba(6,182,212,.06);color:#e2e8f0;cursor:pointer;transition:all .2s;flex-shrink:0;font-family:inherit;text-decoration:none;display:inline-block;}
.security-btn:hover{border-color:rgba(6,182,212,.4);color:#fff;}
.security-btn.danger{color:#f87171;border-color:rgba(244,63,94,.25);background:rgba(244,63,94,.06);}
.security-btn.danger:hover{background:rgba(244,63,94,.12);}

/* ── SAVE BAR FIXA ── */
.save-bar{position:fixed;bottom:0;left:0;right:0;z-index:999;padding:12px 16px;background:rgba(6,9,15,.95);backdrop-filter:blur(20px);border-top:1px solid rgba(6,182,212,.2);display:flex;align-items:center;justify-content:space-between;transform:translateY(100%);transition:transform .3s;gap:10px;flex-wrap:wrap;}
.save-bar.visible{transform:translateY(0);}
.save-bar-msg{font-size:12px;color:#94a3b8;}
.save-bar-msg strong{color:#f59e0b;}
.save-bar-actions{display:flex;gap:8px;}
.tbtn{padding:8px 14px;border-radius:10px;font-size:12px;font-weight:600;border:1px solid rgba(6,182,212,.2);background:#111827;color:#e2e8f0;cursor:pointer;font-family:inherit;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.tbtn:hover{border-color:rgba(255,255,255,.16);color:#fff;}
.tbtn.primary{background:linear-gradient(135deg,#06b6d4,#0ea5e9);border:none;color:#fff;box-shadow:0 4px 14px rgba(6,182,212,.3);}
.tbtn.primary:hover{opacity:.9;}

/* ── AVATAR UPLOAD ── */
.avatar-upload-label{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#111827;border:1px dashed rgba(6,182,212,.35);border-radius:10px;cursor:pointer;transition:all .2s;font-size:12px;color:#94a3b8;}
.avatar-upload-label:hover{border-color:#06b6d4;color:#e2e8f0;background:rgba(6,182,212,.06);}

/* ── MODAL ELIMINAR ── */
.modal-delete{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);padding:16px;}
.modal-delete.open{display:flex;}
.modal-box{background:#0c1220;border:1px solid rgba(244,63,94,.25);border-radius:18px;padding:24px;max-width:400px;width:100%;}

.success-toast{padding:12px 16px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;color:#34d399;font-size:13px;font-weight:600;margin-bottom:14px;}
</style>

{{-- COVER clicável --}}
<div class="p-cover-wrap">
    <div class="p-cover-bg">
        @if($user->cover)
        <img id="coverPreview" src="{{ asset('storage/'.$user->cover) }}" alt="">
        @else
        <img id="coverPreview" style="display:none;" alt="">
        @endif
        <div class="p-cover-glow"></div>
    </div>
    <div class="p-cover-fade"></div>
    <label class="cover-edit-overlay" style="cursor:pointer;">
    <input type="file" id="cover-input" name="cover" accept="image/*" form="profileForm" style="display:none"
           onchange="previewCover(this);markDirty()">
    <div class="cover-edit-btn">📷 Alterar capa</div>
    </label>
</div>

{{-- HEADER igual ao show --}}
<div class="p-header">
    <div class="p-top">
        <label class="p-ava" style="cursor:pointer;">
            <input type="file" id="avatar-input" name="avatar" accept="image/*" form="profileForm" style="display:none"
                   onchange="previewAvatar(this);markDirty()">
            @if($user->avatar)
                <img id="avatar-preview" src="{{ asset('storage/'.$user->avatar) }}" alt="">
            @else
                <span id="avatar-initials">{{ strtoupper(substr($user->name,0,2)) }}</span>
                <img id="avatar-preview" src="" alt="" style="display:none">
            @endif
            <div class="p-ava-edit-icon">📷</div>
        </label>

        <div class="p-info">
            <div class="p-name-row">
                <span class="p-name">{{ e($user->name) }}</span>
                @if($user->is_verified)<span style="color:#06b6d4;font-size:14px;">✓</span>@endif
                <span class="p-badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="p-handle">@{{ $handle }} · <span style="color:#10b981;">● Online</span></div>
            <div style="font-size:11px;color:#64748b;margin-top:3px;">Clica na foto ou capa para alterar</div>
        </div>

        <div class="p-actions">
            <a href="{{ route('profile.show', ['id' => $user->id]) }}" class="btn-back">👁 Ver como os outros vêem</a>
        </div>
    </div>
</div>

{{-- SUCCESS --}}
@if(session('status') === 'profile-updated')
<div class="success-toast">✅ Perfil actualizado com sucesso!</div>
@endif

{{-- STATS --}}
<div class="dash-stats">
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">📅</div><div class="dstat-trend trend-neu">→</div></div>
        @if($user->role === 'creator')
            <div class="dstat-num">{{ $user->eventos()->count() }}</div>
            <div class="dstat-lbl">Eventos criados</div>
        @elseif($user->role === 'admin')
            <div class="dstat-num">{{ \App\Models\Evento::count() }}</div>
            <div class="dstat-lbl">Total eventos</div>
        @else
            <div class="dstat-num">{{ $user->eventosCurtidos()->count() }}</div>
            <div class="dstat-lbl">Eventos curtidos</div>
        @endif
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">📝</div><div class="dstat-trend trend-neu">→</div></div>
        <div class="dstat-num">{{ $user->postagens()->count() }}</div>
        <div class="dstat-lbl">Publicações</div>
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">👥</div><div class="dstat-trend trend-up">↑</div></div>
        <div class="dstat-num">{{ $user->seguidores()->count() }}</div>
        <div class="dstat-lbl">Seguidores</div>
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">🎟</div><div class="dstat-trend trend-up">↑</div></div>
        <div class="dstat-num">{{ $user->eventosCurtidos()->count() }}</div>
        <div class="dstat-lbl">Bilhetes / Curtidos</div>
    </div>
</div>

{{-- FORM PRINCIPAL --}}
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
@csrf @method('PATCH')

    {{-- INFORMAÇÕES PESSOAIS --}}
    <div class="section open" style="animation-delay:.05s">
        <div class="section-head" onclick="toggleSection(this)">
            <div class="section-head-left">
                <div class="section-icon cyan">👤</div>
                <div>
                    <div class="section-title-txt">Informações Pessoais</div>
                    <div class="section-sub">Nome, email e foto de perfil</div>
                </div>
            </div>
            <span class="section-chevron">⌄</span>
        </div>
        <div class="section-body">
            <div class="form-group full" style="margin-bottom:14px;">
                <label class="form-label">Foto de perfil</label>
                <label for="avatar-input" class="avatar-upload-label">
                    📸 Clica para carregar nova foto · JPG, PNG (máx. 2MB)
                </label>
                @error('avatar')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nome completo</label>
                    <input type="text" name="name" class="form-input"
                           value="{{ old('name',$user->name) }}" oninput="markDirty()" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input"
                           value="{{ old('email',$user->email) }}" oninput="markDirty()" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group full">
                    <label class="form-label">Bio <span style="color:#64748b;font-weight:400;">(aparece no teu perfil público)</span></label>
                    <textarea name="bio" class="form-input" rows="3" maxlength="300"
                              placeholder="Ex: Apaixonado por eventos culturais em Luanda 🇦🇴"
                              oninput="markDirty();document.getElementById('bio-count').textContent=this.value.length+'/300 caracteres'">{{ old('bio',$user->bio) }}</textarea>
                    <span id="bio-count" style="font-size:11px;color:#64748b;margin-top:3px;">{{ strlen($user->bio??'') }}/300 caracteres</span>
                    @error('bio')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            @if(!$user->email_verified_at)
            <div style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:10px 14px;margin-top:12px;font-size:12px;color:#f59e0b;">
                ⚠️ O teu email ainda não foi verificado.
            </div>
            @endif
        </div>
    </div>

    {{-- PRIVACIDADE --}}
    <div class="section" style="animation-delay:.10s">
        <div class="section-head" onclick="toggleSection(this)">
            <div class="section-head-left">
                <div class="section-icon purple">🛡</div>
                <div>
                    <div class="section-title-txt">Privacidade</div>
                    <div class="section-sub">Controla quem vê o teu perfil e conteúdo</div>
                </div>
            </div>
            <span class="section-chevron">⌄</span>
        </div>
        <div class="section-body">
            <div class="form-grid" style="margin-bottom:14px;">
                <div class="form-group">
                    <label class="form-label">Visibilidade do perfil</label>
                    <select name="visibilidade_perfil" class="form-input" onchange="markDirty();atualizarDescPrivacidade(this)">
                        <option value="publico"   {{ ($user->visibilidade_perfil??'publico')==='publico'   ? 'selected' : '' }}>🌍 Público</option>
                        <option value="seguidores"{{ ($user->visibilidade_perfil??'')==='seguidores' ? 'selected' : '' }}>👥 Apenas seguidores</option>
                        <option value="privado"   {{ ($user->visibilidade_perfil??'')==='privado'   ? 'selected' : '' }}>🔒 Privado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quem pode enviar mensagens</label>
                    <select name="quem_mensagens" class="form-input" onchange="markDirty()">
                        <option value="todos"     {{ ($user->quem_mensagens??'todos')==='todos'     ? 'selected' : '' }}>🌍 Todos</option>
                        <option value="seguidores"{{ ($user->quem_mensagens??'')==='seguidores' ? 'selected' : '' }}>👥 Apenas quem sigo</option>
                        <option value="ninguem"   {{ ($user->quem_mensagens??'')==='ninguem'   ? 'selected' : '' }}>🔒 Ninguém</option>
                    </select>
                </div>
            </div>

            {{-- Descrição dinâmica da privacidade --}}
            <div id="priv-desc" style="padding:10px 14px;background:rgba(139,92,246,.08);border:1px solid rgba(139,92,246,.2);border-radius:10px;font-size:12px;color:#a78bfa;margin-bottom:14px;line-height:1.6;">
                @if(($user->visibilidade_perfil??'publico')==='privado')
                    🔒 <strong>Perfil privado</strong> — Apenas os teus seguidores aprovados vêem as tuas publicações, bilhetes e eventos. Outros utilizadores vêem apenas o teu nome e foto.
                @elseif(($user->visibilidade_perfil??'')==='seguidores')
                    👥 <strong>Apenas seguidores</strong> — Quem te segue vê tudo. Outros utilizadores vêem apenas o teu nome e foto de perfil.
                @else
                    🌍 <strong>Perfil público</strong> — Qualquer pessoa pode ver o teu perfil, publicações e eventos.
                @endif
            </div>

            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Mostrar eventos que vou frequentar</div>
                    <div class="toggle-desc">Outros utilizadores podem ver os eventos nos quais tens bilhetes</div>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="mostrar_bilhetes" value="1"
                           {{ ($user->mostrar_bilhetes??true) ? 'checked' : '' }} onchange="markDirty()">
                    <div class="toggle-track"></div><div class="toggle-thumb"></div>
                </label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Mostrar lista de seguidores</div>
                    <div class="toggle-desc">Outros utilizadores podem ver quem te segue e quem segues</div>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="mostrar_seguidores" value="1"
                           {{ ($user->mostrar_seguidores??true) ? 'checked' : '' }} onchange="markDirty()">
                    <div class="toggle-track"></div><div class="toggle-thumb"></div>
                </label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Permitir ser encontrado por pesquisa</div>
                    <div class="toggle-desc">O teu perfil aparece nos resultados de pesquisa</div>
                </div>
                <label class="toggle">
                    <input type="checkbox" name="pesquisavel" value="1"
                           {{ ($user->pesquisavel??true) ? 'checked' : '' }} onchange="markDirty()">
                    <div class="toggle-track"></div><div class="toggle-thumb"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- NOTIFICAÇÕES --}}
    <div class="section" style="animation-delay:.14s">
        <div class="section-head" onclick="toggleSection(this)">
            <div class="section-head-left">
                <div class="section-icon orange">🔔</div>
                <div>
                    <div class="section-title-txt">Notificações</div>
                    <div class="section-sub">Controla o que recebes e como</div>
                </div>
            </div>
            <span class="section-chevron">⌄</span>
        </div>
        <div class="section-body">
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Novos eventos perto de mim</div>
                    <div class="toggle-desc">Quando alguém que segues publica um evento</div>
                </div>
                <label class="toggle"><input type="checkbox" name="notif_eventos" value="1" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Bilhetes a esgotar</div>
                    <div class="toggle-desc">Alerta quando um evento nos teus interesses está quase esgotado</div>
                </div>
                <label class="toggle"><input type="checkbox" name="notif_bilhetes" value="1" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Mensagens privadas</div>
                    <div class="toggle-desc">Notificação quando recebes uma mensagem nova</div>
                </div>
                <label class="toggle"><input type="checkbox" name="notif_mensagens" value="1" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Novos seguidores</div>
                    <div class="toggle-desc">Quando alguém começa a seguir o teu perfil</div>
                </div>
                <label class="toggle"><input type="checkbox" name="notif_seguidores" value="1" onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
        </div>
    </div>

    {{-- SEGURANÇA --}}
    <div class="section" style="animation-delay:.18s">
        <div class="section-head" onclick="toggleSection(this)">
            <div class="section-head-left">
                <div class="section-icon red">🔐</div>
                <div>
                    <div class="section-title-txt">Segurança</div>
                    <div class="section-sub">Palavra-passe e autenticação</div>
                </div>
            </div>
            <span class="section-badge">Recomendado</span>
            <span class="section-chevron" style="margin-left:10px;">⌄</span>
        </div>
        <div class="section-body">
            <div class="security-item">
                <div class="security-icon" style="background:rgba(16,185,129,.1);">🔑</div>
                <div class="security-info">
                    <div class="security-lbl">Palavra-passe</div>
                    <div class="security-desc">Altera a tua palavra-passe · ●●●●●●●●</div>
                </div>
                <button type="button" class="security-btn" onclick="alert('Em breve!')">Alterar</button>
            </div>
            <div class="security-item">
                <div class="security-icon" style="background:rgba(6,182,212,.1);">📱</div>
                <div class="security-info">
                    <div class="security-lbl">Autenticação em dois factores (2FA)</div>
                    <div class="security-desc">Adiciona uma camada extra de segurança</div>
                </div>
                <button type="button" class="security-btn" onclick="alert('Em breve!')">Ativar</button>
            </div>
            <div class="security-item">
                <div class="security-icon" style="background:rgba(244,63,94,.1);">🗑</div>
                <div class="security-info">
                    <div class="security-lbl">Eliminar conta</div>
                    <div class="security-desc">Esta acção é irreversível — todos os dados serão apagados</div>
                </div>
                <button type="button" class="security-btn danger"
                        onclick="document.getElementById('modal-delete').classList.add('open')">
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    {{-- SAVE BAR FIXA --}}
    <div class="save-bar" id="saveBar">
        <div class="save-bar-msg">Tens <strong>alterações não guardadas</strong>!</div>
        <div class="save-bar-actions">
            <button type="button" class="tbtn" onclick="hideSaveBar()">Descartar</button>
            <button type="submit" class="tbtn primary">💾 Guardar</button>
        </div>
    </div>
</form>

{{-- DADOS BANCÁRIOS (fora do form principal) --}}
@if(auth()->user()->role === 'creator' || auth()->user()->role === 'admin')
@php $dadosBancarios = \App\Models\DadosBancariosCriador::where('user_id', auth()->id())->first(); @endphp
<div class="section" style="margin-top:14px;">
    <div class="section-head" onclick="toggleSection(this)">
        <div class="section-head-left">
            <div class="section-icon" style="background:rgba(16,185,129,.12);">🏦</div>
            <div>
                <div class="section-title-txt">Dados Bancários para Recebimento</div>
                <div class="section-sub">Privado — só o admin vê. Necessário para receber os 90% das vendas.</div>
            </div>
        </div>
        <span class="section-chevron">⌄</span>
    </div>
    <div class="section-body">
        @if(session('status') === 'dados-bancarios-guardados')
        <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;padding:10px 14px;margin-bottom:12px;color:#34d399;font-size:12px;">
            ✅ Dados bancários guardados com sucesso!
        </div>
        @endif
        <form method="POST" action="{{ route('dados-bancarios.guardar') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nome do Banco</label>
                    <input type="text" name="nome_banco" class="form-input"
                           value="{{ old('nome_banco', $dadosBancarios->nome_banco ?? '') }}"
                           placeholder="Ex: BFA, BAI, BIC..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Titular da Conta</label>
                    <input type="text" name="titular" class="form-input"
                           value="{{ old('titular', $dadosBancarios->titular ?? '') }}"
                           placeholder="Nome completo do titular" required>
                </div>
                <div class="form-group full">
                    <label class="form-label">IBAN</label>
                    <input type="text" name="iban" class="form-input"
                           value="{{ old('iban', $dadosBancarios->iban ?? '') }}"
                           placeholder="AO06 0006 0000 0000 0000 1014 3" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Número de Conta (opcional)</label>
                    <input type="text" name="numero_conta" class="form-input"
                           value="{{ old('numero_conta', $dadosBancarios->numero_conta ?? '') }}"
                           placeholder="Opcional">
                </div>
            </div>
            <button type="submit" class="btn-save-full" style="margin-top:8px;">
                💾 Guardar Dados Bancários
            </button>
        </form>
    </div>
</div>
@endif


{{-- MODAL ELIMINAR CONTA --}}
<div id="modal-delete" class="modal-delete" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box">
        <h3 style="font-size:16px;font-weight:800;color:#fff;margin-bottom:8px;">⚠️ Eliminar conta</h3>
        <p style="font-size:12px;color:#94a3b8;margin-bottom:18px;line-height:1.6;">Esta acção é irreversível. Todos os teus dados, eventos e bilhetes serão permanentemente apagados.</p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Confirma a tua palavra-passe</label>
                <input type="password" name="password" class="form-input" placeholder="A tua palavra-passe actual" required>
                @error('password','userDeletion')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="tbtn" onclick="document.getElementById('modal-delete').classList.remove('open')">Cancelar</button>
                <button type="submit" class="tbtn" style="background:rgba(244,63,94,.15);border-color:rgba(244,63,94,.35);color:#f87171;">🗑 Eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSection(head){head.closest('.section').classList.toggle('open');}
function markDirty(){document.getElementById('saveBar').classList.add('visible');}
function hideSaveBar(){document.getElementById('saveBar').classList.remove('visible');document.getElementById('profileForm').reset();}

function previewAvatar(input){
    if(!input.files[0])return;
    const reader=new FileReader();
    reader.onload=e=>{
        const img=document.getElementById('avatar-preview');
        const ini=document.getElementById('avatar-initials');
        img.src=e.target.result;img.style.display='block';
        if(ini)ini.style.display='none';
    };
    reader.readAsDataURL(input.files[0]);
}

function previewCover(input){
    if(!input.files[0])return;
    const reader=new FileReader();
    reader.onload=e=>{
        const img=document.getElementById('coverPreview');
        img.src=e.target.result;img.style.display='block';
    };
    reader.readAsDataURL(input.files[0]);
}

function atualizarDescPrivacidade(sel){
    const desc=document.getElementById('priv-desc');
    const msgs={
        publico:  '🌍 <strong>Perfil público</strong> — Qualquer pessoa pode ver o teu perfil, publicações e eventos.',
        seguidores:'👥 <strong>Apenas seguidores</strong> — Quem te segue vê tudo. Outros utilizadores vêem apenas o teu nome e foto de perfil.',
        privado:  '🔒 <strong>Perfil privado</strong> — Apenas os teus seguidores aprovados vêem as tuas publicações, bilhetes e eventos. Outros utilizadores vêem apenas o teu nome e foto.',
    };
    desc.innerHTML=msgs[sel.value]||msgs.publico;
}

@if($errors->any())
document.getElementById('saveBar').classList.add('visible');
@endif
</script>

@endsection