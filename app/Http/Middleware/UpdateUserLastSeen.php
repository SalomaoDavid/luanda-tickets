<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Só atualiza se nunca foi definido ou se passaram 2+ minutos
            // Evita uma query à BD em cada request
            if (!$user->last_seen || $user->last_seen->diffInMinutes(now()) >= 2) {
                $user->updateQuietly(['last_seen' => now()]);
            }
        }

        return $next($request);
    }
}