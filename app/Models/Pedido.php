<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['user_id', 'reserva_id', 'total_pago', 'metodo_pagamento', 'status', 'comprovativo_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bilhetes()
    {
        return $this->hasMany(Bilhete::class);
    }
}