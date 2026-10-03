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

    protected $casts = [
        'hidden_for_sender_at'   => 'datetime',
        'hidden_for_receiver_at' => 'datetime',
    ];

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
     * Único ponto de criação/busca de conversa 1:1. A identidade da conversa
     * é sempre o par de participantes (+ evento, quando houver) — nunca o
     * "tipo". Isto evita duplicados e garante que todo o sistema usa a
     * mesma regra, seja qual for o botão que o utilizador clicou.
     */
    public static function findOrCreateBetween(int $userA, int $userB, ?int $eventoId = null): self
    {
        $conversation = self::when(
                $eventoId,
                fn ($q) => $q->where('evento_id', $eventoId),
                fn ($q) => $q->whereNull('evento_id')
            )
            ->where(function ($q) use ($userA, $userB) {
                $q->where(fn ($i) => $i->where('sender_id', $userA)->where('receiver_id', $userB))
                  ->orWhere(fn ($i) => $i->where('sender_id', $userB)->where('receiver_id', $userA));
            })
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $evento = $eventoId ? \App\Models\Evento::find($eventoId) : null;

        return self::create([
            'sender_id'   => $userA,
            'receiver_id' => $userB,
            'evento_id'   => $eventoId,
            'titulo'      => $evento ? "Interesse: " . $evento->titulo : null,
            'tipo'        => $eventoId ? 'evento' : 'pessoal',
        ]);
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

    /**
     * Escopo: só as conversas que devem aparecer na LISTA deste utilizador
     * — ele participa E não a escondeu. Usado por todo o sistema (admin
     * incluído) em vez de qualquer filtro manual de sender/receiver.
     */
    public function scopeVisibleTo($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(function ($qq) use ($userId) {
                $qq->where('sender_id', $userId)
                   ->where(function ($h) {
                       $h->whereNull('hidden_for_sender_at')
                         ->orWhereColumn('hidden_for_sender_at', '<', 'updated_at');
                   });
            })->orWhere(function ($qq) use ($userId) {
                $qq->where('receiver_id', $userId)
                   ->where(function ($h) {
                       $h->whereNull('hidden_for_receiver_at')
                         ->orWhereColumn('hidden_for_receiver_at', '<', 'updated_at');
                   });
            });
        });
    }

    /**
     * Segurança: este utilizador participa nesta conversa?
     */
    public function temParticipante(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return (int) $this->sender_id === $userId
            || (int) $this->receiver_id === $userId
            || $this->users()->where('users.id', $userId)->exists();
    }

    /**
     * Esta conversa está escondida da LISTA deste utilizador? Reaparece
     * sozinha assim que chegar atividade nova (updated_at mais recente que
     * o momento em que foi escondida) — igual ao "apagar" do WhatsApp.
     */
    public function hiddenFor(int $userId): bool
    {
        if ((int) $this->sender_id === $userId) {
            return $this->hidden_for_sender_at && $this->hidden_for_sender_at->gte($this->updated_at);
        }
        if ((int) $this->receiver_id === $userId) {
            return $this->hidden_for_receiver_at && $this->hidden_for_receiver_at->gte($this->updated_at);
        }
        return false;
    }

    /**
     * Esconde esta conversa só da lista deste utilizador — nunca apaga
     * mensagens nem afeta o outro participante.
     *
     * ⚠️ BUG REAL ENCONTRADO (confirmado pelos logs): 'hidden_for_sender_at'
     * e 'hidden_for_receiver_at' NÃO estão (e não devem estar) no
     * $fillable — de propósito, não é suposto isto ser preenchível a
     * partir de um formulário. Mas isso significa que $this->update([...])
     * como MÉTODO DE INSTÂNCIA passa pela proteção de mass-assignment do
     * Eloquent e ignora SILENCIOSAMENTE qualquer campo fora do $fillable —
     * sem exceção, sem log, só não grava nada. Era por isso que os dois
     * campos ficavam sempre null.
     *
     * A correção: usar update() em massa via query builder
     * (Model::where(...)->update([...])), que NUNCA passa pelo
     * $fillable (só se aplica a fill()/instâncias) — o mesmo caminho que
     * já usávamos para esconder mensagens e que sempre funcionou. Isto
     * também evita de vez o problema anterior do 'updated_at' ser
     * reescrito sozinho pelo Eloquent, porque update() em massa respeita
     * o valor que passamos explicitamente.
     */
    public function hideFor(int $userId): void
    {
        $agora = now();
        $coluna = null;

        if ((int) $this->sender_id === $userId) {
            $coluna = 'hidden_for_sender_at';
        } elseif ((int) $this->receiver_id === $userId) {
            $coluna = 'hidden_for_receiver_at';
        }

        if (!$coluna) {
            return;
        }

        static::where('id', $this->id)->update([
            $coluna => $agora,
            'updated_at' => $agora,
        ]);

        // Mantém o objeto em memória coerente com o que foi gravado,
        // caso seja usado logo a seguir na mesma requisição.
        $this->setAttribute($coluna, $agora);
        $this->setAttribute('updated_at', $agora);
    }
}