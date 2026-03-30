<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Postagem extends Model
{
    //
    use HasFactory;
    protected $table = 'postagens';

    protected $fillable = ['user_id', 'conteudo', 'imagem'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reacoes()
    {
        return $this->hasMany(PostagemReacao::class);
    }

    public function curtidas()
    {
        return $this->hasMany(PostagemReacao::class)->where('tipo', 'curtida');
    }

    public function adoros()
    {
        return $this->hasMany(PostagemReacao::class)->where('tipo', 'adoro');
    }

    public function comentarios()
    {
        return $this->hasMany(PostagemComentario::class)->whereNull('parent_id')->with('user', 'respostas')->latest();
    }

    public function minhaReacao()
    {
        return $this->hasOne(PostagemReacao::class)->where('user_id', auth()->id());
    }
}
