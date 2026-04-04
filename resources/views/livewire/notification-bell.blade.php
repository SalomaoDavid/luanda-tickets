<div class="relative">

    {{-- Botão do sino --}}
    <button wire:click="toggleOpen" onclick="toggleNotifDropdown()" class="relative p-1.5 text-gray-400 hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-purple-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div id="notif-dropdown"
         class="hidden fixed top-16 right-2 w-72 bg-gray-900 border border-gray-700 rounded-xl shadow-xl overflow-hidden"
         style="z-index: 9999;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-3 py-2 border-b border-gray-700">
            <span class="text-xs font-semibold text-white">Notificações</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-purple-400 hover:text-purple-300">
                    Marcar todas lidas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-800">
            @forelse($notifications as $notification)
            @php
                $data = $notification['data'];
                $tipo = $notification['type'];

                // ✅ Link correcto para cada tipo de notificação
                $link = match($tipo) {
                    // Mensagem → abre a conversa directamente
                    'App\Notifications\NewMessageNotification'
                        => route('mensagens.index', ['conversation' => $data['conversation_id'] ?? '']),

                    // Curtiu o evento → vai ao evento
                    'App\Notifications\EventLikedNotification'
                        => isset($data['evento_id'])
                            ? route('evento.detalhes', $data['evento_id'])
                            : '#',

                    // Comentou no evento → vai ao evento (âncora ao comentário se tiver id)
                    'App\Notifications\EventCommentNotification'
                        => isset($data['evento_id'])
                            ? route('evento.detalhes', $data['evento_id']) . (isset($data['comentario_id']) ? '#comentario-' . $data['comentario_id'] : '')
                            : '#',

                    // Alguém que segues curtiu um evento → vai ao evento
                    'App\Notifications\FollowedUserLikedEventNotification'
                        => isset($data['evento_id'])
                            ? route('evento.detalhes', $data['evento_id'])
                            : '#',

                    // Comprou bilhete → vai ao evento
                    'App\Notifications\TicketPurchasedNotification'
                        => isset($data['evento_id'])
                            ? route('evento.detalhes', $data['evento_id'])
                            : '#',

                    default => '#',
                };

                // Foto do utilizador que gerou a notificação
                $foto = $data['user_photo']
                    ?? $data['sender_photo']
                    ?? $data['comprador_foto']
                    ?? null;

                $fotoUrl = $foto
                    ? (str_starts_with($foto, 'http') ? $foto : asset('storage/' . $foto))
                    : 'https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF';
            @endphp

            <a href="{{ $link }}"
               wire:click="markAsRead('{{ $notification['id'] }}')"
               class="flex items-start gap-2 px-3 py-2.5 hover:bg-gray-800 transition {{ is_null($notification['read_at']) ? 'bg-gray-800/60' : '' }}">

                <img src="{{ $fotoUrl }}"
                     class="w-8 h-8 rounded-full object-cover flex-shrink-0 mt-0.5"
                     onerror="this.src='https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF'">

                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-200 leading-snug">
                        @switch($tipo)
                            @case('App\Notifications\NewMessageNotification')
                                <span class="font-semibold text-white">{{ $data['sender_name'] ?? '' }}</span>
                                enviou-te uma mensagem
                                @if(!empty($data['preview']))
                                <span class="block text-gray-400 truncate mt-0.5">{{ $data['preview'] }}</span>
                                @endif
                                @break

                            @case('App\Notifications\EventLikedNotification')
                                <span class="font-semibold text-white">{{ $data['user_name'] ?? '' }}</span>
                                curtiu o teu evento
                                <span class="text-purple-400 truncate block mt-0.5">{{ Str::limit($data['evento_titulo'] ?? '', 28) }}</span>
                                @break

                            @case('App\Notifications\EventCommentNotification')
                                <span class="font-semibold text-white">{{ $data['user_name'] ?? '' }}</span>
                                comentou no teu evento
                                <span class="text-purple-400 truncate block mt-0.5">{{ Str::limit($data['evento_titulo'] ?? '', 28) }}</span>
                                @if(!empty($data['preview']))
                                <span class="block text-gray-500 truncate text-[10px]">{{ $data['preview'] }}</span>
                                @endif
                                @break

                            @case('App\Notifications\FollowedUserLikedEventNotification')
                                <span class="font-semibold text-white">{{ $data['user_name'] ?? '' }}</span>
                                curtiu o evento
                                <span class="text-purple-400 truncate block mt-0.5">{{ Str::limit($data['evento_titulo'] ?? '', 28) }}</span>
                                @break

                            @case('App\Notifications\TicketPurchasedNotification')
                                <span class="font-semibold text-white">{{ $data['comprador_nome'] ?? '' }}</span>
                                comprou {{ $data['quantidade'] ?? 1 }} bilhete{{ ($data['quantidade'] ?? 1) > 1 ? 's' : '' }}
                                <span class="text-purple-400 truncate block mt-0.5">{{ Str::limit($data['evento_titulo'] ?? '', 28) }}</span>
                                @break

                            @default
                                <span class="text-gray-400">Nova notificação</span>
                        @endswitch
                    </p>
                    <p class="text-[10px] text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                    </p>
                </div>

                @if(is_null($notification['read_at']))
                    <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0 mt-1.5"></span>
                @endif
            </a>
            @empty
                <div class="px-3 py-8 text-center text-gray-500 text-xs">
                    <div class="text-2xl mb-2">🔔</div>
                    Sem notificações por enquanto
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($notifications) > 0)
        <div class="px-3 py-2 border-t border-gray-700 text-center">
            <span class="text-[10px] text-gray-600">{{ count($notifications) }} notificação{{ count($notifications) !== 1 ? 'ões' : '' }}</span>
        </div>
        @endif
    </div>
</div>