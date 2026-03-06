<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id', 
        'titulo', 
        'descricao', 
        'localizacao', 
        'data_evento', 
        'imagem_capa', 
        'lotacao_maxima', 
        'status'
    ];

    // Relacionamento com a Categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relacionamento com a Galeria de Fotos (O QUE ESTAVA FALTANDO)
    public function fotos()
    {
        return $this->hasMany(EventoFoto::class, 'evento_id');
    }

    // Relacionamento com os Ingressos
    public function tiposIngresso() 
    {
        return $this->hasMany(TipoIngresso::class, 'evento_id');
    }

    // Relacionamento com as Curtidas (Tabela pivot ou direta)
    public function curtidas() 
    {
        return $this->hasMany(Curtida::class);
    }

    // Lista de usuários para o Modal de "Quem Curtiu"
    public function usuariosQueCurtiram() 
    {
        return $this->belongsToMany(User::class, 'curtidas')->withTimestamps();
    }
    // app/Models/Evento.php
}

 
