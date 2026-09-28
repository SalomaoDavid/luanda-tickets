<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Impede o site de ser embutido noutro (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Impede o navegador de "adivinhar" tipos de ficheiro
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Controla a informação enviada no cabeçalho Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Limita APIs sensíveis do navegador
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Esconde a versão do PHP
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}