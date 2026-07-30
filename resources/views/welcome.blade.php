@extends('layouts.app')
@section('title', 'Luanda Tickets - Rede Social de Entretenimento')
@section('content')

<style>
/* ── STORIES DUPLOS ── */
.story-outer{
    position:relative;width:72px;height:72px;flex-shrink:0;
    cursor:pointer;
}
.story-outer-ring{
    width:72px;height:72px;border-radius:50%;
    border:3px solid #3b82f6;
    overflow:hidden;
    box-shadow:0 0 0 2px #fff, 0 4px 12px rgba(59,130,246,.4);
    transition:transform .2s,box-shadow .2s;
}
.story-outer-ring:hover{transform:scale(1.07);box-shadow:0 0 0 2px #fff, 0 6px 20px rgba(59,130,246,.6);}
.story-outer-ring img{width:100%;height:100%;object-fit:cover;}
.story-outer-ring-ph{width:100%;height:100%;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:28px;}

/* Circulo pequeno (foto) dentro do grande */
.story-inner{
    position:absolute;bottom:-4px;right:-4px;
    width:28px;height:28px;border-radius:50%;
    border:2px solid #fff;
    overflow:hidden;
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    box-shadow:0 2px 8px rgba(0,0,0,.3);
    display:flex;align-items:center;justify-content:center;
}
.story-inner img{width:100%;height:100%;object-fit:cover;}
.story-inner-ph{font-size:12px;}

/* Badge de video */
.story-video-badge{
    position:absolute;top:-2px;left:-2px;
    width:18px;height:18px;border-radius:50%;
    background:#f43f5e;border:1.5px solid #fff;
    display:flex;align-items:center;justify-content:center;
    font-size:7px;color:#fff;font-weight:900;
}
.story-nome{font-size:10px;color:#374151;text-align:center;margin-top:4px;width:72px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

/* ── CARD EVENTO MELHORADO ── */
.ev-card{background:#fff;border-radius:20px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;margin-bottom:16px;}
.ev-card-img{position:relative;height:160px;}
.ev-card-img img{width:100%;height:100%;object-fit:cover;}
.ev-card-img-ph{width:100%;height:100%;background:linear-gradient(135deg,#1e40af,#4f46e5);display:flex;align-items:center;justify-content:center;font-size:40px;}
.ev-card-img-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,transparent 55%);}
.ev-card-title-wrap{position:absolute;bottom:0;left:0;right:0;padding:10px 14px;}
.ev-card-title{color:#fff;font-size:15px;font-weight:800;line-height:1.2;margin-bottom:3px;}
.ev-card-cat{display:inline-flex;align-items:center;gap:4px;background:rgba(59,130,246,.85);color:#fff;font-size:9px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:2px 8px;border-radius:20px;}

/* Info compacta */
.ev-info-row{display:flex;flex-wrap:wrap;gap:6px;padding:10px 14px 4px;}
.ev-info-chip{display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#475569;background:#f1f5f9;padding:3px 8px;border-radius:20px;font-weight:500;}
.ev-info-chip.destaque{background:#dbeafe;color:#1d4ed8;font-weight:700;}
.ev-desc{padding:0 14px 8px;font-size:12px;color:#64748b;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ev-actions{display:flex;align-items:center;justify-content:space-between;padding:8px 14px 12px;border-top:1px solid #f1f5f9;}

/* ── POPULARES — efeito atrativo ── */
.populares-wrap{
    position:relative;
    border-radius:20px;overflow:hidden;
    background:linear-gradient(135deg,#0f172a,#1e1b4b,#0f172a);
    padding:16px;margin-bottom:16px;
}
.populares-wrap::before{
    content:'';position:absolute;inset:0;
    background:
        radial-gradient(ellipse at 20% 50%,rgba(99,102,241,.25),transparent 60%),
        radial-gradient(ellipse at 80% 20%,rgba(6,182,212,.2),transparent 50%);
    pointer-events:none;
}
.pop-title{
    font-size:11px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;
    margin-bottom:12px;position:relative;
    background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
    background-clip:text;
    animation:shimmer-text 3s linear infinite;
    background-size:200% auto;
}
@keyframes shimmer-text{to{background-position:200% center}}
.pop-title::after{
    content:'';display:block;height:2px;border-radius:999px;margin-top:6px;
    background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6);
    animation:shimmer-text 3s linear infinite;
    background-size:200% auto;
}
.pop-item{
    display:flex;align-items:center;gap:10px;
    padding:8px 10px;border-radius:12px;margin-bottom:6px;
    background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);
    text-decoration:none;transition:all .2s;position:relative;overflow:hidden;
}
.pop-item:hover{background:rgba(255,255,255,.1);transform:translateX(4px);}
.pop-item::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
    background:linear-gradient(to bottom,#38bdf8,#818cf8);
    border-radius:0 2px 2px 0;
}
.pop-rank{font-size:11px;font-weight:900;color:#64748b;width:16px;flex-shrink:0;}
.pop-rank.top{background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.pop-img{width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.1);}
.pop-img-ph{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.pop-nome{font-size:12px;font-weight:700;color:#f0f6ff;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.pop-info{font-size:10px;color:#64748b;}
.pop-badge{
    font-size:9px;font-weight:800;padding:2px 7px;border-radius:20px;flex-shrink:0;
    background:linear-gradient(135deg,#0ea5e9,#6366f1);color:#fff;
}

/* ── NOTIFICAÇÕES SIDEBAR ── */
.notif-section{background:#fff;border-radius:16px;padding:14px;margin-bottom:12px;box-shadow:0 2px 12px rgba(0,0,0,.06);}
.notif-title{font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#94a3b8;margin-bottom:10px;}
.notif-item{
    display:flex;align-items:center;gap:9px;
    padding:8px;border-radius:10px;text-decoration:none;
    transition:background .15s;position:relative;
}
.notif-item:hover{background:#f8fafc;}
.notif-item-icon{
    width:34px;height:34px;border-radius:10px;flex-shrink:0;
    display:flex;align-items:center;justify-content:center;font-size:16px;
}
.notif-item-icon.blue{background:#dbeafe;}
.notif-item-icon.green{background:#d1fae5;}
.notif-item-icon.purple{background:#ede9fe;}
.notif-item-icon.amber{background:#fef3c7;}
.notif-item-label{font-size:13px;font-weight:600;color:#1e293b;flex:1;}
.notif-item-sub{font-size:10px;color:#94a3b8;}
/* badge de contagem */
.notif-badge{
    min-width:18px;height:18px;border-radius:999px;
    background:#f43f5e;color:#fff;
    font-size:10px;font-weight:800;
    display:flex;align-items:center;justify-content:center;
    padding:0 4px;flex-shrink:0;
    animation:badge-pulse .8s ease infinite alternate;
}
@keyframes badge-pulse{from{transform:scale(1)}to{transform:scale(1.15)}}

/* ── ÍCONE MENSAGENS FLUTUANTE ── */
.msg-float{
    position:fixed;bottom:80px;right:18px;z-index:400;
    width:52px;height:52px;border-radius:50%;
    background:linear-gradient(135deg,#2563eb,#7c3aed);
    box-shadow:0 4px 20px rgba(37,99,235,.5);
    display:flex;align-items:center;justify-content:center;
    font-size:22px;cursor:pointer;text-decoration:none;
    animation:float-bounce 2s ease-in-out infinite;
    transition:transform .2s;
}
.msg-float:hover{transform:scale(1.12);}
@keyframes float-bounce{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-8px);}
}
.msg-float-badge{
    position:absolute;top:-2px;right:-2px;
    min-width:18px;height:18px;border-radius:999px;
    background:#f43f5e;border:2px solid #fff;
    color:#fff;font-size:9px;font-weight:800;
    display:flex;align-items:center;justify-content:center;
    padding:0 3px;
}
</style>

{{-- ════════════════════════════════════════
     STORIES — CÍRCULOS DUPLOS
════════════════════════════════════════ --}}
<div class="sticky top-0 z-10 bg-white/90 backdrop-blur-md rounded-2xl py-3 mb-4 overflow-hidden shadow-sm">
    <div id="stories-track" class="flex items-center" style="will-change:transform;">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0 story-item px-3">
            <a href="{{ route('evento.detalhes', $evento->id) }}">
                <div style="position:relative;width:72px;height:72px;display:inline-block;">
                    {{-- Círculo grande — vídeo (usa a imagem de capa como placeholder) --}}
                    <div class="story-outer-ring">
                        @if($evento->imagem_capa)
                            <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}">
                        @else
                            <div class="story-outer-ring-ph">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
                        @endif
                    </div>
                    {{-- Badge de video no topo --}}
                    <div class="story-video-badge">▶</div>
                    {{-- Círculo pequeno — foto (primeira foto da galeria) --}}
                    <div class="story-inner">
                        @if($evento->fotos && $evento->fotos->count() > 0)
                            <img src="{{ asset('storage/'.$evento->fotos->first()->caminho) }}" alt="">
                        @elseif($evento->imagem_capa)
                            <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">
                        @else
                            <span class="story-inner-ph">📷</span>
                        @endif
                    </div>
                </div>
                <p class="story-nome">{{ Str::limit($evento->titulo, 10) }}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- ESPAÇO DE PUBLICAÇÃO --}}
@auth
<div class="bg-white p-3 md:p-4 rounded-2xl shadow-xl mb-4">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-100 text-gray-800 p-3 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                  rows="2"></textarea>
        <div class="flex justify-between items-center mt-2">
            <div class="text-gray-400 text-xs">📷 Foto | 🎥 Vídeo | 🎟 Evento</div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-1.5 rounded-xl font-semibold hover:scale-105 transition text-sm">
                Publicar
            </button>
        </div>
    </form>
</div>
@endauth
{{-- FEED UNIFICADO --}}
@foreach($feed as $entry)

    @if($entry['tipo'] === 'post')
    @php
        $post = $entry['item'];
        $minhaReacaoPost = auth()->check() ? $post->reacoes->where('user_id', auth()->id())->first() : null;
        $euCurtiPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'curtida';
        $euAdoroPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'adoro';
    @endphp
    <div class="bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">
        <div class="flex items-center space-x-3 px-4 pt-4 pb-2">
            <a href="{{ route('profile.show', $post->user->id) }}">
                <img src="{{ $post->user->avatar ? asset('storage/'.$post->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($post->user->name).'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div class="flex-1">
                <a href="{{ route('profile.show', $post->user->id) }}" class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $post->user->name }}
                </a>
                <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <div class="px-4 pb-3">
            <p class="text-gray-700 text-sm leading-relaxed">{{ $post->conteudo }}</p>
        </div>
        <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100 text-gray-500 text-xs">
            <button onclick="toggleReacaoPost({{ $post->id }}, 'adoro', this)"
                    class="flex items-center gap-1 hover:text-red-500 transition font-medium {{ $euAdoroPost ? 'text-red-500' : '' }}">
                ❤️ <span class="adoro-count-post-{{ $post->id }}">{{ $post->reacoes->where('tipo','adoro')->count() ?: '' }}</span>
            </button>
            <button onclick="toggleReacaoPost({{ $post->id }}, 'curtida', this)"
                    class="flex items-center gap-1 hover:text-blue-500 transition font-medium {{ $euCurtiPost ? 'text-blue-500' : '' }}">
                👍 <span class="curtida-count-post-{{ $post->id }}">{{ $post->reacoes->where('tipo','curtida')->count() ?: '' }}</span>
            </button>
            <button onclick="abrirModalComentariosPost('modal-comentarios-post-{{ $post->id }}')"
                    class="flex items-center gap-1 hover:text-blue-500 transition font-medium">
                💬 <span class="contador-comentarios-post-{{ $post->id }}">{{ $post->comentarios->count() }}</span>
            </button>
            <button onclick="partilhar('{{ addslashes($post->conteudo) }}', window.location.href)"
                    class="flex items-center gap-1 hover:text-green-500 transition font-medium">
                🔗 Partilhar
            </button>
        </div>
        @auth
        <div class="px-4 pb-3 pt-1">
            <form class="flex gap-2 items-center" onsubmit="enviarComentarioPost(event, {{ $post->id }})">
                @csrf
                <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                <input type="text" name="corpo" placeholder="Escreve um comentário..."
                       class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition input-comentario-post-{{ $post->id }}"
                       style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                <button type="submit"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button>
            </form>
        </div>
        @else
        <div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);">
            <a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a>
        </div>
        @endauth
    </div>

    @php
    /* modal comentários post */
    @endphp
    <div id="modal-comentarios-post-{{ $post->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center"
         style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
             style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
            <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);">
                <h3 class="font-black text-white text-sm uppercase tracking-widest">
                    💬 Comentários (<span class="contador-modal-post-{{ $post->id }}">{{ $post->comentarios->count() }}</span>)
                </h3>
                <button onclick="fecharModalComentariosPost('modal-comentarios-post-{{ $post->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 lista-comentarios-post-{{ $post->id }}" style="scrollbar-width:thin;">
                @forelse($post->comentarios as $com)
                <div class="flex gap-3">
                    <a href="{{ route('profile.show', $com->user->id) }}" class="flex-shrink-0">
                        <img src="{{ $com->user->avatar ? asset('storage/'.$com->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($com->user->name).'&background=0ea5e9&color=fff&size=64' }}"
                             class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                    </a>
                    <div class="flex-1">
                        <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);">
                            <p class="font-bold text-white text-xs">{{ $com->user->name }}</p>
                            <p class="text-gray-200 text-sm mt-1">{{ $com->corpo }}</p>
                        </div>
                        <span class="text-gray-500 text-[10px] ml-1">{{ $com->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-600">
                    <p class="text-2xl mb-2">💬</p>
                    <p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @else
    {{-- ════════════════════════════════════════
         CARD DE EVENTO — MELHORADO
    ════════════════════════════════════════ --}}
    @php
        $evento = $entry['item'];
        $euCurtiEvento = $evento->usuariosQueCurtiram->contains(auth()->id());
        $horaEvento = $evento->hora_inicio
            ? substr($evento->hora_inicio, 0, 5)
            : \Carbon\Carbon::parse($evento->data_evento)->format('H:i');
        $temHora = $evento->hora_inicio || \Carbon\Carbon::parse($evento->data_evento)->format('H:i') !== '00:00';
    @endphp

    <div class="ev-card">

        {{-- Cabeçalho --}}
        <div class="flex items-center space-x-3 px-4 pt-3 pb-2">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}">
                <img src="{{ $evento->user && $evento->user->avatar ? asset('storage/'.$evento->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($evento->user->name??'U').'&background=0ea5e9&color=fff&size=128' }}"
                     class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div class="flex-1 min-w-0">
                <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $evento->user ? $evento->user->name : 'Usuário' }}
                </a>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p>
            </div>
            {{-- Categoria badge --}}
            @if($evento->categoria)
            <span class="ev-card-cat">{{ $evento->categoria->nome }} {{ $evento->categoria->nome }}</span>
            @endif
        </div>

        {{-- Imagem + título --}}
        <div class="ev-card-img">
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}">
            @else
                <div class="ev-card-img-ph">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
            @endif
            <div class="ev-card-img-overlay"></div>
            <div class="ev-card-title-wrap">
                <div class="ev-card-title">{{ e($evento->titulo) }}</div>
            </div>
        </div>

        {{-- INFO CHIPS — mais informação sem aumentar o card --}}
        <div class="ev-info-row">
            <span class="ev-info-chip">📍 {{ Str::limit($evento->localizacao ?? 'Local n/d', 22) }}</span>
            <span class="ev-info-chip destaque">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
            @if($evento->hora_inicio)
                <span class="ev-info-chip destaque">🕐 {{ substr($evento->hora_inicio, 0, 5) }}</span>
            @endif
            @if($evento->provincia)
                <span class="ev-info-chip">🗺 {{ $evento->provincia }}</span>
            @endif
            @if($evento->lotacao_maxima)
                <span class="ev-info-chip">👥 {{ number_format($evento->lotacao_maxima) }}</span>
            @endif
            @if($evento->tiposIngresso && $evento->tiposIngresso->count() > 0)
                @php $precoMin = $evento->tiposIngresso->min('preco'); @endphp
                <span class="ev-info-chip destaque">
                    🎟 @if($precoMin == 0) Grátis @else {{ number_format($precoMin, 0, ',', '.') }} Kz @endif
                </span>
            @endif
            @if($evento->online)
                <span class="ev-info-chip" style="background:#d1fae5;color:#065f46;">🌐 Online</span>
            @endif
        </div>

        {{-- Descrição --}}
        <p class="ev-desc">{{ $evento->descricao }}</p>

        {{-- Ações --}}
        <div class="ev-actions">
            <button onclick="toggleCurtida({{ $evento->id }}, this)"
                    class="flex items-center gap-1 text-xs hover:text-blue-500 transition font-medium {{ $euCurtiEvento ? 'text-blue-500' : 'text-gray-500' }}">
                👍 <span class="curtida-texto-{{ $evento->id }}">{{ $euCurtiEvento ? 'Curtido' : 'Curtir' }}</span>
                (<span class="curtida-count-{{ $evento->id }}">{{ $evento->curtidas->count() }}</span>)
            </button>

            <button onclick="abrirModalComentarios('modal-comentarios-{{ $evento->id }}')"
                    class="flex items-center gap-1 text-xs text-gray-500 hover:text-blue-500 transition font-medium">
                💬 <span class="comentario-count-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>
            </button>

            <button onclick="partilhar('{{ addslashes(e($evento->titulo)) }}', '{{ route('evento.detalhes', $evento->id) }}')"
                    class="flex items-center gap-1 text-xs text-gray-500 hover:text-green-500 transition font-medium">
                🔗 Partilhar
            </button>

            <a href="{{ route('evento.detalhes', $evento->id) }}"
               class="bg-blue-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:scale-105 transition text-xs">
                🎟 Comprar
            </a>
        </div>

        {{-- Quem curtiu --}}
        @if($evento->usuariosQueCurtiram->count() > 0)
        @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
        <button onclick="abrirModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="flex items-center px-4 pb-3">
            <div class="flex" style="direction:rtl;">
                @foreach($curtidores->take(5) as $cu)
                <img src="{{ $cu->avatar ? asset('storage/'.$cu->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($cu->name).'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-6 h-6 rounded-full border-2 border-white object-cover -mr-2" style="direction:ltr;" title="{{ $cu->name }}">
                @endforeach
            </div>
            @if($evento->usuariosQueCurtiram->count() > 5)
            <span class="text-xs text-gray-400 ml-4">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>
            @endif
        </button>
        @endif
    </div>

    {{-- Modal comentários evento --}}
    <div id="modal-comentarios-{{ $evento->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center"
         style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
             style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
            <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);">
                <h3 class="font-black text-white text-sm uppercase tracking-widest">
                    💬 Comentários (<span class="contador-modal-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>)
                </h3>
                <button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5" style="scrollbar-width:thin;scrollbar-color:rgba(59,130,246,0.3) transparent;">
                @forelse($evento->comentarios as $comentario)
                <div class="flex gap-3" id="comentario-{{ $comentario->id }}">
                    <a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0">
                        <img src="{{ $comentario->user->avatar ? asset('storage/'.$comentario->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($comentario->user->name).'&background=0ea5e9&color=fff&size=64' }}"
                             class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                    </a>
                    <div class="flex-1">
                        <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);">
                            <div class="flex justify-between items-start gap-2">
                                <a href="{{ route('profile.show', $comentario->user->id) }}" class="font-bold text-white text-xs hover:text-blue-400 transition">{{ $comentario->user->name }}</a>
                                @if(auth()->id() === $comentario->user_id)
                                <button onclick="eliminarComentario({{ $comentario->id }}, this)" class="text-gray-600 hover:text-red-400 transition text-xs">🗑</button>
                                @endif
                            </div>
                            <p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p>
                        </div>
                        <div class="flex items-center gap-4 mt-1.5 ml-1">
                            <span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span>
                            <button onclick="toggleLikeComentario({{ $comentario->id }}, this)"
                                    class="text-[11px] font-bold transition {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                👍 <span class="like-count-{{ $comentario->id }}">{{ $comentario->likes->count() > 0 ? $comentario->likes->count() : '' }}</span>
                            </button>
                            @auth
                            <button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')" class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">Responder</button>
                            @endauth
                        </div>
                        @auth
                        <div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2">
                            <div class="flex gap-2">
                                <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}"
                                     class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0">
                                <input type="text" placeholder="Responder..." id="input-resposta-{{ $comentario->id }}"
                                       class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none"
                                       style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                                <button onclick="enviarResposta({{ $evento->id }}, {{ $comentario->id }})"
                                        class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition"
                                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button>
                            </div>
                        </div>
                        @endauth
                        @if($comentario->respostas && $comentario->respostas->count() > 0)
                        <div class="mt-2 pl-4 space-y-2" id="respostas-{{ $comentario->id }}">
                            @foreach($comentario->respostas as $resposta)
                            <div class="flex gap-2" id="resposta-{{ $resposta->id }}">
                                <a href="{{ route('profile.show', $resposta->user->id) }}" class="flex-shrink-0">
                                    <img src="{{ $resposta->user->avatar ? asset('storage/'.$resposta->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($resposta->user->name).'&background=0ea5e9&color=fff&size=64' }}"
                                         class="w-7 h-7 rounded-full object-cover border border-blue-400">
                                </a>
                                <div class="flex-1">
                                    <div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background:rgba(30,41,59,0.6);border:1px solid rgba(59,130,246,0.08);">
                                        <p class="font-bold text-white text-[11px]">{{ $resposta->user->name }}</p>
                                        <p class="text-gray-300 text-xs mt-0.5">{{ $resposta->corpo }}</p>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1 ml-1">
                                        <span class="text-gray-500 text-[10px]">{{ $resposta->created_at->diffForHumans() }}</span>
                                        <button onclick="toggleLikeComentario({{ $resposta->id }}, this)"
                                                class="text-[10px] font-bold transition {{ $resposta->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                            👍 <span class="like-count-{{ $resposta->id }}">{{ $resposta->likes->count() > 0 ? $resposta->likes->count() : '' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div id="respostas-{{ $comentario->id }}" class="mt-2 pl-4 space-y-2 hidden"></div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-600">
                    <p class="text-2xl mb-2">💬</p>
                    <p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p>
                    <p class="text-xs mt-1">Sê o primeiro a comentar!</p>
                </div>
                @endforelse
            </div>
            @auth
            <div class="px-6 py-4" style="border-top:1px solid rgba(59,130,246,0.15);">
                <form id="form-comentario-{{ $evento->id }}" class="flex gap-3 items-center"
                      onsubmit="enviarComentario(event, {{ $evento->id }})">
                    @csrf
                    <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                    <input type="text" name="corpo" placeholder="Escreve um comentário..."
                           class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none"
                           style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                    <button type="submit"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold hover:scale-105 flex-shrink-0"
                            style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 15px rgba(37,99,235,0.3);">➤</button>
                </form>
            </div>
            @else
            <div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);">
                <a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- Modal curtidas --}}
    <div id="modal-curtidas-{{ $evento->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/50">
        <div class="bg-white rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden" style="position:relative;z-index:10000;">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="font-bold text-gray-900">👍 Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3>
                <button onclick="fecharModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
            </div>
            <div class="overflow-y-auto max-h-72 p-4 space-y-3">
                @foreach($evento->usuariosQueCurtiram->reverse() as $user)
                <a href="{{ route('profile.show', $user->id) }}" class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-1 transition">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=64' }}"
                         class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
                    <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @endif
@endforeach

{{-- ════════════════════════════════════════
     ÍCONE MENSAGENS FLUTUANTE
════════════════════════════════════════ --}}
@auth
<a href="{{ route('mensagens.index') }}" class="msg-float">
    💬
    @php $msgNaoLidas = auth()->user()->unreadNotifications->where('type', 'App\\Notifications\\NovaMensagem')->count(); @endphp
    @if($msgNaoLidas > 0)
    <span class="msg-float-badge">{{ $msgNaoLidas > 99 ? '99+' : $msgNaoLidas }}</span>
    @endif
</a>
@endauth

<script>
function escapeHTML(str){
    return str.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];});
}

// ── CARROSSEL STORIES ────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('stories-track');
    if (!track) return;
    const items = Array.from(track.querySelectorAll('.story-item'));
    if (items.length > 1) {
        for (let i = 0; i < 3; i++) {
            items.forEach(item => { const c = item.cloneNode(true); track.appendChild(c); });
        }
        const itemWidth = 96;
        const totalOriginalWidth = items.length * itemWidth;
        let position = 0;
        function animate() {
            position += 0.5;
            if (position >= totalOriginalWidth) { position = 0; }
            track.style.transform = `translateX(-${position}px)`;
            requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    }
});

