<?php

namespace App\Notifications;

use App\Models\Curtida;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class FollowedUserLikedEventNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public Curtida $curtida,
        public User $quemCurtiu
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id'       => $this->quemCurtiu->id,
            'user_name'     => $this->quemCurtiu->name,
            'user_photo'    => $this->quemCurtiu->avatar_url ?? null,
            'evento_id'     => $this->curtida->evento_id,
            'evento_titulo' => $this->curtida->evento->titulo,
            'evento_capa'   => $this->curtida->evento->imagem_capa ?? null,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}