<div class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-5xl overflow-hidden flex"
     style="height: 700px; background: rgba(15,23,42,0.85); backdrop-filter: blur(20px);
            border: 1px solid rgba(59,130,246,0.15); border-radius: 32px;">

    {{-- SIDEBAR: Lista de conversas --}}
    <aside class="w-80 flex flex-col flex-shrink-0"
           style="border-right: 1px solid rgba(59,130,246,0.15);">

        {{-- Header --}}
        <div class="p-5" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
            <h1 class="text-lg font-black text-white mb-4">💬 Mensagens</h1>
            <div class="relative">
                <input type="text"
                       placeholder="Pesquisar conversa..."
                       class="w-full rounded-xl py-2.5 pl-9 pr-4 text-sm text-white placeholder-gray-500 outline-none transition"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <span class="absolute left-3 top-2.5 text-gray-500 text-sm">🔍</span>
            </div>
        </div>

        {{-- Lista de conversas --}}
        <div class="flex-1 overflow-y-auto py-2"
             style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

            @foreach($conversations as $conv)
            @php
                $receiver = $conv->getReceiver();
                $ultimaMensagem = $conv->messages->last();
                $isSelected = $selectedConversation && $selectedConversation->id === $conv->id;
            @endphp

            <div class="flex items-center gap-3 px-4 py-3 cursor-pointer transition group"
                 style="{{ $isSelected
                    ? 'background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6;'
                    : 'border-left: 3px solid transparent;' }}"
                 wire:click="loadConversation({{ $conv->id }})">

                <div class="relative flex-shrink-0">
                    <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
                         class="w-12 h-12 rounded-full object-cover border-2 {{ $isSelected ? 'border-blue-400' : 'border-transparent' }}">
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

                            {{-- Botão eliminar conversa --}}
                            <button wire:click.stop="deleteConversation({{ $conv->id }})"
                                    wire:confirm="Tens a certeza que queres eliminar esta conversa?"
                                    class="text-gray-600 hover:text-red-400 transition opacity-0 group-hover:opacity-100 text-xs"
                                    title="Eliminar conversa">
                                🗑
                            </button>
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
    <main class="flex-1 flex flex-col">
        @if($selectedConversation)
            @livewire('messages.chat-box',
                ['conversation' => $selectedConversation],
                key('chat-box-' . $selectedConversation->id)
            )
        @else
            <div class="flex-1 flex flex-col items-center justify-center"
                 style="color: rgba(148,163,184,0.3);">
                <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="font-black uppercase text-xs tracking-[0.2em]">Seleciona uma conversa</p>
            </div>
        @endif
    </main>

</div>
</div>