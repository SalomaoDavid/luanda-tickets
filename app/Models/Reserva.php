<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str; // Importante para gerar o código aleatório

class Reserva extends Model
{
    use HasFactory;

    // Adicionados os campos novos no fillable
    protected $fillable = [
        'user_id',
        'tipo_ingresso_id', 
        'nome_cliente', 
        'whatsapp', 
        'quantidade', 
        'total', 
        'status',
        'comprovativo_path',
        'codigo_pedido'
    ];

    // Gerar o código do bilhete automaticamente antes de salvar na base de dados
    protected static function booted()
    {
        static::creating(function ($reserva) {
            if (!$reserva->codigo_pedido) {
                // Gera um código tipo LT-A1B2C3D4E5
                $reserva->codigo_pedido = 'LT-' . strtoupper(Str::random(10));
            }
        });
    }

    public function tipoIngresso() 
    {
        return $this->belongsTo(TipoIngresso::class, 'tipo_ingresso_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Se a tabela reservas não tem evento_id, o relacionamento passa pelo tipoIngresso
    public function evento()
    {
        return $this->hasOneThrough(
            Evento::class,
            TipoIngresso::class,
            'id', // Chave estrangeira em tipo_ingressos
            'id', // Chave estrangeira em eventos
            'tipo_ingresso_id', // Chave local em reservas
            'evento_id' // Chave local em tipo_ingressos
        );
    }
}
