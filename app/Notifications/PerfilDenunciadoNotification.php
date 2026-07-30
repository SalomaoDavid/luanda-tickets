<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PerfilDenunciadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $denunciadoId,
        public string $denunciadoNome,
        public int    $denuncianteId,
        public string $denuncianteNome,
        public ?string $userPhoto,
        public string $motivo,
    ) {}

    public static function fromUsers(\App\Models\User $denunciado, \App\Models\User $denunciante, string $motivo): self
    {
        return new self(
            denunciadoId:    $denunciado->id,
            denunciadoNome:  $denunciado->name,
            denuncianteId:   $denunciante->id,
            denuncianteNome: $denunciante->name,
            userPhoto:       $denunciante->avatar_url ?? null,
            motivo:          $motivo,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'denunciado_id'    => $this->denunciadoId,
            'denunciado_nome'  => $this->denunciadoNome,
            'denunciante_id'   => $this->denuncianteId,
            'denunciante_nome' => $this->denuncianteNome,
            'user_photo'       => $this->userPhoto,
            'motivo'           => $this->motivo,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}