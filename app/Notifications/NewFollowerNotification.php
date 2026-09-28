<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewFollowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $seguidorId,
        public string $seguidorNome,
        public ?string $seguidorFoto,
    ) {}

    public static function fromSeguidor(\App\Models\User $seguidor): self
    {
        return new self(
            seguidorId:    $seguidor->id,
            seguidorNome:  $seguidor->name,
            seguidorFoto:  $seguidor->avatar_url ?? null,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'seguidor_id'   => $this->seguidorId,
            'seguidor_nome' => $this->seguidorNome,
            'seguidor_foto' => $this->seguidorFoto,
            'mensagem'      => "{$this->seguidorNome} começou a seguir-te.",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Novo seguidor',
            'body'  => "{$this->seguidorNome} começou a seguir-te.",
            'url'   => route('profile.show', ['id' => $this->seguidorId]),
            'icon'  => $this->seguidorFoto ? asset('storage/'.$this->seguidorFoto) : asset('logos.png'),
        ];
    }
}