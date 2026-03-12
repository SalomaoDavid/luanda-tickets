<div class="flex flex-col h-full"
     style="background: rgba(15,23,42,0.55); backdrop-filter: blur(12px);">

    {{-- Header --}}
    <div class="p-5" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
        <h2 class="font-black text-white text-sm uppercase tracking-widest mb-3">💬 Mensagens</h2>
        <div class="relative">
            <input type="text"
                   placeholder="Pesquisar..."
                   class="w-full rounded-xl py-2 pl-8 pr-3 text-sm text-white placeholder-gray-500 outline-none"
                   style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
            <span class="absolute left-2.5 top-2 text-gray-500 text-xs">🔍</span>
        </div>
    </div>

    {{-- Lista --}}
    <div class="flex-1 overflow-y-auto py-2"
         style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

        @forelse($conversations as $conv)
        @php
            $outroParticipante = $conv->users->where('id', '!=', auth()->id())->first();
            $ultimaMensagem = $conv->messages->last();
        @endphp

        <div wire:click="selectConversation({{ $conv->id }})"
             class="flex items-center gap-3 px-4 py-3 cursor-pointer transition group"
             style="border-left: 3px solid transparent;"
             onmouseover="this.style.background='rgba(255,255,255,0.05)'; this.style.borderLeft='3px solid rgba(59,130,246,0.4)'"
             onmouseout="this.style.background='transparent'; this.style.borderLeft='3px solid transparent'">

            <div class="relative flex-shrink-0">
                <img src="{{ $outroParticipante && $outroParticipante->avatar
                    ? asset('storage/'.$outroParticipante->avatar)
                    : 'https://ui-avatars.com/api/?name='.urlencode($outroParticipante->name ?? 'U').'&background=0ea5e9&color=fff&size=64' }}"
                     class="w-11 h-11 rounded-full object-cover border-2 border-transparent">
                @if($outroParticipante)
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-[#020617]
                    {{ $outroParticipante->isOnline() ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold text-white text-sm truncate">
                        {{ $outroParticipante->name ?? 'Grupo' }}
                    </h3>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-gray-500 text-[10px]">
                            {{ $ultimaMensagem ? $ultimaMensagem->created_at->format('H:i') : '' }}
                        </span>
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
                        <span class="italic">Sem mensagens ainda</span>
                    @endif
                </p>
            </div>

        </div>

        @empty
        <div class="flex flex-col items-center justify-center py-10 text-gray-600">
            <p class="text-[10px] uppercase font-black tracking-widest">Nenhuma conversa</p>
        </div>
        @endforelse

    </div>
</div>