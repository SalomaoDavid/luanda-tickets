<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PostagemLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $reacaoId,
        public string $tipoReacao,
        public int    $userId,
        public string $userName,
        public ?string $userPhoto,
        public int    $postagemId,
        public string $postagemPreview,
    ) {}

    public static function fromReacao(\App\Models\PostagemReacao $reacao, \App\Models\Postagem $postagem): self
    {
        return new self(
            reacaoId:       $reacao->id,
            tipoReacao:     $reacao->tipo,
            userId:         $reacao->user_id,
            userName:       $reacao->user->name,
            userPhoto:      $reacao->user->avatar_url ?? null,
            postagemId:     $postagem->id,
            postagemPreview: str($postagem->conteudo)->limit(50),
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'reacao_id'        => $this->reacaoId,
            'tipo_reacao'      => $this->tipoReacao,
            'user_id'          => $this->userId,
            'user_name'        => $this->userName,
            'user_photo'       => $this->userPhoto,
            'postagem_id'      => $this->postagemId,
            'postagem_preview' => $this->postagemPreview,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}