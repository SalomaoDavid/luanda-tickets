<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PerfilDenunciadoNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public User $denunciado,
        public User $denunciante,
        public string $motivo
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'denunciado_id'   => $this->denunciado->id,
            'denunciado_nome' => $this->denunciado->name,
            'user_photo'      => $this->denunciante->avatar_url,
            'denunciante_id'  => $this->denunciante->id,
            'denunciante_nome'=> $this->denunciante->name,
            'motivo'          => $this->motivo,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}