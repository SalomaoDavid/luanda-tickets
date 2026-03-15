<?php

namespace App\Notifications;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TicketPurchasedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Reserva $reserva) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $evento = $this->reserva->tipoIngresso->evento;

        return [
            'reserva_id'     => $this->reserva->id,
            'comprador_id'   => $this->reserva->user_id,
            'comprador_nome' => $this->reserva->user->name ?? $this->reserva->nome_cliente,
            'comprador_foto' => $this->message->user->avatar_url ?? null,
            'evento_id'      => $evento->id,
            'evento_titulo'  => $evento->titulo,
            'quantidade'     => $this->reserva->quantidade,
            'total_pago'     => $this->reserva->total,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}