@extends('layouts.app')

@section('title', 'Meu Perfil — Luanda Tickets')

@section('content')
<style>
:root {
  --bg:      #06090f;
  --s1:      #0d1526;
  --s2:      #111827;
  --s3:      #162032;
  --border:  rgba(6,182,212,0.15);
  --accent:  #06b6d4;
  --accent2: #0ea5e9;
  --gold:    #f59e0b;
  --green:   #10b981;
  --red:     #f43f5e;
  --orange:  #f97316;
  --purple:  #8b5cf6;
  --text:    #eef0f6;
  --muted:   #64748b;
  --muted2:  #94a3b8;
}

@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

/* ── TOP BAR ── */
.edit-topbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 20px; flex-wrap: wrap; gap: 10px;
  animation: fadeUp .3s ease both;
}
.edit-topbar-title { font-size: 18px; font-weight: 800; color: #fff; letter-spacing: -.4px; }
@media(min-width:768px){ .edit-topbar-title { font-size: 22px; } }
.edit-topbar-title span { color: var(--accent); }
.edit-topbar-actions { display: flex; gap: 10px; }
.tbtn {
  padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 600;
  border: 1px solid var(--border); background: var(--s2);
  color: var(--text); cursor: pointer; transition: all .2s; text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
}
@media(min-width:768px){ .tbtn { padding: 9px 18px; font-size: 13px; } }
.tbtn:hover { border-color: rgba(255,255,255,.16); color: #fff; }
.tbtn.primary {
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  border: none; color: #fff; box-shadow: 0 4px 14px rgba(6,182,212,.3);
}
.tbtn.primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(6,182,212,.4); }

/* ── COVER + AVA ── */
.cover-section {
  border-radius: 16px; overflow: hidden; margin-bottom: 20px;
  border: 1px solid var(--border); animation: fadeUp .35s ease both;
}
.cover-edit {
  height: 140px; position: relative; cursor: pointer;
  background: linear-gradient(135deg,#050d1a 0%,#091828 40%,#0c1f3a 70%,#071220 100%);
  overflow: hidden;
}
@media(min-width:768px){ .cover-edit { height: 190px; } }
.cover-edit img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
.cover-edit::before {
  content: ''; position: absolute; inset: 0; z-index: 1;
  background: radial-gradient(ellipse at 60% 40%, rgba(6,182,212,.15) 0%, transparent 60%);
  pointer-events: none;
}
.cover-edit-btn {
  position: absolute; top: 10px; right: 10px; z-index: 2;
  display: flex; align-items: center; gap: 5px;
  background: rgba(6,9,15,.8); backdrop-filter: blur(10px);
  border: 1px solid var(--border); border-radius: 9px;
  padding: 6px 10px; font-size: 11px; font-weight: 600; color: var(--text);
  cursor: pointer; transition: all .2s;
}
.cover-edit-btn:hover { border-color: rgba(6,182,212,.35); color: var(--accent); }

.ava-section {
  background: #0d1526; border-top: 1px solid rgba(6,182,212,.15);
  padding: 0 16px 16px;
  display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
}
@media(min-width:768px){ .ava-section { padding: 0 24px 22px; gap: 18px; } }
.ava-wrap { position: relative; margin-top: -40px; flex-shrink: 0; }
@media(min-width:768px){ .ava-wrap { margin-top: -48px; } }
.ava-main {
  width: 80px; height: 80px; border-radius: 50%;
  background: linear-gradient(135deg,#0c3a4a,#1e6a7a);
  border: 3px solid var(--bg);
  display: flex; align-items: center; justify-content: center;
  font-size: 26px; font-weight: 800; color: var(--accent);
  cursor: pointer; box-shadow: 0 8px 28px rgba(6,182,212,.2);
  transition: all .2s; overflow: hidden;
}
@media(min-width:768px){ .ava-main { width: 96px; height: 96px; font-size: 34px; } }
.ava-main img { width: 100%; height: 100%; object-fit: cover; }
.ava-edit-icon {
  position: absolute; bottom: 2px; right: 2px;
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--accent); border: 2px solid var(--bg);
  display: flex; align-items: center; justify-content: center;
  font-size: 10px; cursor: pointer;
}
.ava-info { padding-bottom: 4px; flex: 1; min-width: 0; }
.ava-name { font-size: 16px; font-weight: 800; letter-spacing: -.3px; color: #fff; }
@media(min-width:768px){ .ava-name { font-size: 20px; } }
.ava-handle { font-size: 11px; color: var(--muted2); margin-top: 2px; }
.ava-view-btn {
  padding: 7px 12px; border-radius: 10px;
  font-size: 11px; font-weight: 600; color: var(--muted2);
  border: 1px solid var(--border); background: var(--s2);
  cursor: pointer; transition: all .2s; align-self: flex-end; text-decoration: none;
  display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
}
@media(min-width:768px){ .ava-view-btn { margin-left: auto; font-size: 12px; padding: 8px 16px; } }
.ava-view-btn:hover { color: var(--text); border-color: rgba(255,255,255,.16); }

/* ── STATS ── */
.dash-stats {
  display: grid; grid-template-columns: repeat(2,1fr); gap: 10px;
  margin-bottom: 20px; animation: fadeUp .4s .04s ease both;
}
@media(min-width:768px){ .dash-stats { grid-template-columns: repeat(4,1fr); gap: 13px; margin-bottom: 24px; } }
.dstat {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 12px; padding: 12px 14px; transition: all .2s;
}
.dstat:hover { border-color: rgba(6,182,212,.2); transform: translateY(-1px); }
.dstat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.dstat-icon { font-size: 17px; }
.dstat-trend { font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 20px; }
.trend-up  { background: rgba(16,185,129,.15); color: var(--green); }
.trend-neu { background: rgba(255,255,255,.06); color: var(--muted); }
.dstat-num { font-size: 20px; font-weight: 800; line-height: 1; margin-bottom: 3px; color: #fff; }
@media(min-width:768px){ .dstat-num { font-size: 24px; } }
.dstat-lbl { font-size: 10px; color: var(--muted); }

/* ── SECTIONS ── */
.section {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 14px; margin-bottom: 14px; overflow: hidden;
  animation: fadeUp .4s ease both;
}
.section-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px; border-bottom: 1px solid var(--border);
  cursor: pointer; transition: background .2s;
}
@media(min-width:768px){ .section-head { padding: 16px 20px; } }
.section-head-left { display: flex; align-items: center; gap: 10px; }
.section-icon {
  width: 30px; height: 30px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;
}
@media(min-width:768px){ .section-icon { width: 34px; height: 34px; font-size: 15px; } }
.section-icon.cyan   { background: rgba(6,182,212,.12); }
.section-icon.gold   { background: rgba(245,158,11,.12); }
.section-icon.purple { background: rgba(139,92,246,.12); }
.section-icon.green  { background: rgba(16,185,129,.12); }
.section-icon.red    { background: rgba(244,63,94,.12); }
.section-icon.orange { background: rgba(249,115,22,.12); }
.section-title-txt { font-size: 13px; font-weight: 700; color: #fff; }
@media(min-width:768px){ .section-title-txt { font-size: 14px; } }
.section-sub { font-size: 10px; color: var(--muted); margin-top: 1px; }
.section-chevron { color: var(--muted); font-size: 14px; transition: transform .2s; }
.section.open .section-chevron { transform: rotate(180deg); }
.section-badge {
  font-size: 9px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
  background: rgba(244,63,94,.15); color: var(--red); border: 1px solid rgba(244,63,94,.25);
}
.section-body { padding: 16px; display: none; }
@media(min-width:768px){ .section-body { padding: 20px; } }
.section.open .section-body { display: block; }

/* ── FORM ── */
.form-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
@media(min-width:640px){ .form-grid { grid-template-columns: 1fr 1fr; } }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: span 1; }
@media(min-width:640px){ .form-group.full { grid-column: span 2; } }
.form-label {
  font-size: 10px; font-weight: 600; color: var(--muted2);
  text-transform: uppercase; letter-spacing: .6px;
}
.form-input {
  background: var(--s2); border: 1px solid var(--border);
  border-radius: 10px; padding: 10px 12px;
  color: var(--text); font-size: 13px; font-family: inherit;
  transition: all .2s; outline: none; width: 100%;
}
.form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(6,182,212,.1); }
.form-input::placeholder { color: var(--muted); }
textarea.form-input { resize: vertical; min-height: 80px; }
select.form-input { cursor: pointer; }
.form-error { font-size: 11px; color: var(--red); margin-top: 2px; }

/* ── AVATAR FILE INPUT ── */
.avatar-upload-label {
  display: flex; align-items: center; gap: 10px; padding: 10px 14px;
  background: var(--s2); border: 1px dashed rgba(6,182,212,.35);
  border-radius: 10px; cursor: pointer; transition: all .2s;
  font-size: 12px; color: var(--muted2);
}
.avatar-upload-label:hover { border-color: var(--accent); color: var(--text); background: rgba(6,182,212,.06); }

/* ── TOGGLE ── */
.toggle-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 11px 0; border-bottom: 1px solid var(--border);
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; padding-right: 12px; }
.toggle-lbl { font-size: 12px; font-weight: 500; color: var(--text); margin-bottom: 2px; }
@media(min-width:768px){ .toggle-lbl { font-size: 13px; } }
.toggle-desc { font-size: 10px; color: var(--muted); }
.toggle { position: relative; width: 42px; height: 23px; flex-shrink: 0; }
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle-track {
  position: absolute; inset: 0; border-radius: 12px;
  background: var(--s3); cursor: pointer; transition: all .25s;
  border: 1px solid var(--border);
}
.toggle input:checked + .toggle-track { background: var(--accent); border-color: var(--accent); }
.toggle-thumb {
  position: absolute; top: 3px; left: 3px;
  width: 15px; height: 15px; border-radius: 50%;
  background: #fff; transition: transform .25s;
  box-shadow: 0 1px 4px rgba(0,0,0,.3); pointer-events: none;
}
.toggle input:checked ~ .toggle-thumb { transform: translateX(19px); }

/* ── SECURITY ── */
.security-item {
  display: flex; align-items: center; gap: 10px; padding: 12px 0;
  border-bottom: 1px solid var(--border); flex-wrap: wrap;
}
.security-item:last-child { border-bottom: none; }
.security-icon {
  width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.security-info { flex: 1; min-width: 0; }
.security-lbl { font-size: 12px; font-weight: 600; color: #fff; margin-bottom: 2px; }
@media(min-width:768px){ .security-lbl { font-size: 13px; } }
.security-desc { font-size: 10px; color: var(--muted); }
.security-btn {
  padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 600;
  border: 1px solid var(--border); background: var(--s2); color: var(--text);
  cursor: pointer; transition: all .2s; flex-shrink: 0; font-family: inherit;
}
.security-btn:hover { border-color: rgba(255,255,255,.16); }
.security-btn.danger {
  color: var(--red); border-color: rgba(244,63,94,.25); background: rgba(244,63,94,.06);
}
.security-btn.danger:hover { background: rgba(244,63,94,.12); }

/* ── SAVE BAR ── */
.save-bar {
  position: fixed; bottom: 0; left: 0; right: 0; z-index: 999;
  padding: 12px 16px;
  background: rgba(6,9,15,.93); backdrop-filter: blur(20px);
  border-top: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
  transform: translateY(100%); transition: transform .3s; gap: 10px; flex-wrap: wrap;
}
@media(min-width:768px){ .save-bar { padding: 14px 36px; } }
.save-bar.visible { transform: translateY(0); }
.save-bar-msg { font-size: 12px; color: var(--muted2); }
@media(min-width:768px){ .save-bar-msg { font-size: 13px; } }
.save-bar-msg strong { color: var(--gold); }
.save-bar-actions { display: flex; gap: 8px; }
</style>

{{-- TOP BAR --}}
<div class="edit-topbar">
    <div class="edit-topbar-title">Meu <span>Perfil</span></div>
    <div class="edit-topbar-actions">
        <a href="{{ route('profile.show', auth()->user()->id) }}" class="tbtn">👁 Ver perfil</a>
    </div>
</div>

{{-- COVER + AVA --}}
<div class="cover-section">
    <div class="cover-edit" id="coverWrap">
        @if(isset(auth()->user()->cover) && auth()->user()->cover)
            <img src="{{ asset('storage/'.auth()->user()->cover) }}" alt="cover" id="coverPreview">
        @else
            <img src="" alt="" id="coverPreview" style="display:none">
        @endif
        <label for="cover-input" class="cover-edit-btn">📷 Alterar capa</label>
        <input type="file" id="cover-input" accept="image/*" style="display:none" onchange="previewCover(this)">
    </div>
    <div class="ava-section">
        <div class="ava-wrap">
            <div class="ava-main" id="avaMainEl" onclick="document.getElementById('avatar-input').click()">
                @if(auth()->user()->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                @else
                    <span id="avatar-initials">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    <img id="avatar-preview" src="" alt="" style="display:none">
                @endif
            </div>
            <div class="ava-edit-icon" onclick="document.getElementById('avatar-input').click()">📷</div>
        </div>
        <div class="ava-info">
            <div class="ava-name">{{ auth()->user()->name }}</div>
            <div class="ava-handle">
                @php $h = strtolower(preg_replace('/\s+/', '.', trim(auth()->user()->name))); @endphp
                &#64;{{ $h }} · <span style="color:var(--green)">● Online</span>
            </div>
        </div>
        <a href="{{ route('profile.show', auth()->user()->id) }}" class="ava-view-btn">Ver como os outros vêem →</a>
    </div>
</div>

{{-- STATS --}}
<div class="dash-stats">
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">📅</div><div class="dstat-trend trend-neu">→</div></div>
        @if(auth()->user()->role === 'creator')
            <div class="dstat-num">{{ auth()->user()->eventos()->count() }}</div>
            <div class="dstat-lbl">Eventos criados</div>
        @elseif(auth()->user()->role === 'admin')
            <div class="dstat-num">{{ \App\Models\Evento::count() }}</div>
            <div class="dstat-lbl">Total eventos</div>
        @else
            <div class="dstat-num">{{ auth()->user()->eventosCurtidos()->count() }}</div>
            <div class="dstat-lbl">Eventos curtidos</div>
        @endif
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">📝</div><div class="dstat-trend trend-neu">→</div></div>
        <div class="dstat-num">{{ auth()->user()->postagens()->count() }}</div>
        <div class="dstat-lbl">Publicações</div>
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">👥</div><div class="dstat-trend trend-up">↑</div></div>
        <div class="dstat-num">0</div>
        <div class="dstat-lbl">Seguidores</div>
    </div>
    <div class="dstat">
        <div class="dstat-top"><div class="dstat-icon">🎟</div><div class="dstat-trend trend-up">↑</div></div>
        @if(auth()->user()->role === 'creator')
            @php $totalVendidos = auth()->user()->eventos->sum(fn($e) => optional($e->tiposIngresso)->sum('quantidade_vendida') ?? 0); @endphp
            <div class="dstat-num">{{ number_format($totalVendidos) }}</div>
            <div class="dstat-lbl">Bilhetes vendidos</div>
        @else
            <div class="dstat-num">{{ auth()->user()->eventosCurtidos()->count() }}</div>
            <div class="dstat-lbl">Bilhetes comprados</div>
        @endif
    </div>
</div>

{{-- FORM PRINCIPAL --}}
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
    @csrf
    @method('patch')
    <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display:none" onchange="previewAvatar(this); markDirty()">

    {{-- INFORMAÇÕES PESSOAIS --}}
    <div class="section open" style="animation-delay:.06s">
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
            @if(session('status') === 'profile-updated')
            <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); border-radius:10px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:var(--green);">
                ✅ Perfil actualizado com sucesso!
            </div>
            @endif

            <div class="form-group full" style="margin-bottom:14px">
                <label class="form-label">Foto de perfil</label>
                <label for="avatar-input" class="avatar-upload-label">
                    📸 Clica para carregar nova foto · JPG, PNG ou GIF (máx. 2MB)
                </label>
                @error('avatar') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nome completo</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', auth()->user()->name) }}" oninput="markDirty()">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', auth()->user()->email) }}" oninput="markDirty()">
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label class="form-label">Bio <span style="color:var(--muted);font-weight:400;">(aparece no teu perfil público)</span></label>
                <textarea name="bio" class="form-input" rows="3" maxlength="300"
                          placeholder="Ex: Apaixonada por eventos culturais em Luanda 🇦🇴"
                          oninput="markDirty(); this.nextElementSibling.textContent = this.value.length + '/300 caracteres'">{{ old('bio', auth()->user()->bio) }}</textarea>
                <span style="font-size:11px;color:var(--muted);margin-top:3px;">{{ strlen(auth()->user()->bio ?? '') }}/300 caracteres</span>
                @error('bio') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            @if(auth()->user()->email_verified_at === null)
            <div style="background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); border-radius:10px; padding:10px 14px; margin-top:12px; font-size:12px; color:var(--gold);">
                ⚠️ O teu email ainda não foi verificado.
            </div>
            @endif
        </div>
    </div>

    {{-- NOTIFICAÇÕES --}}
    <div class="section" style="animation-delay:.10s">
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
                    <div class="toggle-desc">Quando alguém que segues publica um evento em Luanda</div>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Bilhetes a esgotar</div>
                    <div class="toggle-desc">Alerta quando um evento nos teus interesses está quase esgotado</div>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Mensagens privadas</div>
                    <div class="toggle-desc">Notificação quando recebes uma mensagem nova</div>
                </div>
                <label class="toggle"><input type="checkbox" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-lbl">Novos seguidores</div>
                    <div class="toggle-desc">Quando alguém começa a seguir o teu perfil</div>
                </div>
                <label class="toggle"><input type="checkbox" onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
            </div>
        </div>
    </div>

    {{-- PRIVACIDADE --}}
    <div class="section" style="animation-delay:.14s">
        <div class="section-head" onclick="toggleSection(this)">
            <div class="section-head-left">
                <div class="section-icon purple">🛡</div>
                <div>
                    <div class="section-title-txt">Privacidade</div>
                    <div class="section-sub">Quem pode ver o quê no teu perfil</div>
                </div>
            </div>
            <span class="section-chevron">⌄</span>
        </div>
        <div class="section-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Visibilidade do perfil</label>
                    <select class="form-input" onchange="markDirty()">
                        <option>🌍 Público</option>
                        <option>👥 Apenas seguidores</option>
                        <option>🔒 Privado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Quem pode enviar mensagens</label>
                    <select class="form-input" onchange="markDirty()">
                        <option>🌍 Todos</option>
                        <option selected>👥 Apenas quem sigo</option>
                        <option>🔒 Ninguém</option>
                    </select>
                </div>
            </div>
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-lbl">Mostrar eventos que vou frequentar</div>
                        <div class="toggle-desc">Outros utilizadores podem ver os eventos nos quais tens bilhetes</div>
                    </div>
                    <label class="toggle"><input type="checkbox" checked onchange="markDirty()"><div class="toggle-track"></div><div class="toggle-thumb"></div></label>
                </div>
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
            <span class="section-chevron" style="margin-left:10px">⌄</span>
        </div>
        <div class="section-body">
            <div class="security-item">
                <div class="security-icon" style="background:rgba(16,185,129,.1)">🔑</div>
                <div class="security-info">
                    <div class="security-lbl">Palavra-passe</div>
                    <div class="security-desc">Altera a tua palavra-passe · ●●●●●●●●</div>
                </div>
                <a href="#" class="security-btn" onclick="alert('Funcionalidade em breve!')">Alterar</a>
            </div>
            <div class="security-item">
                <div class="security-icon" style="background:rgba(6,182,212,.1)">📱</div>
                <div class="security-info">
                    <div class="security-lbl">Autenticação em dois fatores (2FA)</div>
                    <div class="security-desc">Adiciona uma camada extra de segurança</div>
                </div>
                <button type="button" class="security-btn">Ativar</button>
            </div>
            <div class="security-item">
                <div class="security-icon" style="background:rgba(244,63,94,.1)">🗑</div>
                <div class="security-info">
                    <div class="security-lbl">Eliminar conta</div>
                    <div class="security-desc">Esta acção é irreversível.</div>
                </div>
                <button type="button" class="security-btn danger" onclick="document.getElementById('modal-delete').style.display='flex'">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- SAVE BAR --}}
    <div class="save-bar" id="saveBar">
        <div class="save-bar-msg">Tens <strong>alterações não guardadas</strong>!</div>
        <div class="save-bar-actions">
            <button type="button" class="tbtn" onclick="hideSaveBar()">Descartar</button>
            <button type="submit" class="tbtn primary">💾 Guardar</button>
        </div>
    </div>
