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
}
