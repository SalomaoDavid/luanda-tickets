<div class="flex-1 w-full min-w-0 overflow-hidden flex items-center justify-center bg-[#020617] p-0"
     style="height: calc(100vh - 64px); height: calc(100dvh - 64px);">

<style>
    /* Estas regras sobrepõem o <main> do layout partilhado (app.blade.php)
       SEM alterar esse ficheiro — só têm efeito enquanto esta página de
       mensagens está aberta, porque só aqui é que este <style> existe.
       :not(#msg-chat) exclui o nosso próprio <main> interno do chat. */
    main:not(#msg-chat) {
        overflow: hidden !important;
        padding: 0 !important;
        height: calc(100vh - 64px) !important;
        height: calc(100dvh - 64px) !important;
    }
    #msg-container { border-radius: 0; }
    @media(min-width:768px){
        #msg-container { border-radius: 32px; }
    }
    #msg-sidebar { display: flex; }
    #msg-chat    { display: none; flex-direction: column; }
    @media(min-width:768px){
        /* Reduzimos a largura da lista de 320px para 260px (aproximadamente 25% do container) */
        #msg-sidebar { display: flex !important; width: 260px; }
        #msg-chat    { display: flex !important; }
    }
    #msg-container.show-chat #msg-sidebar { display: none; }
    #msg-container.show-chat #msg-chat    { display: flex; flex: 1; height: 100%; }

    .online-strip { display: flex; gap: 10px; overflow-x: auto; scrollbar-width: none; padding: 8px 14px 10px; }
    .online-strip::-webkit-scrollbar { display: none; }
    .online-person { display: flex; flex-direction: column; align-items: center; gap: 3px; flex-shrink: 0; cursor: pointer; }
    .online-person-ava { position: relative; }
    .online-person-ava img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #10b981; transition: transform .2s; }
    .online-person:hover .online-person-ava img { transform: scale(1.08); }
    .online-person-dot { position: absolute; bottom: 1px; right: 1px; width: 9px; height: 9px; background: #10b981; border-radius: 50%; border: 2px solid #020617; }
    .online-person-name { font-size: 9px; font-weight: 700; color: #94a3b8; white-space: nowrap; max-width: 44px; overflow: hidden; text-overflow: ellipsis; text-align: center; }
</style>

{{-- Usamos a lógica reativa do Livewire no ID do container para evitar que o layout feche sozinho --}}
<div id="msg-container"
     class="overflow-hidden flex {{ $selectedConversation ? 'show-chat' : '' }}"
     style="width: 100%; height: 100%;
            background: rgba(15,23,42,0.85); backdrop-filter: blur(20px);
            border: 1px solid rgba(59,130,246,0.15);">

    {{-- SIDEBAR (Reduzido para w-64 no desktop para ficar mais compacto) --}}
    <aside id="msg-sidebar" class="flex-col flex-shrink-0 w-full md:w-64"
           style="border-right: 1px solid rgba(59,130,246,0.15);">

        {{-- Header --}}
        <div class="p-4" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
            <h1 class="text-base font-black text-white mb-3">💬 Mensagens</h1>
            <div class="relative">
                <input type="text"
                       placeholder="Pesquisar conversa..."
                       class="w-full rounded-xl py-2 pl-8 pr-3 text-sm text-white placeholder-gray-500 outline-none transition"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <span class="absolute left-2.5 top-2.5 text-gray-500 text-xs">🔍</span>
            </div>
        </div>

        {{-- ✅ PESSOAS ONLINE — só desktop --}}
        @php
            $onlineUsers = \App\Models\User::whereNotNull('last_seen')
                ->where('last_seen', '>=', now()->subMinutes(5))
                ->where('id', '!=', auth()->id())
                ->select('id','name','avatar')
                ->get();
        @endphp
        @if($onlineUsers->count() > 0)
        <div class="hidden md:block" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
            <div style="padding: 8px 14px 0;">
                <span style="font-size:9px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#4a5568;">
                    🟢 Online · {{ $onlineUsers->count() }}
                </span>
            </div>
            <div class="online-strip">
                @foreach($onlineUsers as $ou)
                @php
                    $convExistente = \App\Models\Conversation::where(function($q) use ($ou) {
                        $q->where('sender_id', auth()->id())->where('receiver_id', $ou->id);
                    })->orWhere(function($q) use ($ou) {
                        $q->where('sender_id', $ou->id)->where('receiver_id', auth()->id());
                    })->first();
                @endphp
                <div class="online-person"
                     @if($convExistente)
                     wire:click="loadConversation({{ $convExistente->id }})"
                     @endif
                     title="{{ $ou->name }}">
                    <div class="online-person-ava">
                        <img src="{{ $ou->avatar ? asset('storage/'.$ou->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($ou->name).'&background=0ea5e9&color=fff&size=64' }}"
                             alt="{{ $ou->name }}">
                        <div class="online-person-dot"></div>
                    </div>
                    <div class="online-person-name">{{ explode(' ', $ou->name)[0] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Lista de conversas --}}
        <div class="flex-1 overflow-y-auto py-2"
             style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

            @foreach($conversations as $conv)
            @php
                $receiver       = $conv->getReceiver();
                $ultimaMensagem = $conv->messages->last();
                $isSelected     = $selectedConversation && $selectedConversation->id === $conv->id;
            @endphp

            {{-- Removeu-se o onclick problemático daqui. O Livewire agora trata de tudo dinamicamente através da classe inserida no container pai --}}
            <div class="flex items-center gap-3 px-4 py-3 cursor-pointer transition group"
                 style="{{ $isSelected
                    ? 'background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6;'
                    : 'border-left: 3px solid transparent;' }}"
                 wire:click="loadConversation({{ $conv->id }})">

                <div class="relative flex-shrink-0">
                    <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
                         class="w-11 h-11 rounded-full object-cover border-2 {{ $isSelected ? 'border-blue-400' : 'border-transparent' }}">
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-[#020617]
                        {{ $receiver->isOnline() ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="text-white font-semibold text-sm truncate">{{ $receiver->name }}</p>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($conv->unread_count > 0)
                                <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                                    {{ $conv->unread_count }}
                                </span>
                            @else
                                <span class="text-gray-500 text-[10px]">
                                    {{ $ultimaMensagem ? $ultimaMensagem->created_at->format('H:i') : '' }}
                                </span>
                            @endif
                            <button wire:click.stop="deleteConversation({{ $conv->id }})"
                                    wire:confirm="Tens a certeza que queres eliminar esta conversa?"
                                    class="text-gray-600 hover:text-red-400 transition opacity-0 group-hover:opacity-100 text-xs"
                                    title="Eliminar conversa">🗑</button>
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs truncate mt-0.5">
                        @if($ultimaMensagem)
                            {{ $ultimaMensagem->user_id == auth()->id() ? 'Tu: ' : '' }}{{ $ultimaMensagem->body }}
                        @else
                            <span class="italic">Sem mensagens</span>
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </aside>

    {{-- ÁREA DO CHAT --}}
    <main id="msg-chat" class="flex-1 flex-col" style="min-width: 0; overflow: hidden;"
          data-conversation-id="{{ $selectedConversation->id ?? '' }}">

        {{-- Botão voltar mobile: Executa uma ação no backend para desmarcar a conversa e atualizar o estado global --}}
        <div class="flex md:hidden items-center px-4 py-2 border-b border-white/10 flex-shrink-0"
             style="background: rgba(15,23,42,0.95);">
            <button wire:click="$set('selectedConversationId', null)"
                    class="text-blue-400 font-bold text-sm flex items-center gap-1">
                ← Voltar
            </button>
        </div>

        @if($selectedConversation)
            @livewire('messages.chat-box',
                ['conversation' => $selectedConversation],
                key('chat-box-' . $selectedConversation->id)
            )
        @else
            <div class="flex-1 flex flex-col items-center justify-center"
                 style="color: rgba(148,163,184,0.3);">
                <svg class="w-14 h-14 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="font-black uppercase text-xs tracking-[0.2em]">Seleciona uma conversa</p>
            </div>
        @endif
    </main>
</div>

<script>
(function () {
    // Trava o scroll da página (body/html) só enquanto esta página de
    // mensagens está aberta — não mexe no app.blade.php. Ao navegar para
    // outra página (recarregamento normal do Laravel), isto desaparece
    // sozinho porque o estilo inline não sobrevive à navegação.
    const prevBodyOverflow = document.body.style.overflow;
    const prevHtmlOverflow = document.documentElement.style.overflow;
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';

    window.addEventListener('beforeunload', function () {
        document.body.style.overflow = prevBodyOverflow;
        document.documentElement.style.overflow = prevHtmlOverflow;
    });

    // Evita que uma sessão expirada (419) ou um erro (404/500) do Livewire,
    // ao ficar muito tempo parado nesta página, substitua o ecrã inteiro
    // por uma página de erro crua. Mostra só um aviso discreto.
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if ([404, 419, 500, 503].includes(status)) {
                    preventDefault();

                    let toast = document.getElementById('livewire-lost-toast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'livewire-lost-toast';
                        toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1e293b;color:#fff;padding:10px 18px;border-radius:14px;font-size:12px;font-weight:700;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.4);border:1px solid rgba(59,130,246,.3);';
                        document.body.appendChild(toast);
                    }
                    toast.textContent = status === 419
                        ? 'Sessão expirada — toca para atualizar'
                        : 'Ligação perdida — toca para atualizar';
                    toast.onclick = () => window.location.reload();
                    clearTimeout(window.__livewireToastTimeout);
                    window.__livewireToastTimeout = setTimeout(() => toast.remove(), 5000);
                }
            });
        });
    });
})();