</form>

{{-- MODAL ELIMINAR CONTA --}}
<div id="modal-delete" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:rgba(0,0,0,.7); backdrop-filter:blur(4px); padding:16px;">
    <div style="background:#0c1220; border:1px solid rgba(244,63,94,.25); border-radius:18px; padding:24px; max-width:400px; width:100%;">
        <h3 style="font-size:16px; font-weight:800; color:#fff; margin-bottom:8px;">⚠️ Eliminar conta</h3>
        <p style="font-size:12px; color:var(--muted2); margin-bottom:18px; line-height:1.6;">Esta acção é irreversível. Todos os teus dados serão permanentemente apagados.</p>
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('delete')
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Confirma a tua palavra-passe</label>
                <input type="password" name="password" class="form-input" placeholder="A tua palavra-passe actual">
                @error('password', 'userDeletion') <span class="form-error">{{ $message }}</span> @enderror
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" class="tbtn" onclick="document.getElementById('modal-delete').style.display='none'">Cancelar</button>
                <button type="submit" class="tbtn" style="background:rgba(244,63,94,.15); border-color:rgba(244,63,94,.35); color:var(--red);">🗑 Eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSection(head) { head.closest('.section').classList.toggle('open'); }
function markDirty() { document.getElementById('saveBar').classList.add('visible'); }
function hideSaveBar() { document.getElementById('saveBar').classList.remove('visible'); }
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (initials) initials.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewCover(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('coverPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
        markDirty();
    }
}
@if($errors->any())
document.getElementById('saveBar').classList.add('visible');
@endif
document.getElementById('modal-delete').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

@endsection