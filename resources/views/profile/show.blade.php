@extends('layouts.app')
@section('title', $user->name . ' — Luanda Bilhetes')
@section('content')

@push('modals')
<div id="modalBilhetes" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="absolute inset-0 cursor-pointer" onclick="fecharModalBilhetes()"></div>
    <div class="relative bg-slate-900 w-full max-w-2xl max-h-[90vh] rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl flex flex-col">
        <div class="p-5 border-b border-white/5 flex justify-between items-center bg-slate-800/40 sticky top-0 z-20 backdrop-blur-md">
            <div>
                <h3 class="text-lg font-black text-white tracking-tight">LUANDA TICKETS</h3>
                <p class="text-blue-400 text-xs font-bold uppercase tracking-widest">Meus Ingressos Ativos</p>
            </div>
            <button onclick="fecharModalBilhetes()" class="w-9 h-9 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-red-500 transition-colors">✕</button>
        </div>
        <div class="flex-1 overflow-y-auto p-5 space-y-5">
            @forelse($bilhetes as $bilhete)
            <div class="relative flex flex-col md:flex-row rounded-3xl overflow-hidden bg-white">
                <div class="flex-1 p-5 border-r-2 border-dashed border-gray-200">
                    <span class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black rounded-full uppercase">{{ $bilhete->tipoIngresso->nome ?? 'Acesso Geral' }}</span>
                    <h4 class="text-slate-900 font-black text-lg leading-tight mt-3 mb-2">{{ $bilhete->evento->titulo }}</h4>
                    <p class="text-gray-500 text-sm">📅 {{ \Carbon\Carbon::parse($bilhete->evento->data)->format('d/m/Y H:i') }}</p>
                    <p class="text-gray-500 text-sm">📍 {{ $bilhete->evento->localizacao }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-[10px] font-mono text-gray-400">ID: {{ substr($bilhete->codigo_unico, 0, 13) }}</span>
                        <a href="{{ route('bilhete.individual.download', $bilhete->id) }}" target="_blank"
                           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all">
                            📄 Baixar
                        </a>
                    </div>
                </div>
                <div class="w-full md:w-40 bg-slate-50 flex flex-col items-center justify-center p-5 border-t-2 md:border-t-0 border-dashed border-gray-200">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ $bilhete->codigo_unico }}" alt="QR" class="w-24 h-24 mb-2">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Apresente na entrada</p>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white/5 rounded-3xl border border-dashed border-white/10">
                <p class="text-gray-400">Nenhum bilhete encontrado.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endpush

@php $handle = strtolower(preg_replace('/\s+/', '.', trim($user->name))); @endphp

<style>
*, *::before, *::after { box-sizing: border-box; }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

