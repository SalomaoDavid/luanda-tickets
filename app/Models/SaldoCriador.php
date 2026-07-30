<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoCriador extends Model
{
    protected $table = 'saldos_criadores';

    protected $fillable = [
        'reserva_id','criador_id','evento_id',
        'valor_total','valor_admin','valor_criador',
        'estado','pago_em','referencia_transferencia',
    ];

    protected $casts = [
        'pago_em'      => 'datetime',
        'valor_total'  => 'decimal:2',
        'valor_admin'  => 'decimal:2',
        'valor_criador'=> 'decimal:2',
    ];

    public function reserva()  { return $this->belongsTo(Reserva::class); }
    public function criador()  { return $this->belongsTo(User::class, 'criador_id'); }
    public function evento()   { return $this->belongsTo(Evento::class); }
}