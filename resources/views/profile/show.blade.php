@extends('layouts.app')
@section('title', $user->name . ' — Luanda Bilhetes')
@section('content')

{{-- CSS partilhado — carregado uma vez, em cache pelo browser --}}
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

@php
$handle = strtolower(preg_replace('/\s+/', '.', trim($user->name)));
$isOnline = $user->last_seen && $user->last_seen->diffInMinutes(now()) < 5;
@endphp

{{-- ══ COVER ══ --}}
<div class="p-cover-wrap">
    <div class="p-cover-bg">
        @if($user->cover)
        <img src="{{ asset('storage/'.$user->cover) }}" alt="" loading="lazy">
        @endif
        <div class="p-cover-glow"></div>
    </div>
    <div class="p-cover-fade"></div>
</div>

{{-- ══ HEADER ══ --}}
<div class="p-header">
    <div class="p-top">

        {{-- Avatar --}}
        <div class="p-ava">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ e($user->name) }}" loading="lazy">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
            @if($user->is_verified)<div class="p-verified">✓</div>@endif
            <div class="{{ $isOnline ? 'p-dot-on' : 'p-dot-off' }}"></div>
        </div>

        {{-- Info --}}
        <div class="p-info">
            <div class="p-name-row">
                <span class="p-name">{{ e($user->name) }}</span>
                @if($user->is_verified)<span style="color:#06b6d4;font-size:14px;">✓</span>@endif
                <span class="p-badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="p-handle">@{{ $handle }}</div>
            @if($user->bio)
            <p class="p-bio">{{ e($user->bio) }}</p>
            @endif
            <div class="p-meta">
                <span class="p-meta-item">📅 Desde {{ $user->created_at->format('M Y') }}</span>
                @if($isOnline)
                <span class="p-meta-item" style="color:#10b981;">🟢 Online agora</span>
                @else
                <span class="p-meta-item">⏱ {{ $user->last_seen?->diffForHumans() ?? 'Nunca activo' }}</span>
                @endif
            </div>
        </div>

        {{-- Acções --}}
        <div class="p-actions">
            @auth
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar</a>
            @else
            @php
                $euSigo   = auth()->user()->estaSeguindo($user->id);
                $estaBloq = auth()->user()->estaBloqueado($user->id);
            @endphp
                <button id="btn-seguir" onclick="toggleSeguir({{ $user->id }})" class="btn-follow"
                        style="{{ $euSigo ? 'background:#1e293b;border:1px solid #334155;color:#e2e8f0;' : '' }}">
                    {{ $euSigo ? '✓ A seguir' : '➕ Seguir' }}
                </button>
                <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬</a>
                <div style="position:relative;" x-data="{open:false}">
                    <button class="btn-msg" x-on:click="open=!open" style="width:34px;padding:0;justify-content:center;font-weight:900;">•••</button>
                    <div x-show="open" x-cloak x-on:click.away="open=false"
                         style="position:absolute;right:0;top:38px;width:190px;background:#0d1526;border:1px solid rgba(6,182,212,.2);border-radius:14px;overflow:hidden;z-index:100;box-shadow:0 8px 30px rgba(0,0,0,.5);">
                        <button onclick="toggleBloquear({{ $user->id }})" id="btn-bloquear"
                                style="width:100%;padding:12px 14px;background:none;border:none;text-align:left;font-size:13px;color:{{ $estaBloq ? '#f87171' : '#e2e8f0' }};cursor:pointer;display:flex;align-items:center;gap:8px;border-bottom:1px solid rgba(255,255,255,.06);">
                            {{ $estaBloq ? '🔓 Desbloquear' : '🚫 Bloquear' }}
                        </button>
                        <button onclick="abrirDrawer('drawer-denunciar')"
                                style="width:100%;padding:12px 14px;background:none;border:none;text-align:left;font-size:13px;color:#f87171;cursor:pointer;display:flex;align-items:center;gap:8px;border-bottom:1px solid rgba(255,255,255,.06);">
                            ⚠️ Denunciar
                        </button>
                        <button onclick="partilharPerfil({{ $user->id }})"
                                style="width:100%;padding:12px 14px;background:none;border:none;text-align:left;font-size:13px;color:#e2e8f0;cursor:pointer;display:flex;align-items:center;gap:8px;">
                            🔗 Partilhar perfil
                        </button>
                    </div>
                </div>
            @endif
            @endauth
            @guest
            <button onclick="partilharPerfil({{ $user->id }})" class="btn-msg">🔗 Partilhar</button>
            @endguest
        </div>
    </div>

    {{-- Stats --}}
    <div class="p-stats">
        <div class="p-stat" onclick="switchTab('postagens')">
            <div class="p-stat-num">{{ $user->postagens_count }}</div>
            <div class="p-stat-lbl">Posts</div>
        </div>
        <div class="p-stat" onclick="switchTab('eventos')">
            <div class="p-stat-num">{{ $statsCount }}</div>
            <div class="p-stat-lbl">{{ $statsLabel }}</div>
        </div>
        <div class="p-stat">
            <div class="p-stat-num">{{ $statsCount2 }}</div>
            <div class="p-stat-lbl">{{ $statsLabel2 }}</div>
        </div>
        @if($isOwner)
        <div class="p-stat" onclick="switchTab('bilhetes')">
            <div class="p-stat-num">{{ $bilhetes->total() }}</div>
            <div class="p-stat-lbl">Bilhetes</div>
        </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="p-quick-actions">
        <button class="p-qa-btn active" id="tab-btn-postagens" onclick="switchTab('postagens')">
            <span class="p-qa-icon">📝</span>
            <span class="p-qa-label">Posts</span>
        </button>
        <button class="p-qa-btn" id="tab-btn-eventos" onclick="switchTab('eventos')">
            <span class="p-qa-icon">🎟</span>
            <span class="p-qa-label">{{ $statsLabel }}</span>
        </button>
        @if($isOwner)
        <button class="p-qa-btn" id="tab-btn-bilhetes" onclick="switchTab('bilhetes')">
            <span class="p-qa-icon">🎫</span>
            <span class="p-qa-label">Bilhetes</span>
            @if($bilhetes->total() > 0)
            <span class="p-qa-badge">{{ $bilhetes->total() }}</span>
            @endif
        </button>
        <button class="p-qa-btn" id="tab-btn-galeria" onclick="switchTab('galeria')">
            <span class="p-qa-icon">🖼</span>
            <span class="p-qa-label">Galeria</span>
        </button>
        @endif
    </div>
