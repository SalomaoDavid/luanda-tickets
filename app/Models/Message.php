<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'read_at',
        // ✅ Faltava este campo aqui — sem estar no $fillable, o Laravel
        // ignora-o silenciosamente em Message::create([...]), então o selo
        // "Comunicado Oficial" nunca chegava a gravar nas mensagens novas.
        'is_aviso_admin',
    ];

    protected $casts = [
        'read_at'                => 'datetime',
        'is_aviso_admin'         => 'boolean',
        'hidden_for_sender_at'   => 'datetime',
        'hidden_for_receiver_at' => 'datetime',
    ];

    /**
     * Esta é a relação que o erro diz que está faltando!
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Esta mensagem está escondida para este utilizador? "sender"/"receiver"
     * referem-se aos papéis da CONVERSA (ver migration), não a quem a
     * escreveu — dá para esconder qualquer mensagem, tua ou da outra
     * pessoa, só do teu lado.
     */
    public function hiddenFor(int $userId): bool
    {
        $conversa = $this->conversation;
        if (!$conversa) {
            return false;
        }

        if ((int) $conversa->sender_id === $userId) {
            return (bool) $this->hidden_for_sender_at;
        }
        if ((int) $conversa->receiver_id === $userId) {
            return (bool) $this->hidden_for_receiver_at;
        }

        return false;
    }

    /**
     * Esconde esta mensagem só para este utilizador.
     */
    public function hideFor(int $userId): void
    {
        $conversa = $this->conversation;
        if (!$conversa) {
            return;
        }

        if ((int) $conversa->sender_id === $userId) {
            $this->update(['hidden_for_sender_at' => now()]);
        } elseif ((int) $conversa->receiver_id === $userId) {
            $this->update(['hidden_for_receiver_at' => now()]);
        }
    }
}