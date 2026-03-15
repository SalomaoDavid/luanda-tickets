<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $fillable = ['categoria_id', 'nome', 'slug'];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }
}
