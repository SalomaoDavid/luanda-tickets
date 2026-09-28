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
        #msg-sidebar { display: flex !important; width: 260px; }
        #msg-chat    { display: flex !important; }
    }
    #msg-container.show-chat #msg-sidebar { display: none; }
    #msg-container.show-chat #msg-chat    { display: flex; flex: 1; height: 100%; }

    /* ── Ao abrir uma conversa em mobile, o chat cobre o ecrã todo,
       incluindo o cabeçalho fixo do site (que tem z-50) — sem alterar
       o app.blade.php, só sobrepomos visualmente por cima dele. ── */
    @media(max-width:767px){
        #msg-container.show-chat {
            position: fixed !important;
            inset: 0 !important;
            z-index: 9999;
            height: 100dvh !important;
        }
        /* ✅ CORRIGIDO — antes era "header" (genérico), o que também
           apanhava o <header> do próprio chat-box e apagava os dois.
           Agora aponta só ao header do site (app.blade.php). */
        body:has(#msg-container.show-chat) #site-header {
            display: none !important;
        }
    }

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
     class="overflow-hidden flex {{ ($selectedConversation && $forcarMostrarChat) ? 'show-chat' : '' }}"
     style="width: 100%; height: 100%;
            background: rgba(15,23,42,0.85); backdrop-filter: blur(20px);
            border: 1px solid rgba(59,130,246,0.15);">

    {{-- SIDEBAR --}}
    <aside id="msg-sidebar" class="flex-col flex-shrink-0 w-full md:w-64"
           style="border-right: 1px solid rgba(59,130,246,0.15);">

        {{-- Header --}}
        <div class="p-4" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
            <h1 class="text-base font-black text-white mb-3">💬 Mensagens</h1>
            <div class="relative">
                <input type="text"
                       id="chat-search-input"
                       placeholder="Pesquisar conversa..."
                       class="w-full rounded-xl py-2 pl-8 pr-3 text-sm text-white placeholder-gray-500 outline-none transition"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <span class="absolute left-2.5 top-2.5 text-gray-500 text-xs">🔍</span>
            </div>
        </div>

        {{-- ✅ PESSOAS ONLINE — só desktop --}}
        @php
            $authId = auth()->id();
            $onlineUsers = \App\Models\User::whereNotNull('last_seen')
                ->where('last_seen', '>=', now()->subMinutes(5))
                ->where('id', '!=', $authId)
                ->where(function ($q) use ($authId) {
                    $q->whereIn('id', function ($sub) use ($authId) {
                        $sub->select('seguido_id')->from('seguidores')->where('seguidor_id', $authId);
                    })->orWhereIn('id', function ($sub) use ($authId) {
                        $sub->select('seguidor_id')->from('seguidores')->where('seguido_id', $authId);
                    });
                })
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
                     wire:click="{{ $convExistente ? 'onChatListSelected('.$convExistente->id.')' : 'onlineUserClicked('.$ou->id.')' }}"
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

        {{-- Lista de conversas — vem do componente ChatList --}}
        <div class="flex-1 overflow-y-auto py-2"
             style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">
            @livewire('messages.chat-list', ['selectedConversationId' => $selectedConversationId])
        </div>
    </aside>

    {{-- ÁREA DO CHAT --}}
    <main id="msg-chat" class="flex-1 flex-col" style="min-width: 0; overflow: hidden;"
          data-conversation-id="{{ $selectedConversation->id ?? '' }}">

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
    const prevBodyOverflow = document.body.style.overflow;
    const prevHtmlOverflow = document.documentElement.style.overflow;
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';

    window.addEventListener('beforeunload', function () {
        document.body.style.overflow = prevBodyOverflow;
        document.documentElement.style.overflow = prevHtmlOverflow;
    });

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

    function chatScrollToBottomSeJaPerto() {
        const c = document.getElementById('chat-content');
        if (!c) return;
        const distanciaAoFundo = c.scrollHeight - c.scrollTop - c.clientHeight;
        if (distanciaAoFundo < 150) c.scrollTop = c.scrollHeight;
    }

    function chatMostrarEstadoEnvio(aEnviar) {
        const btn = document.getElementById('send-btn');
        const icon = document.getElementById('send-icon');
        const spinner = document.getElementById('send-spinner');
        if (!btn) return;
        btn.style.pointerEvents = aEnviar ? 'none' : '';
        btn.style.opacity = aEnviar ? '.7' : '';
        if (icon) icon.style.display = aEnviar ? 'none' : '';
        if (spinner) spinner.style.display = aEnviar ? 'block' : 'none';
    }

    function chatMostrarErroEnvio() {
        let toast = document.getElementById('chat-send-error-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'chat-send-error-toast';
            toast.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#7f1d1d;color:#fff;padding:10px 18px;border-radius:14px;font-size:12px;font-weight:700;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.4);border:1px solid rgba(248,113,113,.4);';
            document.body.appendChild(toast);
        }
        toast.textContent = '⚠️ Não foi possível enviar — o texto continua na caixa, tenta outra vez';
        clearTimeout(window.__chatErrorToastTimeout);
        window.__chatErrorToastTimeout = setTimeout(() => toast.remove(), 3500);
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
        chatMostrarEstadoEnvio(true);

        try {
            await component.call('sendMessage', val);
        } catch (err) {
            console.error('[chat] falhou o envio da mensagem:', err);
            chatMostrarErroEnvio();
        } finally {
            sending = false;
            chatMostrarEstadoEnvio(false);

            const inputAtual = document.getElementById('message-input');
            if (inputAtual) {
                inputAtual.value = '';
                chatAutoResize(inputAtual);
                inputAtual.focus();
                requestAnimationFrame(() => inputAtual.focus());
                setTimeout(() => { inputAtual.focus(); chatScrollToBottom(); }, 60);
                setTimeout(() => inputAtual.focus(), 250);
            } else {
                chatScrollToBottom();
            }
        }
    }

    document.addEventListener('mousedown', function (e) {
        if (e.target.closest('#send-btn')) {
                e.preventDefault();
            }
        });

    document.addEventListener('click', function (e) {
        if (e.target.closest('#send-btn')) {
            e.preventDefault();
            chatHandleSend();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.target && e.target.id === 'message-input' && e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatHandleSend();
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'message-input') {
            chatAutoResize(e.target);
        }
    });

        // ✅ Pesquisa de conversas — filtra na hora, sem ir ao servidor
    function aplicarFiltroConversas() {
        const input = document.getElementById('chat-search-input');
        if (!input) return;
        const termo = input.value.trim().toLowerCase();
        document.querySelectorAll('#msg-sidebar [data-nome]').forEach(function (item) {
            const nome = item.getAttribute('data-nome') || '';
            item.style.display = (termo === '' || nome.includes(termo)) ? '' : 'none';
        });
    }

    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'chat-search-input') {
            aplicarFiltroConversas();
        }
    });

    // ✅ Eliminar conversa — feedback instantâneo (esconde já), sem esperar o servidor
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.chat-delete-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();

        if (!confirm('Tens a certeza que queres eliminar esta conversa?')) return;

        const convId = btn.getAttribute('data-conv-id');
        const row = btn.closest('[data-nome]');

        if (row) {
            row.style.transition = 'opacity .15s, transform .15s';
            row.style.opacity = '0';
            row.style.transform = 'scale(0.96)';
            setTimeout(() => { if (row) row.style.display = 'none'; }, 150);
        }

        const component = chatFindComponent(btn);
        if (component) {
            component.call('requestDelete', convId).catch(() => {
                if (row) { row.style.display = ''; row.style.opacity = ''; row.style.transform = ''; }
                alert('Não foi possível eliminar a conversa. Tenta outra vez.');
            });
        }
    });

    document.addEventListener('livewire:updated', () => {
        setTimeout(chatScrollToBottomSeJaPerto, 50);
        aplicarFiltroConversas(); // ✅ reaplica o filtro depois de qualquer atualização da lista
    });
    window.addEventListener('scroll-down', () => setTimeout(chatScrollToBottom, 100));

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
                container.innerHTML = '<div style="padding:16px;font-size:12px;color:#fca5a5;text-align:center;">📡 Emojis indisponíveis agora (sem internet ou o serviço está em baixo). Podes continuar a escrever normalmente.</div>';
            }
        }
    });

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

    if (window.visualViewport) {
        const msgContainer = document.getElementById('msg-container');
        function ajustarAlturaTeclado() {
            if (msgContainer && msgContainer.classList.contains('show-chat') && window.innerWidth < 768) {
                msgContainer.style.height = window.visualViewport.height + 'px';
            }
        }
        window.visualViewport.addEventListener('resize', ajustarAlturaTeclado);
        window.visualViewport.addEventListener('scroll', ajustarAlturaTeclado);
    }
})();
</script>

</div>