<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TicketPurchasedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // Dados primitivos em vez de objectos Eloquent — serialização segura
    public function __construct(
        public int    $reservaId,
        public ?int   $compradorId,
        public string $compradorNome,
        public ?string $compradorFoto,
        public int    $eventoId,
        public string $eventoTitulo,
        public int    $quantidade,
        public float  $totalPago,
    ) {}

    // Factory method — resolve os dados antes de criar a notificação
    public static function fromReserva(\App\Models\Reserva $reserva): self
    {
        $evento = $reserva->tipoIngresso->evento;
        return new self(
            reservaId:      $reserva->id,
            compradorId:    $reserva->user_id,
            compradorNome:  $reserva->user?->name ?? $reserva->nome_cliente,
            compradorFoto:  $reserva->user?->avatar_url ?? null,
            eventoId:       $evento->id,
            eventoTitulo:   $evento->titulo,
            quantidade:     $reserva->quantidade,
            totalPago:      $reserva->total,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', \App\Channels\WebPushChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'reserva_id'     => $this->reservaId,
            'comprador_id'   => $this->compradorId,
            'comprador_nome' => $this->compradorNome,
            'comprador_foto' => $this->compradorFoto,
            'evento_id'      => $this->eventoId,
            'evento_titulo'  => $this->eventoTitulo,
            'quantidade'     => $this->quantidade,
            'total_pago'     => $this->totalPago,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Bilhete vendido 🎟️',
            'body'  => "{$this->compradorNome} comprou {$this->quantidade}x bilhete(s) para \"{$this->eventoTitulo}\".",
            'url'   => \App\Support\NotificationLinks::adminReservas(),
            'icon'  => $this->compradorFoto ? asset('storage/'.$this->compradorFoto) : asset('logos.png'),
        ];
    }
}