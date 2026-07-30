<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'user_id',
        'reserva_id',
        'total_pago',
        'metodo_pagamento',
        // status REMOVIDO — controlado pelo sistema
        // comprovativo_path REMOVIDO — gerido pelo controller
    ];

    // Relação única (user e usuario eram duplicadas)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bilhetes()
    {
        return $this->hasMany(Bilhete::class);
    }
}