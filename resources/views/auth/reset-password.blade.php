@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-xl">
        <h2 class="text-2xl font-bold mb-6 text-center text-white">Nova <span class="text-sky-500">Senha</span></h2>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none opacity-50" required readonly>
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Nova Senha</label>
                <input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none focus:border-sky-500 transition" required autofocus>
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white outline-none focus:border-sky-500 transition" required>
            </div>

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 py-4 rounded-xl font-black uppercase tracking-widest transition">
                Redefinir Senha
            </button>
        </form>
    </div>
</div>
@endsection
