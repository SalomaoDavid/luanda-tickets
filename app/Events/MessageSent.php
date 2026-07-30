<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ShouldBroadcastNow (em vez de ShouldBroadcast) porque queremos que a
 * mensagem seja emitida de imediato, sem depender de um worker de filas
 * (php artisan queue:work) estar a correr. Isto é o que garante que o
 * outro lado recebe a mensagem quase instantaneamente.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message->loadMissing('user');
    }

    /**
     * Canal privado por conversa. Só quem participa na conversa
     * consegue subscrever (ver routes/channels.php).
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->conversation_id),
        ];
    }

    /**
     * Nome do evento tal como é ouvido no JS: .listen('.MessageSent', ...)
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user_id'         => $this->message->user_id,
            'user_name'       => $this->message->user->name ?? null,
            'body'            => $this->message->body,
            'created_at'      => $this->message->created_at->format('H:i'),
        ];
    }
}