<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin e criadores passam — para rotas partilhadas
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'creator'])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Acesso restrito!');
    }
}