<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'titulo', 'tipo', 'evento_id', 'is_blocked', 'blocked_by'];

    /**
     * Retorna a outra pessoa da conversa
     */
    public function getReceiver()
    {
        if ($this->sender_id === auth()->id()) {
            return $this->receiver;
        }
        return $this->sender;
    }

    /**
     * Relacionamento com o remetente
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relacionamento com o destinatário
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relacionamento com as Mensagens
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relacionamento com o Evento
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    /**
     * Caso use conversas em grupo (opcional)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_admin_grupo');
    }

    /**
     * Escopo de Segurança
     */
    public function scopeForUser($query, $user) 
    {
        if ($user->role === 'admin') {
            return $query;
        }
        
        return $query->where(function($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        });
    }
}