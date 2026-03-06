<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoIngresso extends Model
{
    //
    use HasFactory;
    protected $fillable = ['evento_id', 'nome', 'preco', 'quantidade_total', 'quantidade_disponivel'];
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'tipo_ingresso_id');
    }
}

