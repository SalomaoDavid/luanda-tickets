<div class="relative" x-data="{ open: @entangle('open') }">

    {{-- Botão do sino --}}
    <button wire:click="toggleOpen" class="relative p-2 text-gray-400 hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-purple-600 rounded-full">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak @click.outside="open = false"
        class="absolute right-0 mt-2 w-80 bg-gray-900 border border-gray-700 rounded-xl shadow-xl z-50 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
            <span class="text-sm font-semibold text-white">Notificações</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-purple-400 hover:text-purple-300">
                    Marcar todas como lidas
                </button>
            @endif
        </div>

        {{-- Lista --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-800">
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
                   class="flex items-start gap-3 px-4 py-3 cursor-pointer hover:bg-gray-800 transition {{ is_null($notification['read_at']) ? 'bg-gray-800/60' : '' }}">

                    {{-- Avatar --}}
                    <img src="{{ $data['user_photo'] ?? $data['sender_photo'] ?? $data['comprador_foto'] ?? 'https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF' }}"
                        class="w-9 h-9 rounded-full object-cover flex-shrink-0" />

                    {{-- Texto --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-200 leading-snug">
                            @switch($notification['type'])
                                @case('App\Notifications\NewMessageNotification')
                                    <span class="font-semibold">{{ $data['sender_name'] }}</span> enviou-te uma mensagem
                                    @break
                                @case('App\Notifications\EventLikedNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> curtiu o teu evento
                                    <span class="text-purple-400">{{ $data['evento_titulo'] }}</span>
                                    @break
                                @case('App\Notifications\EventCommentNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> comentou no teu evento
                                    <span class="text-purple-400">{{ $data['evento_titulo'] }}</span>
                                    @break
                                @case('App\Notifications\TicketPurchasedNotification')
                                    <span class="font-semibold">{{ $data['comprador_nome'] }}</span> comprou bilhete para
                                    <span class="text-purple-400">{{ $data['evento_titulo'] }}</span>
                                    @break
                                @case('App\Notifications\FollowedUserLikedEventNotification')
                                    <span class="font-semibold">{{ $data['user_name'] }}</span> curtiu o evento
                                    <span class="text-purple-400">{{ $data['evento_titulo'] }}</span>
                                    @break
                            @endswitch
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</p>
                    </div>

                    {{-- Indicador não lido --}}
                    @if(is_null($notification['read_at']))
                        <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0 mt-1"></span>
                    @endif
                </a>
            @empty
                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                    Sem notificações
                </div>
            @endforelse
        </div>
    </div>
</div>