@extends('layouts.app')
@section('title', 'Notificações')
@section('content')

<style>
.notif-page{max-width:600px;margin:0 auto;padding:16px 8px 80px;}
@@media(min-width:768px){.notif-page{padding:0 0 60px;}}

.notif-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
.notif-page-title{font-size:18px;font-weight:800;color:#f0f6ff;}
.notif-mark-all{font-size:12px;font-weight:600;color:#a78bfa;background:rgba(167,139,250,.1);border:1px solid rgba(167,139,250,.2);padding:6px 14px;border-radius:20px;cursor:pointer;transition:all .2s;text-decoration:none;}
.notif-mark-all:hover{background:rgba(167,139,250,.2);}

.notif-group-label{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#475569;margin:16px 0 8px;padding-left:4px;}

.notif-card{
    display:flex;align-items:flex-start;gap:12px;
    padding:14px 16px;border-radius:16px;margin-bottom:6px;
    background:rgba(15,23,42,0.9);border:1px solid rgba(59,130,246,.1);
    text-decoration:none;transition:all .2s;cursor:pointer;
}
.notif-card:hover{background:rgba(15,23,42,1);border-color:rgba(59,130,246,.25);transform:translateX(2px);}
.notif-card.nao-lida{background:rgba(124,58,237,.08);border-color:rgba(124,58,237,.2);}
.notif-card.nao-lida:hover{background:rgba(124,58,237,.12);}

.notif-avatar-wrap{position:relative;flex-shrink:0;}
.notif-avatar{width:46px;height:46px;border-radius:50%;object-fit:cover;border:2px solid rgba(59,130,246,.3);}
.notif-avatar-icon{
    position:absolute;bottom:-2px;right:-2px;
    width:20px;height:20px;border-radius:50%;
    border:2px solid #0f172a;
    display:flex;align-items:center;justify-content:center;
    font-size:11px;
}
.notif-avatar-icon.msg    {background:#3b82f6;}
.notif-avatar-icon.like   {background:#2563eb;}
.notif-avatar-icon.comment{background:#7c3aed;}
.notif-avatar-icon.ticket {background:#10b981;}
.notif-avatar-icon.default{background:#64748b;}

.notif-body{flex:1;min-width:0;}
.notif-text{font-size:13px;color:#cbd5e1;line-height:1.5;}
.notif-text strong{color:#f0f6ff;font-weight:700;}
.notif-preview{font-size:11px;color:#64748b;margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.notif-evento{font-size:11px;color:#a78bfa;margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.notif-time{font-size:10px;color:#475569;margin-top:5px;}

.notif-dot{width:8px;height:8px;border-radius:50%;background:#7c3aed;flex-shrink:0;margin-top:6px;}

.notif-empty{text-align:center;padding:60px 20px;color:#475569;}
.notif-empty-icon{font-size:48px;margin-bottom:12px;}
.notif-empty-title{font-size:15px;font-weight:700;color:#94a3b8;margin-bottom:6px;}
.notif-empty-sub{font-size:13px;}
</style>

<div class="notif-page">

    {{-- CABEÇALHO --}}
    <div class="notif-page-head">
        <div class="notif-page-title">🔔 Notificações</div>
        @if($totalNaoLidas > 0)
        <form method="POST" action="{{ route('notificacoes.marcarTodas') }}">
            @csrf
            <button type="submit" class="notif-mark-all">✓ Marcar todas como lidas</button>
        </form>
        @endif
    </div>

    @forelse($grupos as $grupo => $notificacoes)

    <div class="notif-group-label">{{ $grupo }}</div>

    @foreach($notificacoes as $notification)
    @php
        $data  = $notification->data;
        $tipo  = $notification->type;
        $lida  = !is_null($notification->read_at);

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

        $iconeClass = match($tipo) {
            'App\Notifications\NewMessageNotification'              => 'msg',
            'App\Notifications\EventLikedNotification'             => 'like',
            'App\Notifications\EventCommentNotification'           => 'comment',
            'App\Notifications\FollowedUserLikedEventNotification' => 'like',
            'App\Notifications\TicketPurchasedNotification'        => 'ticket',
            default => 'default',
        };

        $iconeEmoji = match($tipo) {
            'App\Notifications\NewMessageNotification'              => '💬',
            'App\Notifications\EventLikedNotification'             => '👍',
            'App\Notifications\EventCommentNotification'           => '💬',
            'App\Notifications\FollowedUserLikedEventNotification' => '❤️',
            'App\Notifications\TicketPurchasedNotification'        => '🎟',
            default => '🔔',
        };
    @endphp

    <a href="{{ $link }}"
       class="notif-card {{ $lida ? '' : 'nao-lida' }}"
       onclick="marcarLida('{{ $notification->id }}', this)">

        <div class="notif-avatar-wrap">
            <img src="{{ $fotoUrl }}" class="notif-avatar"
                 onerror="this.src='https://ui-avatars.com/api/?name=?&color=7F9CF5&background=EBF4FF'">
            <div class="notif-avatar-icon {{ $iconeClass }}">{{ $iconeEmoji }}</div>
        </div>

        <div class="notif-body">
            <div class="notif-text">
                @switch($tipo)
                    @case('App\Notifications\NewMessageNotification')
                        <strong>{{ $data['sender_name'] ?? '' }}</strong> enviou-te uma mensagem
                        @if(!empty($data['preview']))
                        <div class="notif-preview">{{ $data['preview'] }}</div>
                        @endif
                        @break
                    @case('App\Notifications\EventLikedNotification')
                        <strong>{{ $data['user_name'] ?? '' }}</strong> curtiu o teu evento
                        <div class="notif-evento">{{ $data['evento_titulo'] ?? '' }}</div>
                        @break
                    @case('App\Notifications\EventCommentNotification')
                        <strong>{{ $data['user_name'] ?? '' }}</strong> comentou no teu evento
                        <div class="notif-evento">{{ $data['evento_titulo'] ?? '' }}</div>
                        @if(!empty($data['preview']))
                        <div class="notif-preview">{{ $data['preview'] }}</div>
                        @endif
                        @break
                    @case('App\Notifications\FollowedUserLikedEventNotification')
                        <strong>{{ $data['user_name'] ?? '' }}</strong> curtiu o evento
                        <div class="notif-evento">{{ $data['evento_titulo'] ?? '' }}</div>
                        @break
                    @case('App\Notifications\TicketPurchasedNotification')
                        <strong>{{ $data['comprador_nome'] ?? '' }}</strong>
                        comprou {{ $data['quantidade'] ?? 1 }} bilhete{{ ($data['quantidade'] ?? 1) > 1 ? 's' : '' }}
                        <div class="notif-evento">{{ $data['evento_titulo'] ?? '' }}</div>
                        @break
                    @default
                        <span>Nova notificação</span>
                @endswitch
            </div>
            <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
        </div>

        @if(!$lida)
        <div class="notif-dot"></div>
        @endif
    </a>
    @endforeach

    @empty
    <div class="notif-empty">
        <div class="notif-empty-icon">🔔</div>
        <div class="notif-empty-title">Sem notificações</div>
        <div class="notif-empty-sub">Quando alguém interagir contigo aparecerá aqui.</div>
    </div>
    @endforelse

</div>

<script>
async function marcarLida(id, el) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        await fetch(`/notificacoes/${id}/lida`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }
        });
        // Remove o estilo de não lida visualmente
        el.classList.remove('nao-lida');
        const dot = el.querySelector('.notif-dot');
        if (dot) dot.remove();
    } catch(e) {}
    // Deixa o link navegar normalmente
}
</script>
@endsection