<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $userId   = auth()->id();
            $cacheKey = "user_last_seen_{$userId}";
            $checkKey = "user_status_check_{$userId}";

            // Verificar bloqueio/suspensão/eliminação a cada 30 segundos
            if (!Cache::has($checkKey)) {
                $user = \App\Models\User::find($userId);

                // Utilizador eliminado
                if (!$user) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'A tua conta foi eliminada pelo administrador.');
                }

                // Utilizador bloqueado
                if ($user->is_blocked) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'A tua conta foi bloqueada. Contacta o suporte.');
                }

                // Utilizador suspenso
                if ($user->suspended_at && $user->suspended_at->isFuture()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $ate = $user->suspended_at->format('d/m/Y');
                    return redirect()->route('login')
                        ->with('error', "A tua conta está suspensa até {$ate}.");
                }

                // Verificação feita — cache por 30 segundos
                Cache::put($checkKey, true, now()->addSeconds(30));
            }

            // Actualizar last_seen — cache por 2 minutos
            if (!Cache::has($cacheKey)) {
                auth()->user()->updateQuietly(['last_seen' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(2));
            }
        }

        return $next($request);
    }
}