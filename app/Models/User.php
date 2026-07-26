<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'cover',
        'role', 'bio', 'is_verified', 'last_seen',
        'is_blocked', 'suspended_at',
        'visibilidade_perfil',
        'quem_mensagens',
        'mostrar_bilhetes',
        'mostrar_seguidores',
        'pesquisavel',
        'notif_eventos',
        'notif_bilhetes',
        'notif_mensagens',
        'notif_seguidores',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_seen'         => 'datetime',
            'suspended_at'      => 'datetime',
            'is_blocked'        => 'boolean',
            'is_verified'       => 'boolean',
            'mostrar_bilhetes'   => 'boolean',
            'mostrar_seguidores' => 'boolean',
            'pesquisavel'        => 'boolean',
            'notif_eventos'      => 'boolean',
            'notif_bilhetes'     => 'boolean',
            'notif_mensagens'    => 'boolean',
            'notif_seguidores'   => 'boolean',
        ];
    }

    // ── ONLINE ──────────────────────────────────────────────
    public function isOnline(): bool
    {
        return $this->last_seen !== null &&
               $this->last_seen->diffInMinutes(now()) < 5;
    }

    // ── ROLES ───────────────────────────────────────────────
    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isCreator(): bool  { return in_array($this->role, ['creator','admin']); }
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null && $this->suspended_at->isFuture();
    }

    // ── AVATAR URL ──────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    // ── RELAÇÕES ────────────────────────────────────────────
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
        return $this->belongsToMany(\App\Models\Evento::class, 'curtidas', 'user_id', 'evento_id')
                    ->withTimestamps();
    }

    // ── SEGUIDORES ──────────────────────────────────────────
    public function seguidores()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'seguido_id', 'seguidor_id')
                    ->withTimestamps();
    }

    public function seguindo()
    {
        return $this->belongsToMany(User::class, 'seguidores', 'seguidor_id', 'seguido_id')
                    ->withTimestamps();
    }

    public function estaSeguindo(int $userId): bool
    {
        return $this->seguindo()->where('seguido_id', $userId)->exists();
    }

    // ── BLOQUEIOS ──────────────────────────────────────────
    public function bloqueados()
    {
        return $this->belongsToMany(User::class, 'bloqueios', 'bloqueador_id', 'bloqueado_id')
                    ->withTimestamps();
    }

    public function estaBloqueado(int $userId): bool
    {
        return $this->bloqueados()->where('bloqueado_id', $userId)->exists();
    }

    public function foiBloqueadoPor(int $userId): bool
    {
        return static::find($userId)?->estaBloqueado($this->id) ?? false;
    }
}