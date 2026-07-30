<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContaBancaria extends Model
{
    protected $table = 'contas_bancarias';

    protected $fillable = [
        'nome_banco','titular','iban','numero_conta','logo','activa','ordem',
    ];

    protected $casts = ['activa' => 'boolean'];

    public function scopeActivas($q) { return $q->where('activa', true)->orderBy('ordem'); }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('images/bancos/'.$this->logo)
            : asset('images/bancos/default.png');
    }
}