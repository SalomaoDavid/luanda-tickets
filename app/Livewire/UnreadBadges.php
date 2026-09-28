<?php

namespace App\Livewire;

use Livewire\Component;

class UnreadBadges extends Component
{
    // Qual dos 3 selos é este ('msg-desktop', 'msg-mobile', 'avatar-dot')
    public string $type;

    // ✅ Escuta o MESMO aviso que o ChatBox já dispara hoje
    // (markAsRead() e sendMessage() já fazem $this->dispatch('refresh-list'))
    // — não precisei de acrescentar nenhum aviso novo em lado nenhum.
    protected $listeners = [
        'refresh-list' => '$refresh',
    ];

    public function mount(string $type)
    {
        $this->type = $type;
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->type === 'avatar-dot') {
            $count = $user->unreadNotifications->count();
        } else {
            // Mesma lógica que já existia — só corrigi a inconsistência de
            // nomes de notificação que encontrei: os selos de mensagens só
            // verificavam 'NovaMensagem', mas o ChatBox cria as notificações
            // reais como 'NewMessageNotification'. O botão flutuante já
            // verificava os dois nomes corretamente — agora aqui também.
            $count = $user->unreadNotifications->whereIn('type', [
                'App\Notifications\NewMessageNotification',
                'App\Notifications\NovaMensagem',
            ])->count();
        }

        return view('livewire.unread-badges', [
            'count' => $count,
        ]);
    }
}