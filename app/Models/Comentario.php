<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = ['evento_id', 'user_id', 'parent_id', 'corpo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function respostas()
    {
        return $this->hasMany(Comentario::class, 'parent_id')->with('user', 'likes');
    }

    public function parent()
    {
        return $this->belongsTo(Comentario::class, 'parent_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'comentario_likes');
    }

    public function jaGostei()
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }
}