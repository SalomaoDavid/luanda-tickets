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
            <div style="border-bottom: 1px solid rgba(59,130,246,0.1);">
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
             style="min-height: 0; "scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">
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

{{-- ✅ MODAL DE CONFIRMAÇÃO PERSONALIZADO — substitui o confirm()/alert()
     feio e padrão do navegador em todo o módulo de mensagens. Chama-se com
     window.confirmarAcao('texto').then(ok => { ... }) de qualquer sítio
     (Alpine @click, ou JS simples). Para um simples aviso (sem botão
     cancelar), passa { somenteAviso: true } como segundo argumento. --}}
{{-- ⚠️ CORRIGIDO — antes usava x-show, que ao "mostrar de novo" repunha
     display:'' (bloco), perdendo o display:flex necessário para centrar
     o popup. Agora o display é sempre calculado diretamente via :style a
     partir de "open" — nunca depende do que o Alpine decidir repor — e o
     style INICIAL já começa em "display:none" (não só via x-cloak), para
     nunca aparecer a cobrir o ecrã nem por uma fração de segundo antes do
     Alpine arrancar. Sem isto: (a) um "flash" de ecrã inteiro ao abrir a
     lista de conversas, e (b) um toque podia atravessar para a conversa
     por baixo, abrindo-a sem querer ao mesmo tempo que o pedido de
     apagar. --}}
