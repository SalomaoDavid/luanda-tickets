@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-xl">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-white">Recuperar <span class="text-sky-500">Acesso</span></h2>
            <p class="mt-4 text-xs text-slate-400 leading-relaxed">
                Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para você escolher uma nova.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-500 text-xs font-bold text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">E-mail de Cadastro</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none focus:border-sky-500 transition" required autofocus>
                @error('email') <span class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 py-4 rounded-xl font-black uppercase tracking-widest transition shadow-lg shadow-sky-500/20">
                Enviar Link de Recuperação
            </button>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white transition">
                    ← Voltar para o Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
