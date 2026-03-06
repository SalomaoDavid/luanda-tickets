<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Curtida extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'evento_id'];

    // Relacionamento: Uma curtida pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
