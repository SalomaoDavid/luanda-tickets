<div class="flex flex-col" style="height: 100%; overflow: hidden;"
     x-data="{ modoSelecao: false, selecionadas: [] }">

@php $receiver = $conversation->getReceiver(); @endphp

{{-- HEADER --}}
<header class="flex items-center justify-between px-4 py-3 border-b border-white/10 flex-shrink-0"
        style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px);">

    {{-- Info do contacto — fica sempre visível, mesmo em modo de seleção --}}
    <div class="flex items-center gap-3 min-w-0">
        <div class="relative flex-shrink-0">
            <a href="{{ route('profile.show', $receiver->id) }}">
                <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
            </a>
            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-[#020617]
                {{ $receiver->isOnline() ? 'bg-green-500' : 'bg-gray-500' }}"></span>
        </div>
        <div class="min-w-0">
            <a href="{{ route('profile.show', $receiver->id) }}"
               class="font-bold text-white text-sm hover:text-blue-400 transition block truncate">
                {{ $receiver->name }}
            </a>
            @if($conversation->tipo === 'evento' && $conversation->evento)
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider truncate">
                    🎟 {{ $conversation->evento->titulo }}
                </p>
            @else
                <p class="text-[10px] font-semibold {{ $receiver->isOnline() ? 'text-green-400' : 'text-gray-500' }}">
                    {{ $receiver->isOnline() ? '● Online agora' : '● Offline' }}
                </p>
            @endif
        </div>
    </div>

    {{-- Ícones normais — escondidos em modo de seleção --}}
    <div class="flex gap-2 items-center flex-shrink-0 ml-2" x-show="!modoSelecao" x-cloak>
        {{-- ✅ Esconde a conversa só do MEU lado (mesmo mecanismo da lista)
             em vez de apagar as mensagens para os dois participantes. --}}
        {{-- ✅ Confirmação personalizada (window.confirmarAcao, definida em
             messages-index.blade.php) em vez do wire:confirm, que usa o
             alerta feio e padrão do navegador. --}}
        <button type="button"
                @click="window.confirmarAcao('Apagar esta conversa da tua lista? (continua a existir para a outra pessoa)').then(ok => { if (ok) $dispatch('delete-conversation-request', { id: {{ $conversation->id }} }) })"
                class="text-gray-400 hover:text-red-400 transition p-1.5"
                title="Apagar conversa">🗑</button>

        @php
            $euBloqueei  = $conversation->is_blocked && $conversation->blocked_by === auth()->id();
            $elaBloqueou = $conversation->is_blocked && $conversation->blocked_by !== auth()->id();
        @endphp
        @if(!$elaBloqueou)
            <button type="button"
                    @click="window.confirmarAcao('{{ $euBloqueei ? 'Desbloquear esta conversa?' : 'Bloquear esta conversa?' }}').then(ok => { if (ok) $wire.toggleBlock() })"
                    class="transition p-1.5 {{ $euBloqueei ? 'text-red-400' : 'text-gray-400 hover:text-yellow-400' }}">
                {{ $euBloqueei ? '🔒' : '🔓' }}
            </button>
        @else
            <span class="text-red-400 p-1.5">🔒</span>
        @endif
    </div>

    {{-- Barra de seleção — só aparece depois de uma pressão longa numa mensagem --}}
    <div class="flex items-center gap-3 flex-shrink-0 ml-2" x-show="modoSelecao" x-cloak>
        <button type="button"
                @click="modoSelecao = false; selecionadas = []"
                class="flex items-center justify-center rounded-full text-gray-300 hover:text-white transition"
                style="width:30px;height:30px;flex:0 0 30px;"
                title="Cancelar seleção">✕</button>

        <span class="text-xs font-bold text-white whitespace-nowrap" x-text="selecionadas.length + ' selecionada' + (selecionadas.length === 1 ? '' : 's')"></span>

        <button type="button"
                @click="$wire.apagarSelecionadas(selecionadas); modoSelecao = false; selecionadas = [];"
                class="flex items-center justify-center rounded-full text-red-400 hover:text-red-300 transition"
                style="width:34px;height:34px;flex:0 0 34px;font-size:16px;line-height:1;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);"
                title="Apagar selecionadas">🗑</button>
    </div>
</header>

{{-- AVISO BLOQUEIO --}}
@if($conversation->is_blocked)
<div class="px-4 py-2 text-center text-xs font-bold text-red-400 uppercase tracking-widest flex-shrink-0"
     style="background: rgba(239,68,68,0.1); border-bottom: 1px solid rgba(239,68,68,0.2);">
    @if($euBloqueei)
        🔒 Bloqueaste esta conversa
    @else
        🔒 Foste bloqueado nesta conversa
    @endif
</div>
@endif

{{-- MENSAGENS --}}
<div id="chat-content"
     wire:poll.15s.visible="$refresh"
     class="overflow-y-auto p-4 space-y-3"
     style="flex: 1 1 0; min-height: 0;
            background: linear-gradient(180deg, rgba(2,6,23,0.3) 0%, rgba(15,23,42,0.2) 100%);
            scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

    @php $ultimaDataMostrada = null; @endphp
    @foreach($messages as $msg)
    @php
        $isMine = $msg->user_id === auth()->id();
        $dataMsg = $msg->created_at->format('Y-m-d');
        $mostrarSeparador = $dataMsg !== $ultimaDataMostrada;
        $ultimaDataMostrada = $dataMsg;
    @endphp
    @if($mostrarSeparador)
    <div class="flex justify-center my-2">
        <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full"
              style="background: rgba(59,130,246,0.1); color: #7dd3fc;">
            {{ $msg->created_at->isToday() ? 'Hoje' : ($msg->created_at->isYesterday() ? 'Ontem' : $msg->created_at->translatedFormat('d \d\e F')) }}
        </span>
    </div>
    @endif

    @if($msg->is_aviso_admin)
    <div class="flex justify-center my-2">
        <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full"
              style="background: rgba(34,211,238,.15); color:#22d3ee; border:1px solid rgba(34,211,238,.3);">
            🛡 Comunicado Oficial
        </span>
    </div>
    @endif

    {{-- ✅ Pressão longa (mobile) ou clique mantido (desktop) seleciona a
         mensagem e ativa o modo de seleção; com o modo já ativo, um toque
         simples em qualquer mensagem alterna a sua seleção. 250ms de
         espera — rápido o suficiente para não atrapalhar o scroll normal,
         mas sem disparar sem querer num toque rápido. --}}
    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2 select-none"
         style="-webkit-touch-callout: none; -webkit-user-select: none; user-select: none;"
         x-data="{ pressTimer: null }"
         @touchstart="pressTimer = setTimeout(() => { modoSelecao = true; if (!selecionadas.includes({{ $msg->id }})) selecionadas.push({{ $msg->id }}); }, 250)"
         @touchend="clearTimeout(pressTimer)"
         @touchmove="clearTimeout(pressTimer)"
         @mousedown="pressTimer = setTimeout(() => { modoSelecao = true; if (!selecionadas.includes({{ $msg->id }})) selecionadas.push({{ $msg->id }}); }, 250)"
         @mouseup="clearTimeout(pressTimer)"
         @mouseleave="clearTimeout(pressTimer)"
         @contextmenu.prevent
         @click="if (modoSelecao) { selecionadas.includes({{ $msg->id }}) ? selecionadas = selecionadas.filter(i => i !== {{ $msg->id }}) : selecionadas.push({{ $msg->id }}) }">

        {{-- Círculo/visto de seleção — tamanho sempre fixo, nunca estica --}}
        <div x-show="modoSelecao" x-cloak
             class="flex items-center justify-center rounded-full transition-colors"
             style="width:22px;height:22px;flex:0 0 22px;"
             :style="selecionadas.includes({{ $msg->id }})
                ? 'width:22px;height:22px;flex:0 0 22px;background:#2563eb;border:2px solid #2563eb;'
                : 'width:22px;height:22px;flex:0 0 22px;background:transparent;border:2px solid #64748b;'">
            <span x-show="selecionadas.includes({{ $msg->id }})" x-cloak style="font-size:12px;color:#fff;line-height:1;">✓</span>
        </div>

        @if(!$isMine)
        <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
             class="w-7 h-7 rounded-full object-cover flex-shrink-0 mb-1">
        @endif
        <div style="max-width: min(75%, 300px);">
            <div class="px-3 py-2 rounded-2xl {{ $isMine ? 'rounded-br-sm' : 'rounded-bl-sm' }}"
                 style="{{ $isMine
                    ? 'background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 12px rgba(37,99,235,0.3);'
                    : 'background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);' }}">
                <p class="{{ $isMine ? 'text-white' : 'text-gray-200' }} text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $msg->body }}</p>
            </div>
            <p class="text-gray-600 text-[10px] mt-1 {{ $isMine ? 'text-right mr-1' : 'ml-1' }}">
                {{ $msg->created_at->format('H:i') }}
                @if($isMine) ✓✓ @endif
            </p>
        </div>
    </div>
    @endforeach
