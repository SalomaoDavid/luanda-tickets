@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')

<!-- Stories / Eventos em Carrossel Infinito -->
<div class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm rounded-2xl py-3 mb-4 overflow-hidden">
    <div id="stories-track" class="flex" style="will-change: transform;">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0 story-item px-3">
            <a href="{{ route('evento.detalhes', $evento->id) }}">
                <div class="w-20 h-20 rounded-full border-4 border-blue-400 overflow-hidden shadow-lg hover:border-blue-600 hover:scale-105 transition">
                    <img src="{{ $evento->imagem_capa ? asset('storage/' . $evento->imagem_capa) : 'https://picsum.photos/200' }}"
                         class="w-full h-full object-cover">
                </div>
                <p class="mt-1 text-sm text-gray-800 truncate w-20 hover:text-blue-500 transition">{{ $evento->titulo }}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Espaço de publicação -->
<div class="bg-white p-4 rounded-3xl shadow-xl mb-6">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-100 text-gray-800 p-3 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                  rows="2"></textarea>
        <div class="flex justify-between items-center mt-3">
            <div class="text-gray-400 text-sm">📷 Foto | 🎥 Vídeo | 🎟 Evento</div>
            <button type="submit" class="bg-blue-500 text-white px-5 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                Publicar
            </button>
        </div>
    </form>
</div>

