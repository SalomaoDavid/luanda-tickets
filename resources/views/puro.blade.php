<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luanda Tickets')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        .glass-panel {
            background: rgba(15, 23, 42, 0.6) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        * { -ms-overflow-style: none; scrollbar-width: none; }
        *::-webkit-scrollbar { display: none; }

        body {
            background: #020617;
            color: white;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .post-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        [x-cloak] { display: none !important; }

        .bg-main-image {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover; z-index: -20;
            filter: brightness(0.6);
        }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ sideMenu: false }">
    <img src="{{ asset('images/luanda-noite.png') }}" class="bg-main-image" alt="Luanda Background">

    {{-- BARRA DE GESTÃO FLUTUANTE (Aparece apenas para Admin e Creator) --}}
    @auth
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'creator')
            <div class="fixed top-6 right-6 z-[100] flex gap-3">
                
                {{-- Botão de Notificações/Mensagens rápido --}}
                <a href="{{ route('mensagens.index') }}" class="glass-panel p-4 rounded-2xl hover:bg-sky-500/20 transition border border-white/10">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </a>

                            
                           {{-- Menu de Administração --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="glass-panel px-6 py-4 rounded-2xl flex items-center gap-3 border border-white/10 hover:border-sky-500/50 transition">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] italic">Painel {{ ucfirst(auth()->user()->role) }}</span>
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" 
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            @click.away="open = false" 
                            x-cloak 
                            class="absolute right-0 mt-3 w-64 glass-panel rounded-[2rem] p-4 shadow-2xl border border-white/10 backdrop-blur-3xl z-[110]">
                            
                            <div class="flex flex-col gap-1">
                                {{-- SEÇÃO: GESTÃO DE EVENTOS --}}
                                <p class="px-4 py-2 text-[8px] font-black text-slate-500 uppercase tracking-widest">Gestão de Eventos</p>
                                
                                {{-- Criar Evento --}}
                                <a href="{{ route('admin.eventos.criar') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group">
                                    <svg class="w-4 h-4 text-sky-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white text-slate-300">Criar Novo Evento</span>
                                </a>

                                {{-- Meus Eventos / Lista --}}
                                <a href="{{ route('admin.eventos') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group text-slate-300">
                                    <svg class="w-4 h-4 text-sky-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white">{{ auth()->user()->role === 'admin' ? 'Todos Eventos' : 'Meus Eventos' }}</span>
                                </a>

                                {{-- SEÇÃO: FINANCEIRO --}}
                                <div class="h-px bg-white/5 my-2"></div>
                                <p class="px-4 py-2 text-[8px] font-black text-slate-500 uppercase tracking-widest">Financeiro & Reservas</p>

                                {{-- Admin Pagos --}}
                                <a href="{{ route('admin.pagos') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group text-slate-300">
                                    <svg class="w-4 h-4 text-green-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white">Admin Pagos</span>
                                </a>

                                {{-- Admin Reservas --}}
                                <a href="{{ route('admin.reservas') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group text-slate-300">
                                    <svg class="w-4 h-4 text-yellow-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white">Admin Reservas</span>
                                </a>

                                {{-- SEÇÃO EXCLUSIVA: ADMIN --}}
                                @if(auth()->user()->role === 'admin')
                                    <div class="h-px bg-white/5 my-2"></div>
                                    <p class="px-4 py-2 text-[8px] font-black text-sky-500 uppercase tracking-widest text-slate-300">Administração Total</p>
                                    
                                    {{-- Usuários --}}
                                    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group text-slate-300">
                                        <svg class="w-4 h-4 text-sky-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white">Gerir Usuários</span>
                                    </a>

                                    {{-- Sincronizar Notícias --}}
                                    <a href="{{ route('noticias.sincronizar') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-sky-500 rounded-xl transition group text-slate-300">
                                        <svg class="w-4 h-4 text-sky-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m13 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider group-hover:text-white">Sincronizar Notícias</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>             
            </div>
        @endif
    @endauth

    <div class="relative z-10">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    {{-- MODAL DE LOGIN --}}
    <div id="loginModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[9999] hidden flex items-center justify-center p-4">
        <div class="glass-panel p-10 rounded-[40px] max-w-sm w-full text-center border border-white/20">
            <h3 class="text-white font-black text-xl uppercase italic mb-6">Acesso Restrito</h3>
            <p class="text-slate-400 text-xs mb-8">Faça login para interagir com a comunidade Luanda Tickets.</p>
            <div class="flex flex-col gap-3">
                <a href="{{ route('login') }}" class="block w-full py-4 bg-sky-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-sky-400 transition">Entrar Agora</a>
                <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="text-slate-500 font-black text-[10px] uppercase py-2 text-slate-300">Voltar</button>
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').classList.remove('hidden');
        }
    </script>
