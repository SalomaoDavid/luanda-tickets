<!DOCTYPE html>
{{-- --}}
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Luanda Tickets</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <script src="https://cdn.tailwindcss.com"></script>
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>  
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(18px); border-bottom: 1px solid rgba(59, 130, 246, 0.2); }
        .glass-sidebar { background: rgba(15, 23, 42, 0.92); backdrop-filter: blur(18px); border-right: 1px solid rgba(59, 130, 246, 0.2); }
        .glass-sidebar-right { background: rgba(15, 23, 42, 0.92); backdrop-filter: blur(18px); border-left: 1px solid rgba(59, 130, 246, 0.2); }
        .active-link { color: #60a5fa; font-weight: 600; }
        .hover-blue:hover { color: #60a5fa; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* DEPOIS — sem transform, só opacity */
        .page-transition {
            opacity: 0;
            animation: fadeIn 0.35s ease forwards;
        }
            @keyframes fadeIn {
            to { opacity: 1; }
        }
        .btn-blue { background: #2563eb; color: white; padding: 8px 16px; border-radius: 12px; font-weight: 600; transition: 0.3s; }
        .btn-blue:hover { background: #1d4ed8; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@emoji-mart/data"></script>
</head>
<body class="text-white relative bg-slate-900" x-data="{ sidebarOpen: false, rightSidebarOpen: false }" x-init="Livewire.on('refresh-alpine', () => {})">

@php $isHome = request()->routeIs('home'); @endphp

<div class="fixed inset-0 -z-10">
    <img src="{{ asset('images/luanda-noite.png') }}" class="w-full h-full object-cover brightness-50">
</div>

{{-- ═══════════ HEADER ═══════════ --}}
<header class="glass fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-4 md:px-10 z-50">

    <div class="flex items-center gap-3">
        {{-- Botão hambúrguer mobile (sidebar esquerda) --}}
        @if($isHome)
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        @endif

        <a href="{{ route('home') }}" class="text-xl font-bold">
            <span class="text-blue-400">Luanda</span> <span class="text-white">bilhetes</span>
        </a>
    </div>

    {{-- Nav desktop --}}
    <div class="hidden md:flex space-x-6 text-gray-300 items-center">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active-link' : 'hover-blue' }}">Ínicio</a>
        <a href="{{ route('eventos.todos') }}" class="{{ request()->routeIs('eventos.*') ? 'active-link' : 'hover-blue' }}">Explorar</a>
        <a href="{{ route('noticias.index') }}" class="{{ request()->routeIs('noticias.*') ? 'active-link' : 'hover-blue' }}">Notícias</a>
        @auth <a href="{{ route('mensagens.index') }}" class="{{ request()->routeIs('mensagens.*') ? 'active-link' : 'hover-blue' }}">Mensagens</a> @endauth
        <form action="{{ route('eventos.todos') }}" method="GET">
            <input type="text" name="search" placeholder="Buscar..." class="px-3 py-1 rounded-lg text-black text-sm focus:outline-none">
        </form>
    </div>

    {{-- Direita: botões + notificações + avatar --}}
    <div class="flex items-center space-x-2 md:space-x-4">
        @guest
            {{-- Entrar: ícone em mobile, texto em desktop --}}
            <a href="{{ route('login') }}" class="flex items-center justify-center w-9 h-9 md:w-auto md:h-auto rounded-lg border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white transition md:px-4 md:py-2">
                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden md:inline">Entrar</span>
            </a>
            {{-- Cadastrar: ícone em mobile, texto em desktop --}}
            <a href="{{ route('register') }}" class="flex items-center justify-center w-9 h-9 md:w-auto md:h-auto rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition md:px-4 md:py-2 font-semibold">
                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="hidden md:inline">Cadastrar</span>
            </a>
        @endguest

        @auth
        @php
            $user = auth()->user();
            $unread = $user->unreadNotifications->count();
        @endphp

        {{-- Sino de Notificações --}}
        <livewire:notification-bell />

        {{-- Botão sidebar direita mobile --}}
        @if($isHome)
        <button @click="rightSidebarOpen = !rightSidebarOpen" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>
        @endif

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="relative focus:outline-none">
                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                     class="w-9 h-9 md:w-10 md:h-10 rounded-full object-cover border-2 border-blue-400">
                @if($unread > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-xs px-1.5 rounded-full">{{ $unread }}</span>
                @endif
            </button>
            <div x-show="open" x-cloak @click.away="open = false" x-transition
                 class="absolute right-0 mt-3 w-56 bg-white text-gray-800 rounded-2xl shadow-2xl overflow-hidden z-[60]">
                <div class="p-4 border-b">
                    <p class="font-semibold">{{ explode(' ', $user->name)[0] }}</p>
                    <p class="text-sm text-gray-500">{{ ucfirst($user->role) }}</p>
                </div>
                <a href="{{ route('profile.show', ['id' => auth()->user()->id]) }}" class="block px-4 py-3 hover:bg-blue-50">Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-3 hover:bg-red-100 text-red-600">Sair</button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</header>

@if($isHome)

  {{-- Overlay sidebar esquerda mobile --}}
  <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/60 z-40 md:hidden"></div>

  {{-- Overlay sidebar direita mobile --}}
  <div x-show="rightSidebarOpen" x-cloak @click="rightSidebarOpen = false"
       class="fixed inset-0 bg-black/60 z-40 md:hidden"></div>

  <div class="flex pt-16 h-screen overflow-hidden">

    <!-- SIDEBAR ESQUERDA -->
      <aside :class="sidebarOpen ? 'translate-x-0 !flex' : '-translate-x-full md:translate-x-0'"
       class="w-72 glass-sidebar fixed left-0 top-16 bottom-0 p-6 overflow-y-auto no-scrollbar hidden md:flex flex-col z-40 transition-transform duration-300">

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
            <p class="mt-3 font-semibold text-lg uppercase tracking-wider text-white">{{ $firstName }}</p>
            <p class="text-sm text-gray-400">{{ $role }}</p>
        </div>
        @endauth

        <p class="text-gray-400 text-xs mb-2 font-bold uppercase">Geral</p>
        <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
            <a href="{{ route('home') }}" class="block hover-blue" @click="sidebarOpen = false">🏠 Início</a>
            <a href="#" class="block hover-blue">🔔 Notificações</a>
            <a href="{{ route('eventos.todos') }}" class="block hover-blue" @click="sidebarOpen = false">🎟 Eventos</a>
            @auth
            <a href="{{ route('mensagens.index') }}" class="block hover-blue" @click="sidebarOpen = false">💬 Mensagens</a>
            <a href="{{ route('profile.edit') }}" class="block hover-blue" @click="sidebarOpen = false">👤 Perfil</a>
            @endauth
        </nav>

        @auth
            @if($user->role === 'admin')
                <p class="text-gray-400 text-xs mb-2 font-bold uppercase border-t border-gray-700 pt-4">Administração</p>
                <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
                    <a href="{{ route('admin.dashboard') }}" class="block hover-blue" @click="sidebarOpen = false">📊 Dashboard</a>
                    <a href="{{ route('admin.usuarios.index') }}" class="block hover-blue" @click="sidebarOpen = false">👥 Usuários</a>
                    <a href="{{ route('admin.eventos') }}" class="block hover-blue" @click="sidebarOpen = false">🎫 Gerenciar Eventos</a>
                    <a href="{{ route('admin.reservas') }}" class="block hover-blue" @click="sidebarOpen = false">📦 Reservas Gerais</a>
                    <a href="{{ route('admin.pagos') }}" class="block hover-blue" @click="sidebarOpen = false">💰 Pagamentos</a>
                    <a href="{{ route('admin.scanner') }}" class="block hover-blue" @click="sidebarOpen = false">📸 Validar Bilhete</a>
                </nav>
            @elseif($user->role === 'creator')
                <p class="text-gray-400 text-xs mb-2 font-bold uppercase border-t border-gray-700 pt-4">Painel Criador</p>
                <nav class="flex flex-col space-y-3 mb-6 text-gray-300">
                    <a href="{{ route('admin.eventos') }}" class="block hover-blue" @click="sidebarOpen = false">📋 Meus Eventos</a>
                    <a href="{{ route('admin.eventos.criar') }}" class="block hover-blue" @click="sidebarOpen = false">➕ Criar Evento</a>
                    <a href="{{ route('admin.reservas') }}" class="block hover-blue" @click="sidebarOpen = false">📦 Minhas Reservas</a>
                    <a href="{{ route('admin.pagos') }}" class="block hover-blue" @click="sidebarOpen = false">💰 Meus Ganhos</a>
                </nav>
            @endif
        @endauth
    </aside>

    <!-- CONTEÚDO CENTRAL -->
    <main class="flex-1 md:ml-72 md:mr-72 overflow-y-auto no-scrollbar p-4 md:p-10 page-transition">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- SIDEBAR DIREITA -->
    <aside :class="rightSidebarOpen ? 'translate-x-0 !block' : 'translate-x-full md:translate-x-0'"
       class="w-72 glass-sidebar-right fixed right-0 top-16 bottom-0 p-6 overflow-y-auto no-scrollbar z-40 hidden md:block transition-transform duration-300">

        <h2 class="text-sm font-bold mb-4 text-blue-400 uppercase tracking-widest">🎟 Populares</h2>
        @foreach(\App\Models\Evento::latest()->take(3)->get() as $ev)
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="block bg-gray-800/50 p-4 rounded-2xl mb-3 text-sm hover:bg-gray-700/50 transition">
                {{ $ev->titulo }}
            </a>
        @endforeach

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
    <main class="max-w-6xl mx-auto px-4 md:p-10">
        @yield('content')
        {{ $slot ?? '' }}
    </main>
</div>
@endif
<script>
    document.addEventListener('alpine:init', () => {
    });
function toggleNotifDropdown() {
    const d = document.getElementById('notif-dropdown');
    if (d) d.classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const d = document.getElementById('notif-dropdown');
    const btn = e.target.closest('button[wire\\:click="toggleOpen"]');
    if (d && !btn && !d.contains(e.target)) d.classList.add('hidden');
});
</script>
@livewireScripts
</body>
</html>