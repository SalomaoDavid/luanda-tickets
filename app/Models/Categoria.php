<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends Model
{
    //
    use HasFactory;
    protected $fillable = ['nome', 'slug', 'tipo'];

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class);
    }
    public function getEmojiAttribute(): string
    {
        return match(true) {
            str_contains($this->nome, 'Show')        => '🎤',
            str_contains($this->nome, 'Festival')    => '🎉',
            str_contains($this->nome, 'Viagem')      => '✈️',
            str_contains($this->nome, 'Desporto')    => '⚽',
            str_contains($this->nome, 'Conferência') => '🎙️',
            str_contains($this->nome, 'Workshop')    => '📚',
            str_contains($this->nome, 'Cultura')     => '🎭',
            default                                  => '📌',
        };
    }
}
