<?php

namespace App\Notifications;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NovoEventoCriadoNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Evento $evento,
        public User $criador
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'evento_id'      => $this->evento->id,
            'evento_titulo'  => $this->evento->titulo,
            'evento_status'  => $this->evento->status,
            'criador_id'     => $this->criador->id,
            'criador_nome'   => $this->criador->name,
            'user_photo'     => $this->criador->avatar_url ?? null,
            'data_evento'    => $this->evento->data_evento,
            'localizacao'    => $this->evento->localizacao,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}