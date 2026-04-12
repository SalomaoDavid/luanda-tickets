@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')

<!-- Stories / Eventos em Carrossel Infinito -->
<div class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm rounded-2xl py-3 mb-4 overflow-hidden">
    <div id="stories-track" class="flex" style="will-change: transform;">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0 story-item px-3">
            <a href="{{ route('evento.detalhes', $evento->id) }}">
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-full border-4 border-blue-400 overflow-hidden shadow-lg hover:border-blue-600 hover:scale-105 transition">
                    <img src="{{ $evento->imagem_capa ? asset('storage/' . $evento->imagem_capa) : 'https://picsum.photos/200' }}"
                         class="w-full h-full object-cover">
                </div>
                <p class="mt-1 text-xs md:text-sm text-gray-800 truncate w-16 md:w-20 hover:text-blue-500 transition">{{ $evento->titulo }}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Espaço de publicação -->
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

<!-- Feed unificado -->
@foreach($feed as $entry)

    @if($entry['tipo'] === 'post')
    {{-- ═══════════════════════════════════════
         CARD DE POSTAGEM
    ═══════════════════════════════════════ --}}
    @php
        $post = $entry['item'];
        $minhaReacaoPost = auth()->check() ? $post->reacoes->where('user_id', auth()->id())->first() : null;
        $euCurtiPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'curtida';
        $euAdoroPost = $minhaReacaoPost && $minhaReacaoPost->tipo === 'adoro';
    @endphp
    <div class="bg-white rounded-2xl shadow-lg mb-3 overflow-hidden">

        {{-- Cabeçalho --}}
        <div class="flex items-center space-x-3 px-4 pt-4 pb-2">
            <a href="{{ route('profile.show', $post->user->id) }}">
                <img src="{{ $post->user->avatar
                    ? asset('storage/' . $post->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div class="flex-1">
                <a href="{{ route('profile.show', $post->user->id) }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $post->user->name }}
                </a>
                <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="px-4 pb-3">
            <p class="text-gray-700 text-sm leading-relaxed">{{ $post->conteudo }}</p>
        </div>

        {{-- Ações --}}
        <div class="flex items-center justify-between px-4 py-2 border-t border-gray-100 text-gray-500 text-xs">

            {{-- Adoro --}}
            <button onclick="toggleReacaoPost({{ $post->id }}, 'adoro', this)"
                    class="flex items-center gap-1 transition font-medium {{ $euAdoroPost ? 'text-red-500' : 'hover:text-red-500' }}">
                ❤️ <span class="adoro-count-post-{{ $post->id }}">{{ $post->adoros()->count() ?: '' }}</span>
            </button>

            {{-- Curtir --}}
            <button onclick="toggleReacaoPost({{ $post->id }}, 'curtida', this)"
                    class="flex items-center gap-1 transition font-medium {{ $euCurtiPost ? 'text-blue-500' : 'hover:text-blue-500' }}">
                👍 <span class="curtida-count-post-{{ $post->id }}">{{ $post->curtidas()->count() ?: '' }}</span>
            </button>

            {{-- Comentar --}}
            <button onclick="abrirModalComentariosPost('modal-comentarios-post-{{ $post->id }}')"
                    class="flex items-center gap-1 hover:text-blue-500 transition font-medium">
                💬 {{ $post->comentarios->count() ?: '' }}
            </button>

            {{-- Partilhar --}}
            <button onclick="partilhar('{{ addslashes($post->user->name) }}', '{{ url()->current() }}')"
                    class="flex items-center gap-1 hover:text-green-500 transition font-medium">
                🔗 Partilhar
            </button>
        </div>


            {{-- Pilha de fotos quem curtiu/adorou --}}
        @php
            $curtidoresPost = $post->reacoes->where('tipo', 'curtida')->take(3);
            $adoradoresPost = $post->reacoes->where('tipo', 'adoro')->take(3);
            $totalCurtidasPost = $post->curtidas()->count();
            $totalAdorosPost = $post->adoros()->count();
        @endphp

        <div class="flex items-center gap-4 px-4 pb-3 text-xs text-gray-500">
            {{-- Pilha curtidas --}}
            @if($totalCurtidasPost > 0)
            <div class="flex items-center gap-1">
                <div class="flex" style="direction: rtl;">
                    @foreach($curtidoresPost as $reacao)
                    <img src="{{ $reacao->user->avatar_url }}"
                        class="w-6 h-6 rounded-full border-2 border-white object-cover -mr-2"
                        style="direction: ltr;" title="{{ $reacao->user->name }}">
                    @endforeach
                </div>
                @if($totalCurtidasPost > 3)
                    <span class="ml-3 text-gray-400">+{{ $totalCurtidasPost - 3 }} pessoas</span>
                @endif
            </div>
            @endif

            {{-- Pilha adoros --}}
            @if($totalAdorosPost > 0)
            <div class="flex items-center gap-1">
                <div class="flex" style="direction: rtl;">
                    @foreach($adoradoresPost as $reacao)
                    <img src="{{ $reacao->user->avatar_url }}"
                        class="w-6 h-6 rounded-full border-2 border-white object-cover -mr-2"
                        style="direction: ltr;" title="{{ $reacao->user->name }}">
                    @endforeach
                </div>
                @if($totalAdorosPost > 3)
                    <span class="ml-3 text-gray-400">+{{ $totalAdorosPost - 3 }} pessoas</span>
                @endif
            </div>
            @endif
        </div>

    </div>

    {{-- MODAL COMENTÁRIOS POSTAGEM --}}
    <div id="modal-comentarios-post-{{ $post->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center"
         style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
        <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
             style="max-height: 80vh; background: rgba(15,23,42,0.97); border: 1px solid rgba(59,130,246,0.2);">

            <div class="flex justify-between items-center px-6 py-4"
                 style="border-bottom: 1px solid rgba(59,130,246,0.15);">
                <h3 class="font-black text-white text-sm uppercase tracking-widest">
                    💬 Comentários (<span class="contador-comentarios-post-{{ $post->id }}">{{ $post->comentarios->count() }}</span>)
                </h3>
                <button onclick="fecharModalComentariosPost('modal-comentarios-post-{{ $post->id }}')"
                        class="text-gray-400 hover:text-white text-xl font-bold transition">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5 lista-comentarios-post-{{ $post->id }}"
                 style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">
                @forelse($post->comentarios as $comentario)
                <div class="flex gap-3">
                    <a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0">
                        <img src="{{ $comentario->user->avatar
                            ? asset('storage/' . $comentario->user->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($comentario->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                             class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                    </a>
                    <div class="flex-1">
                        <div class="rounded-2xl rounded-tl-sm px-4 py-3"
                             style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                            <a href="{{ route('profile.show', $comentario->user->id) }}"
                               class="font-bold text-white text-xs hover:text-blue-400 transition">
                                {{ $comentario->user->name }}
                            </a>
                            <p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p>
                        </div>
                        <span class="text-gray-500 text-[10px] ml-1">{{ $comentario->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-600">
                    <p class="text-2xl mb-2">💬</p>
                    <p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p>
                </div>
                @endforelse
            </div>

            @auth
            <div class="px-6 py-4" style="border-top: 1px solid rgba(59,130,246,0.15);">
                <form class="flex gap-3 items-center"
                      onsubmit="enviarComentarioPost(event, {{ $post->id }})">
                    @csrf
                    <img src="{{ auth()->user()->avatar
                        ? asset('storage/' . auth()->user()->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                    <input type="text" name="corpo" placeholder="Escreve um comentário..."
                           class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition input-comentario-post-{{ $post->id }}"
                           style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                    <button type="submit"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0"
                            style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        ➤
                    </button>
                </form>
            </div>
            @else
            <div class="px-6 py-4 text-center" style="border-top: 1px solid rgba(59,130,246,0.15);">
                <a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">
                    Entra para comentar →
                </a>
            </div>
            @endauth
        </div>
    </div>

    @else
    {{-- ═══════════════════════════════════════
         CARD DE EVENTO
    ═══════════════════════════════════════ --}}
    @php
        $evento = $entry['item'];
        $euCurtiEvento = $evento->usuariosQueCurtiram->contains(auth()->id());
    @endphp
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">

        {{-- Cabeçalho do criador --}}
        <div class="flex items-center space-x-3 px-4 pt-3 pb-2">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}">
                <img src="{{ $evento->user && $evento->user->avatar
                    ? asset('storage/' . $evento->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($evento->user->name ?? 'U') . '&background=0ea5e9&color=fff&size=128' }}"
                     class="w-9 h-9 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div class="flex-1 min-w-0">
                <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $evento->user ? $evento->user->name : 'Usuário' }}
                </a>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Imagem compacta --}}
        <div class="relative">
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-full h-40 md:h-48 object-cover">
            @else
                <div class="w-full h-40 md:h-48 bg-gradient-to-br from-blue-600 to-blue-900 flex items-center justify-center">
                    <span class="text-white text-4xl">🎟</span>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-4 py-3">
                <h2 class="text-white text-base font-bold drop-shadow truncate">{{ $evento->titulo }}</h2>
            </div>
        </div>

        {{-- Infos compactas --}}
        <div class="px-4 py-2">
            <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-gray-500 mb-2">
                <span>📍 {{ Str::limit($evento->localizacao ?? 'Local não informado', 25) }}</span>
                <span>📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
                <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
            </div>

            <p class="text-gray-600 text-xs leading-relaxed line-clamp-1 mb-2">{{ $evento->descricao }}</p>

            {{-- Ações --}}
            <div class="flex items-center justify-between border-t pt-2 text-xs text-gray-500">

                {{-- Curtir --}}
                <button onclick="toggleCurtida({{ $evento->id }}, this)"
                        class="flex items-center gap-1 hover:text-blue-500 transition font-medium {{ $euCurtiEvento ? 'text-blue-500' : '' }}">
                    👍 <span class="curtida-texto-{{ $evento->id }}">{{ $euCurtiEvento ? 'Curtido' : 'Curtir' }}</span>
                    (<span class="curtida-count-{{ $evento->id }}">{{ $evento->curtidas->count() }}</span>)
                </button>

                {{-- Comentar --}}
                <button onclick="abrirModalComentarios('modal-comentarios-{{ $evento->id }}')"
                        class="flex items-center gap-1 hover:text-blue-500 transition font-medium">
                    💬 <span class="comentario-count-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>
                </button>

                {{-- Partilhar --}}
                <button onclick="partilhar('{{ addslashes($evento->titulo) }}', '{{ route('evento.detalhes', $evento->id) }}')"
                        class="flex items-center gap-1 hover:text-green-500 transition font-medium">
                    🔗 Partilhar
                </button>

                {{-- Comprar --}}
                <a href="{{ route('evento.detalhes', $evento->id) }}"
                   class="bg-blue-500 text-white px-3 py-1 rounded-lg font-semibold hover:scale-105 transition">
                    🎟 Comprar
                </a>
            </div>

            {{-- Avatares de quem curtiu --}}
            @if($evento->usuariosQueCurtiram->count() > 0)
            @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
            <button onclick="abrirModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="flex items-center mt-2">
                <div class="flex" style="direction: rtl;">
                    @foreach($curtidores->take(5) as $user)
                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-6 h-6 rounded-full border-2 border-white object-cover -mr-2" style="direction: ltr;" title="{{ $user->name }}">
                    @endforeach
                </div>
                @if($evento->usuariosQueCurtiram->count() > 5)
                    <span class="text-xs text-gray-400 ml-4">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>
                @endif
            </button>
            @endif
        </div>
    </div>

   {{-- ═══════════════════════════════════════
     MODAL COMENTÁRIOS EVENTO
═══════════════════════════════════════ --}}
<div id="modal-comentarios-{{ $evento->id }}"
     class="hidden fixed inset-0 z-[9999] items-center justify-center"
     style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
    <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
         style="max-height: 80vh; background: rgba(15,23,42,0.97); border: 1px solid rgba(59,130,246,0.2);">

        <div class="flex justify-between items-center px-6 py-4"
             style="border-bottom: 1px solid rgba(59,130,246,0.15);">
            <h3 class="font-black text-white text-sm uppercase tracking-widest">
                💬 Comentários (<span class="contador-modal-{{ $evento->id }}">{{ $evento->comentarios->count() }}</span>)
            </h3>
            <button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')"
                    class="text-gray-400 hover:text-white text-xl font-bold transition">✕</button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5"
             style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

            @forelse($evento->comentarios as $comentario)
            <div class="flex gap-3" id="comentario-{{ $comentario->id }}">
                <a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0">
                    <img src="{{ $comentario->user->avatar
                        ? asset('storage/' . $comentario->user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($comentario->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                </a>
                <div class="flex-1">
                    <div class="rounded-2xl rounded-tl-sm px-4 py-3"
                         style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                        <div class="flex justify-between items-start gap-2">
                            <a href="{{ route('profile.show', $comentario->user->id) }}"
                               class="font-bold text-white text-xs hover:text-blue-400 transition">
                                {{ $comentario->user->name }}
                            </a>
                            @if(auth()->id() === $comentario->user_id)
                            <button onclick="eliminarComentario({{ $comentario->id }}, this)"
                                    class="text-gray-600 hover:text-red-400 transition text-xs">🗑</button>
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
                        <button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')"
                                class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">
                            Responder
                        </button>
                        @endauth
                    </div>

                    @auth
                    <div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2">
                        <div class="flex gap-2">
                            <img src="{{ auth()->user()->avatar
                                ? asset('storage/' . auth()->user()->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                 class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0">
                            <input type="text" placeholder="Escreve uma resposta..."
                                   id="input-resposta-{{ $comentario->id }}"
                                   class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none"
                                   style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                            <button onclick="enviarResposta({{ $evento->id }}, {{ $comentario->id }})"
                                    class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition"
                                    style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                ➤
                            </button>
                        </div>
                    </div>
                    @endauth

                    <div id="respostas-{{ $comentario->id }}" class="mt-3 space-y-3 pl-2 {{ $comentario->respostas->count() > 0 ? '' : 'hidden' }}"
                         style="border-left: 2px solid rgba(59,130,246,0.2);">
                        @foreach($comentario->respostas as $resposta)
                        <div class="flex gap-2" id="resposta-{{ $resposta->id }}">
                            <a href="{{ route('profile.show', $resposta->user->id) }}" class="flex-shrink-0">
                                <img src="{{ $resposta->user->avatar
                                    ? asset('storage/' . $resposta->user->avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($resposta->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                     class="w-7 h-7 rounded-full object-cover border border-blue-400">
                            </a>
                            <div class="flex-1">
                                <div class="rounded-2xl rounded-tl-sm px-3 py-2"
                                     style="background: rgba(30,41,59,0.6); border: 1px solid rgba(59,130,246,0.08);">
                                    <div class="flex justify-between items-start">
                                        <a href="{{ route('profile.show', $resposta->user->id) }}"
                                           class="font-bold text-white text-[11px] hover:text-blue-400 transition">
                                            {{ $resposta->user->name }}
                                        </a>
                                        @if(auth()->id() === $resposta->user_id)
                                        <button onclick="eliminarComentario({{ $resposta->id }}, this)"
                                                class="text-gray-600 hover:text-red-400 transition text-[10px]">🗑</button>
                                        @endif
                                    </div>
                                    <p class="text-gray-300 text-xs mt-0.5 leading-relaxed">{{ $resposta->corpo }}</p>
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
        <div class="px-6 py-4" style="border-top: 1px solid rgba(59,130,246,0.15);">
            <form id="form-comentario-{{ $evento->id }}" class="flex gap-3 items-center"
                  onsubmit="enviarComentario(event, {{ $evento->id }})">
                @csrf
                <img src="{{ auth()->user()->avatar
                    ? asset('storage/' . auth()->user()->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                <input type="text" name="corpo" placeholder="Escreve um comentário..."
                       class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition"
                       style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                <button type="submit"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0"
                        style="background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 15px rgba(37,99,235,0.3);">
                    ➤
                </button>
            </form>
        </div>
        @else
        <div class="px-6 py-4 text-center" style="border-top: 1px solid rgba(59,130,246,0.15);">
            <a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">
                Entra para comentar →
            </a>
        </div>
        @endauth
    </div>
</div>

    {{-- Modal curtidas evento --}}
    <div id="modal-curtidas-{{ $evento->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/50"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;">
        <div class="bg-white rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden"
             style="position: relative; z-index: 10000;">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="font-bold text-gray-900">👍 Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3>
                <button onclick="fecharModalCurtidas('modal-curtidas-{{ $evento->id }}')"
                        class="text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
            </div>
            <div class="overflow-y-auto max-h-72 p-4 space-y-3">
                @foreach($evento->usuariosQueCurtiram->reverse() as $user)
                <a href="{{ route('profile.show', $user->id) }}"
                   class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-1 transition">
                    <img src="{{ $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
                    <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @endif
@endforeach

<script>
    function escapeHTML(str) {
    return str.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
        }[m];
    });
}
    // ── Carrossel ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('stories-track');
        const items = Array.from(track.querySelectorAll('.story-item'));

        if (items.length > 1) {
            for (let i = 0; i < 3; i++) {
                items.forEach(item => {
                    const clone = item.cloneNode(true);
                    track.appendChild(clone);
                });
            }

            const itemWidth = 88;
            const totalOriginalWidth = items.length * itemWidth;
            let position = 0;

            function animate() {
                position += 0.5;
                if (position >= totalOriginalWidth) {
                    position = 0;
                    track.style.transition = 'none';
                    track.style.transform = `translateX(0px)`;
                } else {
                    track.style.transition = 'none';
                    track.style.transform = `translateX(-${position}px)`;
                }
                requestAnimationFrame(animate);
            }
            requestAnimationFrame(animate);
        }
    });

    // ── Modal Curtidas Evento ────────────────────────────────
    function abrirModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0'; modal.style.left = '0';
        modal.style.width = '100vw'; modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }
    function fecharModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Modal Comentários Evento ─────────────────────────────
    function abrirModalComentarios(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0'; modal.style.left = '0';
        modal.style.width = '100vw'; modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }
    function fecharModalComentarios(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Modal Comentários Postagem ───────────────────────────
    function abrirModalComentariosPost(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0'; modal.style.left = '0';
        modal.style.width = '100vw'; modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }
    function fecharModalComentariosPost(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Toggle form de resposta ──────────────────────────────
    function toggleResposta(id) {
        const form = document.getElementById(id);
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.querySelector('input').focus();
        }
    }

    // ── Fechar modais ao clicar fora ─────────────────────────
    document.addEventListener('click', function(e) {
        ['modal-curtidas-', 'modal-comentarios-', 'modal-comentarios-post-'].forEach(prefix => {
            document.querySelectorAll(`[id^="${prefix}"]`).forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            });
        });
    });

    // ── Toggle Curtida Evento AJAX ───────────────────────────
    async function toggleCurtida(eventoId, btn) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        try {
           const res = await fetch(`/evento/${eventoId}/curtir`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }
            const data = await res.json();
            const texto = document.querySelector(`.curtida-texto-${eventoId}`);
            const count = document.querySelector(`.curtida-count-${eventoId}`);
            if (data.curtido) {
                btn.classList.add('text-blue-500');
                texto.textContent = 'Curtido';
            } else {
                btn.classList.remove('text-blue-500');
                texto.textContent = 'Curtir';
            }
            count.textContent = data.total;
        } catch (e) { console.error('Erro ao curtir:', e); }
    }

    // ── Toggle Reação Postagem AJAX ──────────────────────────
    async function toggleReacaoPost(postagemId, tipo, btn) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        try {
            const res = await fetch(`/postagens/${postagemId}/reagir/${tipo}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }
            const data = await res.json();

            const curtidaCount = document.querySelector(`.curtida-count-post-${postagemId}`);
            const adoroCount   = document.querySelector(`.adoro-count-post-${postagemId}`);
            if (curtidaCount) curtidaCount.textContent = data.totalCurtidas || '';
            if (adoroCount)   adoroCount.textContent   = data.totalAdoros   || '';

            const btnCurtida = document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'curtida', this)"]`);
            const btnAdoro   = document.querySelector(`[onclick="toggleReacaoPost(${postagemId}, 'adoro', this)"]`);

            if (btnCurtida) btnCurtida.classList.toggle('text-blue-500', data.tipo === 'curtida' && data.ativo);
            if (btnAdoro)   btnAdoro.classList.toggle('text-red-500',   data.tipo === 'adoro'   && data.ativo);

            if (data.ativo) {
                if (data.tipo === 'curtida' && btnAdoro)   btnAdoro.classList.remove('text-red-500');
                if (data.tipo === 'adoro'   && btnCurtida) btnCurtida.classList.remove('text-blue-500');
            }
        } catch (e) { console.error('Erro ao reagir:', e); }
    }

    // ── Enviar Comentário Evento AJAX ────────────────────────
    async function enviarComentario(e, eventoId) {
        e.preventDefault();
        const form = document.getElementById(`form-comentario-${eventoId}`);
        const input = form.querySelector('input[name="corpo"]');
        const corpo = input.value.trim();
        if (!corpo) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        try {
            const res = await fetch(`/evento/${eventoId}/comentar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ corpo })
            });
            if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }

            const lista = document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);
            const vazio = lista.querySelector('.text-center.py-8');
            if (vazio) vazio.remove();

            const html = `
            <div class="flex gap-3">
                <a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0">
                    <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}"
                        class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                </a>
                <div class="flex-1">
                    <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                        <p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p>
                        <p class="text-gray-200 text-sm mt-1">${corpo}</p>
                    </div>
                    <span class="text-gray-500 text-[10px] ml-1">agora mesmo</span>
                </div>
            </div>`;

            lista.insertAdjacentHTML('beforeend', html);
            lista.scrollTop = lista.scrollHeight;
            input.value = '';

            const contador = document.querySelector(`#modal-comentarios-${eventoId} h3`);
            if (contador) {
                const atual = parseInt(contador.textContent.match(/\d+/)?.[0] || 0);
                contador.textContent = `💬 Comentários (${atual + 1})`;
            }

            const contadorExterno = document.querySelector(`.comentario-count-${eventoId}`);
            if (contadorExterno) {
                contadorExterno.textContent = parseInt(contadorExterno.textContent || 0) + 1;
            }

        } catch (err) { console.error('Erro ao comentar:', err); }
    }

    // ── Enviar Comentário Postagem AJAX ──────────────────────
    async function enviarComentarioPost(e, postagemId) {
        e.preventDefault();
        const input = document.querySelector(`.input-comentario-post-${postagemId}`);
        const corpo = input.value.trim();
        if (!corpo) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        try {
            const res = await fetch(`/postagens/${postagemId}/comentar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ corpo })
            });
            if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }

            const lista = document.querySelector(`.lista-comentarios-post-${postagemId}`);
            const vazio = lista.querySelector('.text-center.py-8');
            if (vazio) vazio.remove();

            const html = `
            <div class="flex gap-3">
                <a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0">
                    <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}"
                        class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                </a>
                <div class="flex-1">
                    <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                        <p class="font-bold text-white text-xs">{{ auth()->user()?->name }}</p>
                        <p class="text-gray-200 text-sm mt-1">${escapeHTML(corpo)}</p>
                    </div>
                    <span class="text-gray-500 text-[10px] ml-1">agora mesmo</span>
                </div>
            </div>`;

            lista.insertAdjacentHTML('beforeend', html);
            lista.scrollTop = lista.scrollHeight;
            input.value = '';

            const contador = document.querySelector(`.contador-comentarios-post-${postagemId}`);
            if (contador) contador.textContent = parseInt(contador.textContent || 0) + 1;

        } catch (err) { console.error('Erro ao comentar postagem:', err); }
    }

    // ── Partilhar ────────────────────────────────────────────
    async function partilhar(titulo, url) {
        if (navigator.share) {
            try { await navigator.share({ title: titulo, url: url }); } catch (e) {}
        } else {
            await navigator.clipboard.writeText(url);
            alert('Link copiado!');
        }
    }

    // ── Like Comentário AJAX ─────────────────────────────
async function toggleLikeComentario(comentarioId, btn) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/comentario/${comentarioId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }

        const count = btn.querySelector('span');
        const atual = parseInt(count.textContent || 0);
        const jaGostei = btn.classList.contains('text-blue-400');

        if (jaGostei) {
            btn.classList.remove('text-blue-400');
            btn.classList.add('text-gray-500');
            count.textContent = atual > 1 ? atual - 1 : '';
        } else {
            btn.classList.add('text-blue-400');
            btn.classList.remove('text-gray-500');
            count.textContent = atual + 1;
        }
    } catch (e) { console.error('Erro ao curtir comentário:', e); }
}

// ── Eliminar Comentário AJAX ─────────────────────────
async function eliminarComentario(comentarioId, btn) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/comentario/${comentarioId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }

        const el = document.getElementById(`comentario-${comentarioId}`)
                || document.getElementById(`resposta-${comentarioId}`);
        if (el) el.remove();
    } catch (e) { console.error('Erro ao eliminar:', e); }
}

// ── Enviar Resposta AJAX ─────────────────────────────
async function enviarResposta(eventoId, comentarioId) {
    const input = document.getElementById(`input-resposta-${comentarioId}`);
    const corpo = input.value.trim();
    if (!corpo) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/evento/${eventoId}/comentar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ corpo, parent_id: comentarioId })
        });
        if (res.status === 401) { window.location.href = '{{ route("login") }}'; return; }

        const data = await res.json();
        const container = document.getElementById(`respostas-${comentarioId}`);
        container.classList.remove('hidden');

        const html = `
        <div class="flex gap-2" id="resposta-${data.comentario_id}">
            <a href="{{ auth()->id() ? route('profile.show', auth()->id()) : '#' }}" class="flex-shrink-0">
                <img src="{{ auth()->user()?->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'U') . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-7 h-7 rounded-full object-cover border border-blue-400">
            </a>
            <div class="flex-1">
                <div class="rounded-2xl rounded-tl-sm px-3 py-2" style="background: rgba(30,41,59,0.6); border: 1px solid rgba(59,130,246,0.08);">
                    <p class="font-bold text-white text-[11px]">{{ auth()->user()?->name }}</p>
                    <p class="text-gray-300 text-xs mt-0.5">${escapeHTML(corpo)}</p>
                </div>
                <span class="text-gray-500 text-[10px] ml-1">agora mesmo</span>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        input.value = '';
        document.getElementById(`resposta-form-${comentarioId}`).classList.add('hidden');

    } catch (e) { console.error('Erro ao responder:', e); }
}
</script>
@endsection