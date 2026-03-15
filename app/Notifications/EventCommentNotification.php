<?php

namespace App\Notifications;

use App\Models\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventCommentNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Comentario $comentario) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comentario_id' => $this->comentario->id,
            'user_id'       => $this->comentario->user_id,
            'user_name'     => $this->comentario->user->name,
            'user_photo'    => $this->comentario->user->avatar_url ?? null,
            'evento_id'     => $this->comentario->evento_id,
            'evento_titulo' => $this->comentario->evento->titulo,
            'preview'       => str($this->comentario->corpo)->limit(60),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}