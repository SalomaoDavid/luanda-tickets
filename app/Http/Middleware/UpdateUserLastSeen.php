<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Se o usuário estiver logado, atualizamos a data da última atividade
        if (auth()->check()) {
            auth()->user()->update([
                'last_seen' => now()
            ]);
        }

        return $next($request);
    }
}
