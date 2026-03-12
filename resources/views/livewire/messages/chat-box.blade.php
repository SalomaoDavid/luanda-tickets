<div class="flex flex-col h-full">

@php $receiver = $conversation->getReceiver(); @endphp

{{-- HEADER --}}
<header class="flex items-center justify-between px-6 py-4 border-b border-white/10"
        style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(20px);">
    <div class="flex items-center gap-3">
        <div class="relative">
            <a href="{{ route('profile.show', $receiver->id) }}">
                <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-11 h-11 rounded-full object-cover border-2 border-blue-400 hover:scale-105 transition">
            </a>
            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-[#020617]
                {{ $receiver->isOnline() ? 'bg-green-500' : 'bg-gray-500' }}"></span>
        </div>
        <div>
            <a href="{{ route('profile.show', $receiver->id) }}"
               class="font-bold text-white text-sm hover:text-blue-400 transition">
                {{ $receiver->name }}
            </a>
            @if($conversation->tipo === 'evento' && $conversation->evento)
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">
                    🎟 {{ $conversation->evento->titulo }}
                </p>
            @else
                <p class="text-[10px] font-semibold {{ $receiver->isOnline() ? 'text-green-400' : 'text-gray-500' }}">
                    {{ $receiver->isOnline() ? '● Online agora' : '● Offline' }}
                </p>
            @endif
        </div>
    </div>

    <div class="flex gap-3 items-center">
        {{-- Eliminar mensagens --}}
        <button wire:click="clearMessages"
                wire:confirm="Tens a certeza que queres eliminar todas as mensagens?"
                class="text-gray-400 hover:text-red-400 transition text-base"
                title="Eliminar mensagens">
            🗑
        </button>

        {{-- Bloquear / Desbloquear --}}
        @php
            $euBloqueei  = $conversation->is_blocked && $conversation->blocked_by === auth()->id();
            $elaBloqueou = $conversation->is_blocked && $conversation->blocked_by !== auth()->id();
        @endphp

        @if(!$elaBloqueou)
            <button wire:click="toggleBlock"
                    wire:confirm="{{ $euBloqueei ? 'Desbloquear esta conversa?' : 'Bloquear esta conversa?' }}"
                    class="transition text-lg {{ $euBloqueei ? 'text-red-400 hover:text-red-300' : 'text-gray-400 hover:text-yellow-400' }}"
                    title="{{ $euBloqueei ? 'Desbloquear' : 'Bloquear' }}">
                {{ $euBloqueei ? '🔒' : '🔓' }}
            </button>
        @else
            <span class="text-red-400 text-lg" title="Foste bloqueado(a)">🔒</span>
        @endif
    </div>
</header>

{{-- AVISO DE BLOQUEIO --}}
@if($conversation->is_blocked)
<div class="px-6 py-2 text-center text-xs font-bold text-red-400 uppercase tracking-widest"
     style="background: rgba(239,68,68,0.1); border-bottom: 1px solid rgba(239,68,68,0.2);">
    🔒 Esta conversa está bloqueada
</div>
@endif

{{-- MENSAGENS --}}
<div id="chat-content"
     wire:poll.3s="$refresh"
     class="flex-1 overflow-y-auto p-6 space-y-4"
     style="background: linear-gradient(180deg, rgba(2,6,23,0.3) 0%, rgba(15,23,42,0.2) 100%);
            scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

    @foreach($messages as $msg)
    @php $isMine = $msg->user_id === auth()->id(); @endphp

    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-2">

        @if(!$isMine)
        <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
             class="w-8 h-8 rounded-full object-cover flex-shrink-0 mb-1">
        @endif

        <div class="max-w-xs lg:max-w-md">
            <div class="px-4 py-3 rounded-2xl {{ $isMine ? 'rounded-br-sm' : 'rounded-bl-sm' }}"
                 style="{{ $isMine
                    ? 'background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 15px rgba(37,99,235,0.3);'
                    : 'background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);' }}">
                {{-- nl2br para mostrar quebras de linha --}}
                <p class="{{ $isMine ? 'text-white' : 'text-gray-200' }} text-sm leading-relaxed whitespace-pre-wrap">{{ $msg->body }}</p>
            </div>
            <p class="text-gray-600 text-[10px] mt-1 {{ $isMine ? 'text-right mr-1' : 'ml-1' }}">
                {{ $msg->created_at->format('H:i') }}
                @if($isMine) ✓✓ @endif
            </p>
        </div>

    </div>
    @endforeach

