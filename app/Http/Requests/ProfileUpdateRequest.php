<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio'    => ['nullable', 'string', 'max:300'],

            // Tipo de ficheiro restrito explicitamente
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'cover'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],

            // Privacidade
            'visibilidade_perfil' => ['nullable', 'in:publico,seguidores,privado'],
            'quem_mensagens'      => ['nullable', 'in:todos,seguidores,ninguem'],
            'mostrar_bilhetes'    => ['nullable', 'boolean'],
            'mostrar_seguidores'  => ['nullable', 'boolean'],
            'pesquisavel'         => ['nullable', 'boolean'],

            // Notificações
            'notif_eventos'    => ['nullable', 'boolean'],
            'notif_bilhetes'   => ['nullable', 'boolean'],
            'notif_mensagens'  => ['nullable', 'boolean'],
            'notif_seguidores' => ['nullable', 'boolean'],
        ];
    }
}