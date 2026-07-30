<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $comentarioId,
        public int    $userId,
        public string $userName,
        public ?string $userPhoto,
        public int    $eventoId,
        public string $eventoTitulo,
        public string $preview,
    ) {}

    public static function fromComentario(\App\Models\Comentario $comentario): self
    {
        return new self(
            comentarioId: $comentario->id,
            userId:       $comentario->user_id,
            userName:     $comentario->user->name,
            userPhoto:    $comentario->user->avatar_url ?? null,
            eventoId:     $comentario->evento_id,
            eventoTitulo: $comentario->evento->titulo,
            preview:      str($comentario->corpo)->limit(60),
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comentario_id' => $this->comentarioId,
            'user_id'       => $this->userId,
            'user_name'     => $this->userName,
            'user_photo'    => $this->userPhoto,
            'evento_id'     => $this->eventoId,
            'evento_titulo' => $this->eventoTitulo,
            'preview'       => $this->preview,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}