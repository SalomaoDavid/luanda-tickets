@extends('layouts.app')

@section('content')

<style>
[x-cloak] { display: none !important; }

:root {
  --bg:      #05080f;
  --s1:      #0a0f1e;
  --s2:      #0f1628;
  --s3:      #141d35;
  --border:  rgba(56,189,248,0.12);
  --border2: rgba(56,189,248,0.25);
  --sky:     #38bdf8;
  --sky2:    #0ea5e9;
  --green:   #10b981;
  --red:     #f43f5e;
  --gold:    #f59e0b;
  --purple:  #a78bfa;
  --text:    #e2e8f0;
  --muted:   #64748b;
  --muted2:  #94a3b8;
}

@keyframes fadeUp  { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes pulse-g { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }

.adm-wrap { max-width: 900px; margin: 0 auto; padding: 16px 16px 80px; }
@media(min-width:768px){ .adm-wrap { padding: 24px 16px 80px; } }

/* ── HERO ── */
.adm-hero {
  position: relative; overflow: hidden;
  background: var(--s1); border: 1px solid var(--border2);
  border-radius: 20px; padding: 22px 20px 18px;
  margin-bottom: 16px; animation: fadeUp .4s ease both;
}
@media(min-width:768px){ .adm-hero { border-radius: 24px; padding: 28px 28px 22px; margin-bottom: 20px; } }
.adm-hero-bg {
  position: absolute; inset: 0; pointer-events: none;
  background:
    radial-gradient(ellipse at 80% 40%, rgba(56,189,248,.1) 0%, transparent 55%),
    radial-gradient(ellipse at 10% 80%, rgba(167,139,250,.06) 0%, transparent 40%);
}
.adm-hero-grid {
  position: absolute; inset: 0; opacity: .025;
  background-image: linear-gradient(rgba(56,189,248,1) 1px,transparent 1px),linear-gradient(90deg,rgba(56,189,248,1) 1px,transparent 1px);
  background-size: 28px 28px;
}
.adm-hero-inner { position: relative; z-index: 1; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.adm-eyebrow { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--sky); margin-bottom: 5px; display: flex; align-items: center; gap: 7px; }
.adm-eyebrow::before { content:''; width:16px; height:2px; background:var(--sky); border-radius:1px; }
.adm-title { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: -1px; line-height: 1.1; margin-bottom: 4px; }
@media(min-width:768px){ .adm-title { font-size: 32px; } }
.adm-title span { color: var(--sky); }
.adm-sub { font-size: 12px; color: var(--muted2); }

/* ── STATS ── */
.adm-stats { display: grid; grid-template-columns: repeat(2,1fr); gap: 8px; margin-bottom: 16px; animation: fadeUp .4s .05s ease both; }
@media(min-width:480px){ .adm-stats { grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 20px; } }
.adm-stat {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 14px; padding: 12px 14px; transition: border-color .2s;
}
@media(min-width:768px){ .adm-stat { border-radius: 16px; padding: 14px 16px; } }
.adm-stat:hover { border-color: var(--border2); }
.adm-stat-icon { font-size: 16px; margin-bottom: 5px; }
.adm-stat-num { font-size: 22px; font-weight: 900; color: #fff; line-height: 1; margin-bottom: 3px; }
@media(min-width:768px){ .adm-stat-num { font-size: 26px; } }
.adm-stat-lbl { font-size: 9px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; }

/* ── TOOLBAR ── */
.adm-toolbar { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; animation: fadeUp .4s .08s ease both; }
@media(min-width:640px){ .adm-toolbar { flex-direction: row; align-items: center; flex-wrap: wrap; } }

.adm-search {
  display: flex; align-items: center; gap: 10px;
  background: var(--s1); border: 1px solid var(--border2);
  border-radius: 12px; padding: 10px 14px; transition: border-color .2s;
  width: 100%;
}
@media(min-width:640px){ .adm-search { flex: 1; width: auto; } }
.adm-search:focus-within { border-color: var(--sky); box-shadow: 0 0 0 3px rgba(56,189,248,.08); }
.adm-search input { background: none; border: none; outline: none; color: var(--text); font-size: 13px; width: 100%; }
.adm-search input::placeholder { color: var(--muted); }

.adm-filters { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; }
.adm-filters::-webkit-scrollbar { display: none; }
.adm-filter-btn {
  flex-shrink: 0; display: flex; align-items: center; gap: 5px;
  padding: 8px 14px; border-radius: 10px; font-size: 11px; font-weight: 700;
  border: 1px solid var(--border); background: var(--s1); color: var(--muted2);
  cursor: pointer; transition: all .2s; white-space: nowrap;
}
.adm-filter-btn:hover  { border-color: var(--border2); color: var(--text); }
.adm-filter-btn.active { border-color: var(--sky); color: var(--sky); background: rgba(56,189,248,.1); }

/* ── USERS LIST ── */
.adm-grid { display: flex; flex-direction: column; gap: 8px; }
@media(min-width:768px){ .adm-grid { gap: 10px; } }

/* ── USER CARD ── */
.usr-card {
  background: var(--s2); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
  transition: border-color .2s, transform .15s, box-shadow .15s;
  animation: fadeUp .4s ease both;
}
@media(min-width:768px){ .usr-card { border-radius: 18px; } }
.usr-card:hover { border-color: var(--border2); }

/* MAIN ROW */
.usr-card-main {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 14px; cursor: pointer;
}
@media(min-width:768px){ .usr-card-main { gap: 14px; padding: 14px 16px; } }

/* Avatar */
.usr-ava-wrap { position: relative; flex-shrink: 0; }
.usr-ava {
  width: 42px; height: 42px; border-radius: 12px; overflow: hidden;
  border: 1.5px solid var(--border2); background: var(--s3);
}
@media(min-width:768px){ .usr-ava { width: 46px; height: 46px; border-radius: 14px; } }
.usr-ava img { width: 100%; height: 100%; object-fit: cover; }
.usr-online-dot {
  position: absolute; bottom: -2px; right: -2px;
  width: 11px; height: 11px; border-radius: 50%; border: 2px solid var(--s2);
}
.usr-online-dot.online { background: var(--green); animation: pulse-g 2s infinite; }
.usr-online-dot.offline { background: var(--muted); }
.usr-verified-badge {
  position: absolute; top: -3px; right: -3px;
  width: 15px; height: 15px; border-radius: 50%;
  background: var(--sky); border: 2px solid var(--s2);
  display: flex; align-items: center; justify-content: center;
  font-size: 7px; color: #fff; font-weight: 900;
}

/* Info */
.usr-info { flex: 1; min-width: 0; }
.usr-name { font-size: 13px; font-weight: 800; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
@media(min-width:768px){ .usr-name { font-size: 14px; } }
.usr-email { font-size: 10px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
@media(min-width:768px){ .usr-email { font-size: 11px; } }
.usr-meta { display: flex; gap: 8px; flex-wrap: wrap; }
.usr-meta-item { font-size: 10px; color: var(--muted); display: flex; align-items: center; gap: 3px; }

/* Role badge */
.role-badge {
  font-size: 9px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase;
  padding: 3px 8px; border-radius: 20px; flex-shrink: 0; white-space: nowrap;
}
.role-admin   { background: rgba(244,63,94,.15);  border: 1px solid rgba(244,63,94,.3);  color: var(--red); }
.role-creator { background: rgba(56,189,248,.15); border: 1px solid rgba(56,189,248,.3); color: var(--sky); }
.role-user    { background: rgba(148,163,184,.1); border: 1px solid rgba(148,163,184,.2);color: var(--muted2); }

/* Quick actions */
.usr-quick { display: flex; gap: 5px; align-items: center; flex-shrink: 0; }
.usr-act-btn {
  width: 32px; height: 32px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid var(--border); background: var(--s3); color: var(--muted2);
  cursor: pointer; transition: all .2s; text-decoration: none; font-size: 13px;
}
.usr-act-btn:hover { border-color: var(--border2); color: var(--text); }
.usr-act-btn.sky:hover   { border-color: var(--sky);  color: var(--sky);  background: rgba(56,189,248,.1); }

/* EXPAND */
.usr-expand { display: none; border-top: 1px solid var(--border); padding: 14px; animation: fadeUp .25s ease; }
@media(min-width:768px){ .usr-expand { padding: 16px; } }
.usr-expand.open { display: block; }

/* Mini stats no expand */
.usr-expand-stats { display: grid; grid-template-columns: repeat(2,1fr); gap: 8px; margin-bottom: 14px; }
@media(min-width:480px){ .usr-expand-stats { grid-template-columns: repeat(4,1fr); } }
.usr-exp-stat { background: var(--s3); border-radius: 12px; padding: 11px; text-align: center; }
.usr-exp-stat-num { font-size: 18px; font-weight: 900; color: #fff; line-height: 1; }
.usr-exp-stat-lbl { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; }

/* Expand actions */
.usr-expand-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.usr-exp-btn {
  flex: 1; min-width: 90px; padding: 9px 12px; border-radius: 11px;
  font-size: 11px; font-weight: 700; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 5px;
  text-decoration: none; transition: all .2s; white-space: nowrap;
}
.usr-exp-btn.outline { background: var(--s3); border: 1px solid var(--border2); color: var(--muted2); }
.usr-exp-btn.outline:hover { color: var(--text); border-color: var(--sky); }
.usr-exp-btn.sky    { background: rgba(56,189,248,.12); border: 1px solid rgba(56,189,248,.3); color: var(--sky); }
.usr-exp-btn.sky:hover { background: rgba(56,189,248,.22); }
.usr-exp-btn.green  { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: var(--green); }
.usr-exp-btn.green:hover { background: rgba(16,185,129,.22); }
.usr-exp-btn.red    { background: rgba(244,63,94,.1);  border: 1px solid rgba(244,63,94,.25); color: var(--red); }
.usr-exp-btn.red:hover { background: rgba(244,63,94,.2); }
.usr-exp-btn.gold   { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3); color: var(--gold); }
.usr-exp-btn.gold:hover { background: rgba(245,158,11,.2); }

/* Role select */
.role-select {
  background: var(--s3); border: 1px solid var(--border2); border-radius: 10px;
  padding: 8px 12px; color: var(--text); font-size: 12px; font-weight: 600;
  outline: none; cursor: pointer; transition: border-color .2s;
}
.role-select:focus { border-color: var(--sky); }
.role-select option { background: var(--s2); }

/* ── SUSPENDED ── */
.usr-card.suspended { opacity: .55; }
.usr-suspended-tag {
  font-size: 8px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;
  color: var(--red); border: 1px solid rgba(244,63,94,.3); border-radius: 6px;
  padding: 1px 6px; flex-shrink: 0;
}

/* ── EMPTY ── */
.adm-empty { background: var(--s1); border: 1px dashed var(--border2); border-radius: 20px; padding: 60px 20px; text-align: center; }
.adm-empty-icon { font-size: 40px; margin-bottom: 10px; }
.adm-empty-txt { font-size: 14px; color: var(--muted2); font-weight: 700; }

/* ── PAGINATION ── */
.adm-pagination { margin-top: 20px; }
</style>

<div class="adm-wrap">

    {{-- ── HERO ── --}}
    <div class="adm-hero">
        <div class="adm-hero-bg"></div>
        <div class="adm-hero-grid"></div>
        <div class="adm-hero-inner">
            <div>
                <div class="adm-eyebrow">Painel de Administração</div>
                <div class="adm-title">Gestão de <span>Membros</span></div>
                <div class="adm-sub">Gere roles, verifica contas e monitoriza utilizadores</div>
            </div>
        </div>
    </div>

    {{-- ── STATS ── --}}
    @php
        $totalUsers    = $usuarios->total();
        $totalAdmins   = \App\Models\User::where('role','admin')->count();
        $totalCreators = \App\Models\User::where('role','creator')->count();
        $totalOnline = \App\Models\User::whereNotNull('last_seen')
    ->where('last_seen', '>=', now()->subMinutes(5))
    ->count();
    @endphp
    <div class="adm-stats">
        <div class="adm-stat">
            <div class="adm-stat-icon">👥</div>
            <div class="adm-stat-num">{{ $totalUsers }}</div>
            <div class="adm-stat-lbl">Total membros</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-icon">🛡</div>
            <div class="adm-stat-num" style="color:var(--red)">{{ $totalAdmins }}</div>
            <div class="adm-stat-lbl">Admins</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-icon">🎟</div>
            <div class="adm-stat-num" style="color:var(--sky)">{{ $totalCreators }}</div>
            <div class="adm-stat-lbl">Criadores</div>
        </div>
        <div class="adm-stat">
            <div class="adm-stat-icon">🟢</div>
            <div class="adm-stat-num" style="color:var(--green)">{{ $totalOnline }}</div>
            <div class="adm-stat-lbl">Online agora</div>
        </div>
    </div>

    {{-- ── TOOLBAR ── --}}
    <div class="adm-toolbar">
        <div class="adm-search">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="searchInput" placeholder="Buscar por nome ou email..." oninput="filtrarUsers()">
        </div>
        <div class="adm-filters">
            <button class="adm-filter-btn active" id="filter-all"     onclick="setFilter('all')">👥 Todos</button>
            <button class="adm-filter-btn"         id="filter-admin"   onclick="setFilter('admin')">🛡 Admins</button>
            <button class="adm-filter-btn"         id="filter-creator" onclick="setFilter('creator')">🎟 Criadores</button>
            <button class="adm-filter-btn"         id="filter-user"    onclick="setFilter('user')">👤 Membros</button>
        </div>
    </div>

    {{-- ── USERS LIST ── --}}
    <div class="adm-grid" id="usersGrid">
        @forelse($usuarios as $i => $user)
        @php
            $isSuspended   = isset($user->suspended_at) && $user->suspended_at;
            $isOnline      = method_exists($user,'isOnline') && $user->isOnline();
            $isCreatorAdmin = in_array($user->role, ['admin','creator']);
            $numEventos    = $isCreatorAdmin && method_exists($user,'eventos') ? $user->eventos()->count() : null;
            $numPosts      = method_exists($user,'postagens') ? $user->postagens()->count() : 0;
            $numReservas   = method_exists($user,'reservas') ? $user->reservas()->count() : 0;
        @endphp

        <div class="usr-card {{ $isSuspended ? 'suspended' : '' }}"
             data-role="{{ $user->role }}"
             data-name="{{ strtolower($user->name) }}"
             data-email="{{ strtolower($user->email) }}"
             style="animation-delay:{{ $i * 0.04 }}s">

            {{-- MAIN ROW --}}
            <div class="usr-card-main" onclick="toggleExpand({{ $user->id }})">

                {{-- Avatar --}}
                <div class="usr-ava-wrap">
                    <div class="usr-ava">
                        <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0c2a3a&color=38bdf8&size=96' }}"
                             alt="{{ $user->name }}">
                    </div>
                    <div class="usr-online-dot {{ $isOnline ? 'online' : 'offline' }}"></div>
                    @if($user->is_verified)
                        <div class="usr-verified-badge">✓</div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="usr-info">
                    <div class="usr-name">{{ $user->name }}</div>
                    <div class="usr-email">{{ $user->email }}</div>
                    <div class="usr-meta">
                        <span class="usr-meta-item">🗓 {{ $user->created_at->translatedFormat('M Y') }}</span>
                        {{-- Eventos: só admin e creator --}}
                        @if($isCreatorAdmin && $numEventos !== null)
                            <span class="usr-meta-item">🎟 {{ $numEventos }} evento{{ $numEventos !== 1 ? 's' : '' }}</span>
                        @endif
                        {{-- Posts: todos os utilizadores --}}
                        <span class="usr-meta-item">📝 {{ $numPosts }} post{{ $numPosts !== 1 ? 's' : '' }}</span>
                    </div>
                </div>

                {{-- Role badge + suspended tag --}}
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;flex-shrink:0;">
                    <span class="role-badge {{ $user->role == 'admin' ? 'role-admin' : ($user->role == 'creator' ? 'role-creator' : 'role-user') }}">
                        {{ $user->role == 'admin' ? '🛡 Admin' : ($user->role == 'creator' ? '🎟 Criador' : '👤 Membro') }}
                    </span>
                    @if($isSuspended)
                        <span class="usr-suspended-tag">Suspenso</span>
                    @endif
                </div>

                {{-- Quick actions --}}
                <div class="usr-quick" onclick="event.stopPropagation()">
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}"
                       class="usr-act-btn sky" title="Enviar mensagem">💬</a>
                    <button class="usr-act-btn" id="chevron-{{ $user->id }}" title="Ver mais">↓</button>
                </div>
            </div>

            {{-- EXPAND ROW --}}
            <div class="usr-expand" id="expand-{{ $user->id }}">

                {{-- Mini stats --}}
                <div class="usr-expand-stats">
                    {{-- Eventos: só admin e creator --}}
                    @if($isCreatorAdmin)
                    <div class="usr-exp-stat">
                        <div class="usr-exp-stat-num" style="color:var(--sky)">{{ $numEventos }}</div>
                        <div class="usr-exp-stat-lbl">Eventos</div>
                    </div>
                    @endif
                    {{-- Posts: todos --}}
                    <div class="usr-exp-stat">
                        <div class="usr-exp-stat-num" style="color:var(--purple)">{{ $numPosts }}</div>
                        <div class="usr-exp-stat-lbl">Posts</div>
                    </div>
                    {{-- Reservas: todos --}}
                    <div class="usr-exp-stat">
                        <div class="usr-exp-stat-num" style="color:var(--gold)">{{ $numReservas }}</div>
                        <div class="usr-exp-stat-lbl">Reservas</div>
                    </div>
                    {{-- Notificações: todos --}}
                    <div class="usr-exp-stat">
                        <div class="usr-exp-stat-num" style="color:var(--green)">{{ $user->notifications()->count() }}</div>
                        <div class="usr-exp-stat-lbl">Notificações</div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="usr-expand-actions">

                    {{-- Alterar role --}}
                    <form action="{{ route('admin.usuarios.role', $user->id) }}" method="POST" style="display:flex;align-items:center;">
                        @csrf @method('PATCH')
                        <select name="role" onchange="this.form.submit()" class="role-select">
                            <option value="user"    {{ $user->role == 'user'    ? 'selected' : '' }}>👤 Membro</option>
                            <option value="creator" {{ $user->role == 'creator' ? 'selected' : '' }}>🎟 Criador</option>
                            <option value="admin"   {{ $user->role == 'admin'   ? 'selected' : '' }}>🛡 Admin</option>
                        </select>
                    </form>

                    {{-- Verificar --}}
                    <form action="{{ route('admin.usuarios.verify', $user->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="usr-exp-btn {{ $user->is_verified ? 'green' : 'outline' }}">
                            {{ $user->is_verified ? '✓ Verificado' : '🔍 Verificar' }}
                        </button>
                    </form>

                    {{-- Mensagem --}}
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="usr-exp-btn sky">
                        💬 Mensagem
                    </a>

                    {{-- Ver perfil --}}
                    <a href="{{ route('profile.show', $user->id) }}" target="_blank" class="usr-exp-btn outline">
                        👁 Perfil
                    </a>

                    {{-- Suspender / Reativar --}}
                    @if(Route::has('admin.usuarios.suspend'))
                    <form action="{{ route('admin.usuarios.suspend', $user->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="usr-exp-btn {{ $isSuspended ? 'green' : 'gold' }}"
                                onclick="return confirm('{{ $isSuspended ? 'Reativar' : 'Suspender' }} a conta de {{ $user->name }}?')">
                            {{ $isSuspended ? '✅ Reativar' : '⏸ Suspender' }}
                        </button>
                    </form>
                    @endif

                    {{-- Eliminar --}}
                    @if(Route::has('admin.usuarios.destroy'))
                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST"
                          onsubmit="return confirm('Eliminar permanentemente a conta de {{ $user->name }}? Esta ação é irreversível.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="usr-exp-btn red">🗑 Eliminar</button>
                    </form>
                    @endif

                </div>
            </div>
        </div>
        @empty
        <div class="adm-empty">
            <div class="adm-empty-icon">👥</div>
            <div class="adm-empty-txt">Nenhum utilizador encontrado</div>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="adm-pagination">
        {{ $usuarios->links() }}
    </div>

</div>

<script>
function toggleExpand(userId) {
    const expand  = document.getElementById('expand-'  + userId);
    const chevron = document.getElementById('chevron-' + userId);
    const isOpen  = expand.classList.contains('open');
    document.querySelectorAll('.usr-expand').forEach(e => e.classList.remove('open'));
    document.querySelectorAll('[id^="chevron-"]').forEach(c => c.textContent = '↓');
    if (!isOpen) {
        expand.classList.add('open');
        chevron.textContent = '↑';
    }
}

let currentFilter = 'all';
function setFilter(role) {
    currentFilter = role;
    document.querySelectorAll('.adm-filter-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('filter-' + role).classList.add('active');
    filtrarUsers();
}

function filtrarUsers() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.usr-card').forEach(card => {
        const name  = card.dataset.name  || '';
        const email = card.dataset.email || '';
        const role  = card.dataset.role  || '';
        const matchSearch = name.includes(q) || email.includes(q);
        const matchRole   = currentFilter === 'all' || role === currentFilter;
        card.style.display = (matchSearch && matchRole) ? '' : 'none';
    });
}
</script>

@endsection