</div>

{{-- ══ PANEL POSTAGENS ══ --}}
<div class="p-panel active" id="panel-postagens">

    {{-- Compose (só dono) --}}
    @if($isOwner)
    <div style="background:#111c2d;border:1px solid rgba(6,182,212,.25);border-radius:14px;padding:14px;">
        <form method="POST" action="{{ route('social.publicar') }}" class="flex gap-3 items-start">
            @csrf
            <div style="width:34px;height:34px;border-radius:50%;overflow:hidden;flex-shrink:0;background:linear-gradient(135deg,#0c3a4a,#1e6a7a);border:2px solid rgba(6,182,212,.3);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#06b6d4;">
                @if($user->avatar)<img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">@else{{ strtoupper(substr($user->name,0,1)) }}@endif
            </div>
            <textarea name="conteudo" placeholder="O que estás a pensar?"
                      style="flex:1;background:#0d1a2e;border:1px solid #2d3f55;border-radius:10px;padding:9px 12px;color:#e2e8f0;font-size:13px;resize:none;font-family:inherit;min-height:60px;outline:none;line-height:1.6;width:100%;"
                      required></textarea>
            <button type="submit" style="padding:8px 16px;border-radius:9px;background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;white-space:nowrap;">Publicar</button>
        </form>
    </div>
    @endif

    @forelse($postagens as $post)
    <div class="post-card">
        <div class="post-author">
            <div class="post-ava">
                @if($user->avatar)<img src="{{ asset('storage/'.$user->avatar) }}" alt="" loading="lazy">@else{{ strtoupper(substr($user->name,0,1)) }}@endif
            </div>
            <div>
                <div class="post-name">{{ e($user->name) }}</div>
                <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
            </div>
            @if($isOwner)
            <form method="POST" action="{{ route('social.eliminarPost', $post->id) }}" style="margin-left:auto;" onsubmit="return confirm('Apagar post?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:14px;">🗑</button>
            </form>
            @endif
        </div>
        <p class="post-text">{{ e($post->conteudo) }}</p>
        @if($post->imagem)
        <img src="{{ asset('storage/'.$post->imagem) }}" class="post-img" alt="" loading="lazy">
        @endif
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">📝</div><div class="p-empty-txt">Sem publicações ainda</div></div>
    @endforelse

    {{-- Paginação posts --}}
    @if($postagens->hasPages())
    <div style="padding:10px 0;">{{ $postagens->links() }}</div>
    @endif
</div>

