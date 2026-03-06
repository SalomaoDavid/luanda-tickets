<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Luanda Tickets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        .glass {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(18px);
            border-right: 1px solid rgba(59, 130, 246, 0.2);
        }

        .active-link {
            color: #60a5fa;
            font-weight: 600;
        }

        .hover-blue:hover {
            color: #60a5fa;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .page-transition {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeIn 0.35s ease forwards;
        }

        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-blue {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-blue:hover {
            background: #1d4ed8;
        }
    </style>
    @livewireStyles
</head>

<body class="text-white relative">

@php
    $isHome = request()->routeIs('home');
@endphp

<!-- FUNDO -->
<div class="fixed inset-0 -z-10">
    <img src="{{ asset('images/luanda-noite.png') }}"
         class="w-full h-full object-cover brightness-50">
</div>

<!-- NAVBAR -->
<header class="glass fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-10 z-50">

    <a href="{{ route('home') }}" class="text-xl font-bold">
        <span class="text-blue-400">Luanda</span>
        <span class="text-white">Tickets</span>
    </a>

    <div class="hidden md:flex space-x-6 text-gray-300 items-center">

        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'active-link' : 'hover-blue' }}">
            Feed
        </a>

        <a href="{{ route('eventos.todos') }}"
           class="{{ request()->routeIs('eventos.*') ? 'active-link' : 'hover-blue' }}">
            Explorar
        </a>

        <a href="{{ route('noticias.index') }}"
           class="{{ request()->routeIs('noticias.*') ? 'active-link' : 'hover-blue' }}">
            Notícias
        </a>

        @auth
        <a href="{{ route('mensagens.index') }}"
           class="{{ request()->routeIs('mensagens.*') ? 'active-link' : 'hover-blue' }}">
            Mensagens
        </a>
        @endauth

        <!-- BUSCA -->
        <form action="{{ route('eventos.todos') }}" method="GET">
            <input type="text" name="search"
                   placeholder="Buscar eventos..."
                   class="px-3 py-1 rounded-lg text-black text-sm focus:outline-none">
        </form>
    </div>

    <!-- DIREITA -->
    <div class="flex items-center space-x-4">

        @guest
            <a href="{{ route('login') }}"
               class="px-4 py-2 rounded-lg border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white transition">
                Entrar
            </a>

            <a href="{{ route('register') }}" class="btn-blue">
                Cadastrar
            </a>
        @endguest

        @auth
        @php
            $user = auth()->user();
            $isOnline = $user->isOnline();
            $unread = $user->unreadNotifications->count();
        @endphp

        <div x-data="{ open: false }" class="relative">

            <button @click="open = !open" class="relative">

                <img src="{{ $user->avatar 
                 ? asset('storage/' . $user->avatar) 
                 : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff&size=128' }}"
                 class="w-10 h-10 rounded-full object-cover border-2 border-blue-400 hover:scale-105 transition">

                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-gray-900 
                    {{ $isOnline ? 'bg-green-500' : 'bg-gray-400' }}">
                </span>

                @if($unread > 0)
                <span class="absolute -top-1 -right-1 bg-red-600 text-xs text-white px-1.5 py-0.5 rounded-full">
                    {{ $unread }}
                </span>
                @endif

            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-3 w-56 bg-white text-gray-800 rounded-2xl shadow-2xl overflow-hidden z-50">

                <div class="p-4 border-b">
                    <p class="font-semibold">
                        {{ explode(' ', $user->name)[0] }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ ucfirst($user->role) }}
                    </p>
                </div>

                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-3 hover:bg-blue-50 transition">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-3 hover:bg-red-100 text-red-600 transition">
                        Sair
                    </button>
                </form>
            </div>

        </div>
        @endauth

    </div>
</header>

@if($isHome)
<div class="flex pt-16 h-screen overflow-hidden">

     <!-- SIDEBAR ESQUERDA -->