</div>

{{-- INPUT --}}
<footer class="px-6 py-4 border-t border-white/10 relative"
        style="background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px);">

    @if($conversation->is_blocked)
        <div class="text-center text-red-400 text-xs font-bold uppercase tracking-widest py-2">
            🔒 Não podes enviar mensagens — conversa bloqueada
        </div>
    @else

        {{-- PICKER DE EMOJIS --}}
        <div id="emoji-picker-container" class="absolute bottom-20 left-6 z-50 hidden"></div>

        <form wire:submit.prevent="sendMessage" class="flex items-end gap-3">

            <button type="button"
                    id="emoji-btn"
                    class="text-gray-400 hover:text-blue-400 transition text-xl flex-shrink-0 mb-2">
                😊
            </button>

            <button type="button" class="text-gray-400 hover:text-blue-400 transition text-xl flex-shrink-0 mb-2">📎</button>

            {{-- Textarea em vez de input para suportar múltiplas linhas --}}
            <textarea id="message-input"
                      wire:model="messageBody"
                      placeholder="Escreve uma mensagem... (Shift+Enter para nova linha)"
                      rows="1"
                      class="flex-1 rounded-2xl px-5 py-3 text-sm text-white placeholder-gray-500 outline-none transition resize-none overflow-hidden"
                      style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2); max-height: 120px;"></textarea>

            <button type="submit"
                    class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold transition hover:scale-105 mb-2"
                    style="background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 15px rgba(37,99,235,0.3);">
                ➤
            </button>
        </form>
    @endif
</footer>

<script type="module">
    import * as EmojiMart from 'https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/+esm';

    const container = document.getElementById('emoji-picker-container');
    const btn       = document.getElementById('emoji-btn');
    const input     = document.getElementById('message-input');

    // ── Emoji Picker ──────────────────────────────────────────
    if (btn && container && input) {
        const picker = new EmojiMart.Picker({
            theme: 'dark',
            locale: 'pt',
            onEmojiSelect: (emoji) => {
                const start = input.selectionStart;
                const end   = input.selectionEnd;
                const atual = input.value;
                const novo  = atual.slice(0, start) + emoji.native + atual.slice(end);
                input.value = novo;

                // Sincroniza com wire:model
                input.dispatchEvent(new Event('input'));

                input.selectionStart = input.selectionEnd = start + emoji.native.length;
                input.focus();
                container.classList.add('hidden');

                // Ajusta altura
                autoResize(input);
            },
            skinTonePosition: 'none',
        });

        container.appendChild(picker);

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            container.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target) && e.target !== btn) {
                container.classList.add('hidden');
            }
        });
    }

    // ── Textarea: Enter envia, Shift+Enter nova linha ─────────
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                // Dispara o submit do formulário via Livewire
                window.Livewire.dispatch('sendMessage');
                // Fallback: clica no botão de submit
                input.closest('form').querySelector('button[type="submit"]').click();
            }
        });

        // Auto-resize da textarea conforme o utilizador escreve
        input.addEventListener('input', () => autoResize(input));
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    // ── Scroll ────────────────────────────────────────────────
    function scrollToBottom() {
        const c = document.getElementById('chat-content');
        if (c) c.scrollTop = c.scrollHeight;
    }
    document.addEventListener('livewire:navigated', scrollToBottom);
    window.addEventListener('scroll-down', () => setTimeout(scrollToBottom, 50));
    scrollToBottom();
</script>

</div>