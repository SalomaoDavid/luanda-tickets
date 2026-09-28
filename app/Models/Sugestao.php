<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sugestao extends Model
{
    protected $table = 'sugestoes';
    protected $fillable = [
        'user_id',
        'mensagem',
        // 'estado' fora do fillable — só o sistema/admin muda isto, nunca o formulário do usuário
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}