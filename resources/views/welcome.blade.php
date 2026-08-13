@extends('layouts.app')
@section('title', 'Luanda Tickets - Rede Social de Entretenimento')
@section('content')

<style>
/* ── STORIES DUPLOS ── */
.story-outer{
    position:relative;width:92px;height:92px;flex-shrink:0;
    cursor:pointer;
}
.story-outer-ring{
    width:92px;height:92px;border-radius:50%;
    border:3px solid #3b82f6;
    overflow:hidden;
    box-shadow:0 0 0 2px #fff, 0 4px 12px rgba(59,130,246,.4);
    transition:transform .2s,box-shadow .2s;
    position:relative;background:#000;
}
.story-outer-ring:hover{transform:scale(1.07);box-shadow:0 0 0 2px #fff, 0 6px 20px rgba(59,130,246,.6);}
.story-outer-ring img{width:100%;height:100%;object-fit:cover;}
.story-outer-ring iframe{
    position:absolute;
    width:300%;height:300%;
    top:50%;left:50%;
    transform:translate(-50%,-50%);
    border:none;pointer-events:none;
}
.story-outer-ring-ph{width:100%;height:100%;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:32px;}

/* Circulo pequeno (foto) dentro do grande */
.story-inner{
    position:absolute;bottom:-4px;right:-4px;
    width:30px;height:30px;border-radius:50%;
    border:2px solid #fff;
    overflow:hidden;
    background:linear-gradient(135deg,#0ea5e9,#6366f1);
    box-shadow:0 2px 8px rgba(0,0,0,.3);
    display:flex;align-items:center;justify-content:center;
    z-index:2;
}
.story-inner img{width:100%;height:100%;object-fit:cover;}
.story-inner-ph{font-size:12px;}

/* Badge de video */
.story-video-badge{
    position:absolute;top:-2px;left:-2px;
    width:20px;height:20px;border-radius:50%;
    background:#f43f5e;border:1.5px solid #fff;
    display:flex;align-items:center;justify-content:center;
    font-size:8px;color:#fff;font-weight:900;
    z-index:2;
}
.story-nome-wrap{width:92px;overflow:hidden;margin-top:4px;}
.story-nome{font-size:10px;color:#ffffff;white-space:nowrap;display:inline-block;animation:marquee-story 6s linear infinite;}
@keyframes marquee-story{0%{transform:translateX(100%)}100%{transform:translateX(-100%)}}

/* ── CARD EVENTO ── */
.ev-card{background:#fff;border-radius:20px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;margin-bottom:16px;}
.ev-card-img{position:relative;height:160px;overflow:hidden;}
.ev-card-img img{width:100%;height:100%;object-fit:cover;}
.ev-card-img-ph{width:100%;height:100%;background:linear-gradient(135deg,#1e40af,#4f46e5);display:flex;align-items:center;justify-content:center;font-size:40px;}
.ev-card-img-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.75) 0%,transparent 55%);}
.ev-card-title-wrap{position:absolute;bottom:0;left:0;right:0;padding:10px 14px;}
.ev-card-title{color:#fff;font-size:15px;font-weight:800;line-height:1.2;margin-bottom:3px;}
.ev-card-cat{display:inline-flex;align-items:center;gap:4px;background:rgba(59,130,246,.85);color:#fff;font-size:9px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;padding:2px 8px;border-radius:20px;}

/* Vídeo no card — iframe overlay */
.ev-card-video-wrap{
    position:absolute;inset:0;
    opacity:0;transition:opacity .3s;
    background:#000;z-index:1;
}
.ev-card-img:hover .ev-card-video-wrap{opacity:1;}
.ev-card-video-wrap iframe,
.ev-card-video-wrap video{
    width:100%;height:100%;border:none;object-fit:cover;
}

/* Botão som no card */
.ev-sound-btn{
    position:absolute;bottom:8px;right:8px;z-index:3;
    width:30px;height:30px;border-radius:50%;
    background:rgba(0,0,0,.6);backdrop-filter:blur(6px);
    border:1px solid rgba(255,255,255,.3);
    color:#fff;font-size:13px;
    display:none;align-items:center;justify-content:center;
    cursor:pointer;transition:all .2s;
}
.ev-card-img:hover .ev-sound-btn{display:flex;}
.ev-sound-btn:hover{background:rgba(59,130,246,.7);border-color:#3b82f6;}

/* Círculo de vídeo no card */
.ev-video-circle{
    position:absolute;top:8px;right:8px;z-index:3;
    width:36px;height:36px;border-radius:50%;
    border:2px solid rgba(255,255,255,.8);
    overflow:hidden;cursor:pointer;
    background:#000;
    box-shadow:0 2px 8px rgba(0,0,0,.4);
    transition:transform .2s;
}
.ev-video-circle:hover{transform:scale(1.1);}
.ev-video-circle img{width:100%;height:100%;object-fit:cover;}
.ev-video-circle-play{
    position:absolute;inset:0;background:rgba(0,0,0,.4);
    display:flex;align-items:center;justify-content:center;
    font-size:12px;color:#fff;
}

/* Modal vídeo fullscreen no card */
.ev-video-modal{
    display:none;position:fixed;inset:0;z-index:99999;
    background:rgba(0,0,0,.95);
    align-items:center;justify-content:center;
    flex-direction:column;
}
.ev-video-modal.open{display:flex;}
.ev-video-modal-close{
    position:absolute;top:16px;right:16px;
    width:40px;height:40px;border-radius:50%;
    background:rgba(255,255,255,.15);border:none;
    color:#fff;font-size:20px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
}
.ev-video-modal iframe{
    width:90vw;max-width:800px;
    height:50vw;max-height:450px;
    border-radius:16px;border:none;
}

/* Info compacta */
.ev-info-row{display:flex;flex-wrap:wrap;gap:6px;padding:10px 14px 4px;}
.ev-info-chip{display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#475569;background:#f1f5f9;padding:3px 8px;border-radius:20px;font-weight:500;}
.ev-info-chip.destaque{background:#dbeafe;color:#1d4ed8;font-weight:700;}
.ev-desc{padding:0 14px 8px;font-size:12px;color:#64748b;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ev-actions{display:flex;align-items:center;justify-content:space-between;padding:8px 14px 12px;border-top:1px solid #f1f5f9;}

/* ── POPULARES ── */
.populares-wrap{position:relative;border-radius:20px;overflow:hidden;background:linear-gradient(135deg,#0f172a,#1e1b4b,#0f172a);padding:16px;margin-bottom:16px;}
.populares-wrap::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(99,102,241,.25),transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(6,182,212,.2),transparent 50%);pointer-events:none;}
.pop-title{font-size:11px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;margin-bottom:12px;position:relative;background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shimmer-text 3s linear infinite;background-size:200% auto;}
@keyframes shimmer-text{to{background-position:200% center}}
.pop-title::after{content:'';display:block;height:2px;border-radius:999px;margin-top:6px;background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6);animation:shimmer-text 3s linear infinite;background-size:200% auto;}
.pop-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:12px;margin-bottom:6px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);text-decoration:none;transition:all .2s;position:relative;overflow:hidden;}
.pop-item:hover{background:rgba(255,255,255,.1);transform:translateX(4px);}
.pop-item::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,#38bdf8,#818cf8);border-radius:0 2px 2px 0;}
.pop-rank{font-size:11px;font-weight:900;color:#64748b;width:16px;flex-shrink:0;}
.pop-rank.top{background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.pop-img{width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.1);}
.pop-img-ph{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.pop-nome{font-size:12px;font-weight:700;color:#f0f6ff;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.pop-info{font-size:10px;color:#64748b;}
.pop-badge{font-size:9px;font-weight:800;padding:2px 7px;border-radius:20px;flex-shrink:0;background:linear-gradient(135deg,#0ea5e9,#6366f1);color:#fff;}

/* ── MENSAGENS FLUTUANTE ── */
.msg-float{position:fixed;bottom:80px;right:18px;z-index:400;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#7c3aed);box-shadow:0 4px 20px rgba(37,99,235,.5);display:flex;align-items:center;justify-content:center;font-size:22px;cursor:pointer;text-decoration:none;animation:float-bounce 2s ease-in-out infinite;transition:transform .2s;}
.msg-float:hover{transform:scale(1.12);}
@keyframes float-bounce{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
.msg-float-badge{position:absolute;top:-2px;right:-2px;min-width:18px;height:18px;border-radius:999px;background:#f43f5e;border:2px solid #fff;color:#fff;font-size:9px;font-weight:800;display:flex;align-items:center;justify-content:center;padding:0 3px;}
</style>

{{-- ════ STORIES ════ --}}
<div class="sticky top-0 z-10 bg-white/90 backdrop-blur-md rounded-2xl py-3 mb-4 overflow-hidden shadow-sm">
    <div id="stories-track" class="flex items-center" style="will-change:transform;">
        @foreach($eventos as $evento)
        @php
            $embedUrl   = null;
            $videoLocal = null;
            $vUrl       = $evento->video_preview ?? null;
            if ($vUrl) {
                if (str_starts_with($vUrl, 'http')) {
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $vUrl, $m)) {
                        $embedUrl = "https://www.youtube.com/embed/{$m[1]}?autoplay=1&mute=1&loop=1&playlist={$m[1]}&controls=0&playsinline=1&modestbranding=1&rel=0";
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrl, $m)) {
                        $embedUrl = "https://player.vimeo.com/video/{$m[1]}?autoplay=1&muted=1&loop=1&controls=0";
                    }
                } else {
                    $videoLocal = asset('storage/'.$vUrl);
                }
            }
        @endphp
        <div class="text-center flex-shrink-0 story-item px-2">
            {{-- Círculo: se tem vídeo abre modal, senão vai para o evento --}}
            @if($embedUrl)
            <div style="position:relative;width:92px;height:92px;display:inline-block;cursor:pointer;"
                 onclick="abrirVideoModal('modal-video-{{ $evento->id }}')">
            @else
            <a href="{{ route('evento.detalhes', $evento->id) }}"
               style="position:relative;width:92px;height:92px;display:inline-block;">
            @endif
                {{-- Círculo: thumbnail como placeholder, iframe injectado pelo JS (máx 3 activos) --}}
                @php
                    $storyThumb = null;
                    if ($embedUrl && preg_match('/embed\/([^?]+)/', $embedUrl, $tid)) {
                        $storyThumb = "https://img.youtube.com/vi/{$tid[1]}/mqdefault.jpg";
                    }
                @endphp
                <div class="story-outer-ring story-video-ring"
                     data-embed="{{ $embedUrl ?? '' }}"
                     data-local="{{ $videoLocal ?? '' }}"
                     style="position:relative;overflow:hidden;">
                    {{-- Vídeo local: tag <video> nativa --}}
                    @if(!empty($videoLocal))
                        <video src="{{ $videoLocal }}" autoplay muted loop playsinline
                               onloadedmetadata="this.muted=true;this.volume=0;"
                               style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:1;pointer-events:none;"></video>
                    @else
                    {{-- Thumbnail: mostrada enquanto iframe não carrega --}}
                    @if($storyThumb)
                        <img class="story-thumb" src="{{ $storyThumb }}" alt="{{ e($evento->titulo) }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;z-index:1;">
                    @elseif($evento->imagem_capa)
                        <img class="story-thumb" src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;z-index:1;">
                    @else
                        <div class="story-outer-ring-ph story-thumb" style="position:absolute;inset:0;z-index:1;">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
                    @endif
                    {{-- Ícone ▶ pulsante (só para URL externa) --}}
                    @if($embedUrl)
                    <div class="story-play-icon" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:2;background:rgba(0,0,0,.2);pointer-events:none;">
                        <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.9);display:flex;align-items:center;justify-content:center;font-size:11px;color:#000;animation:play-pulse 1.8s ease-in-out infinite;">▶</div>
                    </div>
                    @endif
                    {{-- Slot para iframe injectado pelo JS --}}
                    <div class="story-iframe-slot" style="position:absolute;inset:0;z-index:3;display:none;"></div>
                    @endif
                    {{-- Overlay para bloquear interacção --}}
                    <div style="position:absolute;inset:0;z-index:4;background:transparent;"></div>
                </div>
                <div class="story-video-badge">{{ ($embedUrl || !empty($videoLocal)) ? '▶' : '🎟' }}</div>
                <div class="story-inner">
                    @if($evento->imagem_capa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">
                    @elseif($evento->fotos && $evento->fotos->count() > 0)
                        <img src="{{ asset('storage/'.$evento->fotos->first()->caminho) }}" alt="">
                    @else
                        <span class="story-inner-ph">📷</span>
                    @endif
                </div>
            @if($embedUrl)
            </div>
            @else
            </a>
            @endif
            {{-- Nome sempre leva para o evento --}}
            <a href="{{ route('evento.detalhes', $evento->id) }}">
                <div class="story-nome-wrap">
                    <p class="story-nome">{{ $evento->titulo }}</p>
                </div>
            </a>
        </div>

        @endforeach
    </div>
</div>

{{-- Modais de vídeo dos stories — FORA do stories-track para não serem cortados --}}
@foreach($eventos as $evento)
@php
    $embedUrlModal  = null;
    $videoLocalModal = null;
    $vUrlM = $evento->video_preview ?? null;
    if ($vUrlM) {
        if (str_starts_with($vUrlM, 'http')) {
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $vUrlM, $mm)) {
                $embedUrlModal = "https://www.youtube.com/embed/{$mm[1]}?autoplay=1&mute=0&loop=1&playlist={$mm[1]}&controls=1&playsinline=1";
            } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrlM, $mm)) {
                $embedUrlModal = "https://player.vimeo.com/video/{$mm[1]}?autoplay=1&muted=0&loop=1";
            }
        } else {
            $videoLocalModal = asset('storage/'.$vUrlM);
        }
    }
@endphp
@if($embedUrlModal || $videoLocalModal)
<div id="modal-video-{{ $evento->id }}" class="ev-video-modal">
    <button class="ev-video-modal-close" onclick="fecharVideoModal('modal-video-{{ $evento->id }}')">✕</button>
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:10px;text-align:center;">{{ e($evento->titulo) }}</div>
    @if($videoLocalModal)
        <video src="{{ $videoLocalModal }}" autoplay controls loop playsinline
               style="width:90vw;max-width:800px;border-radius:16px;max-height:450px;"></video>
    @else
        <iframe data-src="{{ $embedUrlModal }}" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
    @endif
    <a href="{{ route('evento.detalhes', $evento->id) }}" style="margin-top:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;padding:8px 20px;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;">🎟 Ver evento</a>
</div>
@endif
@endforeach

{{-- PUBLICAÇÃO --}}
@auth
<div class="bg-white p-3 md:p-4 rounded-2xl shadow-xl mb-4">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-100 text-gray-800 p-3 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                  rows="2"></textarea>
        <div class="flex justify-between items-center mt-2">
            <div class="text-gray-400 text-xs">📷 Foto | 🎥 Vídeo | 🎟 Evento</div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-1.5 rounded-xl font-semibold hover:scale-105 transition text-sm">Publicar</button>
        </div>
    </form>
</div>
@endauth

{{-- FEED --}}
@foreach($feed as $entry)

    @if($entry['tipo'] === 'post')
    @php $post = $entry['item']; $minhaReacaoPost = auth()->check() ? $post->reacoes->where('user_id', auth()->id())->first() : null; $euCurtiPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'curtida'; $euAdoroPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'adoro'; @endphp
    <div class="bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">
        <div class="flex items-center space-x-3 px-4 pt-4 pb-2">
            <a href="{{ route('profile.show', $post->user->id) }}"><img src="{{ $post->user->avatar ? asset('storage/'.$post->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($post->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover"></a>
            <div class="flex-1"><a href="{{ route('profile.show', $post->user->id) }}" class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">{{ $post->user->name }}</a><p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p></div>
        </div>
        <div class="px-4 pb-3"><p class="text-gray-700 text-sm leading-relaxed">{{ $post->conteudo }}</p></div>
        <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100 text-gray-500 text-xs">
            <button onclick="toggleReacaoPost({{ $post->id }}, 'adoro', this)" class="flex items-center gap-1 hover:text-red-500 transition font-medium {{ $euAdoroPost ? 'text-red-500' : '' }}">❤️ <span class="adoro-count-post-{{ $post->id }}">{{ $post->reacoes->where('tipo','adoro')->count() ?: '' }}</span></button>
            <button onclick="toggleReacaoPost({{ $post->id }}, 'curtida', this)" class="flex items-center gap-1 hover:text-blue-500 transition font-medium {{ $euCurtiPost ? 'text-blue-500' : '' }}">👍 <span class="curtida-count-post-{{ $post->id }}">{{ $post->reacoes->where('tipo','curtida')->count() ?: '' }}</span></button>
            <button onclick="abrirModalComentariosPost('modal-comentarios-post-{{ $post->id }}')" class="flex items-center gap-1 hover:text-blue-500 transition font-medium">💬 <span class="contador-comentarios-post-{{ $post->id }}">{{ $post->comentarios->count() }}</span></button>
            <button onclick="partilhar('{{ addslashes($post->conteudo) }}', window.location.href)" class="flex items-center gap-1 hover:text-green-500 transition font-medium">🔗 Partilhar</button>
        </div>
        @auth
        <div class="px-4 pb-3 pt-1">
            <form class="flex gap-2 items-center" onsubmit="enviarComentarioPost(event, {{ $post->id }})">
                @csrf
                <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                <input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition input-comentario-post-{{ $post->id }}" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button>
            </form>
        </div>
        @else
        <div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);"><a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a></div>
        @endauth
    </div>
    <div id="modal-comentarios-post-{{ $post->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col" style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
            <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);"><h3 class="font-black text-white text-sm uppercase tracking-widest">💬 Comentários (<span class="contador-modal-post-{{ $post->id }}">{{ $post->comentarios->count() }}</span>)</h3><button onclick="fecharModalComentariosPost('modal-comentarios-post-{{ $post->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button></div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 lista-comentarios-post-{{ $post->id }}" style="scrollbar-width:thin;">
                @forelse($post->comentarios as $com)
                <div class="flex gap-3"><a href="{{ route('profile.show', $com->user->id) }}" class="flex-shrink-0"><img src="{{ $com->user->avatar ? asset('storage/'.$com->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($com->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ $com->user->name }}</p><p class="text-gray-200 text-sm mt-1">{{ $com->corpo }}</p></div><span class="text-gray-500 text-[10px] ml-1">{{ $com->created_at->diffForHumans() }}</span></div></div>
                @empty
                <div class="text-center py-8 text-gray-600"><p class="text-2xl mb-2">💬</p><p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p></div>
                @endforelse
            </div>
        </div>
    </div>

    @else
    {{-- ════ CARD EVENTO ════ --}}
    @php
        $evento = $entry['item'];
        $euCurtiEvento = $evento->usuariosQueCurtiram->contains(auth()->id());
        // Embed URL para o card — suporte a URL externa e ficheiro local
        $cardEmbedMudo  = null;
        $cardEmbedSom   = null;
        $cardVideoLocal = null;
        $ym = []; $vm = [];
        $vUrl = $evento->video_preview ?? null;
        if ($vUrl) {
            if (str_starts_with($vUrl, 'http')) {
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([^&\s?]+)/', $vUrl, $ym)) {
                    $cardEmbedMudo = "https://www.youtube.com/embed/{$ym[1]}?autoplay=1&mute=1&loop=1&playlist={$ym[1]}&controls=0&playsinline=1";
                    $cardEmbedSom  = "https://www.youtube.com/embed/{$ym[1]}?autoplay=1&mute=0&loop=1&playlist={$ym[1]}&controls=1&playsinline=1";
                } elseif (preg_match('/vimeo\.com\/(\d+)/', $vUrl, $vm)) {
                    $cardEmbedMudo = "https://player.vimeo.com/video/{$vm[1]}?autoplay=1&muted=1&loop=1&controls=0";
                    $cardEmbedSom  = "https://player.vimeo.com/video/{$vm[1]}?autoplay=1&muted=0&loop=1";
                }
            } else {
                $cardVideoLocal = asset('storage/'.$vUrl);
            }
        }
    @endphp

    <div class="ev-card">
        {{-- Cabeçalho --}}
        <div class="flex items-center space-x-3 px-4 pt-3 pb-2">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"><img src="{{ $evento->user && $evento->user->avatar ? asset('storage/'.$evento->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($evento->user->name??'U').'&background=0ea5e9&color=fff&size=128' }}" class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover"></a>
            <div class="flex-1 min-w-0"><a href="{{ route('profile.show', $evento->user->id ?? '#') }}" class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">{{ $evento->user ? $evento->user->name : 'Usuário' }}</a><p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p></div>
            @if($evento->categoria)<span class="ev-card-cat">{{ $evento->categoria->nome }}</span>@endif
        </div>

        {{-- Imagem/Vídeo --}}
        <div class="ev-card-img"
             @if($cardEmbedMudo || !empty($cardVideoLocal))
             onmouseenter="activarVideoCard(this)"
             onmouseleave="desactivarVideoCard(this)"
             @endif>
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" class="ev-card-thumb">
            @else
                <div class="ev-card-img-ph">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
            @endif


            {{-- Overlay de vídeo (hover para URL externa, automático para local) --}}
            @if($cardEmbedMudo)
            <div class="ev-card-video-wrap"
                 data-mudo="{{ $cardEmbedMudo }}"
                 data-som="{{ $cardEmbedSom }}">
            </div>
            <button class="ev-sound-btn" onclick="toggleSomCard(this)" title="Som">🔇</button>
            <div class="ev-video-circle" onclick="abrirVideoModal('modal-video-card-{{ $evento->id }}')" title="Ver vídeo">
                @if($evento->imagem_capa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">@endif
                <div class="ev-video-circle-play">▶</div>
            </div>
            @elseif(!empty($cardVideoLocal))
            {{-- Vídeo local dentro do wrap: CSS hover controla visibilidade --}}
            <div class="ev-card-video-wrap" data-local="{{ $cardVideoLocal }}">
                {{-- Sem autoplay: JS inicia o play() garantindo muted --}}
            </div>
            <button class="ev-sound-btn" onclick="toggleSomCard(this)" title="Som">🔇</button>
            <div class="ev-video-circle" onclick="abrirVideoModal('modal-video-card-{{ $evento->id }}')" title="Ver vídeo">
                @if($evento->imagem_capa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">@endif
                <div class="ev-video-circle-play">▶</div>
            </div>
            @endif

            <div class="ev-card-img-overlay"></div>
            <div class="ev-card-title-wrap"><div class="ev-card-title">{{ e($evento->titulo) }}</div></div>
        </div>

        {{-- Info --}}
        <div class="ev-info-row">
            <span class="ev-info-chip">📍 {{ Str::limit($evento->localizacao ?? 'Local n/d', 22) }}</span>
            <span class="ev-info-chip destaque">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
            @if($evento->hora_inicio)<span class="ev-info-chip destaque">🕐 {{ substr($evento->hora_inicio, 0, 5) }}</span>@endif
            @if($evento->provincia)<span class="ev-info-chip">🗺 {{ $evento->provincia }}</span>@endif
            @if($evento->lotacao_maxima)<span class="ev-info-chip">👥 {{ number_format($evento->lotacao_maxima) }}</span>@endif
            @if($evento->tiposIngresso && $evento->tiposIngresso->count() > 0)
                @php $precoMin = $evento->tiposIngresso->min('preco'); @endphp
                <span class="ev-info-chip destaque">🎟 @if($precoMin == 0) Grátis @else {{ number_format($precoMin, 0, ',', '.') }} Kz @endif</span>
            @endif
            @if($evento->online)<span class="ev-info-chip" style="background:#d1fae5;color:#065f46;">🌐 Online</span>@endif
        </div>
        <p class="ev-desc">{{ $evento->descricao }}</p>
        <div class="ev-actions">
            <button onclick="toggleCurtida({{ $evento->id }}, this)" class="flex items-center gap-1 text-xs hover:text-blue-500 transition font-medium {{ $euCurtiEvento ? 'text-blue-500' : 'text-gray-500' }}">👍 <span class="curtida-texto-{{ $evento->id }}">{{ $euCurtiEvento ? 'Curtido' : 'Curtir' }}</span> (<span class="curtida-count-{{ $evento->id }}">{{ $evento->curtidas->count() }}</span>)</button>
            <button onclick="abrirModalComentarios('modal-comentarios-{{ $evento->id }}')" class="flex items-center gap-1 text-xs text-gray-500 hover:text-blue-500 transition font-medium">💬 <span class="comentario-count-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span></button>
            <button onclick="partilhar('{{ addslashes(e($evento->titulo)) }}', '{{ route('evento.detalhes', $evento->id) }}')" class="flex items-center gap-1 text-xs text-gray-500 hover:text-green-500 transition font-medium">🔗 Partilhar</button>
            <a href="{{ route('evento.detalhes', $evento->id) }}" class="bg-blue-500 text-white px-3 py-1.5 rounded-lg font-semibold hover:scale-105 transition text-xs">🎟 Comprar</a>
        </div>
        @if($evento->usuariosQueCurtiram->count() > 0)
        @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
        <button onclick="abrirModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="flex items-center px-4 pb-3">
            <div class="flex" style="direction:rtl;">@foreach($curtidores->take(5) as $cu)<img src="{{ $cu->avatar ? asset('storage/'.$cu->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($cu->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-6 h-6 rounded-full border-2 border-white object-cover -mr-2" style="direction:ltr;" title="{{ $cu->name }}">@endforeach</div>
            @if($evento->usuariosQueCurtiram->count() > 5)<span class="text-xs text-gray-400 ml-4">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>@endif
        </button>
        @endif
    </div>

    {{-- Modal vídeo fullscreen do card --}}
    @if($cardEmbedSom)
    <div id="modal-video-card-{{ $evento->id }}" class="ev-video-modal">
        <button class="ev-video-modal-close" onclick="fecharVideoModal('modal-video-card-{{ $evento->id }}')">✕</button>
        <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:10px;text-align:center;">{{ e($evento->titulo) }}</div>
        <iframe data-src="{{ $cardEmbedSom }}" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
        <a href="{{ route('evento.detalhes', $evento->id) }}" style="margin-top:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;padding:8px 20px;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;">🎟 Ver evento</a>
    </div>
    @endif

    {{-- Modais comentários e curtidas --}}
    <div id="modal-comentarios-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col" style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
            <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);"><h3 class="font-black text-white text-sm uppercase tracking-widest">💬 Comentários (<span class="contador-modal-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>)</h3><button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button></div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5" style="scrollbar-width:thin;">
                @forelse($evento->comentarios as $comentario)
                <div class="flex gap-3" id="comentario-{{ $comentario->id }}"><a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0"><img src="{{ $comentario->user->avatar ? asset('storage/'.$comentario->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($comentario->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><div class="flex justify-between items-start gap-2"><a href="{{ route('profile.show', $comentario->user->id) }}" class="font-bold text-white text-xs hover:text-blue-400 transition">{{ $comentario->user->name }}</a>@if(auth()->id() === $comentario->user_id)<button onclick="eliminarComentario({{ $comentario->id }}, this)" class="text-gray-600 hover:text-red-400 transition text-xs">🗑</button>@endif</div><p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p></div><div class="flex items-center gap-4 mt-1.5 ml-1"><span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span><button onclick="toggleLikeComentario({{ $comentario->id }}, this)" class="text-[11px] font-bold transition {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">👍 <span class="like-count-{{ $comentario->id }}">{{ $comentario->likes->count() > 0 ? $comentario->likes->count() : '' }}</span></button>@auth<button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')" class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">Responder</button>@endauth</div>@auth<div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2"><div class="flex gap-2"><img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0"><input type="text" placeholder="Responder..." id="input-resposta-{{ $comentario->id }}" class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button onclick="enviarResposta({{ $evento->id }}, {{ $comentario->id }})" class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button></div></div>@endauth
                @if($comentario->respostas && $comentario->respostas->count() > 0)<div class="mt-2 pl-4 space-y-2" id="respostas-{{ $comentario->id }}">@foreach($comentario->respostas as $resposta)<div class="flex gap-2" id="resposta-{{ $resposta->id }}"><a href="{{ route('profile.show', $resposta->user->id) }}" class="flex-shrink-0"><img src="{{ $resposta->user->avatar ? asset('storage/'.$resposta->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($resposta->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background:rgba(30,41,59,0.6);border:1px solid rgba(59,130,246,0.08);"><p class="font-bold text-white text-[11px]">{{ $resposta->user->name }}</p><p class="text-gray-300 text-xs mt-0.5">{{ $resposta->corpo }}</p></div><div class="flex items-center gap-3 mt-1 ml-1"><span class="text-gray-500 text-[10px]">{{ $resposta->created_at->diffForHumans() }}</span><button onclick="toggleLikeComentario({{ $resposta->id }}, this)" class="text-[10px] font-bold transition {{ $resposta->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">👍 <span class="like-count-{{ $resposta->id }}">{{ $resposta->likes->count() > 0 ? $resposta->likes->count() : '' }}</span></button></div></div></div>@endforeach</div>@else<div id="respostas-{{ $comentario->id }}" class="mt-2 pl-4 space-y-2 hidden"></div>@endif
                </div></div>
                @empty<div class="text-center py-8 text-gray-600"><p class="text-2xl mb-2">💬</p><p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p><p class="text-xs mt-1">Sê o primeiro a comentar!</p></div>@endforelse
            </div>
            @auth<div class="px-6 py-4" style="border-top:1px solid rgba(59,130,246,0.15);"><form id="form-comentario-{{ $evento->id }}" class="flex gap-3 items-center" onsubmit="enviarComentario(event, {{ $evento->id }})">@csrf<img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0"><input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 15px rgba(37,99,235,0.3);">➤</button></form></div>@else<div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);"><a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a></div>@endauth
        </div>
    </div>
    <div id="modal-curtidas-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/50"><div class="bg-white rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden" style="position:relative;z-index:10000;"><div class="flex justify-between items-center p-4 border-b"><h3 class="font-bold text-gray-900">👍 Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3><button onclick="fecharModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button></div><div class="overflow-y-auto max-h-72 p-4 space-y-3">@foreach($evento->usuariosQueCurtiram->reverse() as $user)<a href="{{ route('profile.show', $user->id) }}" class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-1 transition"><img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover"><span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span></a>@endforeach</div></div></div>

    @endif
@endforeach

{{-- MENSAGENS FLUTUANTE --}}
@auth
<a href="{{ route('mensagens.index') }}" class="msg-float">
    💬
    @php $msgNaoLidas = auth()->user()->unreadNotifications->where('type', 'App\\Notifications\\NovaMensagem')->count(); @endphp
    @if($msgNaoLidas > 0)<span class="msg-float-badge">{{ $msgNaoLidas > 99 ? '99+' : $msgNaoLidas }}</span>@endif
</a>
@endauth

<script>
function escapeHTML(str){return str.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m];});}

// ── CARROSSEL STORIES com vídeos lazy (máx 3 iframes activos) ────
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('stories-track');
    if (!track) return;
    const items = Array.from(track.querySelectorAll('.story-item'));
    if (items.length < 1) return;

    // 1. Criar clones ANTES de injectar iframes (clones ficam com thumbnails)
    for (let i = 0; i < 3; i++) {
        items.forEach(item => { track.appendChild(item.cloneNode(true)); });
    }

    // 2. Injectar iframes — mobile: máx 4, desktop: todos
    const isMobile = window.innerWidth < 768;
    const MAX_VIDEOS = isMobile ? 4 : items.length;
    let videoCount = 0;
    items.forEach(function(item) {
        if (videoCount >= MAX_VIDEOS) return;
        const ring  = item.querySelector('.story-video-ring');
        const slot  = item.querySelector('.story-iframe-slot');
        const embed = ring ? ring.dataset.embed : '';
        if (!embed || !slot) return;

        // Mobile: delay escalonado para não bloquear o render
        // Desktop: carrega tudo mas com pequeno delay entre cada um
        const delay = isMobile ? videoCount * 600 : videoCount * 200;
        setTimeout(function() {
            const iframe = document.createElement('iframe');
            iframe.src = embed + '&enablejsapi=1';
            iframe.setAttribute('allow', 'autoplay');
            iframe.setAttribute('frameborder', '0');
            iframe.style.cssText = 'width:300%;height:300%;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);border:none;pointer-events:none;';
            slot.appendChild(iframe);
            slot.style.display = 'block';
            const playIcon = item.querySelector('.story-play-icon');
            if (playIcon) playIcon.style.display = 'none';
        }, delay);

        videoCount++;
    });

    // 3. Animação do carrossel
    const itemWidth = 108;
    const totalOriginalWidth = items.length * itemWidth;
    let position = 0;
    let speed = 0.5;
    function animate() {
        position += speed;
        if (position >= totalOriginalWidth) position = 0;
        track.style.transform = `translateX(-${position}px)`;
        requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);
});

// ── MODAL VÍDEO ──────────────────────────────────────────
function abrirVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const iframe = modal.querySelector('iframe');
    if (iframe && iframe.dataset.src && !iframe.src) iframe.src = iframe.dataset.src;
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fecharVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const iframe = modal.querySelector('iframe');
    if (iframe) iframe.src = '';
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('click', function(e) {
    document.querySelectorAll('.ev-video-modal.open').forEach(function(m) {
        if (e.target === m) fecharVideoModal(m.id);
    });
});

// ── VÍDEO NO CARD (hover) ─────────────────────────────────
var _hoverTimer = null;
function activarVideoCard(cardImg) {
    clearTimeout(_hoverTimer);
    _hoverTimer = setTimeout(function() {
        var wrap = cardImg.querySelector('.ev-card-video-wrap');
        if (!wrap || wrap.querySelector('iframe') || wrap.querySelector('video')) return;

        var localSrc = wrap.dataset.local;
        var mudo     = wrap.dataset.mudo;

        if (localSrc) {
            // Vídeo local: criar elemento com muted ANTES de qualquer play
            var v = document.createElement('video');
            v.setAttribute('muted', '');   // atributo HTML (mais fiável)
            v.setAttribute('loop', '');
            v.setAttribute('playsinline', '');
            v.muted  = true;               // propriedade IDL
            v.volume = 0;
            v.style.cssText = 'width:100%;height:100%;object-fit:cover;pointer-events:none;border:none;';
            v.src = localSrc;             // src só depois de muted
            wrap.appendChild(v);
            v.muted  = true;              // re-garantir após append
            v.volume = 0;
            v.play().catch(function(){});
        } else if (mudo) {
            var urlComApi = mudo.includes('enablejsapi') ? mudo : mudo + '&enablejsapi=1';
            var iframe = document.createElement('iframe');
            iframe.src = urlComApi;
            iframe.setAttribute('allow', 'autoplay');
            iframe.setAttribute('frameborder', '0');
            iframe.style.cssText = 'width:100%;height:100%;border:none;';
            iframe.dataset.mudo   = urlComApi;
            iframe.dataset.som    = wrap.dataset.som;
            iframe.dataset.isMudo = '1';
            wrap.appendChild(iframe);
        }
    }, 300);
}
function desactivarVideoCard(cardImg) {
    clearTimeout(_hoverTimer);
    var wrap = cardImg.querySelector('.ev-card-video-wrap');
    if (!wrap) return;
    var iframe = wrap.querySelector('iframe');
    var video  = wrap.querySelector('video');
    if (iframe) { iframe.src = ''; iframe.remove(); }
    if (video)  { video.pause(); video.muted = true; video.volume = 0; video.src = ''; video.remove(); }
    var btn = cardImg.querySelector('.ev-sound-btn');
    if (btn) btn.textContent = '🔇';
}

// ── TOGGLE SOM LOCAL (tag <video>) ───────────────────────
function toggleSomLocal(btn) {
    const cardImg = btn.closest('.ev-card-img');
    if (!cardImg) return;
    const video = cardImg.querySelector('video');
    if (!video) return;
    video.muted = !video.muted;
    btn.textContent = video.muted ? '🔇' : '🔊';
}

function abrirVideoLocalModal(src, modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const video = modal.querySelector('video');
    if (video) { video.src = src; video.play(); }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

// ── TOGGLE SOM (postMessage — sem reiniciar o vídeo) ──────
var videoComSomActivo = null;

function toggleSomCard(btn) {
    const cardImg = btn.closest('.ev-card-img');
    if (!cardImg) return;
    const wrap = cardImg.querySelector('.ev-card-video-wrap');
    if (!wrap) return;

    // ── Vídeo local (dentro do wrap) ──
    const video = wrap.querySelector('video');
    if (video) {
        video.muted  = !video.muted;
        video.volume = video.muted ? 0 : 1;
        btn.textContent = video.muted ? '🔇' : '🔊';
        return;
    }

    // ── iframe URL externa ──
    const iframe = wrap.querySelector('iframe');
    if (!iframe) return;
    const isMudo = iframe.dataset.isMudo === '1';

    if (!isMudo && videoComSomActivo && videoComSomActivo !== iframe) {
        videoComSomActivo.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'mute', args:[]}), '*'
        );
        videoComSomActivo.dataset.isMudo = '1';
        const outroCard = videoComSomActivo.closest('.ev-card-img');
        if (outroCard) {
            const outroBtn = outroCard.querySelector('.ev-sound-btn');
            if (outroBtn) outroBtn.textContent = '🔇';
        }
    }

    if (isMudo) {
        iframe.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'unMute', args:[]}), '*'
        );
        iframe.dataset.isMudo = '0';
        btn.textContent = '🔊';
        videoComSomActivo = iframe;
    } else {
        iframe.contentWindow.postMessage(
            JSON.stringify({event:'command', func:'mute', args:[]}), '*'
        );
        iframe.dataset.isMudo = '1';
        btn.textContent = '🔇';
        videoComSomActivo = null;
    }
}

