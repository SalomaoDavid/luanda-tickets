<?php

namespace App\Notifications;

use App\Models\PostagemComentario;
use App\Models\Postagem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PostagemComentarioNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public PostagemComentario $comentario,
        public Postagem $postagem
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comentario_id'   => $this->comentario->id,
            'user_id'         => $this->comentario->user_id,
            'user_name'       => $this->comentario->user->name,
            'user_photo'      => $this->comentario->user->avatar_url ?? null,
            'postagem_id'     => $this->postagem->id,
            'postagem_preview'=> str($this->postagem->conteudo)->limit(50),
            'preview'         => str($this->comentario->corpo)->limit(60),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}