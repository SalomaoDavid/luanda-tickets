<?php

namespace App\Notifications;

use App\Models\Curtida;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventLikedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Curtida $curtida) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'curtida_id'  => $this->curtida->id,
            'user_id'     => $this->curtida->user_id,
            'user_name'   => $this->curtida->user->name,
            'user_photo'  => $this->curtida->user->avatar_url ?? null,
            'evento_id'   => $this->curtida->evento_id,
            'evento_titulo' => $this->curtida->evento->titulo,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}