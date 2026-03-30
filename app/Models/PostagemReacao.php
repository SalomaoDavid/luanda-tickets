<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostagemReacao extends Model
{
    use HasFactory;

    protected $table = 'postagem_reacoes';

    protected $fillable = ['user_id', 'postagem_id', 'tipo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class);
    }
}