<aside class="w-72 glass fixed left-0 top-16 bottom-0 p-6 
              overflow-y-auto no-scrollbar flex flex-col">

    @auth
    @php
        $user = auth()->user();
        $firstName = explode(' ', $user->name)[0];
        $role = ucfirst($user->role);
    @endphp

    <!-- BLOCO CONTA -->
    <div class="mb-8 text-center">

        <img src="{{ auth()->user()->avatar 
        ? asset('storage/' . auth()->user()->avatar) 
        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=128' }}"
        class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-blue-400 shadow-lg">

        <p class="mt-3 font-semibold text-lg">
            {{ $firstName }}
        </p>

        <p class="text-sm text-gray-400">
            {{ $role }}
        </p>
    </div>
    @endauth

    <!-- GERAL -->
    <p class="text-gray-400 text-xs mb-2">GERAL</p>
    <nav class="flex flex-col space-y-3 mb-6">
        <a href="{{ route('home') }}" class="block hover-blue">🏠 Feed</a>
        <a href="{{ route('eventos.todos') }}" class="block hover-blue">🎟 Eventos</a>

        @auth
        <a href="{{ route('mensagens.index') }}" class="block hover-blue">💬 Mensagens</a>
        <a href="{{ route('profile.edit') }}" class="block hover-blue">👤 Perfil</a>
        @endauth
    </nav>

    @auth

    <!-- ADMIN -->
    @if($user->role === 'admin')
        <p class="text-gray-400 text-xs mb-2">ADMIN</p>
        <nav class="flex flex-col space-y-3 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="block hover-blue">📊 Dashboard</a>
            <a href="{{ route('admin.usuarios.index') }}" class="block hover-blue">👥 Usuários</a>
            <a href="{{ route('admin.eventos') }}" class="block hover-blue">🎫 Gerenciar Eventos</a>
            <a href="{{ route('admin.reservas') }}" class="block hover-blue">📦 Reservas</a>
            <a href="{{ route('admin.pagos') }}" class="block hover-blue">💰 Pagamentos</a>
            <a href="{{ route('admin.scanner') }}" class="block hover-blue">🎟 Validar Bilhete</a>
        </nav>

    <!-- CREATOR -->
    @elseif($user->role === 'creator')
        <p class="text-gray-400 text-xs mb-2">CRIADOR</p>
        <nav class="flex flex-col space-y-3 mb-6">
            <a href="{{ route('admin.eventos') }}" class="block hover-blue">📋 Meus Eventos</a>
            <a href="{{ route('admin.eventos.criar') }}" class="block hover-blue">➕ Criar Evento</a>
            <a href="{{ route('admin.reservas') }}" class="block hover-blue">📦 Minhas Reservas</a>
            <a href="{{ route('admin.pagos') }}" class="block hover-blue">💰 Meus Pagamentos</a>
        </nav>
    @endif

    @endauth

</aside>

    <!-- CONTEÚDO -->
    <main class="flex-1 ml-72 mr-72 overflow-y-auto no-scrollbar p-10 page-transition">
        @yield('content')
        <!-- CONTEÚDO -->
    {{ $slot ?? '' }}
    </main>

    <!-- SIDEBAR DIREITA -->
    <aside class="w-72 glass fixed right-0 top-16 bottom-0 p-6 overflow-y-auto no-scrollbar">

        <h2 class="text-xl font-bold mb-6">
            <span class="text-blue-400">Eventos</span> <span class="text-white">Populares</span>
        </h2>

        @foreach(\App\Models\Evento::latest()->take(3)->get() as $evento)
            <a href="{{ route('evento.detalhes', $evento->id) }}"
               class="block bg-gray-800 p-4 rounded-2xl hover:scale-105 transition mb-4">
                {{ $evento->titulo }}
                <p class="text-xs text-gray-400 mt-1">
                    {{ \Carbon\Carbon::parse($evento->data_evento)->format('d M Y') }}
                </p>
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
<script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Alpine === 'undefined') {
                // Se o Livewire falhou em carregar o Alpine, carregamos manualmente
                const script = document.createElement('script');
                script.src = "https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js";
                script.defer = true;
                document.head.appendChild(script);
            }
        });
    </script>
</body>
</html>