// ── MODAIS ───────────────────────────────────────────────
function abrirModalCurtidas(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalCurtidas(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function abrirModalComentarios(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalComentarios(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function abrirModalComentariosPost(id){const m=document.getElementById(id);m.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';m.classList.remove('hidden');document.body.appendChild(m);}
function fecharModalComentariosPost(id){const m=document.getElementById(id);m.style.display='none';m.classList.add('hidden');}
function toggleResposta(id){const f=document.getElementById(id);f.classList.toggle('hidden');if(!f.classList.contains('hidden'))f.querySelector('input').focus();}
document.addEventListener('click',function(e){['modal-curtidas-','modal-comentarios-','modal-comentarios-post-'].forEach(p=>{document.querySelectorAll(`[id^="${p}"]`).forEach(m=>{if(e.target===m){m.style.display='none';m.classList.add('hidden');}});});});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.ev-video-modal.open').forEach(function(m) { fecharVideoModal(m.id); });
    }
});

// ── CURTIDA EVENTO AJAX ──────────────────────────────────
async function toggleCurtida(eventoId,btn){const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/evento/${eventoId}/curtir`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});if(res.status===401){window.location.href='{{ route("login") }}';return;}const data=await res.json();const texto=document.querySelector(`.curtida-texto-${eventoId}`);const count=document.querySelector(`.curtida-count-${eventoId}`);if(data.curtido){btn.classList.add('text-blue-500');texto.textContent='Curtido';}else{btn.classList.remove('text-blue-500');texto.textContent='Curtir';}count.textContent=data.total;}catch(e){console.error('Erro ao curtir:',e);}}

// ── REAÇÃO POSTAGEM AJAX ─────────────────────────────────
async function toggleReacaoPost(postagemId,tipo,btn){const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/postagens/${postagemId}/reagir/${tipo}`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});if(res.status===401){window.location.href='{{ route("login") }}';return;}const data=await res.json();const cC=document.querySelector(`.curtida-count-post-${postagemId}`);const cA=document.querySelector(`.adoro-count-post-${postagemId}`);if(cC)cC.textContent=data.totalCurtidas||'';if(cA)cA.textContent=data.totalAdoros||'';const bC=document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'curtida', this)"]`);const bA=document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'adoro', this)"]`);if(bC)bC.classList.toggle('text-blue-500',data.tipo==='curtida'&&data.ativo);if(bA)bA.classList.toggle('text-red-500',data.tipo==='adoro'&&data.ativo);}catch(e){console.error('Erro ao reagir:',e);}}

// ── COMENTÁRIO EVENTO AJAX ───────────────────────────────
async function enviarComentario(e,eventoId){e.preventDefault();const form=document.getElementById(`form-comentario-${eventoId}`);const input=form.querySelector('input[name="corpo"]');const corpo=input.value.trim();if(!corpo)return;const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/evento/${eventoId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});if(res.status===401){window.location.href='{{ route("login") }}';return;}const lista=document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);const vazio=lista.querySelector('.text-center.py-8');if(vazio)vazio.remove();const html=`<div class="flex gap-3"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p><p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;lista.insertAdjacentHTML('beforeend',html);lista.scrollTop=lista.scrollHeight;input.value='';const contExt=document.querySelector(`.comentario-count-${eventoId}`);if(contExt)contExt.textContent=parseInt(contExt.textContent||0)+1;const contModal=document.querySelector(`.contador-modal-${eventoId}`);if(contModal)contModal.textContent=parseInt(contModal.textContent||0)+1;}catch(err){console.error('Erro ao comentar:',err);}}

