<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    // Força o esquema HTTPS se o site for acedido pelo Ngrok
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' 
        || str_contains(request()->getHttpHost(), 'ngrok-free.dev')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
}
