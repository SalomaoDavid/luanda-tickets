<div>
    @forelse($conversations as $conv)
    @php
        $receiver       = $conv->getReceiver();
        $ultimaMensagem = $conv->messages->last();
        $isSelected     = $selectedConversationId && (int) $selectedConversationId === $conv->id;
    @endphp

    <div class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all duration-150 group active:scale-[0.98] active:bg-blue-500/10"
        data-nome="{{ Str::lower($receiver->name) }}"
        wire:loading.class="opacity-50"
        wire:target="selectConversation({{ $conv->id }})"
        style="{{ $isSelected
            ? 'background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6;'
            : 'border-left: 3px solid transparent;' }}"
        wire:click="selectConversation({{ $conv->id }})">

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
                    {{-- Botão eliminar — agora só avisa o MessagesIndex, que já sabia eliminar --}}
                    <button type="button"
                            class="chat-delete-btn text-gray-500 hover:text-red-400 transition text-xs md:opacity-0 md:group-hover:opacity-100"
                            data-conv-id="{{ $conv->id }}"
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
    @empty
    <div class="flex flex-col items-center justify-center py-10 text-gray-600">
        <p class="text-[10px] uppercase font-black tracking-widest">Nenhuma conversa ainda</p>
    </div>
    @endforelse
</div>