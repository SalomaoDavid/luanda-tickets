<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PostagemComentarioNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $comentarioId,
        public int    $userId,
        public string $userName,
        public ?string $userPhoto,
        public int    $postagemId,
        public string $postagemPreview,
        public string $preview,
    ) {}

    public static function fromComentario(\App\Models\PostagemComentario $comentario, \App\Models\Postagem $postagem): self
    {
        return new self(
            comentarioId:    $comentario->id,
            userId:          $comentario->user_id,
            userName:        $comentario->user->name,
            userPhoto:       $comentario->user->avatar_url ?? null,
            postagemId:      $postagem->id,
            postagemPreview: str($postagem->conteudo)->limit(50),
            preview:         str($comentario->corpo)->limit(60),
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comentario_id'    => $this->comentarioId,
            'user_id'          => $this->userId,
            'user_name'        => $this->userName,
            'user_photo'       => $this->userPhoto,
            'postagem_id'      => $this->postagemId,
            'postagem_preview' => $this->postagemPreview,
            'preview'          => $this->preview,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Novo comentário',
            'body'  => "{$this->userName} comentou na tua publicação: {$this->preview}",
            'url'   => \App\Support\NotificationLinks::postagem($this->postagemId),
            'icon'  => $this->userPhoto ? asset('storage/'.$this->userPhoto) : asset('logos.png'),
        ];
    }
}