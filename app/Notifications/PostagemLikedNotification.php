<?php

namespace App\Notifications;

use App\Models\PostagemReacao;
use App\Models\Postagem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PostagemLikedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public PostagemReacao $reacao,
        public Postagem $postagem
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'reacao_id'        => $this->reacao->id,
            'tipo_reacao'      => $this->reacao->tipo,
            'user_id'          => $this->reacao->user_id,
            'user_name'        => $this->reacao->user->name,
            'user_photo'       => $this->reacao->user->avatar_url ?? null,
            'postagem_id'      => $this->postagem->id,
            'postagem_preview' => str($this->postagem->conteudo)->limit(50),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}