<!-- Feed unificado -->
@foreach($feed as $entry)

    @if($entry['tipo'] === 'post')
    {{-- CARD DE POSTAGEM --}}
    <div class="bg-white rounded-3xl shadow-xl p-5 mb-4">
        <div class="flex items-center space-x-3 mb-3">
            <a href="{{ route('profile.show', $entry['item']->user->id) }}">
                <img src="{{ $entry['item']->user->avatar
                    ? asset('storage/' . $entry['item']->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($entry['item']->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div>
                <a href="{{ route('profile.show', $entry['item']->user->id) }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $entry['item']->user->name }}
                </a>
                <p class="text-xs text-gray-400">{{ $entry['item']->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm leading-relaxed">{{ $entry['item']->conteudo }}</p>
    </div>

    @else
    {{-- CARD DE EVENTO --}}
    @php $evento = $entry['item']; @endphp
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">

        {{-- Cabeçalho do criador --}}
        <div class="flex items-center space-x-3 p-4">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}">
                <img src="{{ $evento->user && $evento->user->avatar
                    ? asset('storage/' . $evento->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($evento->user->name ?? 'U') . '&background=0ea5e9&color=fff&size=128' }}"
                     class="w-11 h-11 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div>
                <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $evento->user ? $evento->user->name : 'Usuário' }}
                </a>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Imagem --}}
        <div class="relative">
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-full h-56 object-cover">
            @else
                <div class="w-full h-56 bg-gradient-to-br from-blue-600 to-blue-900 flex items-center justify-center">
                    <span class="text-white text-4xl">🎟</span>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-5 py-4">
                <h2 class="text-white text-lg font-bold drop-shadow">{{ $evento->titulo }}</h2>
            </div>
        </div>

        {{-- Infos --}}
        <div class="p-5">
            <div class="flex flex-col space-y-1 text-sm text-gray-500 mb-4">
                <span>📍 {{ $evento->localizacao ?? 'Local não informado' }}</span>
                <span>📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
                <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 mb-4">{{ $evento->descricao }}</p>

            {{-- Ações --}}
            <div class="flex justify-between items-center text-gray-500 text-sm border-t pt-4">
                <div class="flex flex-col space-y-2">
                    <form method="POST" action="{{ route('evento.curtir', $evento->id) }}">
                        @csrf
                        <button 
                            onclick="toggleCurtida({{ $evento->id }}, this)"
                            data-curtido="{{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'true' : 'false' }}"
                            data-total="{{ $evento->curtidas->count() }}"
                            class="hover:text-blue-500 transition duration-300 {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'text-blue-500' : '' }}">
                            👍 <span class="curtida-texto-{{ $evento->id }}">{{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'Curtido' : 'Curtir' }}</span> 
                            (<span class="curtida-count-{{ $evento->id }}">{{ $evento->curtidas->count() }}</span>)
                        </button>
                    </form>

                    @if($evento->usuariosQueCurtiram->count() > 0)
                    @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
                    <button onclick="abrirModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="flex items-center group">
                        <div class="flex" style="direction: rtl;">
                            @foreach($curtidores->take(5) as $user)
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                 class="w-7 h-7 rounded-full border-2 border-white object-cover -mr-2" style="direction: ltr;" title="{{ $user->name }}">
                            @endforeach
                        </div>
                        @if($evento->usuariosQueCurtiram->count() > 5)
                            <span class="text-xs text-gray-400 ml-4">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>
                        @endif
                    </button>
                    @endif
                </div>

                {{-- Botão comentar abre modal --}}
                <button onclick="abrirModalComentarios('modal-comentarios-{{ $evento->id }}')"
                        class="hover:text-blue-500 transition duration-300">
                    💬 Comentar ({{ $evento->comentarios->count() }})
                </button>

                <a href="{{ route('evento.detalhes', $evento->id) }}"
                   class="bg-blue-500 text-white px-4 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                    🎟 Comprar
                </a>
            </div>
        </div>
    </div>
          {{-- ═══════════════════════════════════════
                MODAL COMENTÁRIOS
            ═══════════════════════════════════════ --}}
        <div id="modal-comentarios-{{ $evento->id }}"
            class="hidden fixed inset-0 z-[9999] items-center justify-center"
            style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
            <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
                style="max-height: 80vh; background: rgba(15,23,42,0.97); border: 1px solid rgba(59,130,246,0.2);">

                {{-- Header modal --}}
                <div class="flex justify-between items-center px-6 py-4"
                    style="border-bottom: 1px solid rgba(59,130,246,0.15);">
                    <h3 class="font-black text-white text-sm uppercase tracking-widest">
                        💬 Comentários ({{ $evento->comentarios->count() }})
                    </h3>
                    <button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')"
                            class="text-gray-400 hover:text-white text-xl font-bold transition">✕</button>
                </div>

                {{-- Lista de comentários --}}
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
                                    <form method="POST" action="{{ route('comentario.eliminar', $comentario->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-600 hover:text-red-400 transition text-xs">🗑</button>
                                    </form>
                                    @endif
                                </div>
                                <p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p>
                            </div>

                            <div class="flex items-center gap-4 mt-1.5 ml-1">
                                <span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span>
                                <form method="POST" action="{{ route('comentario.like', $comentario->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-[11px] font-bold transition {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                        👍 {{ $comentario->likes->count() > 0 ? $comentario->likes->count() : '' }}
                                    </button>
                                </form>
                                @auth
                                <button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')"
                                        class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">
                                    Responder
                                </button>
                                @endauth
                            </div>

                            @auth
                            <div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2">
                                <form method="POST" action="{{ route('evento.comentar', $evento->id) }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comentario->id }}">
                                    <img src="{{ auth()->user()->avatar
                                        ? asset('storage/' . auth()->user()->avatar)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                        class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0">
                                    <input type="text" name="corpo" placeholder="Escreve uma resposta..."
                                        class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none"
                                        style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                                    <button type="submit"
                                            class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition"
                                            style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        ➤
                                    </button>
                                </form>
                            </div>
                            @endauth

                            @if($comentario->respostas->count() > 0)
                            <div class="mt-3 space-y-3 pl-2"
                                style="border-left: 2px solid rgba(59,130,246,0.2);">
                                @foreach($comentario->respostas as $resposta)
                                <div class="flex gap-2">
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
                                                <form method="POST" action="{{ route('comentario.eliminar', $resposta->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-600 hover:text-red-400 transition text-[10px]">🗑</button>
                                                </form>
                                                @endif
                                            </div>
                                            <p class="text-gray-300 text-xs mt-0.5 leading-relaxed">{{ $resposta->corpo }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1 ml-1">
                                            <span class="text-gray-500 text-[10px]">{{ $resposta->created_at->diffForHumans() }}</span>
                                            <form method="POST" action="{{ route('comentario.like', $resposta->id) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-[10px] font-bold transition {{ $resposta->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                                    👍 {{ $resposta->likes->count() > 0 ? $resposta->likes->count() : '' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
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

                {{-- Input novo comentário --}}
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

    {{-- Modal curtidas --}}
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

            const itemWidth = 104;
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

    // ── Modal Curtidas ───────────────────────────────────────
    function abrirModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }

    function fecharModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Modal Comentários ────────────────────────────────────
    function abrirModalComentarios(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }

    function fecharModalComentarios(id) {
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
        ['modal-curtidas-', 'modal-comentarios-'].forEach(prefix => {
            document.querySelectorAll(`[id^="${prefix}"]`).forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            });
        });
    });

    // ── Toggle Curtida AJAX ──────────────────────────────────
    async function toggleCurtida(eventoId, btn) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content 
            || '{{ csrf_token() }}';

        try {
            const res = await fetch(`/eventos/${eventoId}/curtir`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            const data = await res.json();

            // Atualiza o botão
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

        } catch (e) {
            console.error('Erro ao curtir:', e);
        }
    }
    // ── Enviar Comentário AJAX ───────────────────────────────
    async function enviarComentario(e, eventoId) {
        e.preventDefault();

        const form = document.getElementById(`form-comentario-${eventoId}`);
        const input = form.querySelector('input[name="corpo"]');
        const corpo = input.value.trim();

        if (!corpo) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content
            || '{{ csrf_token() }}';

        try {
            const res = await fetch(`/eventos/${eventoId}/comentar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ corpo })
            });

            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            const data = await res.json();

            // Adiciona o comentário à lista sem recarregar
            const lista = document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);
            const vazio = lista.querySelector('.text-center.py-8');
            if (vazio) vazio.remove();

            const html = `
            <div class="flex gap-3">
                <a href="{{ route('profile.show', auth()->id()) }}" class="flex-shrink-0">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                        class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                </a>
                <div class="flex-1">
                    <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                        <p class="font-bold text-white text-xs">{{ auth()->user()->name }}</p>
                        <p class="text-gray-200 text-sm mt-1">${corpo}</p>
                    </div>
                    <span class="text-gray-500 text-[10px] ml-1">agora mesmo</span>
                </div>
            </div>`;

            lista.insertAdjacentHTML('beforeend', html);
            lista.scrollTop = lista.scrollHeight;

            // Limpa o input
            input.value = '';

            // Atualiza o contador
            const contador = document.querySelector(`#modal-comentarios-${eventoId} h3`);
            if (contador) {
                const atual = parseInt(contador.textContent.match(/\d+/)?.[0] || 0);
                contador.textContent = `💬 Comentários (${atual + 1})`;
            }

        } catch (err) {
            console.error('Erro ao comentar:', err);
        }
    }
</script>

@endsection