// ═══════════════════════════════════════════════════════════════════
// LÓGICA DO CHAT (envio, scroll, emojis, tempo real)
// ───────────────────────────────────────────────────────────────────
// Isto vive aqui, no componente PAI (messages-index), que só é montado
// UMA VEZ quando a página carrega — e nunca é destruído. O chat-box
// (filho) É destruído e recriado a cada troca de conversa, e um
// <script> dentro dele só corre no carregamento inicial da página,
// nunca quando é inserido depois (ex: selecionar da lista). Por isso
// usamos DELEGAÇÃO DE EVENTOS aqui: um único listener, sempre vivo,
// que funciona seja qual for a conversa aberta no momento.
// ═══════════════════════════════════════════════════════════════════
(function () {
    function chatFindComponent(el) {
        if (!window.Livewire) return null;
        const root = el.closest('[wire\\:id]');
        if (!root) return null;
        return window.Livewire.find(root.getAttribute('wire:id'));
    }

    function chatAutoResize(el) {
        if (!el) return;
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    }

    function chatScrollToBottom() {
        const c = document.getElementById('chat-content');
        if (c) c.scrollTop = c.scrollHeight;
    }

    let sending = false;
    async function chatHandleSend() {
        const input = document.getElementById('message-input');
        if (!input || sending) return;
        const val = input.value.trim();
        if (val === '') return;

        const component = chatFindComponent(input);
        if (!component) return;

        sending = true;
        input.value = '';
        chatAutoResize(input);

        try {
            await component.call('sendMessage', val);
        } catch (err) {
            console.error('[chat] falhou o envio da mensagem:', err);
        } finally {
            sending = false;
            input.focus();
            requestAnimationFrame(() => input.focus());
            setTimeout(() => { input.focus(); chatScrollToBottom(); }, 60);
        }
    }

    // Clique no botão de enviar (delegado — funciona para qualquer conversa)
    document.addEventListener('click', function (e) {
        if (e.target.closest('#send-btn')) {
            e.preventDefault();
            chatHandleSend();
        }
    });

    // Enter para enviar, Shift+Enter para nova linha
    document.addEventListener('keydown', function (e) {
        if (e.target && e.target.id === 'message-input' && e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatHandleSend();
        }
    });

    // Auto-resize do textarea
    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'message-input') {
            chatAutoResize(e.target);
        }
    });

    // Scroll para o fundo depois de qualquer atualização do Livewire
    // (nova mensagem recebida, refresh do poll, etc.)
    document.addEventListener('livewire:updated', () => {
        setTimeout(chatScrollToBottom, 50);
    });
    window.addEventListener('scroll-down', () => setTimeout(chatScrollToBottom, 100));

    // ── Seletor de emojis (delegado da mesma forma) ──
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('#emoji-btn');
        if (!btn) return;

        const container = document.getElementById('emoji-picker-container');
        const input = document.getElementById('message-input');
        if (!container || !input) return;

        if (container.childElementCount === 0) {
            try {
                const { Picker } = await import('https://cdn.jsdelivr.net/npm/emoji-mart@5.6.0/+esm');
                const picker = new Picker({
                    data: window.EmojiMartData,
                    theme: 'dark',
                    locale: 'pt',
                    set: 'native',
                    skinTonePosition: 'none',
                    onEmojiSelect: (emoji) => {
                        const start = input.selectionStart;
                        input.value = input.value.slice(0, start) + emoji.native + input.value.slice(input.selectionEnd);
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        const alpineData = window.Alpine && Alpine.$data(btn.parentElement);
                        if (alpineData) alpineData.showPicker = false;
                        input.focus();
                        chatAutoResize(input);
                    }
                });
                container.appendChild(picker);
            } catch (err) {
                console.error('Erro crítico ao carregar seletor de emojis:', err);
            }
        }
    });

    // ── Tempo real (WebSocket / Laravel Echo) ──
    // Observa o atributo data-conversation-id do #msg-chat (que o
    // Livewire atualiza normalmente, mesmo sem o script do filho
    // correr) e subscreve o canal certo sempre que a conversa muda.
    window.__activeChatChannels = window.__activeChatChannels || new Set();

    function subscribeRealtime(conversationId) {
        if (!window.Echo || !conversationId) return;

        window.__activeChatChannels.forEach(function (chId) {
            if (chId !== conversationId) {
                window.Echo.leave('chat.' + chId);
                window.__activeChatChannels.delete(chId);
            }
        });

        if (window.__activeChatChannels.has(conversationId)) return;
        window.__activeChatChannels.add(conversationId);

        window.Echo.private('chat.' + conversationId)
            .listen('.MessageSent', function () {
                const input = document.getElementById('message-input');
                const component = input ? chatFindComponent(input) : null;
                if (component) {
                    component.call('$refresh').then(chatScrollToBottom);
                }
            });
    }

    const msgChat = document.getElementById('msg-chat');
    if (msgChat) {
        let lastConvId = null;
        const checkConv = () => {
            const id = msgChat.getAttribute('data-conversation-id');
            if (id && id !== lastConvId) {
                lastConvId = id;
                subscribeRealtime(id);
            }
        };
        checkConv();
        new MutationObserver(checkConv).observe(msgChat, {
            attributes: true,
            attributeFilter: ['data-conversation-id']
        });
    }
})();
</script>

</div>