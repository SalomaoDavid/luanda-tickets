<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Centraliza a construção de links usados nas notificações (WebPush,
 * sino, etc.). Se o nome de alguma rota no teu projeto for diferente do
 * que assumimos aqui, só precisas de corrigir NESTE ficheiro — não em
 * cada Notification individualmente.
 *
 * Cada método tenta, por ordem, os nomes de rota mais prováveis, e só
 * cai para '/' se nenhum existir (nunca lança erro — ver comentário
 * sobre robustez no WebPushChannel).
 */
class NotificationLinks
{
    public static function evento(int $eventoId): string
    {
        foreach (['evento-detalhes', 'eventos.show', 'evento.show', 'eventos.detalhes'] as $nome) {
            if (Route::has($nome)) {
                return route($nome, $eventoId);
            }
        }
        return '/';
    }

    public static function mensagem(int $outroUserId): string
    {
        foreach (['mensagens.index'] as $nome) {
            if (Route::has($nome)) {
                return route($nome, ['user_id' => $outroUserId]);
            }
        }
        return '/';
    }

    public static function postagem(int $postagemId): string
    {
        foreach (['postagens.show', 'postagem.show'] as $nome) {
            if (Route::has($nome)) {
                return route($nome, $postagemId);
            }
        }
        if (Route::has('feed')) {
            return route('feed');
        }
        return '/';
    }

    public static function perfil(int $userId): string
    {
        if (Route::has('profile.show')) {
            return route('profile.show', ['id' => $userId]);
        }
        return '/';
    }

    public static function adminReservas(): string
    {
        return Route::has('admin.reservas.index') ? route('admin.reservas.index') : '/admin';
    }
}