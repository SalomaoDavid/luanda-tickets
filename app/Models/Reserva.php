<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_ingresso_id',
        'nome_cliente',
        'whatsapp',
        'quantidade',
        'total',
        'codigo_pedido',
        // Atribuídos directamente no controller (não via fill() em inputs do utilizador)
        // mas precisam de estar no fillable para create() funcionar internamente
        'comprovativo_path',
    ];

    // status é controlado pelo sistema — atribuído directamente: $reserva->status = 'pago'

    // Campos geridos directamente (não via fill())
    // status, comprovativo_path

    protected static function booted()
    {
        static::creating(function ($reserva) {
            if (!$reserva->codigo_pedido) {
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

    public function evento()
    {
        return $this->hasOneThrough(
            Evento::class,
            TipoIngresso::class,
            'id',
            'id',
            'tipo_ingresso_id',
            'evento_id'
        );
    }
}