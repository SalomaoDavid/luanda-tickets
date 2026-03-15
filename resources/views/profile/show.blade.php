@extends('layouts.app')
@section('title', $user->name . ' — Luanda Bilhetes')
@section('content')
@php
    $handle = strtolower(preg_replace('/\s+/', '.', trim($user->name)));
@endphp
<style>
*, *::before, *::after { box-sizing: border-box; }
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

/* ══ COVER ══ */
.p-cover-wrap { position:relative; height:280px; overflow:hidden; margin:-40px -40px 0; }
.p-cover-bg {
    width:100%; height:100%; position:relative;
    background:linear-gradient(135deg,#050d1a 0%,#091828 30%,#0c1f3a 60%,#071220 100%);
}
.p-cover-bg img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.p-cover-glow {
    position:absolute; inset:0; z-index:1; pointer-events:none;
    background:
        radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.22) 0%,transparent 55%),
        radial-gradient(ellipse at 20% 80%,rgba(245,158,11,.08) 0%,transparent 40%);
}
.p-cover-fade {
    position:absolute; bottom:0; left:0; right:0; height:140px; z-index:2;
    background:linear-gradient(to top,#06090f 0%,transparent 100%);
}

/* ══ HEADER ══ */
.p-header { position:relative; z-index:3; }

.p-top {
    display:flex; align-items:flex-end; gap:24px;
    margin-top:-68px; padding-bottom:24px;
    border-bottom:1px solid rgba(6,182,212,.15); flex-wrap:wrap;
}

.p-ava-wrap { position:relative; flex-shrink:0; }
.p-ava {
    width:120px; height:120px; border-radius:50%;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:4px solid #06090f;
    display:flex; align-items:center; justify-content:center;
    font-size:44px; font-weight:800; color:#06b6d4;
    overflow:hidden; box-shadow:0 8px 32px rgba(6,182,212,.3);
}
.p-ava img { width:100%; height:100%; object-fit:cover; }
.p-dot-on  { position:absolute; bottom:6px; right:6px; width:18px; height:18px; background:#10b981; border-radius:50%; border:3px solid #06090f; box-shadow:0 0 8px rgba(16,185,129,.6); }
.p-dot-off { position:absolute; bottom:6px; right:6px; width:18px; height:18px; background:#475569; border-radius:50%; border:3px solid #06090f; }
.p-verified { position:absolute; top:2px; right:2px; width:22px; height:22px; background:#06b6d4; border-radius:50%; border:2px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:10px; }

.p-info { flex:1; min-width:0; padding-bottom:8px; }
.p-name-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px; }
.p-name   { font-size:26px; font-weight:800; letter-spacing:-.5px; color:#ffffff; }
.p-handle { font-size:14px; color:#94a3b8; }
.p-badge  { font-size:10px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:3px 9px; border-radius:20px; }
.badge-admin   { background:rgba(244,63,94,.2);   border:1px solid rgba(244,63,94,.4);   color:#f87171; }
.badge-creator { background:rgba(6,182,212,.2);   border:1px solid rgba(6,182,212,.4);   color:#22d3ee; }
.badge-user    { background:rgba(148,163,184,.15); border:1px solid rgba(148,163,184,.3); color:#94a3b8; }
.p-bio  { font-size:14px; color:#94a3b8; line-height:1.65; margin-bottom:10px; max-width:520px; }
.p-meta { display:flex; gap:20px; flex-wrap:wrap; }
.p-meta-item { display:flex; align-items:center; gap:6px; font-size:13px; color:#64748b; }
.p-meta-item strong { color:#94a3b8; }

.p-actions { display:flex; gap:10px; align-items:center; padding-bottom:8px; flex-shrink:0; }
.btn-follow {
    padding:10px 24px; border-radius:11px; font-size:14px; font-weight:700;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; border:none;
    cursor:pointer; box-shadow:0 4px 16px rgba(6,182,212,.4); transition:all .2s;
}
.btn-follow:hover { transform:translateY(-1px); box-shadow:0 6px 22px rgba(6,182,212,.5); }
.btn-msg, .btn-edit {
    padding:10px 20px; border-radius:11px; font-size:14px; font-weight:600;
    background:#1e293b; border:1px solid #334155;
    color:#e2e8f0; cursor:pointer; transition:all .2s;
    text-decoration:none; display:inline-flex; align-items:center; gap:6px;
}
.btn-msg:hover, .btn-edit:hover { background:#263548; border-color:#4a6080; color:#fff; }
.btn-edit { background:#0c2a3a; border-color:rgba(6,182,212,.4); color:#22d3ee; }
.btn-edit:hover { background:#0e3347; }
.btn-more {
    width:40px; height:40px; border-radius:11px; background:#1e293b; border:1px solid #334155;
    display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94a3b8; font-size:18px;
}

/* ══ STATS ══ */
.p-stats { display:flex; border-bottom:1px solid rgba(6,182,212,.12); }
.p-stat  { flex:1; text-align:center; padding:18px 12px; border-right:1px solid rgba(6,182,212,.1); cursor:pointer; transition:background .2s; }
.p-stat:last-child { border-right:none; }
.p-stat:hover { background:rgba(6,182,212,.05); }
.p-stat-num { font-size:22px; font-weight:800; color:#ffffff; line-height:1; }
.p-stat-lbl { font-size:11px; color:#64748b; margin-top:3px; text-transform:uppercase; letter-spacing:.8px; }

/* ══ TABS ══ */
.p-tabs { display:flex; border-bottom:1px solid rgba(6,182,212,.12); overflow-x:auto; scrollbar-width:none; }
.p-tabs::-webkit-scrollbar { display:none; }
.p-tab {
    padding:14px 22px; font-size:13.5px; font-weight:600; color:#64748b;
    cursor:pointer; border-bottom:2px solid transparent; transition:all .2s;
    display:flex; align-items:center; gap:7px; margin-bottom:-1px; white-space:nowrap;
}
.p-tab:hover { color:#e2e8f0; }
.p-tab.active { color:#06b6d4; border-bottom-color:#06b6d4; }
.p-tab-count { font-size:10px; background:#1e293b; border:1px solid #334155; border-radius:20px; padding:2px 7px; color:#64748b; }
.p-tab.active .p-tab-count { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.35); color:#06b6d4; }

/* ══ LAYOUT ══ */
.p-body { display:grid; grid-template-columns:1fr 320px; gap:28px; padding-top:28px; padding-bottom:60px; }
.p-panel { display:none; flex-direction:column; gap:16px; }
.p-panel.active { display:flex; }

/* ══ COMPOSE BOX ══ */
.compose-box {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.25);
    border-radius:16px; padding:18px;
    animation:fadeUp .3s ease;
}
.compose-top { display:flex; align-items:flex-start; gap:12px; margin-bottom:14px; }
.compose-ava {
    width:40px; height:40px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:2px solid rgba(6,182,212,.3);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px; color:#06b6d4; overflow:hidden;
}
.compose-ava img { width:100%; height:100%; object-fit:cover; }
.compose-textarea {
    flex:1; background:#0d1a2e; border:1px solid #2d3f55;
    border-radius:10px; padding:12px 14px; color:#e2e8f0;
    font-size:14px; resize:none; font-family:inherit; min-height:85px;
    outline:none; transition:border-color .2s; line-height:1.6;
}
.compose-textarea:focus { border-color:#06b6d4; }
.compose-textarea::placeholder { color:#475569; }
.compose-footer { display:flex; justify-content:flex-end; }
.compose-submit {
    padding:9px 22px; border-radius:9px;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9);
    color:#fff; font-size:13px; font-weight:700; border:none;
    cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(6,182,212,.35);
}
.compose-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(6,182,212,.45); }

/* ══ EV-POST ══ */
.ev-post {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.18);
    border-radius:18px; overflow:hidden;
    animation:fadeUp .4s ease both; transition:border-color .2s;
}
.ev-post:hover { border-color:rgba(6,182,212,.45); }
.ev-post-img {
    height:200px; display:flex; align-items:center; justify-content:center;
    font-size:80px; position:relative; overflow:hidden;
    background:linear-gradient(135deg,#050d1a,#091828);
}
.ev-post-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.ev-post-img-overlay {
    position:absolute; inset:0;
    background:linear-gradient(to bottom,transparent 50%,rgba(6,9,15,.7) 100%);
}
.ev-badge {
    position:absolute; top:12px; left:12px; z-index:2;
    font-size:10px; font-weight:700; letter-spacing:.8px;
    text-transform:uppercase; padding:4px 10px; border-radius:20px;
}
.ev-date-pill {
    position:absolute; bottom:12px; left:12px; z-index:2;
    background:rgba(6,9,15,.88); backdrop-filter:blur(8px);
    border:1px solid rgba(6,182,212,.25); border-radius:7px;
    font-size:11px; font-weight:600; padding:4px 9px; color:#e2e8f0;
}
.ev-save-btn {
    position:absolute; top:12px; right:12px; z-index:2;
    width:30px; height:30px; border-radius:8px;
    background:rgba(6,9,15,.8); border:1px solid rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center;
    font-size:13px; cursor:pointer; transition:all .2s;
}
.ev-save-btn:hover { background:rgba(6,182,212,.25); border-color:#06b6d4; }
.ev-body  { padding:16px 18px 18px; }
.ev-cat   { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:5px; }
.ev-title { font-size:18px; font-weight:700; letter-spacing:-.3px; margin-bottom:10px; line-height:1.25; color:#ffffff; }
.ev-meta  { display:flex; gap:16px; margin-bottom:13px; flex-wrap:wrap; }
.ev-meta span { display:flex; align-items:center; gap:5px; font-size:12.5px; color:#94a3b8; }
.ev-bar-wrap { margin-bottom:14px; }
.ev-bar-top  { display:flex; justify-content:space-between; font-size:11px; margin-bottom:5px; }
.ev-bar-top span   { color:#64748b; }
.ev-bar-top strong { color:#cbd5e1; }
.ev-bar      { height:4px; background:#1e293b; border-radius:2px; overflow:hidden; }
.ev-bar-fill { height:100%; border-radius:2px; }
.fill-ok   { background:linear-gradient(to right,#06b6d4,#0ea5e9); }
.fill-warn { background:linear-gradient(to right,#f59e0b,#f97316); }
.fill-crit { background:linear-gradient(to right,#f43f5e,#f97316); }
.ev-footer { display:flex; align-items:center; justify-content:space-between; padding-top:13px; border-top:1px solid #1e293b; }
.ev-price  { font-size:20px; font-weight:800; color:#f59e0b; }
.ev-price small { font-size:11px; color:#64748b; font-weight:400; }
.ev-price.free  { color:#10b981; font-size:15px; font-weight:700; }
.ev-actions { display:flex; gap:8px; align-items:center; }
.ev-like-btn {
    display:flex; align-items:center; gap:5px; padding:7px 12px;
    border-radius:9px; background:#1e293b; border:1px solid #334155;
    font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; color:#94a3b8;
}
.ev-like-btn:hover { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.1); }
.ev-like-btn.liked { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.12); }
.ev-buy-btn {
    padding:8px 18px; border-radius:9px;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9);
    color:#fff; font-size:13px; font-weight:700; border:none;
    cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(6,182,212,.35);
    text-decoration:none; display:inline-flex; align-items:center; gap:5px;
}
.ev-buy-btn:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(6,182,212,.45); }

/* ══ POST CARD ══ */
.post-card {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.14);
    border-radius:16px; padding:18px;
    animation:fadeUp .4s ease both; transition:border-color .2s;
}
.post-card:hover { border-color:rgba(6,182,212,.35); }
.post-author { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.post-ava {
    width:38px; height:38px; border-radius:50%; flex-shrink:0; overflow:hidden;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:2px solid rgba(6,182,212,.3);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px; color:#06b6d4;
}
.post-ava img { width:100%; height:100%; object-fit:cover; }
.post-name { font-size:13px; font-weight:700; color:#ffffff; }
.post-time { font-size:10px; color:#64748b; }
.post-text { font-size:14px; color:#cbd5e1; line-height:1.65; }
.post-img  { width:100%; border-radius:10px; margin-top:12px; object-fit:cover; max-height:300px; display:block; }
.post-del-btn { margin-left:auto; background:none; border:none; cursor:pointer; color:#64748b; font-size:16px; padding:4px; transition:color .2s; }
.post-del-btn:hover { color:#f43f5e; }

/* ══ EMPTY ══ */
.p-empty {
    text-align:center; padding:56px 20px;
    background:#111c2d; border:1px solid rgba(6,182,212,.1); border-radius:16px;
}
.p-empty-icon { font-size:40px; margin-bottom:10px; }
.p-empty-txt  { font-size:12px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:1px; }

/* ══ SIDEBAR ══ */
.p-sidebar { display:flex; flex-direction:column; gap:18px; }
.side-box {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.14);
    border-radius:16px; padding:18px;
    animation:fadeUp .4s ease both;
}
.side-title {
    font-size:13px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    color:#64748b; margin-bottom:14px; display:flex; align-items:center; gap:8px;
}
.side-title a { margin-left:auto; font-size:11px; color:#06b6d4; text-transform:none; letter-spacing:0; font-weight:600; text-decoration:none; }

/* Interesses */
.interests { display:flex; flex-wrap:wrap; gap:7px; }
.interest-tag { padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; border:1px solid #2d3f55; background:#1a2a3a; color:#94a3b8; }
.interest-tag.active { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.4); color:#22d3ee; }

/* Going list */
.going-list { display:flex; flex-direction:column; gap:10px; }
.going-item {
    display:flex; align-items:center; gap:10px; padding:10px;
    background:#0d1a2e; border-radius:11px; border:1px solid transparent;
    transition:all .2s; text-decoration:none;
}
.going-item:hover { border-color:rgba(6,182,212,.3); background:#0f1e36; }
.going-emoji { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; background:#1e293b; }
.going-info  { flex:1; min-width:0; }
.going-name  { font-size:12px; font-weight:600; color:#ffffff; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.going-date  { font-size:11px; color:#06b6d4; font-weight:600; }
.going-price { font-size:12px; color:#f59e0b; font-weight:700; flex-shrink:0; }

/* Mutuais */
.mutual-list { display:flex; flex-direction:column; gap:12px; }
.mutual-item { display:flex; align-items:center; gap:10px; }
.mutual-ava  { width:38px; height:38px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; color:#fff; border:1.5px solid #2d3f55; }
.mutual-name { font-size:13px; font-weight:600; color:#ffffff; }
.mutual-sub  { font-size:11px; color:#64748b; }
.mutual-info { flex:1; min-width:0; }
.mutual-btn  { padding:4px 12px; border-radius:8px; font-size:11px; font-weight:700; background:rgba(6,182,212,.12); border:1px solid rgba(6,182,212,.3); color:#06b6d4; cursor:pointer; transition:all .2s; text-decoration:none; display:inline-block; }
.mutual-btn:hover { background:rgba(6,182,212,.25); }

/* Galeria */
.gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.gallery-item { aspect-ratio:1; border-radius:8px; overflow:hidden; cursor:pointer; background:#1e293b; display:flex; align-items:center; justify-content:center; font-size:26px; transition:all .2s; border:1px solid #2d3f55; text-decoration:none; }
.gallery-item img { width:100%; height:100%; object-fit:cover; }
.gallery-item:hover { transform:scale(1.03); border-color:rgba(6,182,212,.5); }
</style>

{{-- COVER --}}
<div class="p-cover-wrap">
    <div class="p-cover-bg">
        @if(!empty($user->cover))
            <img src="{{ asset('storage/'.$user->cover) }}" alt="cover">
        @endif
        <div class="p-cover-glow"></div>
    </div>
    <div class="p-cover-fade"></div>
</div>

{{-- HEADER --}}
<div class="p-header">
    <div class="p-top">

        {{-- Avatar --}}
        <div class="p-ava-wrap">
            <div class="p-ava">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            @if(method_exists($user,'isOnline') && $user->isOnline())
                <div class="p-dot-on"></div>
            @else
                <div class="p-dot-off"></div>
            @endif
            <div class="p-verified">✓</div>
        </div>

        {{-- Info --}}
        <div class="p-info">
            <div class="p-name-row">
                <div class="p-name">{{ $user->name }}</div>
                <div class="p-handle">&#64;{{ $handle }}</div>
                @if($user->role === 'admin')
                    <span class="p-badge badge-admin">🛡 Admin</span>
                @elseif($user->role === 'creator')
                    <span class="p-badge badge-creator">🎟 Criador</span>
                @else
                    <span class="p-badge badge-user">👤 Membro</span>
                @endif
            </div>
            @if(!empty($user->bio))
                <div class="p-bio">{{ $user->bio }}</div>
            @endif
            <div class="p-meta">
                <div class="p-meta-item">📍 <strong>Luanda, Angola</strong></div>
                <div class="p-meta-item">🗓 <strong>Desde {{ $user->created_at->translatedFormat('M Y') }}</strong></div>
            </div>
        </div>

        {{-- Botões --}}
        <div class="p-actions">
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar Perfil</a>
            @else
                @auth
                    <button class="btn-follow" id="followBtn" onclick="toggleFollow()">+ Seguir</button>
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬 Mensagem</a>
                @endauth
            @endif
            <div class="btn-more">⋯</div>
        </div>
    </div>

    {{-- STATS — sem bilhetes vendidos nem guardados --}}
    <div class="p-stats">
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">Seguidores</div></div>
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">A seguir</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount }}</div><div class="p-stat-lbl">{{ $statsLabel }}</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $postagens->count() }}</div><div class="p-stat-lbl">Posts</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount2 }}</div><div class="p-stat-lbl">{{ $statsLabel2 }}</div></div>
    </div>

    {{-- TABS --}}
    <div class="p-tabs">
        <div class="p-tab active" data-tab="eventos" onclick="switchTab('eventos')">🎟 Eventos <span class="p-tab-count">{{ $eventos->count() }}</span></div>
        <div class="p-tab" data-tab="posts"    onclick="switchTab('posts')">📝 Posts <span class="p-tab-count">{{ $postagens->count() }}</span></div>
        <div class="p-tab" data-tab="galeria"  onclick="switchTab('galeria')">📸 Galeria</div>
        <div class="p-tab" data-tab="seguidores" onclick="switchTab('seguidores')">👥 Seguidores</div>
        <div class="p-tab" data-tab="avaliacoes" onclick="switchTab('avaliacoes')">⭐ Avaliações</div>
    </div>
</div>

{{-- BODY --}}
<div class="p-body">

    {{-- COLUNA ESQUERDA --}}
    <div>

        {{-- ── TAB EVENTOS ── --}}
        <div class="p-panel active" id="tab-eventos">
            @forelse($eventos as $evento)
            @php
                $preco    = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                $lotacao  = $evento->lotacao_maxima ?? 0;
                $vendidos = optional($evento->tiposIngresso)->sum('quantidade_vendida') ?? 0;
                $pct      = $lotacao > 0 ? round(($vendidos / $lotacao) * 100) : 0;
                $barClass = $pct >= 80 ? 'fill-crit' : ($pct >= 50 ? 'fill-warn' : 'fill-ok');
                $catNome  = optional($evento->categoria)->nome ?? 'Evento';
                $catEmoji = match(strtolower($catNome)) {
                    'música','musica'        => '🎵',
                    'arte','arte & cultura'  => '🎨',
                    'festa','festas'         => '🎉',
                    'desporto'               => '⚽',
                    'gastronomia'            => '🍽',
                    'negócios','negocios'    => '💼',
                    default                  => '🎟'
                };
                $catColor = match(strtolower($catNome)) {
                    'música','musica'        => '#06b6d4',
                    'arte','arte & cultura'  => '#a78bfa',
                    'festa','festas'         => '#f59e0b',
                    'desporto'               => '#10b981',
                    'gastronomia'            => '#f97316',
                    'negócios','negocios'    => '#0ea5e9',
                    default                  => '#06b6d4'
                };
            @endphp
            <div class="ev-post" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div class="ev-post-img">
                    @if($evento->imagem_capa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
                    @else
                        {{ $catEmoji }}
                    @endif
                    <div class="ev-post-img-overlay"></div>

                    @if($pct >= 80)
                        <span class="ev-badge" style="background:rgba(244,63,94,.9);color:#fff">🔥 A Esgotar</span>
                    @elseif($preco == 0)
                        <span class="ev-badge" style="background:rgba(16,185,129,.9);color:#fff">Gratuito</span>
                    @elseif($evento->created_at->isCurrentWeek())
                        <span class="ev-badge" style="background:rgba(14,165,233,.9);color:#fff">✨ Novo</span>
                    @endif

                    <div class="ev-date-pill">
                        📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }}
                        · {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}
                    </div>
                    <div class="ev-save-btn">🔖</div>
                </div>

                <div class="ev-body">
                    <div class="ev-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $catNome }}</div>
                    <div class="ev-title">{{ $evento->titulo }}</div>
                    <div class="ev-meta">
                        <span>📍 {{ $evento->localizacao ?? 'Luanda' }}</span>
                        <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
                        @if($lotacao > 0)<span>👥 {{ number_format($lotacao) }} lugares</span>@endif
                    </div>
                    @if($lotacao > 0)
                    <div class="ev-bar-wrap">
                        <div class="ev-bar-top">
                            <span>Disponibilidade de bilhetes</span>
                            <strong>{{ $vendidos }} / {{ $lotacao }} vendidos</strong>
                        </div>
                        <div class="ev-bar">
                            <div class="ev-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                    <div class="ev-footer">
                        <div class="ev-price {{ $preco == 0 ? 'free' : '' }}">
                            @if($preco == 0)
                                ✅ Entrada Gratuita
                            @else
                                {{ number_format($preco, 0, ',', '.') }} <small>Kz</small>
                            @endif
                        </div>
                        <div class="ev-actions">
                            @auth
                            <form method="POST" action="{{ route('evento.curtir', $evento->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="ev-like-btn {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'liked' : '' }}">
                                    {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? '❤️' : '🤍' }}
                                    {{ $evento->curtidas->count() }}
                                </button>
                            </form>
                            @endauth
                            <a href="{{ route('evento.detalhes', $evento->id) }}" class="ev-buy-btn">🎟 Comprar</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-empty">
                <div class="p-empty-icon">🎟</div>
                <div class="p-empty-txt">Nenhum evento ainda</div>
            </div>
            @endforelse
        </div>

        {{-- ── TAB POSTS ── --}}
        <div class="p-panel" id="tab-posts">

            {{-- Compose box — só para o dono --}}
            @if($isOwner)
            <div class="compose-box">
                <form method="POST" action="{{ route('social.publicar') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="compose-top">
                        <div class="compose-ava">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <textarea name="conteudo" class="compose-textarea"
                                  placeholder="Partilha algo com os teus seguidores..."></textarea>
                    </div>
                    <div class="compose-footer">
                        <button type="submit" class="compose-submit">✈️ Publicar</button>
                    </div>
                </form>
            </div>
            @endif

            @forelse($postagens as $post)
            <div class="post-card" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div class="post-author">
                    <div class="post-ava">
                        @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="">
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <div class="post-name">{{ $user->name }}</div>
                        <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                    @if($isOwner)
                    <form method="POST" action="{{ route('post.eliminar', $post->id) }}"
                          style="margin-left:auto" onsubmit="return confirm('Eliminar esta publicação?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="post-del-btn" title="Eliminar">🗑</button>
                    </form>
                    @endif
                </div>
                <p class="post-text">{{ $post->conteudo }}</p>
                @if($post->imagem)
                    <img src="{{ asset('storage/'.$post->imagem) }}" class="post-img" alt="">
                @endif
            </div>
            @empty
            <div class="p-empty">
                <div class="p-empty-icon">📝</div>
                <div class="p-empty-txt">Nenhuma publicação ainda</div>
            </div>
            @endforelse
        </div>

        {{-- ── TAB GALERIA ── --}}
        <div class="p-panel" id="tab-galeria">
            @php $comImg = $eventos->whereNotNull('imagem_capa'); @endphp
            @if($comImg->count() > 0)
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
                @foreach($comImg as $ev)
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                </a>
                @endforeach
            </div>
            @else
            <div class="p-empty">
                <div class="p-empty-icon">📸</div>
                <div class="p-empty-txt">Nenhuma imagem ainda</div>
            </div>
            @endif
        </div>

        {{-- ── TAB SEGUIDORES ── --}}
        <div class="p-panel" id="tab-seguidores">
            <div class="p-empty">
                <div class="p-empty-icon">👥</div>
                <div class="p-empty-txt">Sistema de seguidores em breve</div>
            </div>
        </div>

        {{-- ── TAB AVALIAÇÕES ── --}}
        <div class="p-panel" id="tab-avaliacoes">
            <div class="p-empty">
                <div class="p-empty-icon">⭐</div>
                <div class="p-empty-txt">Avaliações em breve</div>
            </div>
        </div>

    </div>

    {{-- SIDEBAR --}}
    <div class="p-sidebar">

        {{-- Eventos na sidebar --}}
        @if($eventos->count() > 0)
        <div class="side-box" style="animation-delay:.05s">
            <div class="side-title">
                📅
                @if($user->role === 'creator') Eventos publicados
                @elseif($user->role === 'admin') Eventos recentes
                @else Eventos curtidos @endif
            </div>
            <div class="going-list">
                @foreach($eventos->take(4) as $ev)
                @php
                    $pSide = optional($ev->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                    $eSide = match(strtolower(optional($ev->categoria)->nome ?? '')) {
                        'música','musica' => '🎵', 'arte' => '🎨',
                        'festa','festas'  => '🎉', 'desporto' => '⚽',
                        'gastronomia'     => '🍽', default => '🎟'
                    };
                @endphp
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="going-item">
                    <div class="going-emoji">{{ $eSide }}</div>
                    <div class="going-info">
                        <div class="going-name">{{ $ev->titulo }}</div>
                        <div class="going-date">{{ \Carbon\Carbon::parse($ev->data_evento)->translatedFormat('D, d M · H:i') }}</div>
                    </div>
                    <div class="going-price" style="{{ $pSide == 0 ? 'color:#10b981' : '' }}">
                        {{ $pSide == 0 ? 'Grátis' : number_format($pSide / 1000, 0) . 'k Kz' }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Interesses --}}
        <div class="side-box" style="animation-delay:.10s">
            <div class="side-title">🏷 Interesses</div>
            <div class="interests">
                @php
                    $cats = $eventos->map(fn($e) => optional($e->categoria)->nome)->filter()->unique()->values();
                @endphp
                @if($cats->count() > 0)
                    @foreach($cats->take(8) as $c)
                        <span class="interest-tag active">{{ $c }}</span>
                    @endforeach
                @else
                    @foreach(['🎵 Música','🎉 Festas','🎟 Eventos','🌊 Luanda','🇦🇴 Angola'] as $tag)
                        <span class="interest-tag">{{ $tag }}</span>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Galeria mini --}}
        @if($eventos->whereNotNull('imagem_capa')->count() > 0)
        <div class="side-box" style="animation-delay:.15s">
            <div class="side-title">
                📸 Galeria
                <a href="javascript:void(0)" onclick="switchTab('galeria')">Ver tudo →</a>
            </div>
            <div class="gallery-grid">
                @foreach($eventos->whereNotNull('imagem_capa')->take(6) as $ev)
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                </a>
                @endforeach
                @for($i = $eventos->whereNotNull('imagem_capa')->take(6)->count(); $i < 6; $i++)
                    <div class="gallery-item">🎟</div>
                @endfor
            </div>
        </div>
        @endif

        {{-- Poderás conhecer --}}
        @auth
        @if(!$isOwner)
        <div class="side-box" style="animation-delay:.20s">
            <div class="side-title">👥 Poderás conhecer</div>
            <div class="mutual-list">
                @foreach(\App\Models\User::where('id', '!=', $user->id)->where('id', '!=', auth()->id())->take(3)->get() as $u)
                <div class="mutual-item">
                    <div class="mutual-ava" style="background:linear-gradient(135deg,#0c3a4a,#1e6a7a)">
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                    </div>
                    <div class="mutual-info">
                        <div class="mutual-name">{{ $u->name }}</div>
                        <div class="mutual-sub">{{ ucfirst($u->role) }}</div>
                    </div>
                    <a href="{{ route('profile.show', $u->id) }}" class="mutual-btn">Ver</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endauth

    </div>
</div>

<script>
function switchTab(name) {
    document.querySelectorAll('.p-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.p-panel').forEach(p => p.classList.remove('active'));
    const tab = document.querySelector('[data-tab="' + name + '"]');
    const panel = document.getElementById('tab-' + name);
    if (tab) tab.classList.add('active');
    if (panel) panel.classList.add('active');
}
function toggleFollow() {
    const b = document.getElementById('followBtn');
    if (!b) return;
    b.classList.toggle('following');
    b.textContent = b.classList.contains('following') ? '✓ A seguir' : '+ Seguir';
}
</script>
@endsection