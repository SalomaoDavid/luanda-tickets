@extends('layouts.app')

@section('title', $user->name . ' — Luanda Tickets')

@section('content')
<style>
:root {
  --bg:      #06090f;
  --s1:      rgba(255,255,255,0.04);
  --s2:      rgba(255,255,255,0.07);
  --s3:      rgba(255,255,255,0.10);
  --border:  rgba(255,255,255,0.08);
  --accent:  #06b6d4;
  --accent2: #0ea5e9;
  --gold:    #f59e0b;
  --green:   #10b981;
  --red:     #f43f5e;
  --purple:  #a78bfa;
  --text:    #eef0f6;
  --muted:   #64748b;
  --muted2:  #94a3b8;
}

/* ── COVER ── */
.cover-wrap {
  position: relative; height: 260px; overflow: hidden;
  margin: 0 -40px; /* esticar além do padding do layout */
}
.cover-bg {
  width: 100%; height: 100%; object-fit: cover;
  background: linear-gradient(135deg, #050d1a 0%, #091828 30%, #0c1f3a 60%, #071220 100%);
}
.cover-bg img { width: 100%; height: 100%; object-fit: cover; }
.cover-bg::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse at 60% 40%, rgba(6,182,212,.18) 0%, transparent 55%),
    radial-gradient(ellipse at 20% 80%, rgba(245,158,11,.08) 0%, transparent 40%);
  pointer-events: none;
}
.cover-bg::after {
  content: '';
  position: absolute; inset: 0;
  background-image:
    repeating-linear-gradient(60deg, transparent, transparent 40px, rgba(6,182,212,.03) 40px, rgba(6,182,212,.03) 41px),
    repeating-linear-gradient(-60deg, transparent, transparent 40px, rgba(6,182,212,.03) 40px, rgba(6,182,212,.03) 41px);
  pointer-events: none;
}
.cover-overlay {
  position: absolute; bottom: 0; left: 0; right: 0; height: 100px;
  background: linear-gradient(to top, var(--bg), transparent);
  pointer-events: none;
}

/* ── PROFILE HEADER ── */
.profile-header { position: relative; z-index: 2; margin-bottom: 0; }

.profile-top {
  display: flex; align-items: flex-end; gap: 22px;
  margin-top: -60px; padding-bottom: 22px;
  border-bottom: 1px solid var(--border);
  flex-wrap: wrap;
}

