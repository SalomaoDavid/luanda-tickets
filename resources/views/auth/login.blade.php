@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Bem-vindo de <span class="text-sky-500">Volta</span></h2>

        <form method="POST" action="{{ route('login') }}">
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

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 py-4 rounded-xl font-black uppercase tracking-widest transition">
                Entrar no Sistema
            </button>
        </form>
    </div>
</div>
@endsection
