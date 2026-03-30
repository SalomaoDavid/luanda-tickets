<!DOCTYPE html>
{{-- --}}
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Luanda Tickets</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(18px); border-right: 1px solid rgba(59, 130, 246, 0.2); }
        .glass-sidebar { background: rgba(15, 23, 42, 0.05); backdrop-filter: blur(0px); border-right: 1px solid rgba(59, 130, 246, 0.2); }
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

        {{-- Sino de Notificações --}}

        
        <livewire:notification-bell />

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
                <a href="{{ route('profile.show', ['id' => auth()->user()->id]) }}" class="block px-4 py-3 hover:bg-blue-50">Perfil</a>
                <form method="POST" action="{{ route('logout') }}"> @csrf <button class="w-full text-left px-4 py-3 hover:bg-red-100 text-red-600">Sair</button></form>
            </div>
        </div>
        @endauth
    </div>
</header>

@if($isHome)
  <div class="flex pt-16 h-screen overflow-hidden">

    <!-- SIDEBAR ESQUERDA -->
    <aside class="w-72 glass-sidebar fixed left-0 top-16 bottom-0 p-6 overflow-y-auto no-scrollbar flex flex-col">

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
            <a href="#" class="block hover-blue">🏠 Notificações</a>
            <a href="{{ route('eventos.todos') }}" class="block hover-blue">🎟 Eventos</a>

            @auth
            <a href="{{ route('mensagens.index') }}" class="block hover-blue">💬 Mensagens</a>
            <a href="{{ route('profile.edit') }}" class="block hover-blue">👤 Perfil</a>
            @endauth
        </nav>

        @auth
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

    <!-- CONTEÚDO CENTRAL -->
    <main class="flex-1 ml-72 mr-72 overflow-y-auto no-scrollbar p-10 page-transition">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- SIDEBAR DIREITA -->
    <aside class="w-72 glass-sidebar fixed right-0 top-16 bottom-0 p-6 overflow-y-auto no-scrollbar">

        <!-- Eventos Populares -->
        <h2 class="text-sm font-bold mb-4 text-blue-400 uppercase tracking-widest">🎟 Populares</h2>
        @foreach(\App\Models\Evento::latest()->take(3)->get() as $ev)
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="block bg-gray-800/50 p-4 rounded-2xl mb-3 text-sm hover:bg-gray-700/50 transition">
                {{ $ev->titulo }}
            </a>
        @endforeach

        <!-- Criadores de Eventos -->
        <h2 class="text-sm font-bold mb-4 mt-6 text-blue-400 uppercase tracking-widest">🎨 Criadores</h2>
        @foreach(\App\Models\User::where('role', 'creator')->whereHas('eventos')->latest()->get() as $criador)
        <a href="{{ route('profile.show', $criador->id) }}"
           class="flex items-center space-x-3 p-3 rounded-2xl hover:bg-gray-700/50 transition mb-2">
            <div class="relative flex-shrink-0">
                <img src="{{ $criador->avatar
                    ? asset('storage/' . $criador->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($criador->name) . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-gray-900
                    {{ $criador->isOnline() ? 'bg-green-500' : 'bg-gray-400' }}">
                </span>
            </div>
            <div>
                <p class="text-white text-sm font-semibold leading-tight">{{ explode(' ', $criador->name)[0] }}</p>
                <p class="text-gray-400 text-xs">{{ $criador->eventos->count() }} evento(s)</p>
            </div>
        </a>
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
@stack('modals')
</body>
</html> 















@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')

<!-- Stories / Eventos em Carrossel Infinito -->
<div class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm rounded-2xl py-3 mb-4 overflow-hidden">
    <div id="stories-track" class="flex" style="will-change: transform;">
        @foreach($eventos as $evento)
        <div class="text-center flex-shrink-0 story-item px-3">
            <a href="{{ route('evento.detalhes', $evento->id) }}">
                <div class="w-20 h-20 rounded-full border-4 border-blue-400 overflow-hidden shadow-lg hover:border-blue-600 hover:scale-105 transition">
                    <img src="{{ $evento->imagem_capa ? asset('storage/' . $evento->imagem_capa) : 'https://picsum.photos/200' }}"
                         class="w-full h-full object-cover">
                </div>
                <p class="mt-1 text-sm text-gray-800 truncate w-20 hover:text-blue-500 transition">{{ $evento->titulo }}</p>
            </a>
        </div>
        @endforeach
    </div>
</div>

<!-- Espaço de publicação -->
<div class="bg-white p-4 rounded-3xl shadow-xl mb-6">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-100 text-gray-800 p-3 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                  rows="2"></textarea>
        <div class="flex justify-between items-center mt-3">
            <div class="text-gray-400 text-sm">📷 Foto | 🎥 Vídeo | 🎟 Evento</div>
            <button type="submit" class="bg-blue-500 text-white px-5 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                Publicar
            </button>
        </div>
    </form>
</div>

<!-- Feed unificado -->
@foreach($feed as $entry)

    @if($entry['tipo'] === 'post')
    {{-- CARD DE POSTAGEM --}}
    <div class="bg-white rounded-3xl shadow-xl p-5 mb-4">
        <div class="flex items-center space-x-3 mb-3">
            <a href="{{ route('profile.show', $entry['item']->user->id) }}">
                <img src="{{ $entry['item']->user->avatar
                    ? asset('storage/' . $entry['item']->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($entry['item']->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                     class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div>
                <a href="{{ route('profile.show', $entry['item']->user->id) }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $entry['item']->user->name }}
                </a>
                <p class="text-xs text-gray-400">{{ $entry['item']->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm leading-relaxed">{{ $entry['item']->conteudo }}</p>
    </div>

    @else
    {{-- CARD DE EVENTO --}}
    @php $evento = $entry['item']; @endphp
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">

        {{-- Cabeçalho do criador --}}
        <div class="flex items-center space-x-3 p-4">
            <a href="{{ route('profile.show', $evento->user->id ?? '#') }}">
                <img src="{{ $evento->user && $evento->user->avatar
                    ? asset('storage/' . $evento->user->avatar)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($evento->user->name ?? 'U') . '&background=0ea5e9&color=fff&size=128' }}"
                     class="w-11 h-11 rounded-full border-2 border-blue-400 object-cover">
            </a>
            <div>
                <a href="{{ route('profile.show', $evento->user->id ?? '#') }}"
                   class="font-bold text-sm text-gray-900 hover:text-blue-500 transition">
                    {{ $evento->user ? $evento->user->name : 'Usuário' }}
                </a>
                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}</p>
            </div>
        </div>

        {{-- Imagem --}}
        <div class="relative">
            @if($evento->imagem_capa)
                <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-full h-56 object-cover">
            @else
                <div class="w-full h-56 bg-gradient-to-br from-blue-600 to-blue-900 flex items-center justify-center">
                    <span class="text-white text-4xl">🎟</span>
                </div>
            @endif
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent px-5 py-4">
                <h2 class="text-white text-lg font-bold drop-shadow">{{ $evento->titulo }}</h2>
            </div>
        </div>

        {{-- Infos --}}
        <div class="p-5">
            <div class="flex flex-col space-y-1 text-sm text-gray-500 mb-4">
                <span>📍 {{ $evento->localizacao ?? 'Local não informado' }}</span>
                <span>📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
                <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 mb-4">{{ $evento->descricao }}</p>

            {{-- Ações --}}
            <div class="flex justify-between items-center text-gray-500 text-sm border-t pt-4">
                <div class="flex flex-col space-y-2">
                    <form method="POST" action="{{ route('evento.curtir', $evento->id) }}">
                        @csrf
                        <button 
                            onclick="toggleCurtida({{ $evento->id }}, this)"
                            data-curtido="{{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'true' : 'false' }}"
                            data-total="{{ $evento->curtidas->count() }}"
                            class="hover:text-blue-500 transition duration-300 {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'text-blue-500' : '' }}">
                            👍 <span class="curtida-texto-{{ $evento->id }}">{{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'Curtido' : 'Curtir' }}</span> 
                            (<span class="curtida-count-{{ $evento->id }}">{{ $evento->curtidas->count() }}</span>)
                        </button>
                    </form>

                    @if($evento->usuariosQueCurtiram->count() > 0)
                    @php $curtidores = $evento->usuariosQueCurtiram->reverse(); @endphp
                    <button onclick="abrirModalCurtidas('modal-curtidas-{{ $evento->id }}')" class="flex items-center group">
                        <div class="flex" style="direction: rtl;">
                            @foreach($curtidores->take(5) as $user)
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                 class="w-7 h-7 rounded-full border-2 border-white object-cover -mr-2" style="direction: ltr;" title="{{ $user->name }}">
                            @endforeach
                        </div>
                        @if($evento->usuariosQueCurtiram->count() > 5)
                            <span class="text-xs text-gray-400 ml-4">+{{ $evento->usuariosQueCurtiram->count() - 5 }}</span>
                        @endif
                    </button>
                    @endif
                </div>

                {{-- Botão comentar abre modal --}}
                <button onclick="abrirModalComentarios('modal-comentarios-{{ $evento->id }}')"
                        class="hover:text-blue-500 transition duration-300">
                    💬 Comentar ({{ $evento->comentarios->count() }})
                </button>

                <a href="{{ route('evento.detalhes', $evento->id) }}"
                   class="bg-blue-500 text-white px-4 py-1.5 rounded-xl font-semibold hover:scale-105 transition">
                    🎟 Comprar
                </a>
            </div>
        </div>
    </div>
          {{-- ═══════════════════════════════════════
                MODAL COMENTÁRIOS
            ═══════════════════════════════════════ --}}
        <div id="modal-comentarios-{{ $evento->id }}"
            class="hidden fixed inset-0 z-[9999] items-center justify-center"
            style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
            <div class="w-full max-w-lg mx-4 rounded-3xl overflow-hidden flex flex-col"
                style="max-height: 80vh; background: rgba(15,23,42,0.97); border: 1px solid rgba(59,130,246,0.2);">

                {{-- Header modal --}}
                <div class="flex justify-between items-center px-6 py-4"
                    style="border-bottom: 1px solid rgba(59,130,246,0.15);">
                    <h3 class="font-black text-white text-sm uppercase tracking-widest">
                        💬 Comentários ({{ $evento->comentarios->count() }})
                    </h3>
                    <button onclick="fecharModalComentarios('modal-comentarios-{{ $evento->id }}')"
                            class="text-gray-400 hover:text-white text-xl font-bold transition">✕</button>
                </div>

                {{-- Lista de comentários --}}
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-5"
                    style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

                    @forelse($evento->comentarios as $comentario)
                    <div class="flex gap-3" id="comentario-{{ $comentario->id }}">
                        <a href="{{ route('profile.show', $comentario->user->id) }}" class="flex-shrink-0">
                            <img src="{{ $comentario->user->avatar
                                ? asset('storage/' . $comentario->user->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($comentario->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                        </a>
                        <div class="flex-1">
                            <div class="rounded-2xl rounded-tl-sm px-4 py-3"
                                style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                                <div class="flex justify-between items-start gap-2">
                                    <a href="{{ route('profile.show', $comentario->user->id) }}"
                                    class="font-bold text-white text-xs hover:text-blue-400 transition">
                                        {{ $comentario->user->name }}
                                    </a>
                                    @if(auth()->id() === $comentario->user_id)
                                    <form method="POST" action="{{ route('comentario.eliminar', $comentario->id) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-600 hover:text-red-400 transition text-xs">🗑</button>
                                    </form>
                                    @endif
                                </div>
                                <p class="text-gray-200 text-sm mt-1 leading-relaxed">{{ $comentario->corpo }}</p>
                            </div>

                            <div class="flex items-center gap-4 mt-1.5 ml-1">
                                <span class="text-gray-500 text-[10px]">{{ $comentario->created_at->diffForHumans() }}</span>
                                <form method="POST" action="{{ route('comentario.like', $comentario->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-[11px] font-bold transition {{ $comentario->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                        👍 {{ $comentario->likes->count() > 0 ? $comentario->likes->count() : '' }}
                                    </button>
                                </form>
                                @auth
                                <button onclick="toggleResposta('resposta-form-{{ $comentario->id }}')"
                                        class="text-[11px] font-bold text-gray-500 hover:text-blue-400 transition">
                                    Responder
                                </button>
                                @endauth
                            </div>

                            @auth
                            <div id="resposta-form-{{ $comentario->id }}" class="hidden mt-2">
                                <form method="POST" action="{{ route('evento.comentar', $evento->id) }}" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comentario->id }}">
                                    <img src="{{ auth()->user()->avatar
                                        ? asset('storage/' . auth()->user()->avatar)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                        class="w-7 h-7 rounded-full object-cover border border-blue-400 flex-shrink-0">
                                    <input type="text" name="corpo" placeholder="Escreve uma resposta..."
                                        class="flex-1 rounded-xl px-3 py-1.5 text-xs text-white placeholder-gray-500 outline-none"
                                        style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                                    <button type="submit"
                                            class="text-white text-xs font-bold px-3 py-1.5 rounded-xl transition"
                                            style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        ➤
                                    </button>
                                </form>
                            </div>
                            @endauth

                            @if($comentario->respostas->count() > 0)
                            <div class="mt-3 space-y-3 pl-2"
                                style="border-left: 2px solid rgba(59,130,246,0.2);">
                                @foreach($comentario->respostas as $resposta)
                                <div class="flex gap-2">
                                    <a href="{{ route('profile.show', $resposta->user->id) }}" class="flex-shrink-0">
                                        <img src="{{ $resposta->user->avatar
                                            ? asset('storage/' . $resposta->user->avatar)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($resposta->user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                                            class="w-7 h-7 rounded-full object-cover border border-blue-400">
                                    </a>
                                    <div class="flex-1">
                                        <div class="rounded-2xl rounded-tl-sm px-3 py-2"
                                            style="background: rgba(30,41,59,0.6); border: 1px solid rgba(59,130,246,0.08);">
                                            <div class="flex justify-between items-start">
                                                <a href="{{ route('profile.show', $resposta->user->id) }}"
                                                class="font-bold text-white text-[11px] hover:text-blue-400 transition">
                                                    {{ $resposta->user->name }}
                                                </a>
                                                @if(auth()->id() === $resposta->user_id)
                                                <form method="POST" action="{{ route('comentario.eliminar', $resposta->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-600 hover:text-red-400 transition text-[10px]">🗑</button>
                                                </form>
                                                @endif
                                            </div>
                                            <p class="text-gray-300 text-xs mt-0.5 leading-relaxed">{{ $resposta->corpo }}</p>
                                        </div>
                                        <div class="flex items-center gap-3 mt-1 ml-1">
                                            <span class="text-gray-500 text-[10px]">{{ $resposta->created_at->diffForHumans() }}</span>
                                            <form method="POST" action="{{ route('comentario.like', $resposta->id) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="text-[10px] font-bold transition {{ $resposta->jaGostei() ? 'text-blue-400' : 'text-gray-500 hover:text-blue-400' }}">
                                                    👍 {{ $resposta->likes->count() > 0 ? $resposta->likes->count() : '' }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-600">
                        <p class="text-2xl mb-2">💬</p>
                        <p class="text-xs font-black uppercase tracking-widest">Sem comentários ainda</p>
                        <p class="text-xs mt-1">Sê o primeiro a comentar!</p>
                    </div>
                    @endforelse
                </div>

                {{-- Input novo comentário --}}
                @auth
                <div class="px-6 py-4" style="border-top: 1px solid rgba(59,130,246,0.15);">
                    <form id="form-comentario-{{ $evento->id }}" class="flex gap-3 items-center"
                        onsubmit="enviarComentario(event, {{ $evento->id }})">
                        @csrf
                        <img src="{{ auth()->user()->avatar
                            ? asset('storage/' . auth()->user()->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                            class="w-9 h-9 rounded-full object-cover border-2 border-blue-400 flex-shrink-0">
                        <input type="text" name="corpo" placeholder="Escreve um comentário..."
                            class="flex-1 rounded-2xl px-4 py-2.5 text-sm text-white placeholder-gray-500 outline-none transition"
                            style="background: rgba(30,41,59,0.8); border: 1px solid rgba(59,130,246,0.2);">
                        <button type="submit"
                                class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold transition hover:scale-105 flex-shrink-0"
                                style="background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 4px 15px rgba(37,99,235,0.3);">
                            ➤
                        </button>
                    </form>
                </div>
                @else
                <div class="px-6 py-4 text-center" style="border-top: 1px solid rgba(59,130,246,0.15);">
                    <a href="{{ route('login') }}" class="text-blue-400 text-sm font-bold hover:text-blue-300 transition">
                        Entra para comentar →
                    </a>
                </div>
                @endauth

            </div>
        </div>

    {{-- Modal curtidas --}}
    <div id="modal-curtidas-{{ $evento->id }}"
         class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/50"
         style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;">
        <div class="bg-white rounded-3xl shadow-2xl w-80 max-h-96 overflow-hidden"
             style="position: relative; z-index: 10000;">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="font-bold text-gray-900">👍 Curtidas ({{ $evento->usuariosQueCurtiram->count() }})</h3>
                <button onclick="fecharModalCurtidas('modal-curtidas-{{ $evento->id }}')"
                        class="text-gray-400 hover:text-gray-700 text-xl font-bold">✕</button>
            </div>
            <div class="overflow-y-auto max-h-72 p-4 space-y-3">
                @foreach($evento->usuariosQueCurtiram->reverse() as $user)
                <a href="{{ route('profile.show', $user->id) }}"
                   class="flex items-center space-x-3 hover:bg-gray-50 rounded-xl p-1 transition">
                    <img src="{{ $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=64' }}"
                         class="w-10 h-10 rounded-full border-2 border-blue-400 object-cover">
                    <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @endif
@endforeach

<script>
    // ── Carrossel ────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('stories-track');
        const items = Array.from(track.querySelectorAll('.story-item'));

        if (items.length > 1) {
            for (let i = 0; i < 3; i++) {
                items.forEach(item => {
                    const clone = item.cloneNode(true);
                    track.appendChild(clone);
                });
            }

            const itemWidth = 104;
            const totalOriginalWidth = items.length * itemWidth;
            let position = 0;

            function animate() {
                position += 0.5;
                if (position >= totalOriginalWidth) {
                    position = 0;
                    track.style.transition = 'none';
                    track.style.transform = `translateX(0px)`;
                } else {
                    track.style.transition = 'none';
                    track.style.transform = `translateX(-${position}px)`;
                }
                requestAnimationFrame(animate);
            }
            requestAnimationFrame(animate);
        }
    });

    // ── Modal Curtidas ───────────────────────────────────────
    function abrirModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }

    function fecharModalCurtidas(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Modal Comentários ────────────────────────────────────
    function abrirModalComentarios(id) {
        const modal = document.getElementById(id);
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '99999';
        modal.classList.remove('hidden');
        document.body.appendChild(modal);
    }

    function fecharModalComentarios(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    // ── Toggle form de resposta ──────────────────────────────
    function toggleResposta(id) {
        const form = document.getElementById(id);
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.querySelector('input').focus();
        }
    }

    // ── Fechar modais ao clicar fora ─────────────────────────
    document.addEventListener('click', function(e) {
        ['modal-curtidas-', 'modal-comentarios-'].forEach(prefix => {
            document.querySelectorAll(`[id^="${prefix}"]`).forEach(modal => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modal.classList.add('hidden');
                }
            });
        });
    });

    // ── Toggle Curtida AJAX ──────────────────────────────────
    async function toggleCurtida(eventoId, btn) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content 
            || '{{ csrf_token() }}';

        try {
            const res = await fetch(`/eventos/${eventoId}/curtir`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            const data = await res.json();

            // Atualiza o botão
            const texto = document.querySelector(`.curtida-texto-${eventoId}`);
            const count = document.querySelector(`.curtida-count-${eventoId}`);

            if (data.curtido) {
                btn.classList.add('text-blue-500');
                texto.textContent = 'Curtido';
            } else {
                btn.classList.remove('text-blue-500');
                texto.textContent = 'Curtir';
            }

            count.textContent = data.total;

        } catch (e) {
            console.error('Erro ao curtir:', e);
        }
    }
    // ── Enviar Comentário AJAX ───────────────────────────────
    async function enviarComentario(e, eventoId) {
        e.preventDefault();

        const form = document.getElementById(`form-comentario-${eventoId}`);
        const input = form.querySelector('input[name="corpo"]');
        const corpo = input.value.trim();

        if (!corpo) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.content
            || '{{ csrf_token() }}';

        try {
            const res = await fetch(`/eventos/${eventoId}/comentar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ corpo })
            });

            if (res.status === 401) {
                window.location.href = '{{ route("login") }}';
                return;
            }

            const data = await res.json();

            // 1. Prepara a lista de comentários
            const lista = document.querySelector(`#modal-comentarios-${eventoId} .flex-1.overflow-y-auto`);
            const vazio = lista.querySelector('.text-center.py-8');
            if (vazio) vazio.remove();

            // 2. Só gera e insere o HTML se o usuário estiver logado (Proteção Blade)
            @auth
            const html = `
                <div class="flex gap-3 mb-4">
                    <a href="{{ route('profile.show', ['id' => auth()->id()]) }}" class="flex-shrink-0">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=64' }}"
                            class="w-9 h-9 rounded-full object-cover border-2 border-blue-400">
                    </a>
                    <div class="flex-1">
                        <div class="rounded-2xl rounded-tl-sm px-4 py-3" style="background: rgba(30,41,59,0.9); border: 1px solid rgba(59,130,246,0.1);">
                            <p class="font-bold text-white text-xs">{{ auth()->user()->name }}</p>
                            <p class="text-gray-200 text-sm mt-1">${corpo}</p>
                        </div>
                        <span class="text-gray-500 text-[10px] ml-1">agora mesmo</span>
                    </div>
                </div>`;

            lista.insertAdjacentHTML('beforeend', html);
            lista.scrollTop = lista.scrollHeight;
            @endauth

            // 3. Limpa o campo de texto
            input.value = '';

            // 4. Atualiza o contador de comentários na interface
            const contador = document.querySelector(`#modal-comentarios-${eventoId} h3`);
            if (contador) {
                const match = contador.textContent.match(/\d+/);
                const atual = match ? parseInt(match[0]) : 0;
                contador.textContent = `💬 Comentários (${atual + 1})`;
            }

        } catch (err) {
            console.error('Erro ao comentar:', err);
        }
    }
</script>

@endsection








{{--perfil show--}}

@extends('layouts.app')
@section('title', $user->name . ' — Luanda Bilhetes')
@section('content')

    @push('modals')
<div id="modalBilhetes" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto">
    
    {{-- Overlay: Fecha ao clicar fora --}}
    <div class="absolute inset-0 cursor-pointer" onclick="fecharModalBilhetes()"></div>

    <div class="relative bg-slate-900 w-full max-w-2xl max-h-[90vh] rounded-[2.5rem] overflow-hidden border border-white/10 shadow-2xl flex flex-col animate-in fade-in zoom-in duration-300">
        
        {{-- Header --}}
        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-800/40 sticky top-0 z-20 backdrop-blur-md">
            <div>
                <h3 class="text-xl font-black text-white tracking-tight">LUANDA TICKETS</h3>
                <p class="text-blue-400 text-xs font-bold uppercase tracking-widest">Meus Ingressos Ativos</p>
            </div>
            <button onclick="fecharModalBilhetes()" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-red-500 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Lista de Bilhetes --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            @forelse($bilhetes as $bilhete)
                <div class="ticket-card relative flex flex-col md:flex-row rounded-3xl overflow-hidden bg-white group transition-all hover:scale-[1.01]">
                    
                    <div class="flex-1 p-6 border-r-2 border-dashed border-gray-200">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black rounded-full uppercase">
                                {{ $bilhete->tipoIngresso->nome ?? 'Acesso Geral' }}
                            </span>
                        </div>
                        
                        <h4 class="text-slate-900 font-black text-xl leading-tight mb-2">
                            {{ $bilhete->evento->titulo }}
                        </h4>
                        
                        <div class="space-y-1">
                            <p class="text-gray-500 text-sm flex items-center gap-2">
                                <i class="far fa-calendar text-blue-500"></i>
                                {{ \Carbon\Carbon::parse($bilhete->evento->data)->format('d/m/Y') }} às {{ \Carbon\Carbon::parse($bilhete->evento->data)->format('H:i') }}
                            </p>
                            <p class="text-gray-500 text-sm flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-500"></i>
                                {{ $bilhete->evento->localizacao }}
                            </p>
                        </div>

                             <div class="mt-6 flex items-center justify-between">
                                <span class="text-[10px] font-mono text-gray-400 uppercase tracking-wider">
                                    ID: {{ substr($bilhete->codigo_unico, 0, 13) }}
                                </span>
                                
                                    <a href="{{ route('bilhete.individual.download', $bilhete->id) }}" 
                                    target="_blank"
                                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-500/20">
                                        <i class="fas fa-file-pdf"></i>
                                        BAIXAR BILHETE
                                    </a>
                            </div>
                    </div>

                    <div class="w-full md:w-48 bg-slate-50 flex flex-col items-center justify-center p-6 border-t-2 md:border-t-0 md:border-l-2 border-dashed border-gray-200">
                        <div class="bg-white p-2 rounded-xl shadow-sm mb-3">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $bilhete->codigo_unico }}" 
                                 alt="QR" class="w-28 h-28 object-contain">
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Apresente na entrada</p>
                    </div>

                    {{-- Recortes redondos laterais --}}
                    <div class="hidden md:block absolute top-1/2 left-[calc(100%-12rem)] -translate-x-1/2 -translate-y-[calc(50%+55px)] w-8 h-8 bg-slate-900 rounded-full"></div>
                    <div class="hidden md:block absolute top-1/2 left-[calc(100%-12rem)] -translate-x-1/2 translate-y-[calc(50%+25px)] w-8 h-8 bg-slate-900 rounded-full"></div>
                </div>
            @empty
                <div class="text-center py-20 bg-white/5 rounded-3xl border border-dashed border-white/10">
                    <p class="text-gray-400">Nenhum bilhete encontrado.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endpush

