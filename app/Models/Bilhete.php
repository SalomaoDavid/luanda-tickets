<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bilhete extends Model
{
    protected $fillable = ['pedido_id', 'evento_id', 'tipo_ingressos_id', 'codigo_unico', 'validado_em'];

    public function evento() {
        return $this->belongsTo(Evento::class);
    }

    public function pedido() {
        return $this->belongsTo(Pedido::class);
    }

    public function tipoIngresso() {
        return $this->belongsTo(TipoIngresso::class, 'tipo_ingressos_id');
    }
}