<div x-data="{ open: false, mensagem: '', somenteAviso: false, resolve: null }"
     x-cloak
     @confirm-ask.window="
        open = true;
        mensagem = $event.detail.mensagem;
        somenteAviso = $event.detail.somenteAviso;
        resolve = $event.detail.resolve;
     "
     style="display:none; position: fixed; inset: 0; z-index: 99999; align-items:center; justify-content:center;
            background: rgba(2,6,23,0.6); backdrop-filter: blur(4px); padding: 20px;"
     :style="{ display: open ? 'flex' : 'none' }">
    <div @click.outside="open = false; somenteAviso ? null : resolve(false)"
         style="width: 100%; max-width: 340px; background: #0f172a; border: 1px solid rgba(59,130,246,0.25);
                border-radius: 20px; padding: 22px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
        <p style="color: #e2e8f0; font-size: 14px; line-height: 1.5; text-align: center; margin-bottom: 18px;"
           x-text="mensagem"></p>
        <div style="display:flex; gap:10px;">
            <template x-if="!somenteAviso">
                <button type="button"
                        @click="open = false; resolve(false)"
                        style="flex:1; padding:10px; border-radius:12px; font-size:13px; font-weight:700;
                               background: rgba(148,163,184,.12); color:#cbd5e1; border:1px solid rgba(148,163,184,.2);">
                    Cancelar
                </button>
            </template>
            <button type="button"
                    @click="open = false; if (!somenteAviso) resolve(true)"
                    style="flex:1; padding:10px; border-radius:12px; font-size:13px; font-weight:700;
                           background: linear-gradient(135deg, #dc2626, #b91c1c); color:#fff; border:none;">
                <span x-text="somenteAviso ? 'Ok' : 'Confirmar'"></span>
            </button>
        </div>
    </div>
</div>

<script>
    // ✅ Helper global — devolve uma Promise<boolean>. Usa-se assim:
    // window.confirmarAcao('Apagar isto?').then(ok => { if (ok) { ... } });
    // Para um aviso simples (sem escolha), passa { somenteAviso: true }.
    window.confirmarAcao = function (mensagem, opcoes) {
        opcoes = opcoes || {};
        return new Promise(function (resolve) {
            window.dispatchEvent(new CustomEvent('confirm-ask', {
                detail: {
                    mensagem: mensagem,
                    somenteAviso: !!opcoes.somenteAviso,
                    resolve: resolve
                }
            }));
        });
    };
</script>

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
    // Interruptor "Enviar como Comunicado Oficial" (só existe para admin).
    // Delegado porque o botão é recriado a cada troca de conversa.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('#aviso-toggle');
        if (!btn) return;
        const ativo = btn.dataset.active === 'true';
        btn.dataset.active = ativo ? 'false' : 'true';
        btn.style.background = ativo ? 'rgba(148,163,184,.12)' : 'rgba(34,211,238,.15)';
        btn.style.color = ativo ? '#94a3b8' : '#22d3ee';
        btn.style.borderColor = ativo ? 'rgba(148,163,184,.25)' : 'rgba(34,211,238,.4)';
        const dot = document.getElementById('aviso-toggle-dot');
        if (dot) dot.style.background = ativo ? '#64748b' : '#22d3ee';
    });

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
            const toggleAviso = document.getElementById('aviso-toggle');
            const comoAviso = toggleAviso ? toggleAviso.dataset.active === 'true' : false;

            await component.call('sendMessage', val, comoAviso);

            // Reset do interruptor após enviar — evita mandar a próxima mensagem
            // normal sem querer marcada como oficial.
            if (toggleAviso) {
                toggleAviso.dataset.active = 'false';
                toggleAviso.style.background = 'rgba(148,163,184,.12)';
                toggleAviso.style.color = '#94a3b8';
                toggleAviso.style.borderColor = 'rgba(148,163,184,.25)';
                const dot = document.getElementById('aviso-toggle-dot');
                if (dot) dot.style.background = '#64748b';
            }

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

    // ✅ Eliminar conversa — feedback instantâneo (esconde já), sem esperar o
    // servidor. Confirmação personalizada (window.confirmarAcao) em vez do
    // confirm()/alert() nativo do navegador.
    //
    // ⚠️ CORRIGIDO — este handler estava no "document" na fase normal
    // (bubbling), que só dispara DEPOIS de o clique já ter passado pela
    // linha da conversa (que tem wire:click="selectConversation" ligado
    // diretamente a ela). Por isso a conversa abria-se sozinha ao clicar
    // no balde, mesmo com stopPropagation() — chegava tarde demais. Ao
    // registar com "true" no fim (fase de CAPTURA), este código corre
    // ANTES do clique sequer chegar à linha, por isso o stopPropagation()
    // aqui impede mesmo o clique de lá chegar.
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.chat-delete-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        window.confirmarAcao('Tens a certeza que queres eliminar esta conversa?').then(function (ok) {
            if (!ok) return;

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
                    window.confirmarAcao('Não foi possível eliminar a conversa. Tenta outra vez.', { somenteAviso: true });
                });
            }
        });
    }, true); // ← fase de captura, de propósito (ver comentário acima)

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
                // ✅ CORRIGIDO — ao entrar numa conversa (nova ou reaberta),
                // limpa qualquer altura presa de uma leitura anterior do
                // visualViewport (ver ajustarAlturaTeclado abaixo), para o
                // CSS normal (100dvh) assumir de novo. Sem isto, entrar
                // numa conversa vinda de fora da SPA (ex: eventos-detalhes)
                // podia herdar uma altura pequena de mais, empurrando o
                // rodapé (caixa de escrever) para fora da área visível,
                // mesmo continuando presente no HTML.
                // ⚠️ CORRIGIDO — "" (vazio) apagava também o "height:100%"
                // original que vem do Blade, não só uma altura presa do
                // teclado, e o container encolhia para o tamanho do
                // conteúdo por um instante (o "quebra/reduz" que viste).
                // Repor explicitamente "100%" restaura sempre o valor
                // certo, nunca um vazio.
                const msgContainerReset = document.getElementById('msg-container');
                if (msgContainerReset) msgContainerReset.style.height = '100%';
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
        // Altura "cheia" conhecida, sem teclado — serve de referência para
        // só encolher o contentor quando o teclado REALMENTE abrir, nunca
        // por uma leitura transitória a mais pequena (ex: barra de endereço
        // do telemóvel ainda a assentar logo após navegar de outra página).
        let alturaCheia = window.visualViewport.height;

        function ajustarAlturaTeclado() {
            if (!msgContainer || !msgContainer.classList.contains('show-chat') || window.innerWidth >= 768) {
                return;
            }

            const alturaAtual = window.visualViewport.height;

            // Se a altura atual for maior ou igual à maior já vista,
            // atualiza a referência e NÃO fixa altura nenhuma — deixa o
            // CSS (100dvh) tratar disto normalmente.
            if (alturaAtual >= alturaCheia) {
                alturaCheia = alturaAtual;
                msgContainer.style.height = '100%';
                return;
            }

            // Só aqui, com a altura claramente mais pequena que o máximo
            // visto (teclado a tapar parte do ecrã), é que encolhemos o
            // contentor de propósito.
            msgContainer.style.height = alturaAtual + 'px';
        }

        window.visualViewport.addEventListener('resize', ajustarAlturaTeclado);
        window.visualViewport.addEventListener('scroll', ajustarAlturaTeclado);
    }
})();
</script>

</div>