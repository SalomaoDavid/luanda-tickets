<!DOCTYPE html>
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
                <a href="{{ route('profile.show', auth()->user()->id) }}" class="block px-4 py-3 hover:bg-blue-50">Perfil</a>
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
</body>
</html>