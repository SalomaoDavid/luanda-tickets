@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Bem-vindo de <span class="text-sky-500">Volta</span></h2>
   
       @if ($errors->any())
       <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-sm" id="errorBox">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
       </div>
       @endif
        <form method="POST" action="{{ route('login') }}" id="loginForm">
         
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Seu E-mail</label>
                <input type="email" name="email" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none focus:border-sky-500 transition" required autofocus>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Sua Senha</label>
                <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none focus:border-sky-500 transition" required>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-xs text-slate-400">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-white/10 bg-white/5 text-sky-500"> Lembre de mim
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-sky-500 hover:underline">Esqueceu a senha?</a>
                @endif
            </div>

            <div class="mb-6 flex justify-center">
                <div class="cf-turnstile" data-sitekey="{{ config('turnstile.turnstile_site_key') }}"></div>
            </div>

            <button type="submit" id="submitBtn" class="w-full bg-sky-500 hover:bg-sky-400 py-4 rounded-xl font-black uppercase tracking-widest transition">
                Entrar no Sistema
            </button>
        </form>
    </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const errorBox = document.getElementById('errorBox');
        if (!errorBox) return;

        // Procura um número seguido de "seconds" na mensagem de erro
        const match = errorBox.textContent.match(/(\d+)\s+seconds/);
        if (!match) return;

        let seconds = parseInt(match[1], 10);
        const form = document.getElementById('loginForm');
        const camposParaDesativar = form.querySelectorAll('input, button');

        camposParaDesativar.forEach(function (campo) {
            campo.disabled = true;
        });

        const contador = document.createElement('p');
        contador.className = 'text-center text-xs text-slate-400 mt-4';
        form.appendChild(contador);

        function atualizarContador() {
            if (seconds <= 0) {
                camposParaDesativar.forEach(function (campo) {
                    campo.disabled = false;
                });
                contador.remove();
                return;
            }
            contador.textContent = 'Podes tentar novamente em ' + seconds + ' segundos.';
            seconds--;
            setTimeout(atualizarContador, 1000);
        }

        atualizarContador();
    });
</script>
@endsection