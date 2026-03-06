<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoFoto extends Model
{
    // Importante: permitir gravar o caminho da foto e o ID do evento
    protected $fillable = ['evento_id', 'caminho'];

    // Cada foto pertence a um evento
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}