// ── COMENTÁRIO POSTAGEM AJAX ─────────────────────────────
async function enviarComentarioPost(e,postagemId){e.preventDefault();const input=document.querySelector(`.input-comentario-post-${postagemId}`);const corpo=input.value.trim();if(!corpo)return;const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/postagens/${postagemId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});if(res.status===401){window.location.href='{{ route("login") }}';return;}const lista=document.querySelector(`.lista-comentarios-post-${postagemId}`);const vazio=lista.querySelector('.text-center.py-8');if(vazio)vazio.remove();const html=`<div class="flex gap-3"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p><p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;lista.insertAdjacentHTML('beforeend',html);lista.scrollTop=lista.scrollHeight;input.value='';const cont=document.querySelector(`.contador-comentarios-post-${postagemId}`);if(cont)cont.textContent=parseInt(cont.textContent||0)+1;}catch(err){console.error('Erro ao comentar postagem:',err);}}

async function partilhar(titulo,url){if(navigator.share){try{await navigator.share({title:titulo,url:url});}catch(e){}}else{await navigator.clipboard.writeText(url);alert('Link copiado!');}}
async function toggleLikeComentario(comentarioId,btn){const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/comentario/${comentarioId}/like`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});if(res.status===401){window.location.href='{{ route("login") }}';return;}const count=btn.querySelector('span');const atual=parseInt(count.textContent||0);const jaGostei=btn.classList.contains('text-blue-400');if(jaGostei){btn.classList.remove('text-blue-400');btn.classList.add('text-gray-500');count.textContent=atual>1?atual-1:'';}else{btn.classList.add('text-blue-400');btn.classList.remove('text-gray-500');count.textContent=atual+1;}}catch(e){console.error('Erro ao curtir comentário:',e);}}
async function eliminarComentario(comentarioId,btn){const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/comentario/${comentarioId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});if(res.status===401){window.location.href='{{ route("login") }}';return;}const el=document.getElementById(`comentario-${comentarioId}`)||document.getElementById(`resposta-${comentarioId}`);if(el)el.remove();}catch(e){console.error('Erro ao eliminar:',e);}}
async function enviarResposta(eventoId,comentarioId){const input=document.getElementById(`input-resposta-${comentarioId}`);const corpo=input.value.trim();if(!corpo)return;const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/evento/${eventoId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo,parent_id:comentarioId})});if(res.status===401){window.location.href='{{ route("login") }}';return;}const data=await res.json();const container=document.getElementById(`respostas-${comentarioId}`);container.classList.remove('hidden');const html=`<div class="flex gap-2" id="resposta-${data.comentario_id}"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background:rgba(30,41,59,0.6);border:1px solid rgba(59,130,246,0.08);"><p class="font-bold text-white text-[11px]">{{ auth()->user()?->name }}</p><p class="text-gray-300 text-xs mt-0.5">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;container.insertAdjacentHTML('beforeend',html);input.value='';document.getElementById(`resposta-form-${comentarioId}`).classList.add('hidden');}catch(e){console.error('Erro ao responder:',e);}}

// ── INTERSECTION OBSERVER — pausar vídeos fora do viewport ─
document.addEventListener('DOMContentLoaded', function () {
    if (!('IntersectionObserver' in window)) return;

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) return;
            var cardImg = entry.target.querySelector('.ev-card-img');
            if (!cardImg) return;
            // Vídeo local pré-renderizado
            var wrap2 = cardImg.querySelector('.ev-card-video-wrap');
            var vl = wrap2 ? wrap2.querySelector('video') : null;
            if (vl) { vl.pause(); vl.muted = true; vl.volume = 0; }
            // iframe URL externa
            var wrap   = cardImg.querySelector('.ev-card-video-wrap');
            var iframe = wrap ? wrap.querySelector('iframe') : null;
            if (iframe) { iframe.src = ''; iframe.remove(); }
            var somBtn = entry.target.querySelector('.ev-sound-btn');
            if (somBtn) somBtn.textContent = '🔇';
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.ev-card').forEach(function (card) {
        observer.observe(card);
    });
});
</script>
@endsection