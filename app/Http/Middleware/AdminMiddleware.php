<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado e se tem permissão
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'creator')) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Acesso restrito!');
    }
}