.profile-ava-wrap { position: relative; flex-shrink: 0; }
.profile-ava {
  width: 116px; height: 116px; border-radius: 50%;
  border: 4px solid var(--bg);
  background: linear-gradient(135deg, #0c3a4a, #1e6a7a);
  display: flex; align-items: center; justify-content: center;
  font-size: 42px; font-weight: 800; color: var(--accent);
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(6,182,212,.22);
}
.profile-ava img { width: 100%; height: 100%; object-fit: cover; }
.profile-online {
  position: absolute; bottom: 6px; right: 6px;
  width: 17px; height: 17px; background: var(--green); border-radius: 50%;
  border: 3px solid var(--bg); box-shadow: 0 0 8px rgba(16,185,129,.6);
}
.profile-offline {
  position: absolute; bottom: 6px; right: 6px;
  width: 17px; height: 17px; background: var(--muted); border-radius: 50%;
  border: 3px solid var(--bg);
}

.profile-info { flex: 1; min-width: 0; padding-bottom: 6px; }
.profile-name-row {
  display: flex; align-items: center; gap: 10px;
  flex-wrap: wrap; margin-bottom: 4px;
}
.profile-name { font-size: 26px; font-weight: 800; letter-spacing: -.5px; color: #fff; }
.profile-handle { font-size: 13px; color: var(--muted2); }
.profile-badge {
  font-size: 10px; font-weight: 700; letter-spacing: .8px;
  text-transform: uppercase; padding: 3px 9px; border-radius: 20px;
}
.badge-admin   { background: rgba(244,63,94,.15);  border: 1px solid rgba(244,63,94,.3);  color: var(--red); }
.badge-creator { background: rgba(6,182,212,.15);  border: 1px solid rgba(6,182,212,.3);  color: var(--accent); }
.badge-user    { background: rgba(148,163,184,.12); border: 1px solid rgba(148,163,184,.2); color: var(--muted2); }

.profile-meta { display: flex; gap: 18px; flex-wrap: wrap; margin-top: 8px; }
.profile-meta-item {
  display: flex; align-items: center; gap: 5px;
  font-size: 12px; color: var(--muted);
}
.profile-meta-item strong { color: var(--muted2); }

.profile-actions {
  display: flex; gap: 9px; align-items: center;
  padding-bottom: 6px; flex-shrink: 0;
}
.btn-follow {
  padding: 10px 22px; border-radius: 11px; font-size: 13px; font-weight: 700;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  color: #fff; border: none; cursor: pointer;
  box-shadow: 0 4px 16px rgba(6,182,212,.35);
  transition: all .2s;
}
.btn-follow:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(6,182,212,.45); }
.btn-msg {
  padding: 10px 18px; border-radius: 11px; font-size: 13px; font-weight: 600;
  background: var(--s2); border: 1px solid var(--border);
  color: var(--text); cursor: pointer; transition: all .2s; text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
}
.btn-msg:hover { border-color: rgba(255,255,255,.16); color: #fff; }
.btn-edit {
  padding: 10px 18px; border-radius: 11px; font-size: 13px; font-weight: 600;
  background: rgba(6,182,212,.12); border: 1px solid rgba(6,182,212,.3);
  color: var(--accent); cursor: pointer; transition: all .2s; text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
}
.btn-edit:hover { background: rgba(6,182,212,.22); }

/* ── STATS ── */
.profile-stats {
  display: flex; gap: 0;
  border-bottom: 1px solid var(--border);
}
.stat {
  flex: 1; text-align: center; padding: 16px 12px;
  border-right: 1px solid var(--border);
  cursor: pointer; transition: background .2s;
}
.stat:last-child { border-right: none; }
.stat:hover { background: var(--s1); }
.stat-num {
  font-size: 21px; font-weight: 800; color: var(--text); line-height: 1;
}
.stat-lbl {
  font-size: 10px; color: var(--muted); margin-top: 3px;
  text-transform: uppercase; letter-spacing: .8px;
}

/* ── TABS ── */
.tabs {
  display: flex; gap: 0;
  border-bottom: 1px solid var(--border);
  overflow-x: auto; scrollbar-width: none;
}
.tabs::-webkit-scrollbar { display: none; }
.tab {
  padding: 13px 20px; font-size: 13px; font-weight: 600;
  color: var(--muted); cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all .2s; white-space: nowrap;
  display: flex; align-items: center; gap: 6px;
  margin-bottom: -1px;
}
.tab:hover { color: var(--text); }
.tab.active { color: var(--accent); border-bottom-color: var(--accent); }
.tab-count {
  font-size: 10px; background: var(--s2); border: 1px solid var(--border);
  border-radius: 20px; padding: 2px 7px; color: var(--muted);
}
.tab.active .tab-count {
  background: rgba(6,182,212,.15); border-color: rgba(6,182,212,.3); color: var(--accent);
}

/* ── BODY LAYOUT ── */
.profile-body {
  display: grid; grid-template-columns: 1fr 300px;
  gap: 24px; padding-top: 24px;
}

/* ── FEED ── */
.feed { display: flex; flex-direction: column; gap: 16px; }

/* Tab panels */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* Post publicação */
.post-card {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
  animation: fadeUp .4s ease both; transition: border-color .2s;
}
.post-card:hover { border-color: rgba(6,182,212,.15); }
@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

.post-body { padding: 16px 18px; }
.post-author { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.post-author-left { display: flex; align-items: center; gap: 10px; }
.post-author-ava {
  width: 38px; height: 38px; border-radius: 50%;
  border: 2px solid rgba(6,182,212,.25); overflow: hidden; flex-shrink: 0;
}
.post-author-ava img { width: 100%; height: 100%; object-fit: cover; }
.post-author-name { font-size: 13px; font-weight: 700; color: #fff; }
.post-author-time { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
.post-text { font-size: 14px; color: var(--muted2); line-height: 1.65; }
.btn-delete {
  font-size: 10px; color: #f43f5e; font-weight: 700;
  text-transform: uppercase; letter-spacing: .5px;
  background: none; border: none; cursor: pointer; opacity: .7; transition: opacity .2s;
}
.btn-delete:hover { opacity: 1; }

/* Card evento feed */
.ev-post {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
  animation: fadeUp .4s ease both; transition: border-color .2s;
}
.ev-post:hover { border-color: rgba(6,182,212,.2); }
.ev-post-img {
  height: 190px; display: flex; align-items: center; justify-content: center;
  font-size: 72px; position: relative; overflow: hidden;
}
.ev-post-img img { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; }
.ev-post-img::after {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(to bottom, transparent 50%, rgba(6,9,15,.65) 100%);
}
.ev-post-badge {
  position: absolute; top: 12px; left: 12px; z-index: 2;
  font-size: 10px; font-weight: 700; letter-spacing: .8px;
  text-transform: uppercase; padding: 4px 10px; border-radius: 20px;
}
.ev-post-date {
  position: absolute; bottom: 12px; left: 12px; z-index: 2;
  background: rgba(6,9,15,.85); backdrop-filter: blur(8px);
  border: 1px solid var(--border); border-radius: 7px;
  font-size: 11px; font-weight: 600; padding: 4px 9px;
}
.ev-post-body { padding: 15px 17px 17px; }
.ev-post-cat { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px; }
.ev-post-title { font-size: 17px; font-weight: 800; letter-spacing: -.3px; margin-bottom: 9px; line-height: 1.25; color: #fff; }
.ev-post-meta { display: flex; gap: 14px; margin-bottom: 12px; flex-wrap: wrap; }
.ev-post-meta span { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted2); }
.ev-post-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding-top: 12px; border-top: 1px solid var(--border);
}
.ev-price { font-size: 19px; font-weight: 800; color: var(--gold); }
.ev-price small { font-size: 11px; color: var(--muted); font-weight: 400; }
.ev-price.free { color: var(--green); font-size: 14px; font-weight: 700; }
.ev-foot-actions { display: flex; gap: 7px; align-items: center; }
.ev-buy-btn {
  padding: 8px 16px; border-radius: 9px;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  color: #fff; font-size: 12px; font-weight: 700;
  border: none; cursor: pointer; transition: all .2s;
  box-shadow: 0 4px 12px rgba(6,182,212,.3); text-decoration: none;
  display: inline-flex; align-items: center; gap: 4px;
}
.ev-buy-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(6,182,212,.4); }

/* Barra bilhetes */
.ev-bar-wrap { margin-bottom: 12px; }
.ev-bar-top { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; }
.ev-bar-top span { color: var(--muted); }
.ev-bar-top strong { color: var(--text); }
.ev-bar { height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; overflow: hidden; }
.ev-bar-fill { height: 100%; border-radius: 2px; }
.fill-ok   { background: linear-gradient(to right, var(--accent), var(--accent2)); }
.fill-warn { background: linear-gradient(to right, var(--gold), #f97316); }
.fill-crit { background: linear-gradient(to right, var(--red), #f97316); }

/* Empty state */
.empty-feed {
  text-align: center; padding: 56px 20px;
  background: var(--s1); border: 1px solid var(--border); border-radius: 16px;
}
.empty-feed p:first-child { font-size: 40px; margin-bottom: 10px; }
.empty-feed p:last-child { font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }

/* ── SIDEBAR ── */
.sidebar { display: flex; flex-direction: column; gap: 16px; }
.side-box {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 15px; padding: 16px;
  animation: fadeUp .4s ease both;
}
.side-title {
  font-size: 11px; font-weight: 700; letter-spacing: .8px;
  text-transform: uppercase; color: var(--muted);
  margin-bottom: 13px; display: flex; align-items: center; gap: 7px;
}
.side-title a {
  margin-left: auto; font-size: 11px; color: var(--accent);
  text-transform: none; letter-spacing: 0; font-weight: 600; text-decoration: none;
}
.side-title a:hover { text-decoration: underline; }

/* Interesses */
.interests { display: flex; flex-wrap: wrap; gap: 6px; }
.interest-tag {
  padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 600;
  border: 1px solid var(--border); background: var(--s2); color: var(--muted2);
}

/* Going list */
.going-list { display: flex; flex-direction: column; gap: 9px; }
.going-item {
  display: flex; align-items: center; gap: 9px; padding: 9px;
  background: var(--s2); border-radius: 10px;
  border: 1px solid transparent; transition: all .2s; text-decoration: none;
}
.going-item:hover { border-color: rgba(6,182,212,.2); }
.going-emoji {
  width: 36px; height: 36px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 17px; flex-shrink: 0; background: var(--s3);
}
.going-info { flex: 1; min-width: 0; }
.going-name { font-size: 12px; font-weight: 600; color: #fff; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.going-date { font-size: 10px; color: var(--accent); font-weight: 600; }
.going-price { font-size: 11px; color: var(--gold); font-weight: 700; flex-shrink: 0; }

/* Galeria */
.gallery-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 5px; }
.gallery-item {
  aspect-ratio: 1; border-radius: 7px; overflow: hidden;
  cursor: pointer; background: var(--s2);
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; transition: all .2s;
  border: 1px solid var(--border);
}
.gallery-item img { width: 100%; height: 100%; object-fit: cover; }
.gallery-item:hover { transform: scale(1.03); border-color: rgba(6,182,212,.3); }

/* Mural compose */
.compose-box {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 16px; padding: 16px; margin-bottom: 4px;
}
.compose-box textarea {
  width: 100%; background: var(--s2); border: 1px solid var(--border);
  border-radius: 11px; padding: 12px 14px; font-size: 13px; color: var(--text);
  resize: none; outline: none; transition: border-color .2s;
  font-family: inherit;
}
.compose-box textarea:focus { border-color: rgba(6,182,212,.4); }
.compose-box textarea::placeholder { color: var(--muted); }
.compose-footer { display: flex; justify-content: flex-end; margin-top: 10px; }
.btn-publish {
  padding: 8px 20px; border-radius: 9px; font-size: 12px; font-weight: 700;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  color: #fff; border: none; cursor: pointer;
  box-shadow: 0 4px 12px rgba(6,182,212,.3); transition: all .2s;
}
.btn-publish:hover { transform: translateY(-1px); }

/* Bilhetes tab */
.bilhetes-grid { display: grid; gap: 12px; }
.bilhete-card {
  background: var(--s2); border: 1px solid var(--border);
  border-radius: 13px; padding: 14px 16px;
  display: flex; align-items: center; gap: 14px;
  transition: border-color .2s;
}
.bilhete-card:hover { border-color: rgba(6,182,212,.25); }
.bilhete-emoji {
  width: 46px; height: 46px; border-radius: 10px;
  background: var(--s3); display: flex; align-items: center;
  justify-content: center; font-size: 20px; flex-shrink: 0;
}
.bilhete-info { flex: 1; min-width: 0; }
.bilhete-name { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 3px; }
.bilhete-meta { font-size: 11px; color: var(--muted2); }
.bilhete-status {
  font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px;
  letter-spacing: .5px; text-transform: uppercase;
}
.status-valid   { background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3); color: var(--green); }
.status-used    { background: rgba(100,116,139,.12); border: 1px solid rgba(100,116,139,.2); color: var(--muted); }
.status-pending { background: rgba(245,158,11,.15); border: 1px solid rgba(245,158,11,.3); color: var(--gold); }
</style>

{{-- ══════════════════════════════════════════
     COVER
══════════════════════════════════════════ --}}
<div class="cover-wrap" style="margin: -40px -40px 0;">
    <div class="cover-bg" style="position:relative; height:260px;">
        @if(isset($user->cover) && $user->cover)
            <img src="{{ asset('storage/'.$user->cover) }}" alt="cover"
                 style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0;">
        @endif
    </div>
    <div class="cover-overlay"></div>
</div>

{{-- ══════════════════════════════════════════
     PROFILE HEADER
══════════════════════════════════════════ --}}
<div class="profile-header">

    {{-- TOP: avatar + info + acções --}}
    <div class="profile-top">
        <div class="profile-ava-wrap">
            <div class="profile-ava">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            @if($user->isOnline())
                <div class="profile-online"></div>
            @else
                <div class="profile-offline"></div>
            @endif
        </div>

        <div class="profile-info">
            <div class="profile-name-row">
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-handle">@{{ strtolower(str_replace(' ', '.', $user->name)) }}</div>
                @if($user->role === 'admin')
                    <span class="profile-badge badge-admin">🛡 Admin</span>
                @elseif($user->role === 'creator')
                    <span class="profile-badge badge-creator">🎟 Criador</span>
                @else
                    <span class="profile-badge badge-user">👤 Utilizador</span>
                @endif
            </div>
            <div class="profile-meta">
                <div class="profile-meta-item">📍 <strong>Luanda, Angola</strong></div>
                <div class="profile-meta-item">🗓 <strong>Desde {{ $user->created_at->translatedFormat('M Y') }}</strong></div>
            </div>
        </div>

        <div class="profile-actions">
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar Perfil</a>
            @else
                @auth
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬 Mensagem</a>
                    <button class="btn-follow">+ Seguir</button>
                @endauth
            @endif
        </div>
    </div>

    {{-- STATS --}}
    <div class="profile-stats">
        <div class="stat">
            <div class="stat-num">{{ $statsCount }}</div>
            <div class="stat-lbl">{{ $statsLabel }}</div>
        </div>
        <div class="stat">
            <div class="stat-num">{{ $statsCount2 }}</div>
            <div class="stat-lbl">{{ $statsLabel2 }}</div>
        </div>
        <div class="stat">
            <div class="stat-num">{{ $postagens->count() }}</div>
            <div class="stat-lbl">Posts</div>
        </div>
        @if($user->role === 'creator')
        <div class="stat">
            @php $totalVendidos = $user->eventos->sum(fn($e) => optional($e->tiposIngresso)->sum('quantidade_vendida') ?? 0); @endphp
            <div class="stat-num">{{ number_format($totalVendidos) }}</div>
            <div class="stat-lbl">Bilhetes vendidos</div>
        </div>
        @endif
        <div class="stat">
            <div class="stat-num">{{ $user->isOnline() ? '🟢' : '⚫' }}</div>
            <div class="stat-lbl">{{ $user->isOnline() ? 'Online' : 'Offline' }}</div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="tabs" id="profileTabs">
        <div class="tab active" data-tab="posts" onclick="switchTab('posts')">
            📝 Posts <span class="tab-count">{{ $postagens->count() }}</span>
        </div>
        <div class="tab" data-tab="eventos" onclick="switchTab('eventos')">
            🎟 Eventos <span class="tab-count">{{ $eventos->count() }}</span>
        </div>
        <div class="tab" data-tab="bilhetes" onclick="switchTab('bilhetes')">
            🎫 Meus Bilhetes
        </div>
        <div class="tab" data-tab="guardados" onclick="switchTab('guardados')">
            🔖 Guardados
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     BODY
══════════════════════════════════════════ --}}
<div class="profile-body">

    {{-- ── FEED / TABS ── --}}
    <div class="feed">

        {{-- TAB: POSTS --}}
        <div class="tab-panel active" id="tab-posts">

            @if($isOwner)
            <div class="compose-box">
                <form method="POST" action="{{ route('social.publicar') }}">
                    @csrf
                    <textarea name="conteudo" rows="3"
                              placeholder="Partilha algo com o público..."></textarea>
                    <div class="compose-footer">
                        <button type="submit" class="btn-publish">Publicar</button>
                    </div>
                </form>
            </div>
            @endif

            @forelse($postagens as $post)
            <div class="post-card" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <div class="post-body">
                    <div class="post-author">
                        <div class="post-author-left">
                            <div class="post-author-ava">
                                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=64' }}"
                                     alt="{{ $user->name }}">
                            </div>
                            <div>
                                <div class="post-author-name">{{ $user->name }}</div>
                                <div class="post-author-time">{{ $post->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @if($isOwner)
                        <form method="POST" action="{{ route('post.eliminar', $post->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete"
                                    onclick="return confirm('Eliminar este post?')">🗑 Eliminar</button>
                        </form>
                        @endif
                    </div>
                    <p class="post-text">{{ $post->conteudo }}</p>
                </div>
            </div>
            @empty
            <div class="empty-feed">
                <p>📝</p>
                <p>Nenhuma publicação ainda</p>
            </div>
            @endforelse
        </div>

        {{-- TAB: EVENTOS --}}
        <div class="tab-panel" id="tab-eventos">
            @forelse($eventos as $evento)
            @php
                $preco     = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                $lotacao   = $evento->lotacao_maxima ?? 0;
                $vendidos  = optional($evento->tiposIngresso)->sum('quantidade_vendida') ?? 0;
                $pct       = $lotacao > 0 ? round(($vendidos / $lotacao) * 100) : 0;
                $barClass  = $pct >= 80 ? 'fill-crit' : ($pct >= 50 ? 'fill-warn' : 'fill-ok');
                $categoria = optional($evento->categoria)->nome ?? 'Evento';
                $catEmoji  = match(strtolower($categoria)) {
                    'música','musica'    => '🎵',
                    'arte'               => '🎨',
                    'festa','festas'     => '🎉',
                    'desporto'           => '⚽',
                    'gastronomia'        => '🍽',
                    'negócios','negocios'=> '💼',
                    default              => '🎟'
                };
                $catColor  = match(strtolower($categoria)) {
                    'música','musica'    => 'var(--accent)',
                    'arte'               => 'var(--purple)',
                    'festa','festas'     => 'var(--gold)',
                    'desporto'           => 'var(--green)',
                    'gastronomia'        => '#f97316',
                    'negócios','negocios'=> 'var(--accent2)',
                    default              => 'var(--accent)'
                };
            @endphp
            <div class="ev-post" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <div class="ev-post-img"
                     style="background: linear-gradient(135deg,#050d1a,#0c1f3a)">
                    @if($evento->imagem_capa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
                    @else
                        {{ $catEmoji }}
                    @endif
                    @if($pct >= 80)
                        <span class="ev-post-badge" style="background:rgba(244,63,94,.9);color:#fff">🔥 A Esgotar</span>
                    @elseif($preco == 0)
                        <span class="ev-post-badge" style="background:rgba(16,185,129,.9);color:#fff">Gratuito</span>
                    @elseif(\Carbon\Carbon::parse($evento->created_at)->isCurrentWeek())
                        <span class="ev-post-badge" style="background:rgba(14,165,233,.9);color:#fff">✨ Novo</span>
                    @endif
                    <div class="ev-post-date">
                        📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }}
                        · {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}
                    </div>
                </div>
                <div class="ev-post-body">
                    <div class="ev-post-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $categoria }}</div>
                    <div class="ev-post-title">{{ $evento->titulo }}</div>
                    <div class="ev-post-meta">
                        <span>📍 {{ $evento->localizacao ?? 'Local não informado' }}</span>
                        <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
                    </div>
                    @if($lotacao > 0)
                    <div class="ev-bar-wrap">
                        <div class="ev-bar-top">
                            <span>Bilhetes vendidos</span>
                            <strong>{{ $vendidos }} / {{ $lotacao }}</strong>
                        </div>
                        <div class="ev-bar">
                            <div class="ev-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                    <div class="ev-post-footer">
                        <div class="ev-price {{ $preco == 0 ? 'free' : '' }}">
                            @if($preco == 0) ✅ Gratuito
                            @else {{ number_format($preco,0,',','.') }} <small>Kz</small>
                            @endif
                        </div>
                        <div class="ev-foot-actions">
                            <a href="{{ route('evento.detalhes', $evento->id) }}" class="ev-buy-btn">
                                🎟 Ver Evento
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-feed">
                <p>🎟</p>
                <p>Nenhum evento ainda</p>
            </div>
            @endforelse
        </div>

        {{-- TAB: BILHETES --}}
        <div class="tab-panel" id="tab-bilhetes">
            <div class="bilhetes-grid">
                @forelse($eventos as $evento)
                @php
                    $preco    = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                    $catEmoji = match(strtolower(optional($evento->categoria)->nome ?? '')) {
                        'música','musica' => '🎵', 'arte' => '🎨', 'festa' => '🎉',
                        'desporto' => '⚽', 'gastronomia' => '🍽', 'negócios','negocios' => '💼',
                        default => '🎟'
                    };
                    $isPast = \Carbon\Carbon::parse($evento->data_evento)->isPast();
                @endphp
                <div class="bilhete-card">
                    <div class="bilhete-emoji">{{ $catEmoji }}</div>
                    <div class="bilhete-info">
                        <div class="bilhete-name">{{ $evento->titulo }}</div>
                        <div class="bilhete-meta">
                            📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('d M Y') }}
                            · 📍 {{ $evento->localizacao ?? '—' }}
                        </div>
                    </div>
                    <span class="bilhete-status {{ $isPast ? 'status-used' : 'status-valid' }}">
                        {{ $isPast ? 'Usado' : 'Válido' }}
                    </span>
                </div>
                @empty
                <div class="empty-feed">
                    <p>🎫</p>
                    <p>Nenhum bilhete ainda</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- TAB: GUARDADOS --}}
        <div class="tab-panel" id="tab-guardados">
            <div class="empty-feed">
                <p>🔖</p>
                <p>Nenhum evento guardado ainda</p>
            </div>
        </div>

    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="sidebar">

        {{-- Eventos recentes na sidebar --}}
        @if($eventos->count() > 0)
        <div class="side-box" style="animation-delay:.05s">
            <div class="side-title">
                📅
                @if($user->role === 'creator') Eventos publicados
                @elseif($user->role === 'admin') Eventos recentes
                @else Eventos curtidos
                @endif
            </div>
            <div class="going-list">
                @foreach($eventos->take(4) as $ev)
                @php
                    $p = optional($ev->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                    $emojiSide = match(strtolower(optional($ev->categoria)->nome ?? '')) {
                        'música','musica' => '🎵', 'arte' => '🎨', 'festa' => '🎉',
                        'desporto' => '⚽', 'gastronomia' => '🍽', default => '🎟'
                    };
                @endphp
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="going-item">
                    <div class="going-emoji">{{ $emojiSide }}</div>
                    <div class="going-info">
                        <div class="going-name">{{ $ev->titulo }}</div>
                        <div class="going-date">{{ \Carbon\Carbon::parse($ev->data_evento)->translatedFormat('D, d M · H:i') }}</div>
                    </div>
                    <div class="going-price" style="{{ $p == 0 ? 'color:var(--green)' : '' }}">
                        {{ $p == 0 ? 'Grátis' : number_format($p/1000,0).'k Kz' }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Galeria de fotos dos eventos --}}
        @if($eventos->where('imagem_capa', '!=', null)->count() > 0)
        <div class="side-box" style="animation-delay:.10s">
            <div class="side-title">
                📸 Galeria
                <a href="#">Ver tudo →</a>
            </div>
            <div class="gallery-grid">
                @foreach($eventos->where('imagem_capa', '!=', null)->take(6) as $ev)
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                </a>
                @endforeach
                @for($i = $eventos->where('imagem_capa', '!=', null)->take(6)->count(); $i < 6; $i++)
                <div class="gallery-item" style="background: linear-gradient(135deg,#050d1a,#0c1f3a)">🎟</div>
                @endfor
            </div>
        </div>
        @endif

        {{-- Interesses baseados nas categorias dos eventos --}}
        <div class="side-box" style="animation-delay:.15s">
            <div class="side-title">🏷 Interesses</div>
            <div class="interests">
                @php
                    $cats = $eventos->map(fn($e) => optional($e->categoria)->nome)->filter()->unique()->values();
                    $defaultTags = ['🎵 Música', '🎉 Festas', '🎟 Eventos', '🌊 Luanda', '🇦🇴 Angola'];
                @endphp
                @if($cats->count() > 0)
                    @foreach($cats->take(8) as $cat)
                    <span class="interest-tag" style="background:rgba(6,182,212,.1);border-color:rgba(6,182,212,.25);color:var(--accent)">
                        {{ $cat }}
                    </span>
                    @endforeach
                @else
                    @foreach($defaultTags as $tag)
                    <span class="interest-tag">{{ $tag }}</span>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(name) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-tab="${name}"]`).classList.add('active');
    document.getElementById(`tab-${name}`).classList.add('active');
}
</script>

@endsection