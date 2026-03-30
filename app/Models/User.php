<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'bio',
        'is_verified',
        'last_seen',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_seen'         => 'datetime', // ← FIX: converte para Carbon automaticamente
        ];
    }

    // ── ONLINE ────────────────────────────────────────────────
    public function isOnline(): bool
    {
        return $this->last_seen !== null &&
               $this->last_seen->diffInMinutes(now()) < 5;
    }

    // ── ROLES ─────────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCreator(): bool
    {
        return $this->role === 'creator' || $this->role === 'admin';
    }

    // ── AVATAR ────────────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }

    // ── RELAÇÕES ──────────────────────────────────────────────
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class);
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

    public function seguidores()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'seguido_id', 'seguidor_id')->withTimestamps();
    }

    public function seguindo()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'seguidor_id', 'seguido_id')->withTimestamps();
    }
}