@php
    $handle = strtolower(preg_replace('/\s+/', '.', trim($user->name)));
@endphp
<style>
*, *::before, *::after { box-sizing: border-box; }
@keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

/* ══ COVER ══ */
.p-cover-wrap { position:relative; height:280px; overflow:hidden; margin:-40px -40px 0; }
.p-cover-bg {
    width:100%; height:100%; position:relative;
    background:linear-gradient(135deg,#050d1a 0%,#091828 30%,#0c1f3a 60%,#071220 100%);
}
.p-cover-bg img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.p-cover-glow {
    position:absolute; inset:0; z-index:1; pointer-events:none;
    background:
        radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.22) 0%,transparent 55%),
        radial-gradient(ellipse at 20% 80%,rgba(245,158,11,.08) 0%,transparent 40%);
}
.p-cover-fade {
    position:absolute; bottom:0; left:0; right:0; height:140px; z-index:2;
    background:linear-gradient(to top,#06090f 0%,transparent 100%);
}

/* ══ HEADER ══ */
.p-header { position:relative; z-index:3; }

.p-top {
    display:flex; align-items:flex-end; gap:24px;
    margin-top:-68px; padding-bottom:24px;
    border-bottom:1px solid rgba(6,182,212,.15); flex-wrap:wrap;
}

.p-ava-wrap { position:relative; flex-shrink:0; }
.p-ava {
    width:120px; height:120px; border-radius:50%;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:4px solid #06090f;
    display:flex; align-items:center; justify-content:center;
    font-size:44px; font-weight:800; color:#06b6d4;
    overflow:hidden; box-shadow:0 8px 32px rgba(6,182,212,.3);
}
.p-ava img { width:100%; height:100%; object-fit:cover; }
.p-dot-on  { position:absolute; bottom:6px; right:6px; width:18px; height:18px; background:#10b981; border-radius:50%; border:3px solid #06090f; box-shadow:0 0 8px rgba(16,185,129,.6); }
.p-dot-off { position:absolute; bottom:6px; right:6px; width:18px; height:18px; background:#475569; border-radius:50%; border:3px solid #06090f; }
.p-verified { position:absolute; top:2px; right:2px; width:22px; height:22px; background:#06b6d4; border-radius:50%; border:2px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:10px; }

.p-info { flex:1; min-width:0; padding-bottom:8px; }
.p-name-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:4px; }
.p-name   { font-size:26px; font-weight:800; letter-spacing:-.5px; color:#ffffff; }
.p-handle { font-size:14px; color:#94a3b8; }
.p-badge  { font-size:10px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:3px 9px; border-radius:20px; }
.badge-admin   { background:rgba(244,63,94,.2);   border:1px solid rgba(244,63,94,.4);   color:#f87171; }
.badge-creator { background:rgba(6,182,212,.2);   border:1px solid rgba(6,182,212,.4);   color:#22d3ee; }
.badge-user    { background:rgba(148,163,184,.15); border:1px solid rgba(148,163,184,.3); color:#94a3b8; }
.p-bio  { font-size:14px; color:#94a3b8; line-height:1.65; margin-bottom:10px; max-width:520px; }
.p-meta { display:flex; gap:20px; flex-wrap:wrap; }
.p-meta-item { display:flex; align-items:center; gap:6px; font-size:13px; color:#64748b; }
.p-meta-item strong { color:#94a3b8; }

.p-actions { display:flex; gap:10px; align-items:center; padding-bottom:8px; flex-shrink:0; }
.btn-follow {
    padding:10px 24px; border-radius:11px; font-size:14px; font-weight:700;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; border:none;
    cursor:pointer; box-shadow:0 4px 16px rgba(6,182,212,.4); transition:all .2s;
}
.btn-follow:hover { transform:translateY(-1px); box-shadow:0 6px 22px rgba(6,182,212,.5); }
.btn-msg, .btn-edit {
    padding:10px 20px; border-radius:11px; font-size:14px; font-weight:600;
    background:#1e293b; border:1px solid #334155;
    color:#e2e8f0; cursor:pointer; transition:all .2s;
    text-decoration:none; display:inline-flex; align-items:center; gap:6px;
}
.btn-msg:hover, .btn-edit:hover { background:#263548; border-color:#4a6080; color:#fff; }
.btn-edit { background:#0c2a3a; border-color:rgba(6,182,212,.4); color:#22d3ee; }
.btn-edit:hover { background:#0e3347; }
.btn-more {
    width:40px; height:40px; border-radius:11px; background:#1e293b; border:1px solid #334155;
    display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94a3b8; font-size:18px;
}

/* ══ STATS ══ */
.p-stats { display:flex; border-bottom:1px solid rgba(6,182,212,.12); }
.p-stat  { flex:1; text-align:center; padding:18px 12px; border-right:1px solid rgba(6,182,212,.1); cursor:pointer; transition:background .2s; }
.p-stat:last-child { border-right:none; }
.p-stat:hover { background:rgba(6,182,212,.05); }
.p-stat-num { font-size:22px; font-weight:800; color:#ffffff; line-height:1; }
.p-stat-lbl { font-size:11px; color:#64748b; margin-top:3px; text-transform:uppercase; letter-spacing:.8px; }

/* ══ TABS ══ */
.p-tabs { display:flex; border-bottom:1px solid rgba(6,182,212,.12); overflow-x:auto; scrollbar-width:none; }
.p-tabs::-webkit-scrollbar { display:none; }
.p-tab {
    padding:14px 22px; font-size:13.5px; font-weight:600; color:#64748b;
    cursor:pointer; border-bottom:2px solid transparent; transition:all .2s;
    display:flex; align-items:center; gap:7px; margin-bottom:-1px; white-space:nowrap;
}
.p-tab:hover { color:#e2e8f0; }
.p-tab.active { color:#06b6d4; border-bottom-color:#06b6d4; }
.p-tab-count { font-size:10px; background:#1e293b; border:1px solid #334155; border-radius:20px; padding:2px 7px; color:#64748b; }
.p-tab.active .p-tab-count { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.35); color:#06b6d4; }

/* ══ LAYOUT ══ */
.p-body { display:grid; grid-template-columns:1fr 320px; gap:28px; padding-top:28px; padding-bottom:60px; }
.p-panel { display:none; flex-direction:column; gap:16px; }
.p-panel.active { display:flex; }

/* ══ COMPOSE BOX ══ */
.compose-box {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.25);
    border-radius:16px; padding:18px;
    animation:fadeUp .3s ease;
}
.compose-top { display:flex; align-items:flex-start; gap:12px; margin-bottom:14px; }
.compose-ava {
    width:40px; height:40px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:2px solid rgba(6,182,212,.3);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px; color:#06b6d4; overflow:hidden;
}
.compose-ava img { width:100%; height:100%; object-fit:cover; }
.compose-textarea {
    flex:1; background:#0d1a2e; border:1px solid #2d3f55;
    border-radius:10px; padding:12px 14px; color:#e2e8f0;
    font-size:14px; resize:none; font-family:inherit; min-height:85px;
    outline:none; transition:border-color .2s; line-height:1.6;
}
.compose-textarea:focus { border-color:#06b6d4; }
.compose-textarea::placeholder { color:#475569; }
.compose-footer { display:flex; justify-content:flex-end; }
.compose-submit {
    padding:9px 22px; border-radius:9px;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9);
    color:#fff; font-size:13px; font-weight:700; border:none;
    cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(6,182,212,.35);
}
.compose-submit:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(6,182,212,.45); }

/* ══ EV-POST ══ */
.ev-post {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.18);
    border-radius:18px; overflow:hidden;
    animation:fadeUp .4s ease both; transition:border-color .2s;
}
.ev-post:hover { border-color:rgba(6,182,212,.45); }
.ev-post-img {
    height:200px; display:flex; align-items:center; justify-content:center;
    font-size:80px; position:relative; overflow:hidden;
    background:linear-gradient(135deg,#050d1a,#091828);
}
.ev-post-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.ev-post-img-overlay {
    position:absolute; inset:0;
    background:linear-gradient(to bottom,transparent 50%,rgba(6,9,15,.7) 100%);
}
.ev-badge {
    position:absolute; top:12px; left:12px; z-index:2;
    font-size:10px; font-weight:700; letter-spacing:.8px;
    text-transform:uppercase; padding:4px 10px; border-radius:20px;
}
.ev-date-pill {
    position:absolute; bottom:12px; left:12px; z-index:2;
    background:rgba(6,9,15,.88); backdrop-filter:blur(8px);
    border:1px solid rgba(6,182,212,.25); border-radius:7px;
    font-size:11px; font-weight:600; padding:4px 9px; color:#e2e8f0;
}
.ev-save-btn {
    position:absolute; top:12px; right:12px; z-index:2;
    width:30px; height:30px; border-radius:8px;
    background:rgba(6,9,15,.8); border:1px solid rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center;
    font-size:13px; cursor:pointer; transition:all .2s;
}
.ev-save-btn:hover { background:rgba(6,182,212,.25); border-color:#06b6d4; }
.ev-body  { padding:16px 18px 18px; }
.ev-cat   { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:5px; }
.ev-title { font-size:18px; font-weight:700; letter-spacing:-.3px; margin-bottom:10px; line-height:1.25; color:#ffffff; }
.ev-meta  { display:flex; gap:16px; margin-bottom:13px; flex-wrap:wrap; }
.ev-meta span { display:flex; align-items:center; gap:5px; font-size:12.5px; color:#94a3b8; }
.ev-bar-wrap { margin-bottom:14px; }
.ev-bar-top  { display:flex; justify-content:space-between; font-size:11px; margin-bottom:5px; }
.ev-bar-top span   { color:#64748b; }
.ev-bar-top strong { color:#cbd5e1; }
.ev-bar      { height:4px; background:#1e293b; border-radius:2px; overflow:hidden; }
.ev-bar-fill { height:100%; border-radius:2px; }
.fill-ok   { background:linear-gradient(to right,#06b6d4,#0ea5e9); }
.fill-warn { background:linear-gradient(to right,#f59e0b,#f97316); }
.fill-crit { background:linear-gradient(to right,#f43f5e,#f97316); }
.ev-footer { display:flex; align-items:center; justify-content:space-between; padding-top:13px; border-top:1px solid #1e293b; }
.ev-price  { font-size:20px; font-weight:800; color:#f59e0b; }
.ev-price small { font-size:11px; color:#64748b; font-weight:400; }
.ev-price.free  { color:#10b981; font-size:15px; font-weight:700; }
.ev-actions { display:flex; gap:8px; align-items:center; }
.ev-like-btn {
    display:flex; align-items:center; gap:5px; padding:7px 12px;
    border-radius:9px; background:#1e293b; border:1px solid #334155;
    font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; color:#94a3b8;
}
.ev-like-btn:hover { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.1); }
.ev-like-btn.liked { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.12); }
.ev-buy-btn {
    padding:8px 18px; border-radius:9px;
    background:linear-gradient(135deg,#06b6d4,#0ea5e9);
    color:#fff; font-size:13px; font-weight:700; border:none;
    cursor:pointer; transition:all .2s; box-shadow:0 4px 12px rgba(6,182,212,.35);
    text-decoration:none; display:inline-flex; align-items:center; gap:5px;
}
.ev-buy-btn:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(6,182,212,.45); }

/* ══ POST CARD ══ */
.post-card {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.14);
    border-radius:16px; padding:18px;
    animation:fadeUp .4s ease both; transition:border-color .2s;
}
.post-card:hover { border-color:rgba(6,182,212,.35); }
.post-author { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.post-ava {
    width:38px; height:38px; border-radius:50%; flex-shrink:0; overflow:hidden;
    background:linear-gradient(135deg,#0c3a4a,#1e6a7a);
    border:2px solid rgba(6,182,212,.3);
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:14px; color:#06b6d4;
}
.post-ava img { width:100%; height:100%; object-fit:cover; }
.post-name { font-size:13px; font-weight:700; color:#ffffff; }
.post-time { font-size:10px; color:#64748b; }
.post-text { font-size:14px; color:#cbd5e1; line-height:1.65; }
.post-img  { width:100%; border-radius:10px; margin-top:12px; object-fit:cover; max-height:300px; display:block; }
.post-del-btn { margin-left:auto; background:none; border:none; cursor:pointer; color:#64748b; font-size:16px; padding:4px; transition:color .2s; }
.post-del-btn:hover { color:#f43f5e; }

/* ══ EMPTY ══ */
.p-empty {
    text-align:center; padding:56px 20px;
    background:#111c2d; border:1px solid rgba(6,182,212,.1); border-radius:16px;
}
.p-empty-icon { font-size:40px; margin-bottom:10px; }
.p-empty-txt  { font-size:12px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:1px; }

/* ══ SIDEBAR ══ */
.p-sidebar { display:flex; flex-direction:column; gap:18px; }
.side-box {
    background:#111c2d;
    border:1px solid rgba(6,182,212,.14);
    border-radius:16px; padding:18px;
    animation:fadeUp .4s ease both;
}
.side-title {
    font-size:13px; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    color:#64748b; margin-bottom:14px; display:flex; align-items:center; gap:8px;
}
.side-title a { margin-left:auto; font-size:11px; color:#06b6d4; text-transform:none; letter-spacing:0; font-weight:600; text-decoration:none; }

/* Interesses */
.interests { display:flex; flex-wrap:wrap; gap:7px; }
.interest-tag { padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; border:1px solid #2d3f55; background:#1a2a3a; color:#94a3b8; }
.interest-tag.active { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.4); color:#22d3ee; }

/* Going list */
.going-list { display:flex; flex-direction:column; gap:10px; }
.going-item {
    display:flex; align-items:center; gap:10px; padding:10px;
    background:#0d1a2e; border-radius:11px; border:1px solid transparent;
    transition:all .2s; text-decoration:none;
}
.going-item:hover { border-color:rgba(6,182,212,.3); background:#0f1e36; }
.going-emoji { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; background:#1e293b; }
.going-info  { flex:1; min-width:0; }
.going-name  { font-size:12px; font-weight:600; color:#ffffff; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.going-date  { font-size:11px; color:#06b6d4; font-weight:600; }
.going-price { font-size:12px; color:#f59e0b; font-weight:700; flex-shrink:0; }

/* Mutuais */
.mutual-list { display:flex; flex-direction:column; gap:12px; }
.mutual-item { display:flex; align-items:center; gap:10px; }
.mutual-ava  { width:38px; height:38px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:14px; color:#fff; border:1.5px solid #2d3f55; }
.mutual-name { font-size:13px; font-weight:600; color:#ffffff; }
.mutual-sub  { font-size:11px; color:#64748b; }
.mutual-info { flex:1; min-width:0; }
.mutual-btn  { padding:4px 12px; border-radius:8px; font-size:11px; font-weight:700; background:rgba(6,182,212,.12); border:1px solid rgba(6,182,212,.3); color:#06b6d4; cursor:pointer; transition:all .2s; text-decoration:none; display:inline-block; }
.mutual-btn:hover { background:rgba(6,182,212,.25); }

/* Galeria */
.gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.gallery-item { aspect-ratio:1; border-radius:8px; overflow:hidden; cursor:pointer; background:#1e293b; display:flex; align-items:center; justify-content:center; font-size:26px; transition:all .2s; border:1px solid #2d3f55; text-decoration:none; }
.gallery-item img { width:100%; height:100%; object-fit:cover; }
.gallery-item:hover { transform:scale(1.03); border-color:rgba(6,182,212,.5); }
</style>

{{-- COVER --}}
<div class="p-cover-wrap">
    <div class="p-cover-bg">
        @if(!empty($user->cover))
            <img src="{{ asset('storage/'.$user->cover) }}" alt="cover">
        @endif
        <div class="p-cover-glow"></div>
    </div>
    <div class="p-cover-fade"></div>
</div>

{{-- HEADER --}}
<div class="p-header">
    <div class="p-top">

        {{-- Avatar --}}
        <div class="p-ava-wrap">
            <div class="p-ava">
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            @if(method_exists($user,'isOnline') && $user->isOnline())
                <div class="p-dot-on"></div>
            @else
                <div class="p-dot-off"></div>
            @endif
            <div class="p-verified">✓</div>
        </div>

        {{-- Info --}}
        <div class="p-info">
            <div class="p-name-row">
                <div class="p-name">{{ $user->name }}</div>
                <div class="p-handle">&#64;{{ $handle }}</div>
                @if($user->role === 'admin')
                    <span class="p-badge badge-admin">🛡 Admin</span>
                @elseif($user->role === 'creator')
                    <span class="p-badge badge-creator">🎟 Criador</span>
                @else
                    <span class="p-badge badge-user">👤 Membro</span>
                @endif
            </div>
            @if(!empty($user->bio))
                <div class="p-bio">{{ $user->bio }}</div>
            @endif
            <div class="p-meta">
                <div class="p-meta-item">📍 <strong>Luanda, Angola</strong></div>
                <div class="p-meta-item">🗓 <strong>Desde {{ $user->created_at->translatedFormat('M Y') }}</strong></div>
            </div>
        </div>

        {{-- Botões --}}
        <div class="p-actions">
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar Perfil</a>
            @else
                @auth
                    <button class="btn-follow" id="followBtn" onclick="toggleFollow()">+ Seguir</button>
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬 Mensagem</a>
                @endauth
            @endif
            <div class="btn-more">⋯</div>
        </div>
    </div>

    {{-- STATS — sem bilhetes vendidos nem guardados --}}
    <div class="p-stats">
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">Seguidores</div></div>
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">A seguir</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount }}</div><div class="p-stat-lbl">{{ $statsLabel }}</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $postagens->count() }}</div><div class="p-stat-lbl">Posts</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount2 }}</div><div class="p-stat-lbl">{{ $statsLabel2 }}</div></div>
    </div>

    {{-- TABS --}}
    <div class="p-tabs">
        <div class="p-tab active" data-tab="eventos" onclick="switchTab('eventos')">🎟 Eventos <span class="p-tab-count">{{ $eventos->count() }}</span></div>
        <div class="p-tab" data-tab="posts"    onclick="switchTab('posts')">📝 Posts <span class="p-tab-count">{{ $postagens->count() }}</span></div>
        <div class="p-tab" data-tab="galeria"  onclick="switchTab('galeria')">📸 Galeria</div>
        <div class="p-tab" data-tab="seguidores" onclick="switchTab('seguidores')">👥 Seguidores</div>
        <div class="p-tab" data-tab="avaliacoes" onclick="switchTab('avaliacoes')">⭐ Avaliações</div>
        <div class="p-tab tab-bilhete" onclick="abrirModalBilhetes()">
            🎫 Meus Bilhetes
        </div>
        
    </div>
</div>

{{-- BODY --}}
<div class="p-body">

    {{-- COLUNA ESQUERDA --}}
    <div>

        {{-- ── TAB EVENTOS ── --}}
        <div class="p-panel active" id="tab-eventos">
            @forelse($eventos as $evento)
            @php
                $preco    = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                $lotacao  = $evento->lotacao_maxima ?? 0;
                $vendidos = optional($evento->tiposIngresso)->sum('quantidade_vendida') ?? 0;
                $pct      = $lotacao > 0 ? round(($vendidos / $lotacao) * 100) : 0;
                $barClass = $pct >= 80 ? 'fill-crit' : ($pct >= 50 ? 'fill-warn' : 'fill-ok');
                $catNome  = optional($evento->categoria)->nome ?? 'Evento';
                $catEmoji = match(strtolower($catNome)) {
                    'música','musica'        => '🎵',
                    'arte','arte & cultura'  => '🎨',
                    'festa','festas'         => '🎉',
                    'desporto'               => '⚽',
                    'gastronomia'            => '🍽',
                    'negócios','negocios'    => '💼',
                    default                  => '🎟'
                };
                $catColor = match(strtolower($catNome)) {
                    'música','musica'        => '#06b6d4',
                    'arte','arte & cultura'  => '#a78bfa',
                    'festa','festas'         => '#f59e0b',
                    'desporto'               => '#10b981',
                    'gastronomia'            => '#f97316',
                    'negócios','negocios'    => '#0ea5e9',
                    default                  => '#06b6d4'
                };
            @endphp
            <div class="ev-post" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div class="ev-post-img">
                    @if($evento->imagem_capa)
                        <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
                    @else
                        {{ $catEmoji }}
                    @endif
                    <div class="ev-post-img-overlay"></div>

                    @if($pct >= 80)
                        <span class="ev-badge" style="background:rgba(244,63,94,.9);color:#fff">🔥 A Esgotar</span>
                    @elseif($preco == 0)
                        <span class="ev-badge" style="background:rgba(16,185,129,.9);color:#fff">Gratuito</span>
                    @elseif($evento->created_at->isCurrentWeek())
                        <span class="ev-badge" style="background:rgba(14,165,233,.9);color:#fff">✨ Novo</span>
                    @endif

                    <div class="ev-date-pill">
                        📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }}
                        · {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}
                    </div>
                    <div class="ev-save-btn">🔖</div>
                </div>

                <div class="ev-body">
                    <div class="ev-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $catNome }}</div>
                    <div class="ev-title">{{ $evento->titulo }}</div>
                    <div class="ev-meta">
                        <span>📍 {{ $evento->localizacao ?? 'Luanda' }}</span>
                        <span>🕐 {{ \Carbon\Carbon::parse($evento->data_evento)->format('H:i') }}</span>
                        @if($lotacao > 0)<span>👥 {{ number_format($lotacao) }} lugares</span>@endif
                    </div>
                    @if($lotacao > 0)
                    <div class="ev-bar-wrap">
                        <div class="ev-bar-top">
                            <span>Disponibilidade de bilhetes</span>
                            <strong>{{ $vendidos }} / {{ $lotacao }} vendidos</strong>
                        </div>
                        <div class="ev-bar">
                            <div class="ev-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endif
                    <div class="ev-footer">
                        <div class="ev-price {{ $preco == 0 ? 'free' : '' }}">
                            @if($preco == 0)
                                ✅ Entrada Gratuita
                            @else
                                {{ number_format($preco, 0, ',', '.') }} <small>Kz</small>
                            @endif
                        </div>
                        <div class="ev-actions">
                            @auth
                            <form method="POST" action="{{ route('evento.curtir', $evento->id) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="ev-like-btn {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? 'liked' : '' }}">
                                    {{ $evento->usuariosQueCurtiram->contains(auth()->id()) ? '❤️' : '🤍' }}
                                    {{ $evento->curtidas->count() }}
                                </button>
                            </form>
                            @endauth
                            <a href="{{ route('evento.detalhes', $evento->id) }}" class="ev-buy-btn">🎟 Comprar</a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-empty">
                <div class="p-empty-icon">🎟</div>
                <div class="p-empty-txt">Nenhum evento ainda</div>
            </div>
            @endforelse
        </div>

        {{-- ── TAB POSTS ── --}}
        <div class="p-panel" id="tab-posts">

            {{-- Compose box — só para o dono --}}
            @if($isOwner)
            <div class="compose-box">
                <form method="POST" action="{{ route('social.publicar') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="compose-top">
                        <div class="compose-ava">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <textarea name="conteudo" class="compose-textarea"
                                  placeholder="Partilha algo com os teus seguidores..."></textarea>
                    </div>
                    <div class="compose-footer">
                        <button type="submit" class="compose-submit">✈️ Publicar</button>
                    </div>
                </form>
            </div>
            @endif

            @forelse($postagens as $post)
            <div class="post-card" style="animation-delay:{{ $loop->index * 0.05 }}s">
                <div class="post-author">
                    <div class="post-ava">
                        @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="">
                        @else
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <div class="post-name">{{ $user->name }}</div>
                        <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                    @if($isOwner)
                    <form method="POST" action="{{ route('post.eliminar', $post->id) }}"
                          style="margin-left:auto" onsubmit="return confirm('Eliminar esta publicação?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="post-del-btn" title="Eliminar">🗑</button>
                    </form>
                    @endif
                </div>
                <p class="post-text">{{ $post->conteudo }}</p>
                @if($post->imagem)
                    <img src="{{ asset('storage/'.$post->imagem) }}" class="post-img" alt="">
                @endif
            </div>
            @empty
            <div class="p-empty">
                <div class="p-empty-icon">📝</div>
                <div class="p-empty-txt">Nenhuma publicação ainda</div>
            </div>
            @endforelse
        </div>

        {{-- ── TAB GALERIA ── --}}
        <div class="p-panel" id="tab-galeria">
            @php $comImg = $eventos->whereNotNull('imagem_capa'); @endphp
            @if($comImg->count() > 0)
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
                @foreach($comImg as $ev)
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                </a>
                @endforeach
            </div>
            @else
            <div class="p-empty">
                <div class="p-empty-icon">📸</div>
                <div class="p-empty-txt">Nenhuma imagem ainda</div>
            </div>
            @endif
        </div>

        {{-- ── TAB SEGUIDORES ── --}}
        <div class="p-panel" id="tab-seguidores">
            <div class="p-empty">
                <div class="p-empty-icon">👥</div>
                <div class="p-empty-txt">Sistema de seguidores em breve</div>
            </div>
        </div>

        {{-- ── TAB AVALIAÇÕES ── --}}
        <div class="p-panel" id="tab-avaliacoes">
            <div class="p-empty">
                <div class="p-empty-icon">⭐</div>
                <div class="p-empty-txt">Avaliações em breve</div>
            </div>
        </div>

    </div>

    {{-- SIDEBAR --}}
    <div class="p-sidebar">

        {{-- Eventos na sidebar --}}
        @if($eventos->count() > 0)
        <div class="side-box" style="animation-delay:.05s">
            <div class="side-title">
                📅
                @if($user->role === 'creator') Eventos publicados
                @elseif($user->role === 'admin') Eventos recentes
                @else Eventos curtidos @endif
            </div>
            <div class="going-list">
                @foreach($eventos->take(4) as $ev)
                @php
                    $pSide = optional($ev->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
                    $eSide = match(strtolower(optional($ev->categoria)->nome ?? '')) {
                        'música','musica' => '🎵', 'arte' => '🎨',
                        'festa','festas'  => '🎉', 'desporto' => '⚽',
                        'gastronomia'     => '🍽', default => '🎟'
                    };
                @endphp
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="going-item">
                    <div class="going-emoji">{{ $eSide }}</div>
                    <div class="going-info">
                        <div class="going-name">{{ $ev->titulo }}</div>
                        <div class="going-date">{{ \Carbon\Carbon::parse($ev->data_evento)->translatedFormat('D, d M · H:i') }}</div>
                    </div>
                    <div class="going-price" style="{{ $pSide == 0 ? 'color:#10b981' : '' }}">
                        {{ $pSide == 0 ? 'Grátis' : number_format($pSide / 1000, 0) . 'k Kz' }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Interesses --}}
        <div class="side-box" style="animation-delay:.10s">
            <div class="side-title">🏷 Interesses</div>
            <div class="interests">
                @php
                    $cats = $eventos->map(fn($e) => optional($e->categoria)->nome)->filter()->unique()->values();
                @endphp
                @if($cats->count() > 0)
                    @foreach($cats->take(8) as $c)
                        <span class="interest-tag active">{{ $c }}</span>
                    @endforeach
                @else
                    @foreach(['🎵 Música','🎉 Festas','🎟 Eventos','🌊 Luanda','🇦🇴 Angola'] as $tag)
                        <span class="interest-tag">{{ $tag }}</span>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Galeria mini --}}
        @if($eventos->whereNotNull('imagem_capa')->count() > 0)
        <div class="side-box" style="animation-delay:.15s">
            <div class="side-title">
                📸 Galeria
                <a href="javascript:void(0)" onclick="switchTab('galeria')">Ver tudo →</a>
            </div>
            <div class="gallery-grid">
                @foreach($eventos->whereNotNull('imagem_capa')->take(6) as $ev)
                <a href="{{ route('evento.detalhes', $ev->id) }}" class="gallery-item">
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                </a>
                @endforeach
                @for($i = $eventos->whereNotNull('imagem_capa')->take(6)->count(); $i < 6; $i++)
                    <div class="gallery-item">🎟</div>
                @endfor
            </div>
        </div>
        @endif

        {{-- Poderás conhecer --}}
        @auth
        @if(!$isOwner)
        <div class="side-box" style="animation-delay:.20s">
            <div class="side-title">👥 Poderás conhecer</div>
            <div class="mutual-list">
                @foreach(\App\Models\User::where('id', '!=', $user->id)->where('id', '!=', auth()->id())->take(3)->get() as $u)
                <div class="mutual-item">
                    <div class="mutual-ava" style="background:linear-gradient(135deg,#0c3a4a,#1e6a7a)">
                        {{ strtoupper(substr($u->name, 0, 2)) }}
                    </div>
                    <div class="mutual-info">
                        <div class="mutual-name">{{ $u->name }}</div>
                        <div class="mutual-sub">{{ ucfirst($u->role) }}</div>
                    </div>
                    <a href="{{ route('profile.show', $u->id) }}" class="mutual-btn">Ver</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endauth

    </div>
</div>

<script>
function switchTab(name) {
    document.querySelectorAll('.p-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.p-panel').forEach(p => p.classList.remove('active'));
    const tab = document.querySelector('[data-tab="' + name + '"]');
    const panel = document.getElementById('tab-' + name);
    if (tab) tab.classList.add('active');
    if (panel) panel.classList.add('active');
}
function toggleFollow() {
    const b = document.getElementById('followBtn');
    if (!b) return;
    b.classList.toggle('following');
    b.textContent = b.classList.contains('following') ? '✓ A seguir' : '+ Seguir';
}

function abrirModalBilhetes() {
    const modal = document.getElementById('modalBilhetes');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Trava o scroll do site para o modal ser a única coisa a mexer
    document.body.style.overflow = 'hidden';
}

function fecharModalBilhetes() {
    const modal = document.getElementById('modalBilhetes');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Devolve o scroll ao site
    document.body.style.overflow = 'auto';
}

// Fechar ao apertar a tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        fecharModalBilhetes();
    }
});
</script>
@endsection



{{--message-index--}}

<div class="min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-5xl overflow-hidden flex"
     style="height: 700px; background: rgba(15,23,42,0.85); backdrop-filter: blur(20px);
            border: 1px solid rgba(59,130,246,0.15); border-radius: 32px;">

    {{-- SIDEBAR: Lista de conversas --}}
    <aside class="w-80 flex flex-col flex-shrink-0"
           style="border-right: 1px solid rgba(59,130,246,0.15);">

        {{-- Header --}}
        <div class="p-5" style="border-bottom: 1px solid rgba(59,130,246,0.1);">
            <h1 class="text-lg font-black text-white mb-4">💬 Mensagens</h1>
            <div class="relative">
                <input type="text"
                       placeholder="Pesquisar conversa..."
                       class="w-full rounded-xl py-2.5 pl-9 pr-4 text-sm text-white placeholder-gray-500 outline-none transition"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <span class="absolute left-3 top-2.5 text-gray-500 text-sm">🔍</span>
            </div>
        </div>

        {{-- Lista de conversas --}}
        <div class="flex-1 overflow-y-auto py-2"
             style="scrollbar-width: thin; scrollbar-color: rgba(59,130,246,0.3) transparent;">

            @foreach($conversations as $conv)
            @php
                $receiver = $conv->getReceiver();
                $ultimaMensagem = $conv->messages->last();
                $isSelected = $selectedConversation && $selectedConversation->id === $conv->id;
            @endphp

            <div class="flex items-center gap-3 px-4 py-3 cursor-pointer transition group"
                 style="{{ $isSelected
                    ? 'background: rgba(59,130,246,0.15); border-left: 3px solid #3b82f6;'
                    : 'border-left: 3px solid transparent;' }}"
                 wire:click="loadConversation({{ $conv->id }})">

                <div class="relative flex-shrink-0">
                    <img src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name).'&background=0ea5e9&color=fff&size=64' }}"
                         class="w-12 h-12 rounded-full object-cover border-2 {{ $isSelected ? 'border-blue-400' : 'border-transparent' }}">
                    <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-[#020617]
                        {{ $receiver->isOnline() ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="text-white font-semibold text-sm truncate">{{ $receiver->name }}</p>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($conv->unread_count > 0)
                                <span class="bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[18px] text-center">
                                    {{ $conv->unread_count }}
                                </span>
                            @else
                                <span class="text-gray-500 text-[10px]">
                                    {{ $ultimaMensagem ? $ultimaMensagem->created_at->format('H:i') : '' }}
                                </span>
                            @endif

                            {{-- Botão eliminar conversa --}}
                            <button wire:click.stop="deleteConversation({{ $conv->id }})"
                                    wire:confirm="Tens a certeza que queres eliminar esta conversa?"
                                    class="text-gray-600 hover:text-red-400 transition opacity-0 group-hover:opacity-100 text-xs"
                                    title="Eliminar conversa">
                                🗑
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-400 text-xs truncate mt-0.5">
                        @if($ultimaMensagem)
                            {{ $ultimaMensagem->user_id == auth()->id() ? 'Tu: ' : '' }}{{ $ultimaMensagem->body }}
                        @else
                            <span class="italic">Sem mensagens</span>
                        @endif
                    </p>
                </div>

            </div>
            @endforeach

        </div>
    </aside>

    {{-- ÁREA DO CHAT --}}
    <main class="flex-1 flex flex-col">
        @if($selectedConversation)
            @livewire('messages.chat-box',
                ['conversation' => $selectedConversation],
                key('chat-box-' . $selectedConversation->id)
            )
        @else
            <div class="flex-1 flex flex-col items-center justify-center"
                 style="color: rgba(148,163,184,0.3);">
                <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="font-black uppercase text-xs tracking-[0.2em]">Seleciona uma conversa</p>
            </div>
        @endif
    </main>

</div>
</div>