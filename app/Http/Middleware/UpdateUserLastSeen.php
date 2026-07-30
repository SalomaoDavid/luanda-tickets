<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $userId  = auth()->id();
            $cacheKey = "user_last_seen_{$userId}";

            // Cache por 2 minutos — evita query à BD em cada request
            if (!Cache::has($cacheKey)) {
                auth()->user()->updateQuietly(['last_seen' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(2));
            }
        }

        return $next($request);
    }
}