<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NovoPedidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $reservaId,
        public ?int   $compradorId,
        public string $compradorNome,
        public ?string $compradorFoto,
        public int    $eventoId,
        public string $eventoTitulo,
        public int    $quantidade,
        public float  $total,
    ) {}

    public static function fromReserva(\App\Models\Reserva $reserva): self
    {
        $evento = $reserva->tipoIngresso->evento;

        return new self(
            reservaId:     $reserva->id,
            compradorId:   $reserva->user_id,
            compradorNome: $reserva->user?->name ?? $reserva->nome_cliente,
            compradorFoto: $reserva->user?->avatar_url ?? null,
            eventoId:      $evento->id,
            eventoTitulo:  $evento->titulo,
            quantidade:    $reserva->quantidade,
            total:         $reserva->total,
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
            'total'          => $this->total,
            'mensagem'       => "{$this->compradorNome} acaba de fazer um pedido de compra de bilhetes para \"{$this->eventoTitulo}\".",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => 'Novo pedido de compra',
            'body'  => "{$this->compradorNome} pediu {$this->quantidade}x bilhete(s) para \"{$this->eventoTitulo}\".",
            // ajusta este nome de rota se o teu painel admin de reservas tiver outro
            'url'   => \Illuminate\Support\Facades\Route::has('admin.reservas.index')
                ? route('admin.reservas.index')
                : '/admin',
            'icon'  => $this->compradorFoto ? asset('storage/'.$this->compradorFoto) : asset('logos.png'),
        ];
    }
}