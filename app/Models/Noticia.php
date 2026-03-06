<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Noticia extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'titulo', 'slug', 'conteudo', 'fonte', 'link_original', 
        'imagem_destaque', 'publicado_em', 'autor_id', 'categoria_id'
    ];

    public function categoria() {
    return $this->belongsTo(Categoria::class);
}

public function autor() {
    return $this->belongsTo(User::class, 'autor_id');
}
}


