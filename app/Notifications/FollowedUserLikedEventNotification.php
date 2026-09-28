<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class FollowedUserLikedEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $userId,
        public string $userName,
        public ?string $userPhoto,
        public int    $eventoId,
        public string $eventoTitulo,
        public ?string $eventoCapa,
    ) {}

    public static function fromCurtida(\App\Models\Curtida $curtida, \App\Models\User $quemCurtiu): self
    {
        return new self(
            userId:       $quemCurtiu->id,
            userName:     $quemCurtiu->name,
            userPhoto:    $quemCurtiu->avatar_url ?? null,
            eventoId:     $curtida->evento_id,
            eventoTitulo: $curtida->evento->titulo,
            eventoCapa:   $curtida->evento->imagem_capa ?? null,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'user_id'       => $this->userId,
            'user_name'     => $this->userName,
            'user_photo'    => $this->userPhoto,
            'evento_id'     => $this->eventoId,
            'evento_titulo' => $this->eventoTitulo,
            'evento_capa'   => $this->eventoCapa,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Alguém que segues curtiu um evento',
            'body'  => "{$this->userName} curtiu \"{$this->eventoTitulo}\".",
            'url'   => \App\Support\NotificationLinks::evento($this->eventoId),
            'icon'  => $this->userPhoto ? asset('storage/'.$this->userPhoto) : asset('logos.png'),
        ];
    }
}