/* ══ COVER ══ */
.p-cover-wrap { position:relative; height:160px; overflow:hidden; margin:-40px -16px 0; }
@media(min-width:768px){ .p-cover-wrap { height:240px; margin:-40px -40px 0; } }
.p-cover-bg { width:100%; height:100%; position:relative; background:linear-gradient(135deg,#050d1a 0%,#091828 30%,#0c1f3a 60%,#071220 100%); }
.p-cover-bg img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.p-cover-glow { position:absolute; inset:0; z-index:1; pointer-events:none; background:radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.22) 0%,transparent 55%),radial-gradient(ellipse at 20% 80%,rgba(245,158,11,.08) 0%,transparent 40%); }
.p-cover-fade { position:absolute; bottom:0; left:0; right:0; height:100px; z-index:2; background:linear-gradient(to top,#06090f 0%,transparent 100%); }

/* ══ HEADER ══ */
.p-header { position:relative; z-index:3; }
.p-top { display:flex; align-items:flex-end; gap:14px; margin-top:-44px; padding-bottom:14px; border-bottom:1px solid rgba(6,182,212,.15); flex-wrap:wrap; }
@media(min-width:768px){ .p-top { margin-top:-60px; gap:20px; padding-bottom:20px; } }

.p-ava { width:78px; height:78px; border-radius:50%; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:3px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:800; color:#06b6d4; overflow:hidden; box-shadow:0 8px 28px rgba(6,182,212,.3); position:relative; flex-shrink:0; }
@media(min-width:768px){ .p-ava { width:110px; height:110px; font-size:40px; } }
.p-ava img { width:100%; height:100%; object-fit:cover; }
.p-dot-on  { position:absolute; bottom:4px; right:4px; width:13px; height:13px; background:#10b981; border-radius:50%; border:2px solid #06090f; }
.p-dot-off { position:absolute; bottom:4px; right:4px; width:13px; height:13px; background:#475569; border-radius:50%; border:2px solid #06090f; }
.p-verified { position:absolute; top:2px; right:2px; width:17px; height:17px; background:#06b6d4; border-radius:50%; border:2px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:8px; }

.p-info { flex:1; min-width:0; padding-bottom:6px; }
.p-name-row { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:3px; }
.p-name { font-size:17px; font-weight:800; color:#fff; }
@media(min-width:768px){ .p-name { font-size:24px; } }
.p-handle { font-size:11px; color:#94a3b8; }
.p-badge { font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:2px 7px; border-radius:20px; }
.badge-admin   { background:rgba(244,63,94,.2); border:1px solid rgba(244,63,94,.4); color:#f87171; }
.badge-creator { background:rgba(6,182,212,.2); border:1px solid rgba(6,182,212,.4); color:#22d3ee; }
.badge-user    { background:rgba(148,163,184,.15); border:1px solid rgba(148,163,184,.3); color:#94a3b8; }
.p-bio { font-size:12px; color:#94a3b8; line-height:1.6; margin-bottom:6px; max-width:520px; }
.p-meta { display:flex; gap:10px; flex-wrap:wrap; }
.p-meta-item { display:flex; align-items:center; gap:4px; font-size:11px; color:#64748b; }

.p-actions { display:flex; gap:8px; align-items:center; padding-bottom:6px; flex-shrink:0; }
.btn-follow { padding:8px 16px; border-radius:11px; font-size:12px; font-weight:700; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; border:none; cursor:pointer; transition:all .2s; }
.btn-msg, .btn-edit { padding:8px 14px; border-radius:11px; font-size:12px; font-weight:600; background:#1e293b; border:1px solid #334155; color:#e2e8f0; cursor:pointer; transition:all .2s; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-edit { background:#0c2a3a; border-color:rgba(6,182,212,.4); color:#22d3ee; }
.btn-more { width:34px; height:34px; border-radius:11px; background:#1e293b; border:1px solid #334155; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94a3b8; font-size:15px; }

/* ══ STATS ══ */
.p-stats { display:flex; border-bottom:1px solid rgba(6,182,212,.12); overflow-x:auto; scrollbar-width:none; }
.p-stats::-webkit-scrollbar { display:none; }
.p-stat { flex:1; min-width:55px; text-align:center; padding:10px 6px; border-right:1px solid rgba(6,182,212,.1); cursor:pointer; transition:background .2s; }
.p-stat:last-child { border-right:none; }
.p-stat:hover { background:rgba(6,182,212,.05); }
.p-stat-num { font-size:17px; font-weight:800; color:#fff; line-height:1; }
.p-stat-lbl { font-size:9px; color:#64748b; margin-top:3px; text-transform:uppercase; letter-spacing:.8px; }

/* ══ QUICK ACTIONS ══ */
.p-quick-actions {
    display: flex; gap: 8px; padding: 14px 0; overflow-x: auto;
    scrollbar-width: none; border-bottom: 1px solid rgba(6,182,212,.12);
}
.p-quick-actions::-webkit-scrollbar { display: none; }
.p-qa-btn {
    flex-shrink: 0; display: flex; flex-direction: column; align-items: center; gap: 4px;
    padding: 10px 16px; border-radius: 12px; cursor: pointer;
    background: #111c2d; border: 1px solid rgba(6,182,212,.15);
    transition: all .2s; min-width: 70px;
}
.p-qa-btn:hover { border-color: rgba(6,182,212,.4); background: #162032; }
.p-qa-btn.active { border-color: rgba(6,182,212,.5); background: rgba(6,182,212,.1); }
.p-qa-icon { font-size: 20px; }
.p-qa-label { font-size: 10px; font-weight: 700; color: #64748b; text-align: center; white-space: nowrap; }
.p-qa-btn.active .p-qa-label { color: #06b6d4; }
.p-qa-badge { font-size: 9px; font-weight: 700; background: rgba(6,182,212,.2); color: #06b6d4; border-radius: 10px; padding: 1px 5px; }

/* ══ PANEL ══ */
.p-panel { display:none; flex-direction:column; gap:14px; padding-top:16px; animation:fadeUp .3s ease; }
.p-panel.active { display:flex; }

/* ══ EV-POST ══ */
.ev-post { background:#111c2d; border:1px solid rgba(6,182,212,.18); border-radius:16px; overflow:hidden; transition:border-color .2s; }
.ev-post:hover { border-color:rgba(6,182,212,.45); }
.ev-post-img { height:150px; display:flex; align-items:center; justify-content:center; font-size:50px; position:relative; overflow:hidden; background:linear-gradient(135deg,#050d1a,#091828); }
.ev-post-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.ev-post-img-overlay { position:absolute; inset:0; background:linear-gradient(to bottom,transparent 50%,rgba(6,9,15,.7) 100%); }
.ev-badge { position:absolute; top:10px; left:10px; z-index:2; font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:3px 8px; border-radius:20px; }
.ev-date-pill { position:absolute; bottom:10px; left:10px; z-index:2; background:rgba(6,9,15,.88); backdrop-filter:blur(8px); border:1px solid rgba(6,182,212,.25); border-radius:7px; font-size:10px; font-weight:600; padding:3px 8px; color:#e2e8f0; }
.ev-body { padding:12px 14px 14px; }
.ev-cat { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:3px; }
.ev-title { font-size:15px; font-weight:700; margin-bottom:7px; line-height:1.25; color:#fff; }
.ev-meta { display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap; }
.ev-meta span { display:flex; align-items:center; gap:4px; font-size:11px; color:#94a3b8; }
.ev-bar-wrap { margin-bottom:10px; }
.ev-bar-top { display:flex; justify-content:space-between; font-size:10px; margin-bottom:4px; }
.ev-bar-top span { color:#64748b; } .ev-bar-top strong { color:#cbd5e1; }
.ev-bar { height:4px; background:#1e293b; border-radius:2px; overflow:hidden; }
.ev-bar-fill { height:100%; border-radius:2px; }
.fill-ok   { background:linear-gradient(to right,#06b6d4,#0ea5e9); }
.fill-warn { background:linear-gradient(to right,#f59e0b,#f97316); }
.fill-crit { background:linear-gradient(to right,#f43f5e,#f97316); }
.ev-footer { display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid #1e293b; }
.ev-price { font-size:16px; font-weight:800; color:#f59e0b; }
.ev-price small { font-size:10px; color:#64748b; font-weight:400; }
.ev-price.free { color:#10b981; font-size:13px; font-weight:700; }
.ev-actions { display:flex; gap:6px; align-items:center; }
.ev-like-btn { display:flex; align-items:center; gap:4px; padding:5px 9px; border-radius:9px; background:#1e293b; border:1px solid #334155; font-size:11px; font-weight:600; cursor:pointer; transition:all .2s; color:#94a3b8; }
.ev-like-btn.liked { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.12); }
.ev-buy-btn { padding:6px 12px; border-radius:9px; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; font-size:11px; font-weight:700; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }

/* ══ POST CARD ══ */
.post-card { background:#111c2d; border:1px solid rgba(6,182,212,.14); border-radius:14px; padding:14px; animation:fadeUp .4s ease both; }
.post-author { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.post-ava { width:34px; height:34px; border-radius:50%; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:2px solid rgba(6,182,212,.3); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:#06b6d4; }
.post-ava img { width:100%; height:100%; object-fit:cover; }
.post-name { font-size:12px; font-weight:700; color:#fff; }
.post-time { font-size:10px; color:#64748b; }
.post-text { font-size:13px; color:#cbd5e1; line-height:1.65; }
.post-img { width:100%; border-radius:10px; margin-top:10px; object-fit:cover; max-height:280px; display:block; }
.post-del-btn { margin-left:auto; background:none; border:none; cursor:pointer; color:#64748b; font-size:14px; padding:4px; }
.post-del-btn:hover { color:#f43f5e; }

/* ══ COMPOSE ══ */
.compose-box { background:#111c2d; border:1px solid rgba(6,182,212,.25); border-radius:14px; padding:14px; }
.compose-top { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
.compose-ava { width:34px; height:34px; border-radius:50%; flex-shrink:0; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:2px solid rgba(6,182,212,.3); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:#06b6d4; overflow:hidden; }
.compose-ava img { width:100%; height:100%; object-fit:cover; }
.compose-textarea { flex:1; background:#0d1a2e; border:1px solid #2d3f55; border-radius:10px; padding:9px 12px; color:#e2e8f0; font-size:13px; resize:none; font-family:inherit; min-height:70px; outline:none; transition:border-color .2s; line-height:1.6; }
.compose-textarea:focus { border-color:#06b6d4; }
.compose-textarea::placeholder { color:#475569; }
.compose-footer { display:flex; justify-content:flex-end; }
.compose-submit { padding:7px 18px; border-radius:9px; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; font-size:13px; font-weight:700; border:none; cursor:pointer; }

/* ══ EMPTY ══ */
.p-empty { text-align:center; padding:36px 20px; background:#111c2d; border:1px solid rgba(6,182,212,.1); border-radius:14px; }
.p-empty-icon { font-size:32px; margin-bottom:8px; }
.p-empty-txt { font-size:10px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:1px; }

/* ══ DRAWERS ══ */
.drawer-overlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); }
.drawer-overlay.open { display:flex; align-items:flex-end; }
@media(min-width:640px){ .drawer-overlay.open { align-items:center; justify-content:center; } }
.drawer-box { width:100%; background:#0d1526; border:1px solid rgba(6,182,212,.2); border-radius:24px 24px 0 0; padding:20px 20px 32px; max-height:85vh; overflow-y:auto; }
@media(min-width:640px){ .drawer-box { border-radius:24px; max-width:500px; max-height:80vh; } }
.drawer-handle { width:36px; height:4px; border-radius:2px; background:rgba(6,182,212,.25); margin:0 auto 16px; }
@media(min-width:640px){ .drawer-handle { display:none; } }
.drawer-title { font-size:15px; font-weight:800; color:#fff; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; }
.drawer-close { background:none; border:none; font-size:20px; color:#64748b; cursor:pointer; }
.drawer-close:hover { color:#fff; }

/* ══ GOING LIST ══ */
.going-list { display:flex; flex-direction:column; gap:8px; }
.going-item { display:flex; align-items:center; gap:10px; padding:10px; background:#0d1a2e; border-radius:11px; border:1px solid transparent; transition:all .2s; text-decoration:none; }
.going-item:hover { border-color:rgba(6,182,212,.3); }
.going-emoji { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; background:#1e293b; }
.going-info { flex:1; min-width:0; }
.going-name { font-size:11px; font-weight:600; color:#fff; margin-bottom:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.going-date { font-size:10px; color:#06b6d4; font-weight:600; }
.going-price { font-size:11px; color:#f59e0b; font-weight:700; flex-shrink:0; }

/* ══ INTERESTS ══ */
.interests { display:flex; flex-wrap:wrap; gap:6px; }
.interest-tag { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; border:1px solid #2d3f55; background:#1a2a3a; color:#94a3b8; }
.interest-tag.active { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.4); color:#22d3ee; }

/* ══ MUTUAL ══ */
.mutual-list { display:flex; flex-direction:column; gap:10px; }
.mutual-item { display:flex; align-items:center; gap:10px; }
.mutual-ava { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:#fff; border:1.5px solid #2d3f55; }
.mutual-info { flex:1; }
.mutual-name { font-size:12px; font-weight:600; color:#fff; }
.mutual-sub { font-size:10px; color:#64748b; }
.mutual-btn { padding:4px 10px; border-radius:8px; font-size:10px; font-weight:700; background:rgba(6,182,212,.12); border:1px solid rgba(6,182,212,.3); color:#06b6d4; cursor:pointer; text-decoration:none; display:inline-block; }

/* ══ GALLERY GRID ══ */
.gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.gallery-item { aspect-ratio:1; border-radius:8px; overflow:hidden; background:#1e293b; display:flex; align-items:center; justify-content:center; font-size:20px; border:1px solid #2d3f55; text-decoration:none; }
.gallery-item img { width:100%; height:100%; object-fit:cover; }
</style>

{{-- COVER --}}
<div class="p-cover-wrap">
    <div class="p-cover-bg">
        @if(!empty($user->cover)) <img src="{{ asset('storage/'.$user->cover) }}" alt="cover"> @endif
        <div class="p-cover-glow"></div>
    </div>
    <div class="p-cover-fade"></div>
</div>

{{-- HEADER --}}
<div class="p-header">
    <div class="p-top">
        <div class="p-ava" style="position:relative;">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
            @else
                {{ strtoupper(substr($user->name, 0, 2)) }}
            @endif
            @if(method_exists($user,'isOnline') && $user->isOnline())
                <div class="p-dot-on"></div>
            @else
                <div class="p-dot-off"></div>
            @endif
            <div class="p-verified">✓</div>
        </div>

        <div class="p-info">
            <div class="p-name-row">
                <div class="p-name">{{ $user->name }}</div>
                <div class="p-handle">&#64;{{ $handle }}</div>
                @if($user->role === 'admin') <span class="p-badge badge-admin">🛡 Admin</span>
                @elseif($user->role === 'creator') <span class="p-badge badge-creator">🎟 Criador</span>
                @else <span class="p-badge badge-user">👤 Membro</span> @endif
            </div>
            @if(!empty($user->bio)) <div class="p-bio" style="word-break: normal; overflow-wrap: break-word; white-space: normal;">{{ Str::limit($user->bio, 160) }}</div> @endif
            <div class="p-meta">
                <div class="p-meta-item">📍 <strong style="color:#94a3b8">Luanda</strong></div>
                <div class="p-meta-item">🗓 <strong style="color:#94a3b8">{{ $user->created_at->translatedFormat('M Y') }}</strong></div>
            </div>
        </div>

        <div class="p-actions">
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar</a>
            @else
                @auth
                    <button class="btn-follow" id="followBtn" onclick="toggleFollow()">+ Seguir</button>
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬</a>
                @endauth
            @endif
            <div class="btn-more" onclick="abrirDrawer('drawer-mais')">⋯</div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="p-stats">
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">Seguidores</div></div>
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">A seguir</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount }}</div><div class="p-stat-lbl">{{ $statsLabel }}</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $postagens->count() }}</div><div class="p-stat-lbl">Posts</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount2 }}</div><div class="p-stat-lbl">{{ $statsLabel2 }}</div></div>
    </div>

    {{-- QUICK ACTIONS (substituem as tabs) --}}
    <div class="p-quick-actions">
        <div class="p-qa-btn active" id="qa-eventos" onclick="switchPanel('eventos')">
            <div class="p-qa-icon">🎟</div>
            <div class="p-qa-label">Eventos</div>
            <div class="p-qa-badge">{{ $eventos->count() }}</div>
        </div>
        <div class="p-qa-btn" id="qa-posts" onclick="switchPanel('posts')">
            <div class="p-qa-icon">📝</div>
            <div class="p-qa-label">Posts</div>
            <div class="p-qa-badge">{{ $postagens->count() }}</div>
        </div>
        <div class="p-qa-btn" id="qa-galeria" onclick="abrirDrawer('drawer-galeria')">
            <div class="p-qa-icon">📸</div>
            <div class="p-qa-label">Galeria</div>
        </div>
        @if($eventos->count() > 0)
        <div class="p-qa-btn" id="qa-agenda" onclick="abrirDrawer('drawer-agenda')">
            <div class="p-qa-icon">📅</div>
            <div class="p-qa-label">Agenda</div>
        </div>
        @endif
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-interesses')">
            <div class="p-qa-icon">🏷</div>
            <div class="p-qa-label">Interesses</div>
        </div>
        @if($isOwner)
        <div class="p-qa-btn" onclick="abrirModalBilhetes()">
            <div class="p-qa-icon">🎫</div>
            <div class="p-qa-label">Bilhetes</div>
        </div>
        @endif
        @auth
        @if(!$isOwner)
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-conhecer')">
            <div class="p-qa-icon">👥</div>
            <div class="p-qa-label">Conhecer</div>
        </div>
        @endif
        @endauth
    </div>
</div>

{{-- ══ PANELS ══ --}}

{{-- PANEL EVENTOS --}}
<div class="p-panel active" id="panel-eventos">
    @forelse($eventos as $evento)
    @php
        $preco    = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
        $lotacao  = $evento->lotacao_maxima ?? 0;
        $vendidos = optional($evento->tiposIngresso)->sum('quantidade_vendida') ?? 0;
        $pct      = $lotacao > 0 ? round(($vendidos / $lotacao) * 100) : 0;
        $barClass = $pct >= 80 ? 'fill-crit' : ($pct >= 50 ? 'fill-warn' : 'fill-ok');
        $catNome  = optional($evento->categoria)->nome ?? 'Evento';
        $catEmoji = match(strtolower($catNome)) { 'música','musica'=>'🎵','arte','arte & cultura'=>'🎨','festa','festas'=>'🎉','desporto'=>'⚽','gastronomia'=>'🍽','negócios','negocios'=>'💼',default=>'🎟' };
        $catColor = match(strtolower($catNome)) { 'música','musica'=>'#06b6d4','arte','arte & cultura'=>'#a78bfa','festa','festas'=>'#f59e0b','desporto'=>'#10b981','gastronomia'=>'#f97316','negócios','negocios'=>'#0ea5e9',default=>'#06b6d4' };
    @endphp
    <div class="ev-post">
        <div class="ev-post-img">
            @if($evento->imagem_capa) <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
            @else {{ $catEmoji }} @endif
            <div class="ev-post-img-overlay"></div>
            @if($pct >= 80) <span class="ev-badge" style="background:rgba(244,63,94,.9);color:#fff">🔥 A Esgotar</span>
            @elseif($preco == 0) <span class="ev-badge" style="background:rgba(16,185,129,.9);color:#fff">Gratuito</span>
            @elseif($evento->created_at->isCurrentWeek()) <span class="ev-badge" style="background:rgba(14,165,233,.9);color:#fff">✨ Novo</span>
            @endif
            <div class="ev-date-pill">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }} · {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</div>
        </div>
        <div class="ev-body">
            <div class="ev-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $catNome }}</div>
            <div class="ev-title">{{ $evento->titulo }}</div>
            <div class="ev-meta">
                <span>📍 {{ $evento->localizacao ?? 'Luanda' }}</span>
                <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
                @if($lotacao > 0)<span>👥 {{ number_format($lotacao) }}</span>@endif
            </div>
            @if($lotacao > 0)
            <div class="ev-bar-wrap">
                <div class="ev-bar-top"><span>Disponibilidade</span><strong>{{ $vendidos }}/{{ $lotacao }}</strong></div>
                <div class="ev-bar"><div class="ev-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div></div>
            </div>
            @endif
            <div class="ev-footer">
                <div class="ev-price {{ $preco == 0 ? 'free' : '' }}">
                    @if($preco == 0) ✅ Gratuito
                    @else {{ number_format($preco, 0, ',', '.') }} <small>Kz</small>
                    @endif
                </div>
                <div class="ev-actions">
                    @auth
                    <form method="POST" action="{{ route('evento.curtir', $evento->id) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="ev-like-btn {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'liked' : '' }}">
                            {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? '❤️' : '🤍' }} {{ $evento->curtidas->count() }}
                        </button>
                    </form>
                    @endauth
                    <a href="{{ route('evento.detalhes', $evento->id) }}" class="ev-buy-btn">🎟 Comprar</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">🎟</div><div class="p-empty-txt">Nenhum evento ainda</div></div>
    @endforelse
</div>

{{-- PANEL POSTS --}}
<div class="p-panel" id="panel-posts">
    @if($isOwner)
    <div class="compose-box">
        <form method="POST" action="{{ route('social.publicar') }}" enctype="multipart/form-data">
            @csrf
            <div class="compose-top">
                <div class="compose-ava">
                    @if(auth()->user()->avatar) <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                    @else {{ strtoupper(substr(auth()->user()->name, 0, 2)) }} @endif
                </div>
                <textarea name="conteudo" class="compose-textarea" placeholder="Partilha algo com os teus seguidores..."></textarea>
            </div>
            <div class="compose-footer"><button type="submit" class="compose-submit">✈️ Publicar</button></div>
        </form>
    </div>
    @endif
    @forelse($postagens as $post)
    <div class="post-card">
        <div class="post-author">
            <div class="post-ava">
                @if($user->avatar) <img src="{{ asset('storage/'.$user->avatar) }}" alt="">
                @else {{ strtoupper(substr($user->name, 0, 2)) }} @endif
            </div>
            <div>
                <div class="post-name">{{ $user->name }}</div>
                <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
            </div>
            @if($isOwner)
            <form method="POST" action="{{ route('post.eliminar', $post->id) }}" style="margin-left:auto" onsubmit="return confirm('Eliminar?')">
                @csrf @method('DELETE')
                <button type="submit" class="post-del-btn">🗑</button>
            </form>
            @endif
        </div>
        <p class="post-text">{{ $post->conteudo }}</p>
        @if($post->imagem) <img src="{{ asset('storage/'.$post->imagem) }}" class="post-img" alt=""> @endif
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">📝</div><div class="p-empty-txt">Nenhuma publicação ainda</div></div>
    @endforelse
</div>

{{-- ══ DRAWERS ══ --}}

{{-- DRAWER: GALERIA --}}
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this) fecharDrawer('drawer-galeria')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            📸 Galeria
            <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">✕</button>
        </div>
        @php $comImg = $eventos->whereNotNull('imagem_capa'); @endphp
        @if($comImg->count() > 0)
        <div class="gallery-grid">
            @foreach($comImg as $ev)
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
            </a>
            @endforeach
        </div>
        @else
        <div class="p-empty"><div class="p-empty-icon">📸</div><div class="p-empty-txt">Nenhuma imagem ainda</div></div>
        @endif
    </div>
</div>

{{-- DRAWER: AGENDA (eventos em lista compacta) --}}
<div class="drawer-overlay" id="drawer-agenda" onclick="if(event.target===this) fecharDrawer('drawer-agenda')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            📅 Agenda de Eventos
            <button class="drawer-close" onclick="fecharDrawer('drawer-agenda')">✕</button>
        </div>
        <div class="going-list">
            @foreach($eventos->take(8) as $ev)
            @php
                $pSide = optional($ev->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                $eSide = match(strtolower(optional($ev->categoria)->nome ?? '')) { 'música','musica'=>'🎵','arte'=>'🎨','festa','festas'=>'🎉','desporto'=>'⚽','gastronomia'=>'🍽',default=>'🎟' };
            @endphp
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="going-item">
                <div class="going-emoji">{{ $eSide }}</div>
                <div class="going-info">
                    <div class="going-name">{{ $ev->titulo }}</div>
                    <div class="going-date">{{ \Carbon\Carbon::parse($ev->data_evento)->translatedFormat('D, d M · H:i') }}</div>
                </div>
                <div class="going-price" style="{{ $pSide == 0 ? 'color:#10b981' : '' }}">
                    {{ $pSide == 0 ? 'Grátis' : number_format($pSide / 1000, 0).'k Kz' }}
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- DRAWER: INTERESSES --}}
<div class="drawer-overlay" id="drawer-interesses" onclick="if(event.target===this) fecharDrawer('drawer-interesses')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            🏷 Interesses
            <button class="drawer-close" onclick="fecharDrawer('drawer-interesses')">✕</button>
        </div>
        <div class="interests">
            @php $cats = $eventos->map(fn($e) => optional($e->categoria)->nome)->filter()->unique()->values(); @endphp
            @if($cats->count() > 0)
                @foreach($cats->take(8) as $c) <span class="interest-tag active">{{ $c }}</span> @endforeach
            @else
                @foreach(['🎵 Música','🎉 Festas','🎟 Eventos','🌊 Luanda','🇦🇴 Angola'] as $tag) <span class="interest-tag">{{ $tag }}</span> @endforeach
            @endif
        </div>
    </div>
</div>

{{-- DRAWER: PODERÁS CONHECER --}}
@auth
@if(!$isOwner)
<div class="drawer-overlay" id="drawer-conhecer" onclick="if(event.target===this) fecharDrawer('drawer-conhecer')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            👥 Poderás conhecer
            <button class="drawer-close" onclick="fecharDrawer('drawer-conhecer')">✕</button>
        </div>
        <div class="mutual-list">
            @foreach(\App\Models\User::where('id', '!=', $user->id)->where('id', '!=', auth()->id())->take(6)->get() as $u)
            <div class="mutual-item">
                <div class="mutual-ava" style="background:linear-gradient(135deg,#0c3a4a,#1e6a7a)">{{ strtoupper(substr($u->name, 0, 2)) }}</div>
                <div class="mutual-info">
                    <div class="mutual-name">{{ $u->name }}</div>
                    <div class="mutual-sub">{{ ucfirst($u->role) }}</div>
                </div>
                <a href="{{ route('profile.show', $u->id) }}" class="mutual-btn">Ver</a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endauth

{{-- DRAWER: MAIS OPÇÕES --}}
<div class="drawer-overlay" id="drawer-mais" onclick="if(event.target===this) fecharDrawer('drawer-mais')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            ⋯ Mais opções
            <button class="drawer-close" onclick="fecharDrawer('drawer-mais')">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('profile.show', $user->id) }}" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;text-decoration:none;">
                <span style="font-size:18px;">👤</span>
                <span style="font-size:13px;font-weight:600;">Ver perfil público</span>
            </a>
            @if($isOwner)
            <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;text-decoration:none;">
                <span style="font-size:18px;">✏️</span>
                <span style="font-size:13px;font-weight:600;">Editar perfil</span>
            </a>
            <button onclick="fecharDrawer('drawer-mais'); abrirModalBilhetes();" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;width:100%;text-align:left;cursor:pointer;">
                <span style="font-size:18px;">🎫</span>
                <span style="font-size:13px;font-weight:600;">Meus bilhetes</span>
            </button>
            @endif
            @auth
            @if(!$isOwner)
            <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;text-decoration:none;">
                <span style="font-size:18px;">💬</span>
                <span style="font-size:13px;font-weight:600;">Enviar mensagem</span>
            </a>
            @endif
            @endauth
        </div>
    </div>
</div>

<script>
function switchPanel(name) {
    document.querySelectorAll('.p-qa-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.p-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('qa-' + name)?.classList.add('active');
    document.getElementById('panel-' + name)?.classList.add('active');
}
function toggleFollow() {
    const b = document.getElementById('followBtn');
    if (!b) return;
    b.classList.toggle('following');
    b.textContent = b.classList.contains('following') ? '✓ A seguir' : '+ Seguir';
}
function abrirDrawer(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fecharDrawer(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function abrirModalBilhetes() {
    const modal = document.getElementById('modalBilhetes');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function fecharModalBilhetes() {
    const modal = document.getElementById('modalBilhetes');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.drawer-overlay.open').forEach(d => d.classList.remove('open'));
        fecharModalBilhetes();
        document.body.style.overflow = '';
    }
});
</script>
@endsection