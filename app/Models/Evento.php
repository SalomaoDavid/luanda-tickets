<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        // user_id REMOVIDO — sempre atribuído via auth()->id() no controller
        'categoria_id',
        'subcategoria_id',
        'titulo',
        'descricao',
        'localizacao',
        'municipio',
        'provincia',
        'data_evento',
        'data_fim',
        'hora_inicio',
        'hora_fim',
        'multiplos_dias',
        'online',
        'link_externo',
        // imagem_capa REMOVIDO — gerido pelo controller após upload
        'video_preview',   // URL do vídeo (YouTube, Vimeo, etc.)
        'lotacao_maxima',
        'ingressos_por_pessoa',
        'lista_espera',
        'privado',
        'aprovacao_manual',
        'permitir_comentarios',
        'participantes_publicos',
        'notif_nova_inscricao',
        'notif_lembrete_24h',
        'notif_resumo_semanal',
        // status REMOVIDO — controlado pelo sistema
        'meta',
    ];

    protected $casts = [
        'meta'                   => 'array',
        'multiplos_dias'         => 'boolean',
        'online'                 => 'boolean',
        'lista_espera'           => 'boolean',
        'privado'                => 'boolean',
        'aprovacao_manual'       => 'boolean',
        'permitir_comentarios'   => 'boolean',
        'participantes_publicos' => 'boolean',
        'notif_nova_inscricao'   => 'boolean',
        'notif_lembrete_24h'     => 'boolean',
        'notif_resumo_semanal'   => 'boolean',
        'data_evento'            => 'date',
        'data_fim'               => 'date',
    ];

    // ── RELAÇÕES ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function fotos()
    {
        return $this->hasMany(EventoFoto::class, 'evento_id');
    }

    public function tiposIngresso()
    {
        return $this->hasMany(TipoIngresso::class, 'evento_id');
    }

    public function curtidas()
    {
        return $this->hasMany(Curtida::class);
    }

    public function usuariosQueCurtiram()
    {
        return $this->belongsToMany(User::class, 'curtidas')->withTimestamps();
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class)
            ->whereNull('parent_id')
            ->latest();
    }

    public function comentariosComRelacoes()
    {
        return $this->hasMany(Comentario::class)
            ->whereNull('parent_id')
            ->with(['user:id,name,avatar', 'respostas.user:id,name,avatar', 'likes'])
            ->latest();
    }

    public function usuariosQueComentaram()
    {
        return $this->belongsToMany(User::class, 'comentarios', 'evento_id', 'user_id')->distinct();
    }

        /**
     * Regra de moderação: pode este utilizador pôr o evento neste estado?
     */
    public function podeMudarEstadoPara(?string $novo, \App\Models\User $user): bool
    {
        // Não muda nada: permitido
        if ($novo === $this->status) {
            return true;
        }

        // Só o admin publica
        if ($novo === 'publicado' && $user->role !== 'admin') {
            return false;
        }

        // Evento com reservas não volta a rascunho (protege os triggers)
        if ($novo === 'rascunho' && $this->exists) {
            $temReservas = \App\Models\Reserva::whereHas(
                'tipoIngresso',
                fn ($q) => $q->where('evento_id', $this->id)
            )->exists();

            if ($temReservas) {
                return false;
            }
        }

        return true;
    }
}