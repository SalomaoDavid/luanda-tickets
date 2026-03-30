<div class="min-h-screen flex items-center justify-center p-2 md:p-4">

<style>
    #msg-sidebar { display: flex; }
    #msg-chat    { display: none; flex-direction: column; }
    @media(min-width:768px){
        #msg-sidebar { display: flex !important; width: 320px; }
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

<div id="msg-container"
     class="w-full max-w-5xl overflow-hidden flex"
     style="height: calc(100vh - 100px); min-height: 500px;
            background: rgba(15,23,42,0.85); backdrop-filter: blur(20px);
            border: 1px solid rgba(59,130,246,0.15); border-radius: 32px;">

    {{-- SIDEBAR --}}
    <aside id="msg-sidebar" class="flex-col flex-shrink-0 w-full md:w-80"
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

            <div class="flex items-center gap-3 px-4 py-3 cursor-pointer transition group"
                 style="{{ $isSelected
                    ? 'background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6;'
                    : 'border-left: 3px solid transparent;' }}"
                 wire:click="loadConversation({{ $conv->id }})"
                 onclick="if(window.innerWidth < 768) document.getElementById('msg-container').classList.add('show-chat')">

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
    <main id="msg-chat" class="flex-1 flex-col" style="min-width: 0; overflow: hidden;">

        {{-- Botão voltar mobile --}}
        <div class="flex md:hidden items-center px-4 py-2 border-b border-white/10 flex-shrink-0"
             style="background: rgba(15,23,42,0.95);">
            <button onclick="document.getElementById('msg-container').classList.remove('show-chat')"
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
    document.addEventListener('DOMContentLoaded', function() {
        @if($selectedConversation)
        if (window.innerWidth < 768) {
            document.getElementById('msg-container').classList.add('show-chat');
        }
        @endif
    });
</script>
</div>