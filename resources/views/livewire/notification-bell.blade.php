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
         class="hidden fixed top-16 right-2 w-64 bg-gray-900 border border-gray-700 rounded-xl shadow-xl overflow-hidden"
         style="z-index: 9999;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-3 py-2 border-b border-gray-700">
            <span class="text-xs font-semibold text-white">Notificações</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] text-purple-400 hover:text-purple-300">
                    Marcar lidas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-gray-800">
            @forelse($notifications as $notification)
                @php
                    $data = $notification['data'];
                    $link = match($notification['type']) {
                        'App\Notifications\NewMessageNotification' => route('mensagens.index', ['conversation' => $data['conversation_id'] ?? '']),
                        default => '#',
                    };
                @endphp

                <a href="{{ $link }}"
                   wire:click="markAsRead('{{ $notification['id'] }}')"
                   class="flex items-start gap-2 px-3 py-2 cursor-pointer hover:bg-gray-800 transition {{ is_null($notification['read_at']) ? 'bg-gray-800/60' : '' }}">

                    <img src="{{ $data['user_photo'] ?? $data['sender_photo'] ?? $data['comprador_foto'] ?? 'https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF' }}"
                        class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5" />

                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-200 leading-snug">
                            @switch($notification['type'])
                                @case('App\Notifications\NewMessageNotification')
                                    <span class="font-semibold">{{ $data['sender_name'] }}</span> enviou-te uma mensagem
                                    @break
                                @case('App\Notifications\EventLikedNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> curtiu o teu evento
                                    <span class="text-purple-400 truncate block">{{ Str::limit($data['evento_titulo'], 20) }}</span>
                                    @break
                                @case('App\Notifications\EventCommentNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> comentou no teu evento
                                    <span class="text-purple-400 truncate block">{{ Str::limit($data['evento_titulo'], 20) }}</span>
                                    @break
                                @case('App\Notifications\TicketPurchasedNotification')
                                    <span class="font-semibold">{{ $data['comprador_nome'] }}</span> comprou bilhete
                                    <span class="text-purple-400 truncate block">{{ Str::limit($data['evento_titulo'], 20) }}</span>
                                    @break
                                @case('App\Notifications\FollowedUserLikedEventNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> curtiu
                                    <span class="text-purple-400 truncate block">{{ Str::limit($data['evento_titulo'], 20) }}</span>
                                    @break
                            @endswitch
                        </p>
                        <p class="text-[10px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</p>
                    </div>

                    @if(is_null($notification['read_at']))
                        <span class="w-1.5 h-1.5 bg-purple-500 rounded-full flex-shrink-0 mt-1"></span>
                    @endif
                </a>
            @empty
                <div class="px-3 py-6 text-center text-gray-500 text-xs">
                    Sem notificações
                </div>
            @endforelse
        </div>
    </div>
</div>