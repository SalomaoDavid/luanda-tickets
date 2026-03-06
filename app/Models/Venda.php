<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Venda extends Model
{
    protected $fillable = [
        'evento_id', 'tipo_ingresso_id', 'nome_cliente', 
        'whatsapp', 'email', 'valor_pago', 
        'comprovativo_path', 'status', 'codigo_bilhete'
    ];

    // Este evento do Laravel gera o código do bilhete automaticamente antes de salvar
    protected static function booted()
    {
        static::creating(function ($venda) {
            $venda->codigo_bilhete = 'LT-' . strtoupper(Str::random(10));
        });
    }

    // Relacionamentos
    public function evento() { return $this->belongsTo(Evento::class); }
    public function tipoIngresso() { return $this->belongsTo(TipoIngresso::class); }
}
