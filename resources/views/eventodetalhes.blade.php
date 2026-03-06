@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #0ea5e9; border-radius: 10px; }
    
    /* Configuração do Carrossel Ken Burns */
    .hero-swiper { width: 100%; height: 65vh; position: relative; }
    .ken-burns-img { width: 100%; height: 100%; object-fit: cover; transition: transform 12s linear; }
    .swiper-slide-active .zoom-effect { transform: scale(1.2); }
    
    /* Overlay para o texto brilhar */
    .hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to bottom, transparent 20%, #020617 95%);
        z-index: 10;
    }
</style>

{{-- Container Principal com Alpine.js --}}
<div x-data="{ open: false, showLikesModal: false, ingressoNome: '', ingressoPreco: 0, ingressoId: '', quantidade: 1 }" 
     class="min-h-screen bg-[#020617] text-slate-200 pb-20">

    {{-- 1. Hero Carrossel Cinematográfico --}}
    <div class="relative w-full overflow-hidden shadow-2xl">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                {{-- Foto de Capa --}}
                <div class="swiper-slide">
                    <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="ken-burns-img zoom-effect">
                </div>
                {{-- Fotos da Galeria --}}
                @foreach($evento->fotos as $foto)
                <div class="swiper-slide">
                    <img src="{{ asset('storage/' . $foto->caminho) }}" class="ken-burns-img zoom-effect">
                </div>
                @endforeach
            </div>
            <div class="hero-overlay"></div>
        </div>

        {{-- Título Flutuante sobre o Carrossel --}}
        <div class="absolute bottom-12 left-0 w-full z-20 px-4">
            <div class="max-w-5xl mx-auto">
                <span class="bg-sky-500 text-white px-4 py-1 rounded-full font-black uppercase text-[10px] tracking-[0.2em] shadow-[0_0_20px_rgba(14,165,233,0.5)]">
                    {{ $evento->categoria->nome ?? 'Evento Exclusivo' }}
                </span>
                <h1 class="text-5xl md:text-7xl font-black text-white mt-4 leading-none tracking-tighter italic uppercase">
                    {{ $evento->titulo }}
                </h1>
            </div>
        </div>
    </div>

    {{-- 2. Corpo da Página --}}
    <div class="max-w-5xl mx-auto px-4 mt-8 space-y-8">
        
        {{-- Alertas de Sucesso --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="bg-sky-500/10 border border-sky-500/20 backdrop-blur-md text-sky-400 p-6 rounded-[32px] flex justify-between items-center">
                <p class="font-black text-xs uppercase tracking-widest">✓ {{ session('success') }}</p>
                <button @click="show = false" class="text-xl">&times;</button>
            </div>
        @endif

        {{-- Grid de Info e Interação --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Local e Data --}}
            <div class="md:col-span-2 bg-white/[0.03] border border-white/5 p-8 rounded-[40px] flex flex-wrap gap-12 items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/20 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(14,165,233,0.2)]">📍</div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest">Onde</p>
                        <p class="font-bold text-white text-lg">{{ $evento->localizacao }}</p>
                    </div>


                    {{-- Botão Chat com Organizador --}}
                    <div class="bg-white/[0.03] border border-white/5 p-8 rounded-[40px] flex flex-col justify-center">
                        <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-4 italic text-center">Dúvidas sobre o evento?</p>
                        
                        @auth
                            @if(auth()->id() !== $evento->user_id) {{-- Não mostrar se o dono for o próprio usuário --}}
                                <button onclick="window.Livewire.dispatch('startConversation', { userId: {{ $evento->user_id }} })" 
                                        class="flex items-center justify-center gap-3 w-full py-4 bg-sky-500/10 hover:bg-sky-500 text-sky-500 hover:text-white border border-sky-500/20 rounded-2xl transition-all duration-300 group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Chat com Organizador</span>
                                </button>
                            @else
                                <div class="text-center py-4 bg-white/5 rounded-2xl border border-dashed border-white/10">
                                    <span class="text-[9px] font-black uppercase text-slate-600">Você é o organizador</span>
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="text-center py-4 bg-white/5 rounded-2xl border border-white/10 text-[10px] font-black uppercase text-slate-400 hover:text-white transition">
                                Faça login para contactar
                            </a>
                        @endauth
                    </div>


                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/20 flex items-center justify-center text-xl shadow-[0_0_15px_rgba(14,165,233,0.2)]">📅</div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest">Quando</p>
                        <p class="font-bold text-white text-lg">{{ date('d/m/Y', strtotime($evento->data_evento)) }}</p>
                    </div>
                </div>
            </div>

            {{-- Likes/Dislikes --}}
            <div class="bg-white/[0.03] border border-white/5 p-8 rounded-[40px] flex flex-col justify-center items-center gap-4">
                <div class="flex items-center gap-6">
                    <form action="{{ route('evento.curtir', $evento->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-center group">
                            @php $jaCurtiu = auth()->check() && $evento->curtidas->where('user_id', auth()->id())->first(); @endphp
                            <div class="text-3xl {{ $jaCurtiu ? 'scale-125' : 'grayscale opacity-50' }} group-hover:grayscale-0 group-hover:opacity-100 transition-all">❤️</div>
                            <span class="text-[10px] font-black text-white mt-1 block">{{ $evento->curtidas->count() }}</span>
                        </button>
                    </form>
                    <form action="{{ route('evento.dislike', $evento->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-center group">
                            @php $jaDeuDislike = auth()->check() && \App\Models\Dislike::where('user_id', auth()->id())->where('evento_id', $evento->id)->exists(); @endphp
                            <div class="text-3xl {{ $jaDeuDislike ? 'scale-125' : 'grayscale opacity-50' }} group-hover:grayscale-0 group-hover:opacity-100 transition-all">👎</div>
                            <span class="text-[10px] font-black text-slate-500 mt-1 block">{{ \App\Models\Dislike::where('evento_id', $evento->id)->count() }}</span>
                        </button>
                    </form>
                </div>
                <div @click="showLikesModal = true" class="flex -space-x-2 cursor-pointer hover:scale-105 transition">
                    @foreach($evento->usuariosQueCurtiram->take(4) as $u)
                        <img src="{{ $u->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($u->name).'&background=0ea5e9&color=fff' }}" class="w-7 h-7 rounded-full border-2 border-[#020617] object-cover">
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Descrição Estilizada --}}
        <div class="bg-white/[0.02] border border-white/5 p-10 rounded-[40px]">
             <h3 class="text-sky-500 font-black uppercase text-[10px] tracking-[0.3em] mb-6 italic">Detalhes do Evento</h3>
             <div class="text-slate-400 text-lg leading-relaxed font-medium">
                {!! nl2br(e($evento->descricao)) !!}
             </div>
        </div>

        {{-- Bilheteira --}}
        <div class="space-y-6">
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter flex items-center gap-4">
                <span class="h-8 w-1.5 bg-sky-500 rounded-full shadow-[0_0_15px_#0ea5e9]"></span>
                Escolha o seu Pass
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($evento->tiposIngresso as $tipo)
                <div class="group bg-white/[0.03] border border-white/5 p-8 rounded-[40px] hover:bg-sky-500/5 hover:border-sky-500/40 transition-all duration-500 shadow-xl">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <p class="text-3xl font-black text-white italic tracking-tighter group-hover:text-sky-400 transition">{{ $tipo->nome }}</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase mt-2">{{ $tipo->quantidade_disponivel }} Restantes</p>
                        </div>
                        <p class="text-2xl font-black text-white italic tracking-tighter">
                            {{ number_format($tipo->preco, 0, ',', '.') }}<span class="text-sky-500 text-xs ml-1">Kz</span>
                        </p>
                    </div>
                    <button @click="open = true; ingressoNome = '{{ $tipo->nome }}'; ingressoId = '{{ $tipo->id }}'; ingressoPreco = {{ $tipo->preco }}" 
                            class="w-full py-5 bg-white text-black font-black rounded-3xl hover:bg-sky-500 hover:text-white transition-all uppercase text-[10px] tracking-[0.2em] shadow-lg">
                        Reservar Agora
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL DE QUEM CURTIU --}}
    <div x-show="showLikesModal" x-cloak class="fixed inset-0 z-[150] flex items-center justify-center bg-black/95 backdrop-blur-md p-4">
        <div class="bg-slate-900 border border-white/10 rounded-[40px] max-w-sm w-full p-8 shadow-2xl" @click.away="showLikesModal = false">
            <h3 class="text-white font-black uppercase text-center text-xs tracking-widest mb-8 border-b border-white/5 pb-4 italic">Interessados</h3>
            <div class="space-y-4 max-h-80 overflow-y-auto custom-scroll pr-2">
                @foreach($evento->usuariosQueCurtiram as $usuario)
                <div class="flex items-center gap-4 p-3 hover:bg-white/5 rounded-2xl transition">
                    <img src="{{ $usuario->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($usuario->name).'&background=0ea5e9&color=fff' }}" class="h-10 w-10 rounded-full border border-sky-500/30">
                    <p class="text-white font-bold text-sm">{{ $usuario->name }}</p>
                </div>
                @endforeach
            </div>
            <button @click="showLikesModal = false" class="w-full mt-8 py-4 bg-white/5 text-white text-[10px] font-black uppercase rounded-2xl border border-white/10 hover:bg-red-500 transition">Fechar</button>
        </div>
    </div>

    {{-- MODAL CHECKOUT --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 px-4 overflow-y-auto pt-20 pb-20">
        <div class="bg-slate-900 border border-white/10 rounded-[40px] max-w-lg w-full p-10 shadow-2xl relative" @click.away="open = false">
             <button @click="open = false" class="absolute top-8 right-8 text-slate-500 hover:text-white text-2xl transition">&times;</button>
             
             <h3 class="text-4xl font-black text-white uppercase italic mb-8 tracking-tighter">Checkout</h3>
             
             <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                <div class="p-8 bg-sky-500/5 border border-sky-500/20 rounded-[32px] flex justify-between items-center">
                    <div>
                        <p class="text-sky-500 font-black text-[10px] uppercase tracking-widest mb-1 italic">Tipo de Bilhete</p>
                        <p class="text-3xl font-black text-white italic leading-none" x-text="ingressoNome"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-black text-white italic tracking-tighter" x-text="(ingressoPreco * quantidade).toLocaleString() + ' Kz'"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="nome_cliente" placeholder="Teu Nome" required class="w-full p-5 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-sky-500 transition">
                        <input type="text" name="whatsapp" placeholder="Teu WhatsApp" required class="w-full p-5 bg-white/5 border border-white/10 rounded-2xl text-white outline-none focus:border-sky-500 transition">
                    </div>
                    
                    <div class="flex items-center justify-between bg-white/5 p-5 rounded-2xl border border-white/10">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest italic">Quantos bilhetes?</span>
                        <input type="number" name="quantidade" x-model="quantidade" min="1" class="bg-transparent text-white font-black text-right outline-none w-16 text-xl">
                    </div>

                    <div class="p-8 border-2 border-dashed border-white/10 rounded-3xl bg-white/[0.01] text-center group hover:border-sky-500 transition-colors">
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-4 tracking-[0.2em]">Upload do Comprovativo</label>
                        <input type="file" name="comprovativo" required class="text-xs text-slate-400 file:bg-sky-500 file:text-white file:border-none file:px-4 file:py-2 file:rounded-lg file:mr-4 file:font-black">
                    </div>
                </div>

                <button type="submit" class="w-full bg-sky-600 text-white font-black py-6 rounded-3xl hover:bg-white hover:text-black transition-all uppercase text-[10px] tracking-[0.3em] shadow-[0_15px_30px_rgba(14,165,233,0.2)]">
                    Finalizar Compra
                </button>
             </form>
        </div>
    </div>
</div>

{{-- Swiper Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: { delay: 5000 },
            speed: 3000,
        });
    });
</script>
@endsection