{{-- ══ PANEL EVENTOS ══ --}}
<div class="p-panel" id="panel-eventos">
    @forelse($eventos as $ev)
    @php
        $vendidos = $ev->tiposIngresso->sum(fn($t) => $t->quantidade_total - $t->quantidade_disponivel);
        $total    = $ev->tiposIngresso->sum('quantidade_total') ?: 1;
        $perc     = min(round(($vendidos / $total) * 100), 100);
        $fillClass = $perc >= 90 ? 'fill-crit' : ($perc >= 60 ? 'fill-warn' : 'fill-ok');
        $precoMin = $ev->tiposIngresso->min('preco') ?? 0;
    @endphp
    <div class="ev-post">
        <div class="ev-post-img">
            @if($ev->imagem_capa)
                <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="" loading="lazy">
            @else
                {{ optional($ev->categoria)->emoji ?? '' }}
            @endif
            <div class="ev-post-img-overlay"></div>
            @if($ev->status === 'publicado')
                <span class="ev-badge" style="background:rgba(16,185,129,.85);color:#fff;">✅ Publicado</span>
            @elseif($ev->status === 'rascunho')
                <span class="ev-badge" style="background:rgba(245,158,11,.85);color:#000;">📝 Rascunho</span>
            @else
                <span class="ev-badge" style="background:rgba(100,116,139,.85);color:#fff;">🔒 Encerrado</span>
            @endif
            <div class="ev-date-pill">📅 {{ \Carbon\Carbon::parse($ev->data_evento)->format('d/m/Y') }}</div>
        </div>
        <div class="ev-body">
            @if($ev->categoria)
            <div class="ev-cat" style="color:#06b6d4;">{{ $ev->categoria }} {{ $ev->categoria->nome }}</div>
            @endif
            <div class="ev-title">{{ e($ev->titulo) }}</div>
            <div class="ev-meta">
                <span>📍 {{ Str::limit($ev->localizacao, 25) }}</span>
                @if($ev->hora_inicio)<span>🕐 {{ substr($ev->hora_inicio, 0, 5) }}</span>@endif
            </div>
            <div class="ev-bar">
                <div class="ev-bar-fill {{ $fillClass }}" style="width:{{ $perc }}%"></div>
            </div>
            <div class="ev-footer">
                <div class="ev-price {{ $precoMin == 0 ? 'free' : '' }}">
                    {{ $precoMin == 0 ? 'Gratuito' : number_format($precoMin, 0, ',', '.') . ' Kz' }}
                </div>
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="ev-buy-btn">🎟 Ver</a>
            </div>
        </div>
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">🎟</div><div class="p-empty-txt">Sem eventos</div></div>
    @endforelse

    @if($eventos->hasPages())
    <div style="padding:10px 0;">{{ $eventos->links() }}</div>
    @endif
</div>

{{-- ══ PANEL BILHETES (só dono) ══ --}}
@if($isOwner)
<div class="p-panel" id="panel-bilhetes">
    @forelse($bilhetes as $bilhete)
    <div style="background:#111c2d;border:1px solid rgba(6,182,212,.18);border-radius:14px;padding:14px;display:flex;gap:12px;align-items:center;">
        <div style="width:42px;height:42px;border-radius:10px;background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.2);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">🎟</div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:2px;">{{ Str::limit(optional($bilhete->evento)->titulo ?? '—', 30) }}</div>
            <div style="font-size:11px;color:#64748b;">
                {{ optional($bilhete->tipoIngresso)->nome ?? '—' }} ·
                {{ $bilhete->created_at->format('d/m/Y') }}
            </div>
        </div>
        @if($bilhete->validado_em)
        <span style="font-size:9px;font-weight:700;padding:3px 8px;border-radius:20px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25);color:#10b981;white-space:nowrap;">✅ Usado</span>
        @else
        <span style="font-size:9px;font-weight:700;padding:3px 8px;border-radius:20px;background:rgba(6,182,212,.12);border:1px solid rgba(6,182,212,.25);color:#06b6d4;white-space:nowrap;">🎫 Válido</span>
        @endif
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">🎫</div><div class="p-empty-txt">Sem bilhetes ainda</div></div>
    @endforelse

    @if($bilhetes->hasPages())
    <div style="padding:10px 0;">{{ $bilhetes->links() }}</div>
    @endif
</div>

{{-- ══ PANEL GALERIA ══ --}}
<div class="p-panel" id="panel-galeria">
    @php
        $fotos = collect();
        foreach($postagens as $p) { if($p->imagem) $fotos->push($p->imagem); }
    @endphp
    @if($fotos->count() > 0)
    <div class="gallery-grid">
        @foreach($fotos as $foto)
        <div class="gallery-item" onclick="openLightbox('{{ asset('storage/'.$foto) }}')">
            <img src="{{ asset('storage/'.$foto) }}" alt="" loading="lazy">
        </div>
        @endforeach
    </div>
    @else
    <div class="p-empty"><div class="p-empty-icon">🖼</div><div class="p-empty-txt">Sem fotos ainda</div></div>
    @endif