</body>
</html>














@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')
<div class="px-4">
    <div class="main-grid">
        
        <aside class="left-sidebar">
            <div class="sticky top-24 space-y-2">
                <nav class="space-y-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-4 text-sky-500 font-black text-xs uppercase p-4 bg-sky-500/10 rounded-2xl border border-sky-500/20 transition-all">
                        <span class="text-xl">🏠</span> Feed de Eventos
                    </a>
                    <a href="{{ route('eventos.todos') }}" class="flex items-center gap-4 text-slate-400 font-black text-xs uppercase p-4 hover:bg-white/5 hover:text-white rounded-2xl transition-all">
                        <span class="text-xl">🎫</span> Explorar Bilhetes
                    </a>
                    <a href="#" class="flex items-center gap-4 text-slate-400 font-black text-xs uppercase p-4 hover:bg-white/5 hover:text-white rounded-2xl transition-all">
                        <span class="text-xl">⭐</span> Artistas VIP
                    </a>
                    <a href="{{ route('noticias.index') }}" class="flex items-center gap-4 text-slate-400 font-black text-xs uppercase p-4 hover:bg-white/5 hover:text-white rounded-2xl transition-all">
                        <span class="text-xl">📰</span> Notícias
                    </a>
                </nav>

                <div class="px-4 py-8 border-t border-white/5 mt-4">
                    <p class="text-[8px] text-slate-600 uppercase font-black tracking-[0.3em]">Luanda Tickets &bull; 2026</p>
                </div>
            </div>
        </aside>

        <main>
            <div class="flex gap-6 overflow-x-auto pb-8 no-scrollbar">
                @foreach($eventos->take(8) as $e)
                <div class="flex flex-col items-center gap-2 min-w-[80px] group cursor-pointer">
                    <div class="story-circle group-hover:scale-105 transition-transform">
                        <img src="{{ asset('storage/' . $e->imagem_capa) }}" class="story-img">
                    </div>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter truncate w-16 text-center group-hover:text-sky-400 transition-colors">{{ $e->titulo }}</span>
                </div>
                @endforeach
            </div>

            @foreach($eventos as $evento)
            <article class="post-card">
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-sky-900 flex items-center justify-center font-black text-sky-400 text-xs italic border border-sky-500/30 shadow-inner">LT</div>
                        <div>
                            <div class="flex items-center">
                                <h5 class="text-white font-black text-[11px] uppercase italic">Luanda Tickets Oficial</h5>
                                <span class="verified-badge">✓</span>
                            </div>
                            <span class="text-[9px] text-slate-500 uppercase tracking-widest">Postado em Luanda</span>
                        </div>
                    </div>
                    <button class="text-slate-500 hover:text-white transition">•••</button>
                </div>

                <div class="relative group">
                    <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-full aspect-video object-cover transition-all duration-700 group-hover:brightness-75">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center backdrop-blur-sm">
                        <a href="{{ route('evento.detalhes', $evento->id) }}" class="bg-white text-black px-8 py-3 rounded-full font-black text-[10px] uppercase tracking-widest scale-90 group-hover:scale-100 transition shadow-2xl hover:bg-sky-500 hover:text-white">Adquirir Pass</a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex gap-6">
                            <button onclick="openModal(event)" class="flex items-center gap-2 text-white/70 hover:text-red-500 transition font-black text-xs">❤️ <span>{{ $evento->curtidas->count() }}</span></button>
                            <button onclick="openModal(event)" class="flex items-center gap-2 text-white/70 hover:text-sky-500 transition font-black text-xs">💬 <span>12</span></button>
                            <button class="flex items-center gap-2 text-white/70 hover:text-green-500 transition font-black text-xs">🔗</button>
                        </div>
                        <div class="photo-stack flex items-center">
                            <img src="https://i.pravatar.cc/100?u=1">
                            <img src="https://i.pravatar.cc/100?u=2">
                            <img src="https://i.pravatar.cc/100?u=3">
                            <span class="text-[9px] text-slate-500 ml-2 font-black uppercase">+{{ rand(20, 100) }} Vão</span>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter mb-2">{{ $evento->titulo }}</h3>
                    <p class="text-slate-400 text-xs leading-relaxed mb-6 line-clamp-2 italic">{{ $evento->descricao }}</p>

                    <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-black text-sky-400 uppercase italic">Destaque</span>
                            <span class="verified-badge">✓</span>
                        </div>
                        <p class="text-[11px] text-slate-300 italic">"Garante já o teu lugar, os bilhetes estão a voar! 🚀"</p>
                    </div>
                </div>
            </article>
            @endforeach
        </main>

        <aside class="right-sidebar">
            <div class="sticky top-24 space-y-6">
                <div class="post-card p-6">
                    <h3 class="text-white font-black text-[10px] uppercase italic tracking-[0.2em] mb-6 text-sky-500">// EM_ALTA_LUANDA</h3>
                    <div class="space-y-4">
                        @foreach($eventos->take(4) as $index => $trend)
                        <div class="trending-item group cursor-pointer border-b border-white/5 pb-2 last:border-0">
                            <p class="text-[9px] text-slate-500 font-black uppercase italic">{{ $index + 1 }} º • {{ $trend->categoria->nome ?? 'Show' }}</p>
                            <h5 class="text-white font-black text-xs uppercase group-hover:text-sky-500 transition italic truncate">{{ $trend->titulo }}</h5>
                            <span class="text-[9px] text-sky-500/50 font-black uppercase">{{ rand(100, 500) }} Menções</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="post-card p-6">
                    <h3 class="text-white font-black text-[10px] uppercase italic tracking-[0.2em] mb-6 text-sky-500">// ARTISTAS_VIP</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="https://i.pravatar.cc/100?u=c4" class="w-9 h-9 rounded-full border border-sky-500/50 group-hover:scale-110 transition-transform">
                                    <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 rounded-full border border-[#020617]"></span>
                                </div>
                                <div>
                                    <h6 class="text-white font-black text-[10px] uppercase flex items-center">C4 Pedro <span class="verified-badge">✓</span></h6>
                                    <span class="text-[8px] text-slate-500 uppercase">Ativo agora</span>
                                </div>
                            </div>
                            <button onclick="openModal(event)" class="text-[9px] font-black text-sky-500 uppercase border border-sky-500/20 px-3 py-1 rounded-lg hover:bg-sky-500 hover:text-white transition-all">Seguir</button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

    </div>
</div>

{{-- MODAL DE ACESSO RESTRITO --}}
<div id="loginModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[2000] hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/10 p-10 rounded-[40px] max-w-sm w-full text-center shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-sky-500 to-transparent"></div>
        <div class="w-20 h-20 bg-sky-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-sky-500/20">
            <span class="text-4xl">🔑</span>
        </div>
        <h3 class="text-white font-black text-xl uppercase italic mb-2 tracking-tighter">Entrada Restrita</h3>
        <p class="text-slate-400 text-[10px] mb-8 uppercase tracking-[0.1em] leading-relaxed">
            Faz login na tua conta para interagir com o feed e reservar os teus bilhetes.
        </p>
        <div class="space-y-3">
            <a href="{{ route('login') }}" class="block w-full py-4 bg-sky-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-sky-400 transition-all shadow-lg shadow-sky-500/20">Fazer Login</a>
            <button onclick="closeModal()" class="block w-full py-4 text-slate-600 font-black text-[10px] uppercase tracking-[0.2em] hover:text-white transition">Fechar</button>
        </div>
    </div>
</div>

<script>
    function openModal(e) {
        @guest
            if(e) e.preventDefault();
            document.getElementById('loginModal').classList.remove('hidden');
        @else
            console.log("Ação permitida");
        @endguest
    }

    function closeModal() {
        document.getElementById('loginModal').classList.add('hidden');
    }

    window.onclick = function(event) {
        let modal = document.getElementById('loginModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection