<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'is_verified',
        'last_seen',
    ];

    /**
     * Atributos que devem ser escondidos em respostas JSON.
     * REMOVI 'name' e 'email' daqui para que apareçam no seu site!
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversão de tipos de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relacionamento com as Reservas/Pedidos
     */
    public function pedidos() 
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Relacionamento com as Conversas do Chat
     * APENAS UMA VEZ AQUI PARA NÃO DAR ERRO.
     */
    public function conversations() 
    {
        return $this->belongsToMany(Conversation::class);
    }

    /**
     * Helper para pegar a URL do Avatar
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }

    // app/Models/User.php

  public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCreator()
    {
        return $this->role === 'creator' || $this->role === 'admin';
    }
    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->diffInMinutes(now()) < 5;
    }
    public function eventos()
    {
        return $this->hasMany(\App\Models\Evento::class);
    }

    public function postagens()
    {
        return $this->hasMany(\App\Models\Postagem::class);
    }

    public function eventosCurtidos()
    {
        return $this->belongsToMany(\App\Models\Evento::class, 'curtidas', 'user_id', 'evento_id')->withTimestamps();
    }
}