</div>

{{-- ✅ INPUT CORRIGIDO --}}
<footer class="flex-shrink-0 border-t border-white/10 relative"
        style="background: rgba(15, 23, 42, 0.95); padding: 8px 12px;">

    @if($conversation->is_blocked)
        <div class="text-center text-red-400 text-xs font-bold uppercase tracking-widest py-2">
            🔒 Não podes enviar mensagens
        </div>
    @else

    @if(auth()->user()->role === 'admin')
        <div class="flex items-center gap-2 mb-2 px-1">
            <button type="button" id="aviso-toggle" data-active="false"
                    class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wide px-2 py-1 rounded-full transition-colors"
                    style="background: rgba(148,163,184,.12); color:#94a3b8; border:1px solid rgba(148,163,184,.25);">
                <span id="aviso-toggle-dot" style="width:8px;height:8px;border-radius:50%;background:#64748b;"></span>
                🛡 Enviar como Comunicado Oficial
            </button>
        </div>
    @endif

        <form wire:submit.prevent="sendMessage" class="flex items-end gap-2 relative">

            <div class="relative" x-data="{ showPicker: false }">
                {{-- Container com altura ideal para o Emoji Mart respirar --}}
                <div id="emoji-picker-container"
                    x-show="showPicker"
                    x-cloak
                    @click.away="showPicker = false"
                    class="absolute bottom-full left-0 mb-3 z-[100] shadow-2xl shadow-black rounded-2xl overflow-hidden"
                    style="width: 350px; height: 400px; background: #1e293b;">
                    {{-- O Picker será injetado aqui --}}
                </div>

                <button type="button" id="emoji-btn"
                        @click="showPicker = !showPicker"
                        class="text-gray-400 hover:text-blue-400 transition flex-shrink-0 p-2"
                        style="font-size:20px; line-height:1;">
                    😊
                </button>
            </div>

            <textarea id="message-input"
                    placeholder="Escreve uma mensagem..."
                    rows="1"
                    class="flex-1 rounded-2xl text-sm text-white placeholder-gray-500 outline-none transition resize-none"
                    style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);
                            padding: 9px 14px; max-height: 100px; min-height: 38px;
                            overflow-y: auto; line-height: 1.4;"></textarea>

            <button type="button" id="send-btn"
                    class="flex-shrink-0 flex items-center justify-center rounded-full text-white transition hover:opacity-90 active:scale-95"
                    style="width: 38px; height: 38px; min-width: 38px;
                        background: linear-gradient(135deg, #2563eb, #1d4ed8);
                        box-shadow: 0 3px 10px rgba(37,99,235,0.4);">
                <svg id="send-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="white" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
                <span id="send-spinner" style="display:none;width:15px;height:15px;border-radius:50%;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;animation:chat-spin .7s linear infinite;"></span>
            </button>
        </form>
    @endif

    <style>
    @keyframes chat-spin { to { transform: rotate(360deg); } }
    </style>
</footer>
</div>