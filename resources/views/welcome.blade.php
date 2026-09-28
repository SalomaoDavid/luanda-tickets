@extends('layouts.app')
@section('title', 'Luanda Tickets - Rede Social de Entretenimento')
@section('content')

<style>
/* ── STORIES (imagens estáticas — sem vídeo) ── */
.story-outer{
    position:relative;width:92px;height:92px;flex-shrink:0;
    cursor:pointer;
}
.story-outer-ring{
    width:92px;height:92px;border-radius:50%;padding:3px;
    background:conic-gradient(from 0deg,#00d4ff,#818cf8,#f5a623,#00d4ff);
    animation:story-ring-spin 3.2s linear infinite;
    box-shadow:0 0 0 2px #fff, 0 4px 12px rgba(59,130,246,.4);
    transition:transform .2s;
    position:relative;
}
@keyframes story-ring-spin{to{transform:rotate(360deg);}}
.story-outer-ring:hover{transform:scale(1.07);}
.story-outer-ring-inner{width:100%;height:100%;border-radius:50%;overflow:hidden;border:2px solid #fff;background:#0f172a;position:relative;}
.story-outer-ring-inner img{width:100%;height:100%;object-fit:cover;animation:story-kenburns 8s ease-in-out infinite alternate;}
@keyframes story-kenburns{from{transform:scale(1);}to{transform:scale(1.14);}}
.story-outer-ring-ph{width:100%;height:100%;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:32px;}
.story-live-dot{position:absolute;bottom:1px;right:1px;width:13px;height:13px;border-radius:50%;background:#22c55e;border:2px solid #fff;z-index:3;animation:story-live-pulse 1.6s infinite;}
@keyframes story-live-pulse{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.6);}50%{box-shadow:0 0 0 4px rgba(34,197,94,0);}}

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

.story-nome-wrap{width:92px;overflow:hidden;margin-top:4px;}
.story-nome{font-size:10px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;text-align:center;}

/* ── CARD EVENTO — largura total, espaçamento mínimo ── */
.ev-card{background:#fff;border-radius:0;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;margin-bottom:3px;position:relative;}
.ev-card-img{position:relative;height:160px;overflow:hidden;}
.ev-card-img img{width:100%;height:100%;object-fit:cover;}

/* Banners maiores só em ecrãs grandes (PC/TV) — mobile e tablet ficam com os 160px de sempre */
@media(min-width:1280px){
    .ev-card-img{height:270px;}
}
@media(min-width:1920px){
    .ev-card-img{height:480px;}
}

/* Faixa de cor por categoria */
.ev-card.ev-cat-musica{border-top:4px solid #00d4ff;}
.ev-card.ev-cat-desporto{border-top:4px solid #22c55e;}
.ev-card.ev-cat-gastronomia{border-top:4px solid #f5a623;}
.ev-card.ev-cat-outro{border-top:4px solid #818cf8;}

/* Selos de urgência */
.ev-badge-hot{position:absolute;top:10px;left:10px;z-index:6;background:#f43f5e;color:#fff;font-size:9.5px;font-weight:800;padding:3px 10px;border-radius:20px;animation:ev-badge-pulse 1.6s ease-in-out infinite;}
.ev-badge-new{position:absolute;top:10px;left:10px;z-index:6;background:#00d4ff;color:#04213b;font-size:9.5px;font-weight:800;padding:3px 10px;border-radius:20px;}
@keyframes ev-badge-pulse{0%,100%{box-shadow:0 0 0 rgba(244,63,94,0);transform:scale(1);}50%{box-shadow:0 0 10px rgba(244,63,94,.6);transform:scale(1.05);}}

/* Contagem decrescente sobre a imagem */
.ev-countdown-badge{position:absolute;top:10px;right:10px;z-index:6;background:rgba(0,0,0,.65);backdrop-filter:blur(6px);color:#00d4ff;font-size:10.5px;font-weight:700;padding:4px 10px;border-radius:20px;}

/* Coração ao duplo-toque */
.ev-heart-burst{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:70px;opacity:0;pointer-events:none;z-index:6;}
.ev-heart-burst.burst{animation:ev-heart-anim .8s ease-out;}
@keyframes ev-heart-anim{0%{opacity:0;transform:scale(.4);}30%{opacity:1;transform:scale(1.15);}100%{opacity:0;transform:scale(1.5);}}

/* Entrada suave ao rolar (scroll-reveal) */
.scroll-reveal{opacity:0;transform:translateY(24px);transition:opacity .6s cubic-bezier(.16,1,.3,1),transform .6s cubic-bezier(.16,1,.3,1);}
.scroll-reveal.is-visible{opacity:1;transform:translateY(0);}
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
.ev-video-modal-box{position:relative;width:100%;display:flex;align-items:center;justify-content:center;}
.ev-video-modal-close{
    position:absolute;top:10px;right:10px;z-index:6;
    width:38px;height:38px;border-radius:50%;
    background:rgba(255,255,255,.15);border:none;
    color:#fff;font-size:18px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
}
.ev-video-modal iframe,
.ev-video-modal video{
    width:90vw;max-width:800px;
    height:50vw;max-height:450px;
    border-radius:16px;border:none;
}
.ev-video-loading{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:3;background:#000;border-radius:16px;}
.ev-video-spinner{width:32px;height:32px;border-radius:50%;border:3px solid rgba(0,212,255,.2);border-top-color:#00d4ff;animation:ev-spin .8s linear infinite;}
@keyframes ev-spin{to{transform:rotate(360deg);}}

/* ── Texto publicitário animado sobre o vídeo (título, local, hora, artistas) ── */
@import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap');
.ev-ad-overlay{position:absolute;inset:0;z-index:5;display:flex;align-items:center;justify-content:center;text-align:center;padding:24px;pointer-events:none;}
.ev-ad-line{font-family:'Bebas Neue',sans-serif;font-weight:400;letter-spacing:.03em;opacity:0;text-shadow:0 4px 20px rgba(0,0,0,.75),0 0 26px rgba(0,212,255,.55);white-space:nowrap;}
.ev-ad-line.big{font-size:30px;color:#ffffff;}
.ev-ad-line.small{font-size:17px;color:#00d4ff;letter-spacing:.06em;margin-top:6px;}
@keyframes ev-ad-rota1{0%{opacity:0;transform:translate(46vw,-34vh) scale(.08) rotate(14deg);}8%{opacity:1;}52%{opacity:1;transform:translate(0,0) scale(1.06) rotate(0deg);}62%{transform:translate(0,0) scale(1) rotate(0deg);}88%{opacity:1;transform:translate(0,0) scale(1) rotate(0deg);}100%{opacity:0;transform:translate(0,-6px) scale(1.1) rotate(0deg);}}
@keyframes ev-ad-rota2{0%{opacity:0;transform:translate(46vw,-34vh) scale(.08) rotate(14deg);}8%{opacity:1;}50%{opacity:1;transform:translate(0,0) scale(1.05) rotate(0deg);}100%{opacity:0;transform:translate(-46vw,34vh) scale(.1) rotate(-12deg);}}
@keyframes ev-ad-rota3{0%{opacity:0;transform:translate(-46vw,-34vh) scale(.08) rotate(-14deg);}8%{opacity:1;}52%{opacity:1;transform:translate(0,0) scale(1.06) rotate(0deg);}62%{transform:translate(0,0) scale(1) rotate(0deg);}88%{opacity:1;transform:translate(0,0) scale(1) rotate(0deg);}100%{opacity:0;transform:translate(0,-6px) scale(1.1) rotate(0deg);}}
@keyframes ev-ad-rota4{0%{opacity:0;transform:translate(-46vw,-34vh) scale(.08) rotate(-14deg);}8%{opacity:1;}50%{opacity:1;transform:translate(0,0) scale(1.05) rotate(0deg);}100%{opacity:0;transform:translate(46vw,34vh) scale(.1) rotate(12deg);}}
.ev-ad-line.active{animation-duration:4.6s;animation-timing-function:cubic-bezier(.16,.84,.44,1);animation-fill-mode:forwards;}
.ev-ad-line.small.active{animation-delay:.22s;}
.ev-ad-line.rota-1{animation-name:ev-ad-rota1;}
.ev-ad-line.rota-2{animation-name:ev-ad-rota2;}
.ev-ad-line.rota-3{animation-name:ev-ad-rota3;}
.ev-ad-line.rota-4{animation-name:ev-ad-rota4;}

/* Info compacta */
.ev-info-row{display:flex;flex-wrap:wrap;gap:6px;padding:10px 14px 4px;}
.ev-info-chip{display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#475569;background:#f1f5f9;padding:3px 8px;border-radius:20px;font-weight:500;}
.ev-info-chip.destaque{background:#dbeafe;color:#1d4ed8;font-weight:700;}
.ev-desc{padding:0 14px 8px;font-size:12px;color:#64748b;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ev-actions{display:flex;align-items:center;justify-content:space-between;padding:8px 14px 12px;border-top:1px solid #f1f5f9;}

/* Botões de ação do card (curtir/comentar/partilhar/comprar) — maiores em todos os tamanhos */
.ev-actions button, .ev-actions > a{font-size:13px!important;gap:6px!important;}
.ev-actions > a{padding:7px 14px!important;}
@media(min-width:1280px){
    .ev-actions button, .ev-actions > a{font-size:16px!important;gap:8px!important;}
    .ev-actions > a{padding:9px 18px!important;border-radius:10px;}
}
@media(min-width:1920px){
    .ev-actions button, .ev-actions > a{font-size:19px!important;gap:10px!important;}
    .ev-actions > a{padding:11px 22px!important;}
}

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
.msg-float-badge{position:absolute;top:-2px;right:-2px;min-width:18px;height:18px;border-radius:999px;background:#f43f5e;border:2px solid #fff;color:#fff;font-size:9px;font-weight:800;display:flex;align-items:center;justify-content:center;padding:0 3px;}/* Anula o padding lateral de 40px que o <main> do layout aplica (md:p-10),
   dando um espaçamento menor e controlado só a este feed — sem mexer no
   app.blade.php, que é partilhado por todas as páginas. */
@media(min-width:768px){
    .feed-tight{margin-left:-40px;margin-right:-40px;margin-top:-40px;}
}
/* Barra de círculos verdadeiramente fixa — sticky não é fiável aqui porque
   um elemento acima no layout tem overflow escondido. Fixed com coordenadas
   explícitas resolve de vez; o spacer por baixo evita que o resto do feed
   fique escondido atrás dela. */
.stories-fixed{
    position:fixed; top:64px; left:0; right:0; z-index:40;
    background:#fff; box-shadow:0 2px 8px rgba(0,0,0,.08);
    border-radius:0 0 16px 16px; overflow:hidden;
}
@media(min-width:768px){
    .stories-fixed{ left:288px; right:0; border-radius:16px; }
}
</style>

{{-- ════ STORIES (imagens estáticas dos eventos) ════ --}}
<div class="feed-tight">
<div class="stories-fixed pt-0 pb-3 shadow-sm" id="storiesFixedBox">
    <div id="stories-track" class="flex items-center" style="will-change:transform;touch-action:pan-y;cursor:grab;">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0 story-item px-2">
            <a href="{{ route('evento.detalhes', $evento->id) }}" style="position:relative;width:92px;height:92px;display:inline-block;">
                <div class="story-outer-ring">
                    <div class="story-outer-ring-inner">
                        @if($evento->imagem_capa)
                            <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}">
                        @else
                            <div class="story-outer-ring-ph">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
                        @endif
                        @if(\Carbon\Carbon::parse($evento->data_evento)->isToday())
                            <div class="story-live-dot" title="Hoje"></div>
                        @endif
                    </div>
                </div>
                <div class="story-inner">
                    @if($evento->imagem_capa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">
                    @elseif($evento->fotos && $evento->fotos->count() > 0)
                        <img src="{{ asset('storage/'.$evento->fotos->first()->caminho) }}" alt="">
                    @else
                        <span class="story-inner-ph">📷</span>
                    @endif
                </div>
            </a>
            <div class="story-nome-wrap">
                <p class="story-nome">{{ $evento->titulo }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
<div id="storiesSpacer"></div>

{{-- PUBLICAÇÃO --}}
@auth
<div class="bg-white p-3 md:p-2 rounded-2xl shadow-xl mb-1">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" id="composeTextarea" placeholder="O que estás a pensar?"
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
            <form id="form-comentario-post-{{ $post->id }}" class="flex gap-2 items-center" onsubmit="enviarComentarioPost(event, {{ $post->id }})">
                @csrf
                <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                <input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition input-comentario-post-{{ $post->id }}" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);font-size:18px;">➤</button>
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
            @auth
            <div class="px-6 py-4" style="border-top:1px solid rgba(59,130,246,0.15);">
                <form id="form-comentario-post-modal-{{ $post->id }}" class="flex gap-3 items-center" onsubmit="enviarComentarioPost(event, {{ $post->id }})">
                    @csrf
                    <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                    <input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition input-comentario-post-{{ $post->id }}" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);">
                    <button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 15px rgba(37,99,235,0.3);font-size:18px;">➤</button>
                </form>
            </div>
            @else
            <div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);"><a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a></div>
            @endauth
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

        // ── Melhorias visuais: selo "Novo", selo "A esgotar", contagem para o início ──
        $ehNovo = $evento->created_at->diffInDays(now()) <= 2;

        $totalDisp = 0; $totalGeral = 0;
        if ($evento->tiposIngresso && $evento->tiposIngresso->count() > 0) {
            $totalDisp   = $evento->tiposIngresso->sum('quantidade_disponivel') ?? 0;
            $totalGeral  = $evento->tiposIngresso->sum('quantidade_total') ?? 0;
        }
        $ehUltimos = $totalGeral > 0 && ($totalDisp / $totalGeral) <= 0.15;

        $horasParaComecar = null;
        if ($evento->hora_inicio) {
            $inicioCompleto = \Carbon\Carbon::parse($evento->data_evento->format('Y-m-d').' '.$evento->hora_inicio);
            if ($inicioCompleto->isFuture() && $inicioCompleto->diffInHours(now()) <= 24) {
                $horasParaComecar = max(1, $inicioCompleto->diffInHours(now()));
            }
        }

        // Cor por categoria (mesma linguagem visual já usada nas outras páginas)
        $corCategoria = match(strtolower(optional($evento->categoria)->nome ?? '')) {
            'música', 'musica', 'show' => 'ev-cat-musica',
            'desporto' => 'ev-cat-desporto',
            'gastronomia' => 'ev-cat-gastronomia',
            default => 'ev-cat-outro',
        };
    @endphp

    <div class="ev-card {{ $corCategoria }} scroll-reveal">
        {{-- Cabeçalho --}}
        <div class="flex items-center space-x-3 px-4 pt-3 pb-2">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"><img src="{{ $evento->user && $evento->user->avatar ? asset('storage/'.$evento->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($evento->user->name??'U').'&background=0ea5e9&color=fff&size=128' }}" class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover"></a>
            <div class="flex-1 min-w-0"><a href="{{ route('profile.show', $evento->user->id ?? '#') }}" class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">{{ $evento->user ? $evento->user->name : 'Usuário' }}</a><p class="text-xs text-gray-400">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->isFuture() ? 'Acontece '.\Carbon\Carbon::parse($evento->data_evento)->diffForHumans() : 'Aconteceu '.\Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p></div>
            @if($evento->categoria)<span class="ev-card-cat">{{ $evento->categoria->nome }}</span>@endif
        </div>

        {{-- Imagem/Vídeo --}}
        <div class="ev-card-img"
             ondblclick="curtirComCoracao({{ $evento->id }}, this)"
             @if($cardEmbedMudo || !empty($cardVideoLocal))
             onmouseenter="activarVideoCard(this)"
             onmouseleave="desactivarVideoCard(this)"
             @endif>
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" class="ev-card-thumb">
            @else
                <div class="ev-card-img-ph">{{ optional($evento->categoria)->emoji ?? '🎟' }}</div>
            @endif

            {{-- Selos de urgência — agora dentro da imagem, não sobre o cabeçalho --}}
            @if($ehUltimos)
            <div class="ev-badge-hot">🔥 A esgotar</div>
            @elseif($ehNovo)
            <div class="ev-badge-new">✨ Novo</div>
            @endif

            @if($horasParaComecar)
            <div class="ev-countdown-badge">⏰ Começa em {{ $horasParaComecar }}h</div>
            @endif

            <div class="ev-heart-burst">❤️</div>


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

    {{-- Modal vídeo fullscreen do card — agora cobre vídeo externo E local (antes só existia para externo) --}}
    @if($cardEmbedSom || !empty($cardVideoLocal))
    <div id="modal-video-card-{{ $evento->id }}" class="ev-video-modal">
        <div class="ev-video-modal-box">
            <button class="ev-video-modal-close" onclick="fecharVideoModal('modal-video-card-{{ $evento->id }}')">✕</button>
            <div class="ev-video-loading"><div class="ev-video-spinner"></div></div>
            @if($cardVideoLocal)
                <video data-local="{{ $cardVideoLocal }}" muted loop playsinline
                       style="width:90vw;max-width:800px;border-radius:16px;max-height:450px;display:none;"
                       onloadeddata="this.closest('.ev-video-modal').querySelector('.ev-video-loading').style.display='none'"></video>
            @else
                <iframe data-src="{{ $cardEmbedSom }}" src="" allow="autoplay; fullscreen" allowfullscreen
                        onload="this.closest('.ev-video-modal').querySelector('.ev-video-loading').style.display='none'"></iframe>
            @endif
            {{-- Texto publicitário animado — só começa quando o vídeo está mesmo a reproduzir --}}
            <div class="ev-ad-overlay" data-titulo="{{ e($evento->titulo) }}" data-local="{{ e(Str::limit($evento->localizacao ?? '', 30)) }}" data-hora="{{ $evento->hora_inicio ? substr($evento->hora_inicio,0,5) : '' }}" data-artistas="{{ e(Str::limit($evento->meta['artistas'] ?? '', 40)) }}"></div>
        </div>
        <a href="{{ route('evento.detalhes', $evento->id) }}" style="margin-top:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;padding:8px 20px;border-radius:12px;font-size:13px;font-weight:700;text-decoration:none;">🎟 Ver evento</a>
    </div>
    @endif

    {{-- Modais comentários e curtidas --}}
    <div id="modal-comentarios-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col" style="max-height:80vh;background:rgba(15,23,42,0.97);border:1px solid rgba(59,130,246,0.2);">
            <div class="flex justify-between items-center px-6 py-4" style="border-bottom:1px solid rgba(59,130,246,0.15);"><h3 class="font-black text-white text-sm uppercase tracking-widest">💬 Comentários (<span class="contador-modal-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>)</h3><button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')" class="text-gray-400 hover:text-white text-xl font-bold">✕</button></div>
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5" style="scrollbar-width:thin;">
                @forelse($evento->comentarios as $comentario)
                <div class="flex gap-3" id="comentario-{{ $comentario->id }}"><a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0"><img src="{{ $comentario->user->avatar ? asset('storage/'.$comentario->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($comentario->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><div class="flex justify-between items-start gap-2"><a href="{{ route('profile.show', $comentario->user->id) }}" class="font-bold text-white text-xs hover:text-blue-400 transition">{{ $comentario->user->name }}</a>@if(auth()->id() === $comentario->user_id)<button onclick="eliminarComentario({{ $comentario->id }}, this)" class="text-gray-400 hover:text-red-400 transition text-sm">🗑</button>@endif</div><p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p></div><div class="flex items-center gap-4 mt-1.5 ml-1"><span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span><button onclick="toggleLikeComentario({{ $comentario->id }}, this)" class="text-[11px] font-bold transition {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">👍 <span class="like-count-{{ $comentario->id }}">{{ $comentario->likes->count() > 0 ? $comentario->likes->count() : '' }}</span></button>@auth<button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')" class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">Responder</button>@endauth</div>@auth<div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2"><div class="flex gap-2"><img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0"><input type="text" placeholder="Responder..." id="input-resposta-{{ $comentario->id }}" class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button onclick="enviarResposta({{ $evento->id }}, {{ $comentario->id }})" class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">➤</button></div></div>@endauth
                @if($comentario->respostas && $comentario->respostas->count() > 0)<div class="mt-2 pl-4 space-y-2" id="respostas-{{ $comentario->id }}">@foreach($comentario->respostas as $resposta)<div class="flex gap-2" id="resposta-{{ $resposta->id }}"><a href="{{ route('profile.show', $resposta->user->id) }}" class="flex-shrink-0"><img src="{{ $resposta->user->avatar ? asset('storage/'.$resposta->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($resposta->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background:rgba(30,41,59,0.6);border:1px solid rgba(59,130,246,0.08);"><p class="font-bold text-white text-[11px]">{{ $resposta->user->name }}</p><p class="text-gray-300 text-xs mt-0.5">{{ $resposta->corpo }}</p></div><div class="flex items-center gap-3 mt-1 ml-1"><span class="text-gray-500 text-[10px]">{{ $resposta->created_at->diffForHumans() }}</span><button onclick="toggleLikeComentario({{ $resposta->id }}, this)" class="text-[10px] font-bold transition {{ $resposta->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">👍 <span class="like-count-{{ $resposta->id }}">{{ $resposta->likes->count() > 0 ? $resposta->likes->count() : '' }}</span></button></div></div></div>@endforeach</div>@else<div id="respostas-{{ $comentario->id }}" class="mt-2 pl-4 space-y-2 hidden"></div>@endif
                </div></div>
                @empty<div class="text-center py-8 text-gray-600"><p class="text-2xl mb-2">💬</p><p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p><p class="text-xs mt-1">Sê o primeiro a comentar!</p></div>@endforelse
            </div>
            @auth<div class="px-6 py-4" style="border-top:1px solid rgba(59,130,246,0.15);"><form id="form-comentario-{{ $evento->id }}" class="flex gap-3 items-center" onsubmit="enviarComentario(event, {{ $evento->id }})">@csrf<img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0"><input type="text" name="corpo" placeholder="Escreve um comentário..." class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button type="submit" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold hover:scale-105 flex-shrink-0" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 4px 15px rgba(37,99,235,0.3);font-size:18px;">➤</button></form></div>@else<div class="px-6 py-4 text-center" style="border-top:1px solid rgba(59,130,246,0.15);"><a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">Entra para comentar →</a></div>@endauth
        </div>
    </div>
    <div id="modal-curtidas-{{ $evento->id }}" class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/50"><div class="bg-white rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden" style="position:relative;z-index:10000;"><div class="flex justify-between items-center p-4 border-b"><h3 class="font-bold text-gray-900">👍 Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3><button onclick="fecharModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button></div><div class="overflow-y-auto max-h-72 p-4 space-y-3">@foreach($evento->usuariosQueCurtiram->reverse() as $user)<a href="{{ route('profile.show', $user->id) }}" class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-1 transition"><img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=64' }}" class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover"><span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span></a>@endforeach</div></div></div>

    @endif
@endforeach
</div>

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

// ── Ajusta o espaçador à altura real da caixa de círculos (agora fixed) ──
// Sem isto, o resto do feed ficaria escondido atrás dela.
function ajustarEspacadorStories() {
    var box = document.getElementById('storiesFixedBox');
    var spacer = document.getElementById('storiesSpacer');
    if (box && spacer) spacer.style.height = (box.offsetHeight + 4) + 'px';
}
document.addEventListener('DOMContentLoaded', ajustarEspacadorStories);
window.addEventListener('resize', ajustarEspacadorStories);

// ── CARROSSEL STORIES (imagens estáticas, desliza continuamente + arrastável) ────
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('stories-track');
    if (!track) return;
    const items = Array.from(track.querySelectorAll('.story-item'));
    if (items.length < 1) return;

    // Clona os itens para dar a ilusão de faixa infinita
    for (let i = 0; i < 3; i++) {
        items.forEach(item => { track.appendChild(item.cloneNode(true)); });
    }

    // Desliza a faixa toda continuamente — funciona com qualquer número de eventos
    const itemWidth = 108;
    const totalOriginalWidth = items.length * itemWidth;
    let position = 0;
    let speed = 0.5;
    let arrastando = false;
    let pausadoAteh = 0; // timestamp até quando o auto-deslizar fica pausado após soltar o dedo

    function normalizarPosicao() {
        // mantém a posição sempre dentro de [0, totalOriginalWidth), tanto para a frente como para trás
        position = ((position % totalOriginalWidth) + totalOriginalWidth) % totalOriginalWidth;
    }

    function animate() {
        if (!arrastando && Date.now() > pausadoAteh) {
            position += speed;
            normalizarPosicao();
            track.style.transform = `translateX(-${position}px)`;
        }
        requestAnimationFrame(animate);
    }
    requestAnimationFrame(animate);

    // Arrastar com o dedo — desliza para onde o utilizador quiser, sem parar o movimento automático depois
    let startX = 0;
    let startPosition = 0;

    track.addEventListener('touchstart', function (e) {
        arrastando = true;
        startX = e.touches[0].clientX;
        startPosition = position;
    }, { passive: true });

    track.addEventListener('touchmove', function (e) {
        if (!arrastando) return;
        const deltaX = startX - e.touches[0].clientX;
        position = startPosition + deltaX;
        normalizarPosicao();
        track.style.transform = `translateX(-${position}px)`;
    }, { passive: true });

    track.addEventListener('touchend', function () {
        arrastando = false;
        // dá um pequeno intervalo antes do deslizar automático retomar, para não "roubar" o gesto do utilizador
        pausadoAteh = Date.now() + 1200;
    });

    // Suporte a rato no desktop (arrastar com o botão premido)
    let mouseDown = false;
    track.addEventListener('mousedown', function (e) {
        mouseDown = true; arrastando = true;
        startX = e.clientX; startPosition = position;
    });
    window.addEventListener('mousemove', function (e) {
        if (!mouseDown) return;
        const deltaX = startX - e.clientX;
        position = startPosition + deltaX;
        normalizarPosicao();
        track.style.transform = `translateX(-${position}px)`;
    });
    window.addEventListener('mouseup', function () {
        if (!mouseDown) return;
        mouseDown = false; arrastando = false;
        pausadoAteh = Date.now() + 1200;
    });
});

// ── MODAL VÍDEO ──────────────────────────────────────────
function abrirVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // iframe URL externa
    const iframe = modal.querySelector('iframe');
    if (iframe && iframe.dataset.src && !iframe.src) iframe.src = iframe.dataset.src;
    // video local — só define src ao abrir (evita autoplay escondido)
    const video = modal.querySelector('video[data-local]');
    if (video && video.dataset.local) {
        video.src = video.dataset.local;
        video.style.display = 'block';
        video.muted = false;
        video.play().catch(function(){});
    }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Texto publicitário — só começa quando o vídeo está mesmo a reproduzir
    const overlay = modal.querySelector('.ev-ad-overlay');
    if (overlay && video) {
        video.addEventListener('playing', function onPlay() {
            iniciarTextoPublicitario(overlay);
        }, { once: true });
    } else if (overlay && iframe) {
        // iframes externos não disparam 'playing' de forma fiável — inicia num pequeno atraso
        setTimeout(function () { iniciarTextoPublicitario(overlay); }, 900);
    }
}
function fecharVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // Parar iframe
    const iframe = modal.querySelector('iframe');
    if (iframe) iframe.src = '';
    // Parar e limpar video local
    const video = modal.querySelector('video[data-local]');
    if (video) {
        video.pause();
        video.src = '';
        video.style.display = 'none';
    }
    // Parar o texto publicitário
    const overlay = modal.querySelector('.ev-ad-overlay');
    if (overlay) { clearTimeout(overlay._timer); overlay.innerHTML = ''; }
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('click', function(e) {
    document.querySelectorAll('.ev-video-modal.open').forEach(function(m) {
        if (e.target === m) fecharVideoModal(m.id);
    });
});

// ── Texto publicitário sobre o vídeo (título/local/hora/artistas em rotação) ──
function iniciarTextoPublicitario(overlay) {
    const frases = [];
    if (overlay.dataset.titulo) frases.push({ big: '🎉 ' + overlay.dataset.titulo, small: '' });
    if (overlay.dataset.local)  frases.push({ big: '📍 ' + overlay.dataset.local, small: '' });
    if (overlay.dataset.hora)   frases.push({ big: '🕐 Às ' + overlay.dataset.hora, small: '' });
    if (overlay.dataset.artistas) frases.push({ big: '🎤 Participação especial', small: overlay.dataset.artistas });
    if (!frases.length) return;

    let i = 0;
    function proxima() {
        const f = frases[i % frases.length];
        const rota = 'rota-' + ((i % 4) + 1);
        overlay.innerHTML = `
            <div>
                <div class="ev-ad-line big active ${rota}">${f.big}</div>
                ${f.small ? `<div class="ev-ad-line small active ${rota}">${f.small}</div>` : ''}
            </div>
        `;
        i++;
        overlay._timer = setTimeout(proxima, 4600);
    }
    proxima();
}

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
            var v = document.createElement('video');
            // SEM setAttribute('muted') para permitir unmute posterior
            v.loop   = true;
            v.playsInline = true;
            v.muted  = true;
            v.volume = 0;
            v.style.cssText = 'width:100%;height:100%;object-fit:cover;pointer-events:none;border:none;';
            v.src = localSrc;
            wrap.appendChild(v);
            v.muted  = true;
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
        if (video.muted) {
            video.removeAttribute('muted');
            video.muted  = false;
            video.volume = 1;
            btn.textContent = '🔊';
        } else {
            video.muted  = true;
            video.volume = 0;
            btn.textContent = '🔇';
        }
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
async function enviarComentario(e,eventoId){e.preventDefault();const form=document.getElementById(`form-comentario-${eventoId}`);const input=form.querySelector('input[name="corpo"]');const corpo=input.value.trim();if(!corpo)return;const btn=form.querySelector('button[type="submit"]');if(btn){btn.disabled=true;btn.style.opacity='.6';}const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/evento/${eventoId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});if(res.status===401){window.location.href='{{ route("login") }}';return;}const data=await res.json();const cid=data.comentario_id;const lista=document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);const vazio=lista.querySelector('.text-center.py-8');if(vazio)vazio.remove();const html=`<div class="flex gap-3" id="comentario-${cid}"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><div class="flex justify-between items-start gap-2"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="font-bold text-white text-xs hover:text-blue-400 transition">{{ auth()->user()?->name }}</a><button onclick="eliminarComentario(${cid}, this)" class="text-gray-400 hover:text-red-400 transition text-sm">🗑</button></div><p class="text-gray-200 text-sm mt-1 leading-relaxed">${escapeHTML(corpo)}</p></div><div class="flex items-center gap-4 mt-1.5 ml-1"><span class="text-gray-500 text-[10px]">agora mesmo</span><button onclick="toggleLikeComentario(${cid}, this)" class="text-[11px] font-bold transition text-gray-500 hover:text-blue-400">👍 <span class="like-count-${cid}"></span></button><button onclick="toggleResposta('resposta-form-${cid}')" class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">Responder</button></div><div id="resposta-form-${cid}" class="hidden mt-2"><div class="flex gap-2"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0"><input type="text" placeholder="Responder..." id="input-resposta-${cid}" class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none" style="background:rgba(30,41,59,0.8);border:1px solid rgba(59,130,246,0.2);"><button onclick="enviarResposta(${eventoId}, ${cid})" class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);font-size:16px;">➤</button></div></div><div id="respostas-${cid}" class="mt-2 pl-4 space-y-2 hidden"></div></div></div>`;lista.insertAdjacentHTML('beforeend',html);lista.scrollTop=lista.scrollHeight;input.value='';const contExt=document.querySelector(`.comentario-count-${eventoId}`);if(contExt)contExt.textContent=parseInt(contExt.textContent||0)+1;const contModal=document.querySelector(`.contador-modal-${eventoId}`);if(contModal)contModal.textContent=parseInt(contModal.textContent||0)+1;}catch(err){console.error('Erro ao comentar:',err);}finally{if(btn){btn.disabled=false;btn.style.opacity='';}}}

// ── COMENTÁRIO POSTAGEM AJAX ─────────────────────────────
async function enviarComentarioPost(e,postagemId){e.preventDefault();const form=e.target;const input=form.querySelector('input[name="corpo"]');const corpo=input.value.trim();if(!corpo)return;const btn=form.querySelector('button[type="submit"]');if(btn){btn.disabled=true;btn.style.opacity='.6';}const token=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';try{const res=await fetch(`/postagens/${postagemId}/comentar`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'X-Requested-With':'XMLHttpRequest','Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({corpo})});if(res.status===401){window.location.href='{{ route("login") }}';return;}const lista=document.querySelector(`.lista-comentarios-post-${postagemId}`);const vazio=lista.querySelector('.text-center.py-8');if(vazio)vazio.remove();const html=`<div class="flex gap-3"><a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0"><img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-400"></a><div class="flex-1"><div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background:rgba(30,41,59,0.9);border:1px solid rgba(59,130,246,0.1);"><p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p><p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p></div><span class="text-gray-500 text-[10px] ml-1">agora mesmo</span></div></div>`;lista.insertAdjacentHTML('beforeend',html);lista.scrollTop=lista.scrollHeight;document.querySelectorAll(`.input-comentario-post-${postagemId}`).forEach(el=>el.value='');const cont=document.querySelector(`.contador-comentarios-post-${postagemId}`);if(cont)cont.textContent=parseInt(cont.textContent||0)+1;const contModal=document.querySelector(`.contador-modal-post-${postagemId}`);if(contModal)contModal.textContent=parseInt(contModal.textContent||0)+1;}catch(err){console.error('Erro ao comentar postagem:',err);}finally{if(btn){btn.disabled=false;btn.style.opacity='';}}}

async function partilhar(titulo,url){if(navigator.share){try{await navigator.share({title:titulo,url:url});}catch(e){}}else{await navigator.clipboard.writeText(url);swalToast.fire({icon:'success',title:'Link copiado!'});}}
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

// ── Duplo-toque na imagem para curtir (só curte, nunca descurte, como o Instagram) ──
function curtirComCoracao(eventoId, imgWrap) {
    const heart = imgWrap.querySelector('.ev-heart-burst');
    if (heart) { heart.classList.remove('burst'); void heart.offsetWidth; heart.classList.add('burst'); }

    const card = imgWrap.closest('.ev-card');
    const likeBtn = card ? card.querySelector('[onclick*="toggleCurtida(' + eventoId + ',"]') : null;
    if (likeBtn && !likeBtn.classList.contains('text-blue-500')) {
        toggleCurtida(eventoId, likeBtn);
    }
}

// ── Entrada suave dos cards ao rolar (scroll-reveal) ──
document.addEventListener('DOMContentLoaded', function () {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.scroll-reveal').forEach(el => el.classList.add('is-visible'));
        return;
    }
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.scroll-reveal').forEach(function (el) { revealObserver.observe(el); });
});

// ── Placeholder rotativo na caixa de publicar ──
(function () {
    var textarea = document.getElementById('composeTextarea');
    if (!textarea) return;
    var frases = ['O que estás a pensar?', 'Partilha o teu evento favorito...', 'Alguma novidade para contar?'];
    var i = 0;
    setInterval(function () {
        if (document.activeElement === textarea || textarea.value) return;
        i = (i + 1) % frases.length;
        textarea.placeholder = frases[i];
    }, 3500);
})();
</script>
@endsection