</div>
@endif

{{-- DRAWER DENUNCIAR --}}
<div class="drawer-overlay" id="drawer-denunciar" onclick="if(event.target===this)fecharDrawer('drawer-denunciar')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">
            ⚠️ Denunciar perfil
            <button class="drawer-close" onclick="fecharDrawer('drawer-denunciar')">✕</button>
        </div>
        <p style="font-size:13px;color:#64748b;margin-bottom:16px;">Indica o motivo da denúncia. Vamos analisar e tomar as medidas necessárias.</p>
        <div style="margin-bottom:16px;">
            <select id="select-motivo" style="width:100%;background:#0d1a2e;border:1px solid rgba(6,182,212,.2);border-radius:11px;padding:11px 14px;font-size:14px;color:#e2e8f0;outline:none;-webkit-appearance:none;">
                <option value="">Seleciona o motivo...</option>
                <option value="spam">🚫 Spam</option>
                <option value="conteudo_inapropriado">🔞 Conteúdo inapropriado</option>
                <option value="assedio">😡 Assédio ou bullying</option>
                <option value="perfil_falso">🎭 Perfil falso</option>
                <option value="outro">❓ Outro motivo</option>
            </select>
        </div>
        <button onclick="enviarDenuncia({{ $user->id }})"
                style="width:100%;padding:13px;border-radius:12px;background:linear-gradient(135deg,#f43f5e,#f97316);color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;">
            Enviar Denúncia
        </button>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">✕</button>
    <img class="lightbox-img" id="lightbox-img" src="" alt="">
</div>

<script>
// ── SEGUIR ──────────────────────────────────────────────
async function toggleSeguir(userId) {
    const btn = document.getElementById('btn-seguir');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/perfil/${userId}/seguir`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
        });
        if (res.status === 401) { window.location.href = '/login'; return; }
        const data = await res.json();
        btn.textContent = data.seguindo ? '✓ A seguir' : '➕ Seguir';
        btn.style.cssText = data.seguindo
            ? 'background:#1e293b;border:1px solid #334155;color:#e2e8f0;padding:8px 16px;border-radius:11px;font-size:12px;font-weight:700;cursor:pointer;'
            : '';
        // Actualiza contador de seguidores
        const statNums = document.querySelectorAll('.p-stat-num');
        if (statNums.length >= 3) statNums[2].textContent = data.total_seguidores;
    } catch(e) { console.error(e); }
}

// ── BLOQUEAR ────────────────────────────────────────────
async function toggleBloquear(userId) {
    if (!confirm('Tens a certeza?')) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/perfil/${userId}/bloquear`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
        });
        const data = await res.json();
        const btn = document.getElementById('btn-bloquear');
        if (btn) btn.textContent = data.bloqueado ? '🔓 Desbloquear' : '🚫 Bloquear';
        if (data.bloqueado) {
            // Esconde o botão seguir
            const btnSeguir = document.getElementById('btn-seguir');
            if (btnSeguir) btnSeguir.style.display = 'none';
        }
    } catch(e) { console.error(e); }
}

// ── DENUNCIAR ───────────────────────────────────────────
async function enviarDenuncia(userId) {
    const motivo = document.getElementById('select-motivo').value;
    if (!motivo) { alert('Seleciona um motivo.'); return; }
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/perfil/${userId}/denunciar`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: JSON.stringify({ motivo })
        });
        const data = await res.json();
        fecharDrawer('drawer-denunciar');
        alert(data.message || 'Denúncia enviada!');
    } catch(e) { console.error(e); }
}

// ── PARTILHAR ───────────────────────────────────────────
async function partilharPerfil(userId) {
    const url = `/u/${userId}`;
    const fullUrl = window.location.origin + url;
    if (navigator.share) {
        try { await navigator.share({ title: 'Perfil — Luanda Tickets', url: fullUrl }); } catch(e) {}
    } else {
        await navigator.clipboard.writeText(fullUrl);
        alert('Link copiado!');
    }
}

// Tabs
function switchTab(id) {
    document.querySelectorAll('.p-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.p-qa-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('panel-' + id);
    const btn   = document.getElementById('tab-btn-' + id);
    if (panel) panel.classList.add('active');
    if (btn)   btn.classList.add('active');
}

// Lightbox
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLightbox(); });
</script>
@endsection