// ── MODAIS ───────────────────────────────────────────────
function abrirModalCurtidas(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalCurtidas(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function abrirModalComentarios(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalComentarios(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function abrirModalComentariosPost(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalComentariosPost(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function toggleResposta(id){const f=document.getElementById(id);f.classList.toggle('hidden');if(!f.classList.contains('hidden'))f.querySelector('input').focus();}
document.addEventListener('click',function(e){
    ['modal-curtidas-','modal-comentarios-','modal-comentarios-post-'].forEach(p=>{
        document.querySelectorAll(`[id^="${p}"]`).forEach(m=>{if(e.target===m){m.style.display='none';m.classList.add('hidden');}});
    });
});

// ── CURTIDA EVENTO AJAX ──────────────────────────────────
async function toggleCurtida(eventoId,btn){
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/evento/${eventoId}/curtir`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const data=await res.json();
        const texto=document.querySelector(`.curtida-texto-${eventoId}`);
        const count=document.querySelector(`.curtida-count-${eventoId}`);
        if(data.curtido){btn.classList.add('text-blue-500');texto.textContent='Curtido';}
        else{btn.classList.remove('text-blue-500');texto.textContent='Curtir';}
        count.textContent=data.total;
    }catch(e){console.error('Erro ao curtir:',e);}
}

// ── REAÇÃO POSTAGEM AJAX ─────────────────────────────────
async function toggleReacaoPost(postagemId,tipo,btn){
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/postagens/${postagemId}/reagir/${tipo}`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const data=await res.json();
        const cC=document.querySelector(`.curtida-count-post-${postagemId}`);
        const cA=document.querySelector(`.adoro-count-post-${postagemId}`);
        if(cC)cC.textContent=data.totalCurtidas||'';
        if(cA)cA.textContent=data.totalAdoros||'';
        const bC=document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'curtida', this)"]`);
        const bA=document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'adoro', this)"]`);
        if(bC)bC.classList.toggle('text-blue-500',data.tipo==='curtida'&&data.ativo);
        if(bA)bA.classList.toggle('text-red-500',data.tipo==='adoro'&&data.ativo);
        if(data.ativo){if(data.tipo==='curtida'&&bA)bA.classList.remove('text-red-500');if(data.tipo==='adoro'&&bC)bC.classList.remove('text-blue-500');}
    }catch(e){console.error('Erro ao reagir:',e);}
}

// ── COMENTÁRIO EVENTO AJAX ───────────────────────────────
async function enviarComentario(e,eventoId){
    e.preventDefault();
    const form=document.getElementById(`form-comentario-${eventoId}`);
    const input=form.querySelector('input[name="corpo"]');
    const corpo=input.value.trim();
    if(!corpo)return;
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/evento/${eventoId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const lista=document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);
        const vazio=lista.querySelector('.text-center.py-8');
        if(vazio)vazio.remove();
        const html=`<div class="flex gap-3"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p><p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;
        lista.insertAdjacentHTML('beforeend',html);
        lista.scrollTop=lista.scrollHeight;
        input.value='';
        const contExt=document.querySelector(`.comentario-count-${eventoId}`);
        if(contExt)contExt.textContent=parseInt(contExt.textContent||0)+1;
        const contModal=document.querySelector(`.contador-modal-${eventoId}`);
        if(contModal)contModal.textContent=parseInt(contModal.textContent||0)+1;
    }catch(err){console.error('Erro ao comentar:',err);}
}

// ── COMENTÁRIO POSTAGEM AJAX ─────────────────────────────
async function enviarComentarioPost(e,postagemId){
    e.preventDefault();
    const input=document.querySelector(`.input-comentario-post-${postagemId}`);
    const corpo=input.value.trim();
    if(!corpo)return;
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/postagens/${postagemId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const lista=document.querySelector(`.lista-comentarios-post-${postagemId}`);
        const vazio=lista.querySelector('.text-center.py-8');
        if(vazio)vazio.remove();
        const html=`<div class="flex gap-3"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p><p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;
        lista.insertAdjacentHTML('beforeend',html);
        lista.scrollTop=lista.scrollHeight;
        input.value='';
        const cont=document.querySelector(`.contador-comentarios-post-${postagemId}`);
        if(cont)cont.textContent=parseInt(cont.textContent||0)+1;
    }catch(err){console.error('Erro ao comentar postagem:',err);}
}

// ── PARTILHAR ────────────────────────────────────────────
async function partilhar(titulo,url){
    if(navigator.share){try{await navigator.share({title:titulo,url:url});}catch(e){}}
    else{await navigator.clipboard.writeText(url);alert('Link copiado!');}
}

// ── LIKE COMENTÁRIO ──────────────────────────────────────
async function toggleLikeComentario(comentarioId,btn){
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/comentario/${comentarioId}/like`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const count=btn.querySelector('span');
        const atual=parseInt(count.textContent||0);
        const jaGostei=btn.classList.contains('text-blue-400');
        if(jaGostei){btn.classList.remove('text-blue-400');btn.classList.add('text-gray-500');count.textContent=atual>1?atual-1:'';}
        else{btn.classList.add('text-blue-400');btn.classList.remove('text-gray-500');count.textContent=atual+1;}
    }catch(e){console.error('Erro ao curtir comentário:',e);}
}

// ── ELIMINAR COMENTÁRIO ──────────────────────────────────
async function eliminarComentario(comentarioId,btn){
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/comentario/${comentarioId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const el=document.getElementById(`comentario-${comentarioId}`)||document.getElementById(`resposta-${comentarioId}`);
        if(el)el.remove();
    }catch(e){console.error('Erro ao eliminar:',e);}
}

// ── ENVIAR RESPOSTA ──────────────────────────────────────
async function enviarResposta(eventoId,comentarioId){
    const input=document.getElementById(`input-resposta-${comentarioId}`);
    const corpo=input.value.trim();
    if(!corpo)return;
    const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
    try{
        const res=await fetch(`/evento/${eventoId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo,parent_id:comentarioId})});
        if(res.status===401){window.location.href='{{ route("login") }}';return;}
        const data=await res.json();
        const container=document.getElementById(`respostas-${comentarioId}`);
        container.classList.remove('hidden');
        const html=`<div class="flex gap-2" id="resposta-${data.comentario_id}"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background:rgba(30,41,59,0.6);border:1px solid rgba(59,130,246,0.08);"><p class="font-bold text-white text-[11px]">{{ auth()->user()?->name }}</p><p class="text-gray-300 text-xs mt-0.5">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;
        container.insertAdjacentHTML('beforeend',html);
        input.value='';
        document.getElementById(`resposta-form-${comentarioId}`).classList.add('hidden');
    }catch(e){console.error('Erro ao responder:',e);}
}
</script>
@endsection
