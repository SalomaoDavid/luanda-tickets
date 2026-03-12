<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Luanda Tickets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(18px); border-right: 1px solid rgba(59, 130, 246, 0.2); }
        .active-link { color: #60a5fa; font-weight: 600; }
        .hover-blue:hover { color: #60a5fa; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .page-transition { opacity: 0; transform: translateY(10px); animation: fadeIn 0.35s ease forwards; }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
        .btn-blue { background: #2563eb; color: white; padding: 8px 16px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-blue:hover { background: #1d4ed8; }
    </style>
</head>
<body class="text-white relative bg-slate-900">

@php $isHome = request()->routeIs('home'); @endphp

<div class="fixed inset-0 -z-10">
    <img src="{{ asset('images/luanda-noite.png') }}" class="w-full h-full object-cover brightness-50">
</div>

<header class="glass fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-10 z-50">
    <a href="{{ route('home') }}" class="text-xl font-bold">
        <span class="text-blue-400">Luanda</span> <span class="text-white">bilhetes</span>
    </a>

    <div class="hidden md:flex space-x-6 text-gray-300 items-center">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active-link' : 'hover-blue' }}">Ínicio</a>
        <a href="{{ route('eventos.todos') }}" class="{{ request()->routeIs('eventos.*') ? 'active-link' : 'hover-blue' }}">Explorar</a>
        <a href="{{ route('noticias.index') }}" class="{{ request()->routeIs('noticias.*') ? 'active-link' : 'hover-blue' }}">Notícias</a>
        @auth <a href="{{ route('mensagens.index') }}" class="{{ request()->routeIs('mensagens.*') ? 'active-link' : 'hover-blue' }}">Mensagens</a> @endauth
        <form action="{{ route('eventos.todos') }}" method="GET">
            <input type="text" name="search" placeholder="Buscar..." class="px-3 py-1 rounded-lg text-black text-sm focus:outline-none">
        </form>
    </div>

    <div class="flex items-center space-x-4">
        @guest
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white transition">Entrar</a>
            <a href="{{ route('register') }}" class="btn-blue">Cadastrar</a>
        @endguest

        @auth
        @php
            $user = auth()->user();
            $unread = $user->unreadNotifications->count();
        @endphp
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="relative focus:outline-none">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" class="w-10 h-10 rounded-full object-cover border-2 border-blue-400">
                @if($unread > 0) <span class="absolute -top-1 -right-1 bg-red-600 text-xs px-1.5 rounded-full">{{ $unread }}</span> @endif
            </button>
            <div x-show="open" x-cloak @click.away="open = false" x-transition class="absolute right-0 mt-3 w-56 bg-white text-gray-800 rounded-2xl shadow-2xl overflow-hidden z-[60]">
                <div class="p-4 border-b">
                    <p class="font-semibold">{{ explode(' ', $user->name)[0] }}</p>
                    <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 hover:bg-blue-50">Perfil</a>
                <form method="POST" action="{{ route('logout') }}"> @csrf <button class="w-full text-left px-4 py-3 hover:bg-red-100 text-red-600">Sair</button></form>
            </div>
        </div>
        @endauth
    </div>
</header>

@if($isHome)
  <div class="flex pt-16 h-screen overflow-hidden">
     <aside class="w-72 glass fixed left-0 top-16 bottom-0 p-6 
              overflow-y-auto no-scrollbar flex flex-col">

    @auth
    @php
        $user = auth()->user();
        $firstName = explode(' ', $user->name)[0];
        $role = ucfirst($user->role);
    @endphp

    <div class="mb-8 text-center">
        <img src="{{ $user->avatar 
        ? asset('storage/' . $user->avatar) 
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=128' }}"
        class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-blue-400 shadow-lg">

        <p class="mt-3 font-semibold text-lg uppercase tracking-wider text-white">
            {{ $firstName }}
        </p>

        <p class="text-sm text-gray-400">
            {{ $role }}
        </p>
    </div>
    @endauth

    <p class="text-gray-400 text-xs mb-2 font-bold uppercase">Geral</p>
    <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
        <a href="{{ route('home') }}" class="block hover-blue">🏠 Início</a>
        <a href="{{ route('eventos.todos') }}" class="block hover-blue">🎟 Eventos</a>

        @auth
        <a href="{{ route('mensagens.index') }}" class="block hover-blue">💬 Mensagens</a>
        <a href="{{ route('profile.edit') }}" class="block hover-blue">👤 Perfil</a>
        @endauth
    </nav>

    @auth
        {{-- MENU PARA ADMIN --}}
        @if($user->role === 'admin')
            <p class="text-gray-400 text-xs mb-2 font-bold uppercase border-t border-gray-700 pt-4">Administração</p>
            <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
                <a href="{{ route('admin.dashboard') }}" class="block hover-blue">📊 Dashboard</a>
                <a href="{{ route('admin.usuarios.index') }}" class="block hover-blue">👥 Usuários</a>
                <a href="{{ route('admin.eventos') }}" class="block hover-blue">🎫 Gerenciar Eventos</a>
                <a href="{{ route('admin.reservas') }}" class="block hover-blue">📦 Reservas Gerais</a>
                <a href="{{ route('admin.pagos') }}" class="block hover-blue">💰 Pagamentos</a>
                <a href="{{ route('admin.scanner') }}" class="block hover-blue">📸 Validar Bilhete</a>
            </nav>

        {{-- MENU PARA CRIADOR --}}
        @elseif($user->role === 'creator')
            <p class="text-gray-400 text-xs mb-2 font-bold uppercase border-t border-gray-700 pt-4">Painel Criador</p>
            <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
                <a href="{{ route('admin.eventos') }}" class="block hover-blue">📋 Meus Eventos</a>
                <a href="{{ route('admin.eventos.criar') }}" class="block hover-blue">➕ Criar Evento</a>
                <a href="{{ route('admin.reservas') }}" class="block hover-blue">📦 Minhas Reservas</a>
                <a href="{{ route('admin.pagos') }}" class="block hover-blue">💰 Meus Ganhos</a>
            </nav>
        @endif
    @endauth

   </aside>
    <main class="flex-1 ml-72 mr-72 overflow-y-auto no-scrollbar p-10 page-transition">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
    <aside class="w-72 glass fixed right-0 top-16 bottom-0 p-6">
        <h2 class="text-xl font-bold mb-6 text-blue-400">Populares</h2>
        @foreach(\App\Models\Evento::latest()->take(3)->get() as $ev)
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="block bg-gray-800 p-4 rounded-2xl mb-4 text-sm">{{ $ev->titulo }}</a>
        @endforeach
    </aside>
</div>
@else
<div class="pt-20 min-h-screen page-transition">
    <main class="max-w-6xl mx-auto p-10">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
</div>
@endif

@livewireScripts
</body>
</html>














@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')

<!-- Espaço de publicação -->
<div class="bg-white p-4 rounded-3xl shadow-xl">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-100 text-gray-800 p-3 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                  rows="2"></textarea>

        <div class="flex justify-between items-center mt-3">
            <div class="text-gray-400 text-sm">
                📷 Foto | 🎥 Vídeo | 🎟 Evento
            </div>
            <button type="submit" class="bg-blue-500 text-white px-5 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                Publicar
            </button>
        </div>
    </form>
</div>

<!-- Stories / Eventos em Carrossel -->
<div class="relative overflow-hidden my-4">
    <div id="stories-track" class="flex space-x-6 pb-4 transition-transform duration-500 ease-in-out">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0">
            <div class="w-20 h-20 rounded-full border-4 border-blue-400 overflow-hidden shadow-lg">
                <img src="{{ $evento->imagem_capa ? asset('storage/' . $evento->imagem_capa) : 'https://picsum.photos/200' }}"
                     class="w-full h-full object-cover">
            </div>
            <p class="mt-1 text-sm text-gray-800 truncate w-20">{{ $evento->titulo }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Posts -->
@foreach($eventos as $evento)
<div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">

    <!-- Cabeçalho: foto do criador + nome + data -->
    <div class="flex items-center space-x-3 p-4">
        <img src="{{ $evento->user && $evento->user->avatar
            ? asset('storage/' . $evento->user->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($evento->user->name ?? 'U') . '&background=0ea5e9&color=fff&size=128' }}"
             class="w-11 h-11 rounded-full border-2 border-blue-400 object-cover">
        <div>
            <h3 class="font-bold text-sm text-gray-900">
                {{ $evento->user ? $evento->user->name : 'Usuário' }}
            </h3>
            <p class="text-xs text-gray-400">
                {{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}
            </p>
        </div>
    </div>

    <!-- PARTE SUPERIOR: Foto do evento + título sobreposto -->
    <div class="relative">
        @if($evento->imagem_capa)
            <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-full h-56 object-cover">
        @else
            <div class="w-full h-56 bg-gradient-to-br from-blue-600 to-blue-900 flex items-center justify-center">
                <span class="text-white text-4xl">🎟</span>
            </div>
        @endif

        <!-- Título sobre a foto -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-5 py-4">
            <h2 class="text-white text-lg font-bold drop-shadow">{{ $evento->titulo }}</h2>
        </div>
    </div>

    <!-- PARTE INFERIOR: Informações + ações -->
    <div class="p-5">

        <!-- Detalhes do evento -->
        <div class="flex flex-col space-y-1 text-sm text-gray-500 mb-4">
            <span>📍 {{ $evento->localizacao ?? 'Local não informado' }}</span>
            <span>📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
            <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
        </div>

        <!-- Descrição -->
        <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 mb-4">
            {{ $evento->descricao }}
        </p>

        <!-- Ações -->
        <div class="flex justify-between items-center text-gray-500 text-sm border-t pt-4">

            <div class="flex flex-col space-y-2">
                <!-- Botão curtir -->
                <form method="POST" action="{{ route('evento.curtir', $evento->id) }}">
                    @csrf
                    <button type="submit" class="hover:text-blue-500 transition duration-300">
                        👍 Curtir ({{ $evento->curtidas->count() }})
                    </button>
                </form>

                <!-- Fotos dos utilizadores que curtiram -->
                @if($evento->usuariosQueCurtiram->count() > 0)
                <div class="flex items-center">
                    @foreach($evento->usuariosQueCurtiram->take(5) as $user)
                    <img src="{{ $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-6 h-6 rounded-full border-2 border-white object-cover -ml-1 first:ml-0"
                         title="{{ $user->name }}">
                    @endforeach

                    @if($evento->usuariosQueCurtiram->count() > 5)
                        <span class="text-xs text-gray-400 ml-2">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>
                    @endif
                </div>
                @endif
            </div>

            <button class="hover:text-blue-500 transition duration-300">
                💬 Comentar
            </button>

            <a href="{{ route('evento.detalhes', $evento->id) }}"
               class="bg-blue-500 text-white px-4 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                🎟 Comprar
            </a>
        </div>

    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('stories-track');
        const itemWidth = 104;
        let currentIndex = 0;
        const totalItems = track.children.length;

        setInterval(() => {
            if (totalItems <= 1) return;

            currentIndex++;

            if (currentIndex >= totalItems) {
                currentIndex = 0;
                track.style.transition = 'none';
                track.style.transform = `translateX(0px)`;
                return;
            }

            track.style.transition = 'transform 0.5s ease-in-out';
            track.style.transform = `translateX(-${currentIndex * itemWidth}px)`;

        }, 2500);
    });
</script>
@endsection

