<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Dislike extends Model
{
    use HasFactory;
    // Adicione estas linhas abaixo:
    protected $fillable = [
        'user_id',
        'evento_id'
    ];
}
