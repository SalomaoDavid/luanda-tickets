<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DadosBancariosCriador extends Model
{
    protected $table = 'dados_bancarios_criadores';

    protected $fillable = [
        'user_id','nome_banco','titular','iban','numero_conta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}