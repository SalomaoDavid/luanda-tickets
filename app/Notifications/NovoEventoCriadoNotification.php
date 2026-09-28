<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NovoEventoCriadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $eventoId,
        public string $eventoTitulo,
        public string $eventoStatus,
        public int    $criadorId,
        public string $criadorNome,
        public ?string $userPhoto,
        public ?string $dataEvento,
        public ?string $localizacao,
    ) {}

    public static function fromEvento(\App\Models\Evento $evento, \App\Models\User $criador): self
    {
        return new self(
            eventoId:     $evento->id,
            eventoTitulo: $evento->titulo,
            eventoStatus: $evento->status,
            criadorId:    $criador->id,
            criadorNome:  $criador->name,
            userPhoto:    $criador->avatar_url ?? null,
            dataEvento:   $evento->data_evento,
            localizacao:  $evento->localizacao,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'evento_id'     => $this->eventoId,
            'evento_titulo' => $this->eventoTitulo,
            'evento_status' => $this->eventoStatus,
            'criador_id'    => $this->criadorId,
            'criador_nome'  => $this->criadorNome,
            'user_photo'    => $this->userPhoto,
            'data_evento'   => $this->dataEvento,
            'localizacao'   => $this->localizacao,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Novo evento',
            'body'  => "{$this->criadorNome} criou o evento \"{$this->eventoTitulo}\".",
            'url'   => \App\Support\NotificationLinks::evento($this->eventoId),
            'icon'  => $this->userPhoto ? asset('storage/'.$this->userPhoto) : asset('logos.png'),
        ];
    }
}