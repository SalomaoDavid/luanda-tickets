<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PedidoAprovadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $reservaId,
        public ?int   $pedidoId,
        public int    $eventoId,
        public string $eventoTitulo,
    ) {}

    public static function fromReserva(\App\Models\Reserva $reserva, ?int $pedidoId = null): self
    {
        $evento = $reserva->tipoIngresso->evento;

        return new self(
            reservaId:    $reserva->id,
            pedidoId:     $pedidoId,
            eventoId:     $evento->id,
            eventoTitulo: $evento->titulo,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'reserva_id'    => $this->reservaId,
            'pedido_id'     => $this->pedidoId,
            'evento_id'     => $this->eventoId,
            'evento_titulo' => $this->eventoTitulo,
            'mensagem'      => "O seu pedido foi aprovado, pode verificar o bilhete no seu perfil com o nome \"{$this->eventoTitulo}\".",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Bilhete aprovado ✅',
            'body'  => "O teu pedido para \"{$this->eventoTitulo}\" foi aprovado. Já podes ver o teu bilhete.",
            'url'   => route('profile.show', ['id' => $notifiable->id]),
            'icon'  => asset('logos.png'),
        ];
    }
}