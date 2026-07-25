<div class="relative" x-data @click.stop>

    {{-- Botão sino — só Livewire, sem JS duplicado --}}
    <button wire:click="toggleOpen" class="relative p-1.5 text-gray-400 hover:text-white transition focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-purple-600 rounded-full">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown — controlado APENAS pelo Livewire $open --}}
    @if($open)
    <div class="fixed top-16 right-2 w-80 bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl overflow-hidden"
         style="z-index:9999;"
         x-on:click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700/60">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-white">🔔 Notificações</span>
                @if($unreadCount > 0)
                <span class="text-[10px] font-bold text-white bg-purple-600 px-2 py-0.5 rounded-full">
                    {{ $unreadCount }}
                </span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                        class="text-[11px] text-purple-400 hover:text-purple-300 font-semibold transition">
                    ✓ Marcar todas lidas
                </button>
                @endif
                {{-- Botão fechar --}}
                <button wire:click="toggleOpen"
                        class="text-gray-500 hover:text-white text-lg font-bold leading-none transition">
                    ✕
                </button>
            </div>
        </div>

        {{-- Lista --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-800/60 scrollbar-thin">
            @forelse($notifications as $notification)
            @php
                $data = $notification['data'];
                $tipo = $notification['type'];
                $lida = !is_null($notification['read_at']);

                $link = match($tipo) {
                    'App\Notifications\NewMessageNotification'
                        => route('mensagens.index', ['conversation' => $data['conversation_id'] ?? '']),
                    'App\Notifications\EventLikedNotification'
                        => isset($data['evento_id']) ? route('evento.detalhes', $data['evento_id']) : '#',
                    'App\Notifications\EventCommentNotification'
                        => isset($data['evento_id'])
                            ? route('evento.detalhes', $data['evento_id']).(isset($data['comentario_id']) ? '#comentario-'.$data['comentario_id'] : '')
                            : '#',
                    'App\Notifications\FollowedUserLikedEventNotification'
                        => isset($data['evento_id']) ? route('evento.detalhes', $data['evento_id']) : '#',
                    'App\Notifications\TicketPurchasedNotification'
                        => isset($data['evento_id']) ? route('evento.detalhes', $data['evento_id']) : '#',
                    default => '#',
                };

                $foto = $data['user_photo'] ?? $data['sender_photo'] ?? $data['comprador_foto'] ?? null;
                $fotoUrl = $foto
                    ? (str_starts_with($foto, 'http') ? $foto : asset('storage/'.$foto))
                    : 'https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF';

                $icone = match($tipo) {
                    'App\Notifications\NewMessageNotification'         => '💬',
                    'App\Notifications\EventLikedNotification'         => '👍',
                    'App\Notifications\EventCommentNotification'        => '💬',
                    'App\Notifications\FollowedUserLikedEventNotification' => '❤️',
                    'App\Notifications\TicketPurchasedNotification'    => '🎟',
                    default => '🔔',
                };
            @endphp

            {{-- Ao clicar numa notificação: marca como lida E redireciona --}}
            <a href="{{ $link }}"
               wire:click="markAsRead('{{ $notification['id'] }}')"
               class="flex items-start gap-3 px-4 py-3 transition cursor-pointer
                      {{ $lida ? 'hover:bg-gray-800/40' : 'bg-gray-800/60 hover:bg-gray-800' }}">

                {{-- Avatar com ícone do tipo --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ $fotoUrl }}"
                         class="w-10 h-10 rounded-full object-cover border border-gray-700"
                         onerror="this.src='https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF'">
                    <span class="absolute -bottom-1 -right-1 text-[12px] leading-none">{{ $icone }}</span>
                </div>

                {{-- Conteúdo --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-200 leading-snug">
                        @switch($tipo)
                            @case('App\Notifications\NewMessageNotification')
                                <span class="font-bold text-white">{{ $data['sender_name'] ?? '' }}</span>
                                enviou-te uma mensagem
                                @if(!empty($data['preview']))
                                <span class="block text-gray-400 truncate mt-0.5 text-[11px]">{{ $data['preview'] }}</span>
                                @endif
                                @break
                            @case('App\Notifications\EventLikedNotification')
                                <span class="font-bold text-white">{{ $data['user_name'] ?? '' }}</span>
                                curtiu o teu evento
                                <span class="block text-purple-400 truncate mt-0.5 text-[11px]">{{ Str::limit($data['evento_titulo'] ?? '', 30) }}</span>
                                @break
                            @case('App\Notifications\EventCommentNotification')
                                <span class="font-bold text-white">{{ $data['user_name'] ?? '' }}</span>
                                comentou no teu evento
                                <span class="block text-purple-400 truncate mt-0.5 text-[11px]">{{ Str::limit($data['evento_titulo'] ?? '', 30) }}</span>
                                @if(!empty($data['preview']))
                                <span class="block text-gray-500 truncate text-[10px]">{{ $data['preview'] }}</span>
                                @endif
                                @break
                            @case('App\Notifications\FollowedUserLikedEventNotification')
                                <span class="font-bold text-white">{{ $data['user_name'] ?? '' }}</span>
                                curtiu o evento
                                <span class="block text-purple-400 truncate mt-0.5 text-[11px]">{{ Str::limit($data['evento_titulo'] ?? '', 30) }}</span>
                                @break
                            @case('App\Notifications\TicketPurchasedNotification')
                                <span class="font-bold text-white">{{ $data['comprador_nome'] ?? '' }}</span>
                                comprou {{ $data['quantidade'] ?? 1 }} bilhete{{ ($data['quantidade'] ?? 1) > 1 ? 's' : '' }}
                                <span class="block text-purple-400 truncate mt-0.5 text-[11px]">{{ Str::limit($data['evento_titulo'] ?? '', 30) }}</span>
                                @break
                            @case('App\\Notifications\\PostagemLikedNotification')
                                <span class="font-bold text-white">{{ $data['user_name'] ?? '' }}</span>
                                {{ $data['tipo_reacao'] === 'adoro' ? 'adorou' : 'curtiu' }} a tua publicação
                                @if(!empty($data['postagem_preview']))
                                <span class="block text-gray-400 truncate mt-0.5 text-[11px]">{{ $data['postagem_preview'] }}</span>
                                @endif
                                @break
                            @case('App\\Notifications\\PostagemComentarioNotification')
                                <span class="font-bold text-white">{{ $data['user_name'] ?? '' }}</span>
                                comentou na tua publicação
                                @if(!empty($data['preview']))
                                <span class="block text-gray-400 truncate mt-0.5 text-[11px]">{{ $data['preview'] }}</span>
                                @endif
                                @break
                            @case('App\\Notifications\\NovoEventoCriadoNotification')
                                <span class="font-bold text-white">{{ $data['criador_nome'] ?? '' }}</span>
                                criou um novo evento
                                <span class="block text-purple-400 truncate mt-0.5 text-[11px]">{{ $data['evento_titulo'] ?? '' }}</span>
                                @break
                            @default
                                <span class="text-gray-400">Nova notificação</span>
                        @endswitch
                    </p>
                    <p class="text-[10px] text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                    </p>
                </div>

                {{-- Ponto azul se não lida --}}
                @if(!$lida)
                <span class="w-2 h-2 bg-purple-500 rounded-full flex-shrink-0 mt-2"></span>
                @endif
            </a>
            @empty
            <div class="px-4 py-10 text-center text-gray-500">
                <div class="text-3xl mb-3">🔔</div>
                <p class="text-xs font-semibold">Sem notificações por enquanto</p>
            </div>
            @endforelse
        </div>

        {{-- Footer com link para página completa --}}
        <div class="px-4 py-3 border-t border-gray-700/60 text-center">
            <a href="{{ route('notificacoes.index') }}"
               wire:click="toggleOpen"
               class="text-[11px] font-semibold text-purple-400 hover:text-purple-300 transition">
                Ver todas as notificações →
            </a>
        </div>
    </div>
    @endif
</div>