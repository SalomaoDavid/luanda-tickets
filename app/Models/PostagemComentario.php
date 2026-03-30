<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostagemComentario extends Model
{
    use HasFactory;

    protected $table = 'postagem_comentarios';

    protected $fillable = ['user_id', 'postagem_id', 'parent_id', 'corpo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class);
    }

    public function respostas()
    {
        return $this->hasMany(PostagemComentario::class, 'parent_id')->with('user');
    }

    public function parent()
    {
        return $this->belongsTo(PostagemComentario::class, 'parent_id');
    }
}