<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $curtidaId,
        public int    $userId,
        public string $userName,
        public ?string $userPhoto,
        public int    $eventoId,
        public string $eventoTitulo,
    ) {}

    public static function fromCurtida(\App\Models\Curtida $curtida): self
    {
        return new self(
            curtidaId:    $curtida->id,
            userId:       $curtida->user_id,
            userName:     $curtida->user->name,
            userPhoto:    $curtida->user->avatar_url ?? null,
            eventoId:     $curtida->evento_id,
            eventoTitulo: $curtida->evento->titulo,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'curtida_id'    => $this->curtidaId,
            'user_id'       => $this->userId,
            'user_name'     => $this->userName,
            'user_photo'    => $this->userPhoto,
            'evento_id'     => $this->eventoId,
            'evento_titulo' => $this->eventoTitulo,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Nova curtida',
            'body'  => "{$this->userName} curtiu o teu evento \"{$this->eventoTitulo}\".",
            'url'   => \App\Support\NotificationLinks::evento($this->eventoId),
            'icon'  => $this->userPhoto ? asset('storage/'.$this->userPhoto) : asset('logos.png'),
        ];
    }
}