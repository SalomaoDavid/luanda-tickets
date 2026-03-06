@extends('layouts.app')

@section('title', 'Meu Perfil - Luanda Tickets')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    
    <div class="post-card p-8 mb-8 relative overflow-hidden bg-gradient-to-br from-white/5 to-transparent">
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="relative"> <img id="avatar-preview" src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=0ea5e9&color=fff&size=128' }}" class="w-32 h-32 rounded-full border-4 border-sky-500/30 p-1 shadow-2xl shadow-sky-500/20 object-cover"> <div class="absolute bottom-0 right-0 w-8 h-8 bg-sky-500 rounded-full border-4 border-[#020617] flex items-center justify-center"> 
            <span class="text-xs">📸</span> 
        </div> 
    </div>
            
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">{{ auth()->user()->name }}</h2>
                <p class="text-sky-500 font-bold text-[10px] uppercase tracking-[0.3em] mb-4">Membro Oficial Luanda Tickets</p>
                
                <div class="flex gap-6">
                    <div class="text-center">
                        <span class="block text-white font-black text-xl leading-none">0</span>
                        <span class="text-[8px] text-slate-500 uppercase font-black tracking-widest">Eventos</span>
                    </div>
                    <div class="text-center border-x border-white/10 px-6">
                        <span class="block text-white font-black text-xl leading-none">0</span>
                        <span class="text-[8px] text-slate-500 uppercase font-black tracking-widest">Seguidores</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-8">
        @csrf
        @method('patch')

        <div class="post-card p-8 space-y-6">
            <h3 class="text-white font-black text-[10px] uppercase italic mb-2 flex items-center gap-2">
                <span class="text-sky-500">#</span> Editar Informações
            </h3>

            <div>
                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 block text-sky-500/80">Carregar Novo Avatar</label>
                <input type="file" name="avatar" id="avatar-input" accept="image/*"
                       class="w-full text-[10px] text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-sky-500/10 file:text-sky-500 hover:file:bg-sky-500/20 cursor-pointer transition-all">
            </div>

            <div>
                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 block">Nome de Usuário</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:ring-2 focus:ring-sky-500 outline-none transition-all">
            </div>

            <div>
                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2 block">E-mail de Acesso</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:ring-2 focus:ring-sky-500 outline-none transition-all">
            </div>

            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-400 text-white font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-xl transition-all shadow-lg shadow-sky-500/20 active:scale-95">
                Atualizar Perfil VIP
            </button>
        </div>

        <div class="post-card p-8 flex flex-col justify-between">
            <div>
                <h3 class="text-white font-black text-[10px] uppercase italic mb-6 flex items-center gap-2">
                    <span class="text-red-500">#</span> Segurança da Conta
                </h3>
                <p class="text-slate-500 text-[10px] mb-8 uppercase leading-relaxed font-bold italic">
                    Proteja os seus ingressos digitais. Recomendamos a alteração da senha a cada 90 dias para manter a sua conta segura contra acessos não autorizados.
                </p>
            </div>
            
            <a href="#" class="block w-full text-center border-2 border-white/5 hover:border-sky-500/30 hover:bg-white/5 text-white font-black text-[10px] uppercase tracking-widest py-4 rounded-xl transition-all">
                Alterar Senha de Acesso
            </a>
        </div>
    </form>
</div>

<script>
    document.getElementById('avatar-input').onchange = evt => {
        const [file] = document.getElementById('avatar-input').files;
        if (file) {
            document.getElementById('avatar-preview').src = URL.createObjectURL(file);
        }
    }
</script>
@endsection
