<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Luanda Tickets</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak]{display:none!important;}
        body{font-family:'Inter',sans-serif;}
        .glass{background:rgba(15,23,42,0.85);backdrop-filter:blur(18px);border-bottom:1px solid rgba(59,130,246,0.2);}
        .glass-sidebar{background:rgba(15,23,42,0.92);backdrop-filter:blur(18px);border-right:1px solid rgba(59,130,246,0.2);}
        .glass-sidebar-right{background:rgba(15,23,42,0.92);backdrop-filter:blur(18px);border-left:1px solid rgba(59,130,246,0.2);}
        .active-link{color:#60a5fa;font-weight:600;}
        .hover-blue:hover{color:#60a5fa;}
        .no-scrollbar::-webkit-scrollbar{display:none;}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none;}
        .page-transition{opacity:0;animation:fadeIn 0.35s ease forwards;}
        @keyframes fadeIn{to{opacity:1;}}
        .btn-blue{background:#2563eb;color:white;padding:8px 16px;border-radius:12px;font-weight:600;transition:0.3s;}
        .btn-blue:hover{background:#1d4ed8;}

        /* ── NAV ITEM COM BADGE ── */
        .nav-item{
            display:flex;align-items:center;justify-content:space-between;
            padding:8px 10px;border-radius:12px;
            text-decoration:none;color:#cbd5e1;
            font-size:14px;font-weight:500;
            transition:all .15s;
        }
        .nav-item:hover{background:rgba(59,130,246,.12);color:#60a5fa;}
        .nav-item.active{background:rgba(59,130,246,.15);color:#60a5fa;font-weight:600;}
        .nav-item-left{display:flex;align-items:center;gap:8px;}
        .nav-badge{
            min-width:18px;height:18px;border-radius:999px;
            background:#f43f5e;color:#fff;
            font-size:10px;font-weight:800;
            display:flex;align-items:center;justify-content:center;
            padding:0 4px;
            animation:badge-pulse .9s ease infinite alternate;
        }
        @keyframes badge-pulse{from{transform:scale(1)}to{transform:scale(1.18)}}

        /* ── POPULARES SIDEBAR ── */
        .pop-section{
            position:relative;border-radius:16px;overflow:hidden;
            background:linear-gradient(135deg,#0f172a,#1e1b4b,#0f172a);
            padding:16px;margin-bottom:16px;
        }
        .pop-section::before{
            content:'';position:absolute;inset:0;pointer-events:none;
            background:
                radial-gradient(ellipse at 20% 50%,rgba(99,102,241,.25),transparent 60%),
                radial-gradient(ellipse at 80% 20%,rgba(6,182,212,.2),transparent 50%);
        }
        .pop-title{
            position:relative;
            font-size:11px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;
            margin-bottom:12px;
            background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6,#38bdf8);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;background-size:300% auto;
            animation:shimmer 3s linear infinite;
        }
        .pop-title::after{
            content:'';display:block;height:2px;border-radius:999px;margin-top:5px;
            background:linear-gradient(90deg,#38bdf8,#818cf8,#f472b6,#38bdf8);
            background-size:300% auto;animation:shimmer 3s linear infinite;
        }
        @keyframes shimmer{to{background-position:300% center}}
        .pop-item{
            display:flex;align-items:center;gap:10px;
            padding:8px 10px;border-radius:12px;margin-bottom:6px;
            background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);
            text-decoration:none;transition:all .2s;position:relative;overflow:hidden;
        }
        .pop-item:hover{background:rgba(255,255,255,.1);transform:translateX(3px);}
        .pop-item::before{
            content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
            background:linear-gradient(to bottom,#38bdf8,#818cf8);
            border-radius:0 2px 2px 0;
        }
        .pop-item:last-child{margin-bottom:0;}
        .pop-rank{font-size:11px;font-weight:900;color:#64748b;width:18px;flex-shrink:0;text-align:center;}
        .pop-rank.top1{background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .pop-rank.top2{background:linear-gradient(135deg,#94a3b8,#cbd5e1);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .pop-rank.top3{background:linear-gradient(135deg,#b45309,#d97706);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .pop-thumb{width:36px;height:36px;border-radius:10px;object-fit:cover;flex-shrink:0;border:1px solid rgba(255,255,255,.1);}
        .pop-thumb-ph{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
        .pop-nome{font-size:12px;font-weight:700;color:#f0f6ff;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        .pop-sub{font-size:10px;color:#64748b;margin-top:1px;}
        .pop-badge{font-size:9px;font-weight:800;padding:2px 7px;border-radius:20px;flex-shrink:0;background:linear-gradient(135deg,#0ea5e9,#6366f1);color:#fff;}

        /* ── CRIADORES SIDEBAR ── */
        .criador-item{
            display:flex;align-items:center;gap:10px;
            padding:8px;border-radius:12px;margin-bottom:4px;
            text-decoration:none;transition:background .15s;
        }
        .criador-item:hover{background:rgba(255,255,255,.06);}
        .criador-avatar{position:relative;flex-shrink:0;}
        .criador-avatar img{width:38px;height:38px;border-radius:50%;border:2px solid #3b82f6;object-fit:cover;}
        .criador-online{position:absolute;bottom:0;right:0;width:10px;height:10px;border-radius:50%;border:2px solid #1e293b;}
        .criador-nome{font-size:13px;font-weight:600;color:#f0f6ff;line-height:1.2;}
        .criador-eventos{font-size:10px;color:#64748b;}

        /* ── TÍTULO SIDEBAR COM ANIMAÇÃO ── */
        .sidebar-section-title{
            font-size:11px;font-weight:900;letter-spacing:.16em;
            text-transform:uppercase;
            margin-bottom:8px;margin-top:20px;padding-bottom:6px;
            position:relative;
        }
        .sidebar-section-title:first-child{margin-top:0;}
        .sidebar-section-title::after{
            content:'';display:block;height:2px;border-radius:999px;margin-top:6px;
        }
        /* Geral — branco e azul */
        .sidebar-section-title.geral{
            background:linear-gradient(90deg,#ffffff,#60a5fa,#38bdf8,#ffffff);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;background-size:300% auto;
            animation:shimmer 3s linear infinite;
        }
        .sidebar-section-title.geral::after{
            background:linear-gradient(90deg,#ffffff,#60a5fa,#38bdf8,#ffffff);
            background-size:300% auto;animation:shimmer 3s linear infinite;
        }
        /* Admin / Criador — roxo e ciano */
        .sidebar-section-title.admin{
            background:linear-gradient(90deg,#a78bfa,#38bdf8,#a78bfa,#38bdf8);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;background-size:300% auto;
            animation:shimmer 3s linear infinite;
        }
        .sidebar-section-title.admin::after{
            background:linear-gradient(90deg,#a78bfa,#38bdf8,#a78bfa);
            background-size:300% auto;animation:shimmer 3s linear infinite;
        }
    </style>
    <script src="{{ asset('js/emoji-mart.js') }}" async></script>
    @livewireStyles
</head>

<body class="text-white relative bg-slate-900"
      x-data="{ sidebarOpen: false, rightSidebarOpen: false }">

@php
    $isHome   = request()->routeIs('home');
    $isStatic = request()->routeIs('login','register','password.*');
@endphp

<div class="fixed inset-0 -z-10">
    <img src="{{ asset('images/luanda-noite.png') }}" class="w-full h-full object-cover brightness-50">
</div>

{{-- ═══════════ HEADER ═══════════ --}}
<header class="glass fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-4 md:px-10 z-50">

    <div class="flex items-center gap-3">
        <button x-on:click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <a href="{{ route('home') }}" class="text-xl font-bold">
            <span class="text-blue-400">Luanda</span> <span class="text-white">bilhetes</span>
        </a>
    </div>

    {{-- Nav desktop --}}
    <div class="hidden md:flex space-x-6 text-gray-300 items-center">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active-link' : 'hover-blue' }}">Início</a>
        <a href="{{ route('eventos.todos') }}" class="{{ request()->routeIs('eventos.*') ? 'active-link' : 'hover-blue' }}">Explorar</a>
        <a href="{{ route('noticias.index') }}" class="{{ request()->routeIs('noticias.*') ? 'active-link' : 'hover-blue' }}">Notícias</a>
        @auth
        {{-- Mensagens com badge no header desktop --}}
        @php
            $msgNaoLidas = auth()->user()->unreadNotifications
                ->where('type', 'App\\Notifications\\NovaMensagem')->count();
        @endphp
        <a href="{{ route('mensagens.index') }}"
           class="{{ request()->routeIs('mensagens.*') ? 'active-link' : 'hover-blue' }} relative inline-flex items-center gap-1">
            💬 Mensagens
            @if($msgNaoLidas > 0)
            <span class="nav-badge" style="font-size:9px;min-width:16px;height:16px;">
                {{ $msgNaoLidas > 99 ? '99+' : $msgNaoLidas }}
            </span>
            @endif
        </a>
        @endauth
        <form action="{{ route('eventos.todos') }}" method="GET">
            <input type="text" name="search" placeholder="Buscar..." class="px-3 py-1 rounded-lg text-black text-sm focus:outline-none">
        </form>
    </div>

    {{-- Direita --}}
    <div class="flex items-center space-x-2 md:space-x-4">
        @guest
            <a href="{{ route('login') }}" class="flex items-center justify-center w-9 h-9 md:w-auto md:h-auto rounded-lg border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white transition md:px-4 md:py-2">
                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden md:inline">Entrar</span>
            </a>
            <a href="{{ route('register') }}" class="flex items-center justify-center w-9 h-9 md:w-auto md:h-auto rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition md:px-4 md:py-2 font-semibold">
                <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="hidden md:inline">Cadastrar</span>
            </a>
        @endguest

        @auth
        @php
            $user    = auth()->user();
            $unread  = $user->unreadNotifications->count();
            $msgNaoLidas = $user->unreadNotifications
                ->where('type', 'App\\Notifications\\NovaMensagem')->count();
        @endphp

        {{-- Sino --}}
        <livewire:notification-bell />

        {{-- Ícone mensagens no header mobile com badge --}}
        <a href="{{ route('mensagens.index') }}" class="md:hidden relative flex items-center justify-center w-9 h-9 text-gray-300 hover:text-white">
            <span style="font-size:20px;">💬</span>
            @if($msgNaoLidas > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black rounded-full min-w-[16px] h-4 flex items-center justify-center px-1">
                {{ $msgNaoLidas > 99 ? '99+' : $msgNaoLidas }}
            </span>
            @endif
        </a>

        {{-- Botão sidebar direita mobile --}}
        <button x-on:click="rightSidebarOpen = !rightSidebarOpen" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Avatar + dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button x-on:click="open = !open" class="relative focus:outline-none">
                <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}"
                     class="w-9 h-9 md:w-10 md:h-10 rounded-full object-cover border-2 border-blue-400">
                @if($unread > 0)
                <span class="absolute -top-1 -right-1 bg-red-600 text-xs px-1.5 rounded-full">{{ $unread }}</span>
                @endif
            </button>
            <div x-show="open" x-cloak x-on:click.away="open = false" x-transition
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

{{-- Overlays mobile --}}
<div x-show="sidebarOpen"  x-cloak x-on:click="sidebarOpen = false"  class="fixed inset-0 bg-black/60 z-40 md:hidden"></div>
<div x-show="rightSidebarOpen" x-cloak x-on:click="rightSidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 md:hidden"></div>

<div class="flex pt-16 min-h-screen md:h-[calc(100vh-4rem)] md:overflow-hidden">

    {{-- ═══════════ SIDEBAR ESQUERDA ═══════════ --}}
    <aside x-bind:class="sidebarOpen ? 'translate-x-0 !flex' : '-translate-x-full md:translate-x-0'"
           class="w-72 glass-sidebar fixed left-0 top-16 bottom-0 p-5 overflow-y-auto no-scrollbar hidden md:flex flex-col z-40 transition-transform duration-300">

        @auth
        @php
            $user        = auth()->user();
            $firstName   = explode(' ', $user->name)[0];
            $role        = ucfirst($user->role);
            $unreadNotif = $user->unreadNotifications->count();
            $unreadMsg   = $user->unreadNotifications
                ->whereIn('type', [
                    'App\\Notifications\\NewMessageNotification',
                    'App\\Notifications\\NovaMensagem',
                ])->count();
        @endphp

        {{-- Avatar --}}
        <div class="mb-6 text-center">
            <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0ea5e9&color=fff&size=128' }}"
                 class="w-20 h-20 mx-auto rounded-full object-cover border-4 border-blue-400 shadow-lg">
            <p class="mt-3 font-semibold text-lg uppercase tracking-wider text-white">{{ $firstName }}</p>
            <p class="text-sm text-gray-400">{{ $role }}</p>
        </div>
        @endauth

        {{-- GERAL --}}
        <div class="sidebar-section-title geral">Geral</div>
        <div class="flex flex-col gap-1 mb-2">
            <a href="{{ route('home') }}"
               class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}"
               x-on:click="sidebarOpen = false">
                <span class="nav-item-left">🏠 Início</span>
            </a>

            @auth
            {{-- Notificações com badge --}}
            <a href="{{ route('notificacoes.index') }}"
               class="nav-item {{ request()->routeIs('notificacoes.*') ? 'active' : '' }}"
               x-on:click="sidebarOpen = false">
                <span class="nav-item-left">🔔 Notificações</span>
                @if($unreadNotif > 0)
                <span class="nav-badge">{{ $unreadNotif > 99 ? '99+' : $unreadNotif }}</span>
                @endif
            </a>
            @endauth

            <a href="{{ route('eventos.todos') }}"
               class="nav-item {{ request()->routeIs('eventos.*') ? 'active' : '' }}"
               x-on:click="sidebarOpen = false">
                <span class="nav-item-left">🎟 Eventos</span>
            </a>

            @auth
            {{-- Mensagens com badge --}}
            <a href="{{ route('mensagens.index') }}"
               class="nav-item {{ request()->routeIs('mensagens.*') ? 'active' : '' }}"
               x-on:click="sidebarOpen = false">
                <span class="nav-item-left">💬 Mensagens</span>
                @if($unreadMsg > 0)
                <span class="nav-badge">{{ $unreadMsg > 99 ? '99+' : $unreadMsg }}</span>
                @endif
            </a>

            <a href="{{ route('profile.edit') }}"
               class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
               x-on:click="sidebarOpen = false">
                <span class="nav-item-left">👤 Perfil</span>
            </a>
            @endauth
        </div>

        @auth
        @if($user->role === 'admin')
        <div class="sidebar-section-title admin">Administração</div>
        <div class="flex flex-col gap-1 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📊 Dashboard</span></a>
            <a href="{{ route('admin.usuarios.index') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">👥 Usuários</span></a>
            <a href="{{ route('admin.eventos') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">🎫 Eventos</span></a>
            <a href="{{ route('admin.reservas') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📦 Reservas</span></a>
            <a href="{{ route('admin.pagos') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">💰 Pagamentos</span></a>
            <a href="{{ route('admin.scanner') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📸 Scanner</span></a>
        </div>
        @elseif($user->role === 'creator')
        <div class="sidebar-section-title admin">Painel Criador</div>
        <div class="flex flex-col gap-1 mb-2">
            <a href="{{ route('admin.eventos') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📋 Meus Eventos</span></a>
            <a href="{{ route('admin.eventos.criar') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">➕ Criar Evento</span></a>
            <a href="{{ route('admin.reservas') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📦 Reservas</span></a>
            <a href="{{ route('admin.pagos') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">💰 Ganhos</span></a>
            <a href="{{ route('admin.scanner') }}" class="nav-item" x-on:click="sidebarOpen = false"><span class="nav-item-left">📸 Scanner</span></a>
        </div>
        @endif
        @endauth
    </aside>

    {{-- ═══════════ CONTEÚDO CENTRAL ═══════════ --}}
    <main class="flex-1 md:ml-72 {{ $isHome ? 'md:mr-72' : 'md:mr-0' }}
                 {{ $isStatic ? 'flex items-center justify-center pt-20' : 'overflow-y-auto no-scrollbar' }}
                 md:p-10 page-transition">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    {{-- ═══════════ SIDEBAR DIREITA — POPULARES ═══════════ --}}
    @if($isHome)
    <aside x-bind:class="rightSidebarOpen ? 'translate-x-0 !block' : 'translate-x-full md:translate-x-0'"
           class="w-72 glass-sidebar-right fixed right-0 top-16 bottom-0 p-5 overflow-y-auto no-scrollbar z-40 hidden md:block transition-transform duration-300">

        {{-- POPULARES com efeito atrativo --}}
        @php
            $populares = \App\Models\Evento::withCount('curtidas')
                ->where('status','publicado')
                ->orderBy('curtidas_count','desc')
                ->take(5)->get();
        @endphp

        <div class="pop-section">
            <div class="pop-title">🔥 Populares</div>
            @forelse($populares as $i => $ev)
            <a href="{{ route('evento.detalhes', $ev->id) }}" class="pop-item">
                <span class="pop-rank {{ $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : '')) }}">
                    {{ $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#'.($i+1))) }}
                </span>
                @if($ev->imagem_capa)
                    <img src="{{ asset('storage/'.$ev->imagem_capa) }}" class="pop-thumb" alt="">
                @else
                    <div class="pop-thumb-ph">{{ optional($ev->categoria)->emoji ?? '🎟' }}</div>
                @endif
                <div style="flex:1;min-width:0;">
                    <div class="pop-nome">{{ $ev->titulo }}</div>
                    <div class="pop-sub">{{ $ev->curtidas_count }} curtidas</div>
                </div>
                <span class="pop-badge">HOT</span>
            </a>
            @empty
            <p style="font-size:12px;color:#475569;text-align:center;padding:12px 0;">Sem eventos ainda.</p>
            @endforelse
        </div>

        {{-- CRIADORES --}}
        <div class="sidebar-section-title" style="color:#818cf8;border-color:rgba(129,140,248,.15);">🎨 Criadores Activos</div>
        @foreach(\App\Models\User::where('role','creator')->whereHas('eventos')->withCount('eventos')->orderBy('eventos_count','desc')->take(6)->get() as $criador)
        <a href="{{ route('profile.show', $criador->id) }}" class="criador-item">
            <div class="criador-avatar">
                <img src="{{ $criador->avatar ? asset('storage/'.$criador->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($criador->name).'&background=0ea5e9&color=fff&size=64' }}" alt="">
                <span class="criador-online" style="background:{{ $criador->isOnline() ? '#10b981' : '#475569' }};"></span>
            </div>
            <div>
                <div class="criador-nome">{{ explode(' ', $criador->name)[0] }}</div>
                <div class="criador-eventos">{{ $criador->eventos_count }} evento(s)</div>
            </div>
            @if($criador->isOnline())
            <span style="font-size:9px;font-weight:700;color:#10b981;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);padding:2px 7px;border-radius:20px;margin-left:auto;flex-shrink:0;">online</span>
            @endif
        </a>
        @endforeach

    </aside>
    @endif

</div>

{{-- ── ÍCONE MENSAGENS FLUTUANTE (desktop + mobile) ── --}}
@auth
@php $msgFloat = auth()->user()->unreadNotifications->whereIn('type',['App\\Notifications\\NewMessageNotification','App\\Notifications\\NovaMensagem'])->count(); @endphp
<a href="{{ route('mensagens.index') }}"
   style="
       position:fixed;bottom:80px;right:18px;z-index:400;
       width:54px;height:54px;border-radius:50%;
       background:linear-gradient(135deg,#2563eb,#7c3aed);
       box-shadow:0 4px 20px rgba(37,99,235,.55);
       display:flex;align-items:center;justify-content:center;
       font-size:24px;text-decoration:none;
       animation:float-bounce 2s ease-in-out infinite;
   ">
    💬
    @if($msgFloat > 0)
    <span style="
        position:absolute;top:-2px;right:-2px;
        min-width:18px;height:18px;border-radius:999px;
        background:#f43f5e;border:2px solid #fff;
        color:#fff;font-size:10px;font-weight:800;
        display:flex;align-items:center;justify-content:center;
        padding:0 3px;
    ">{{ $msgFloat > 99 ? '99+' : $msgFloat }}</span>
    @endif
</a>
<style>
@@keyframes float-bounce{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-9px);}
}
</style>
@endauth

<script>
    window.abrirDrawer = function(id){const el=document.getElementById(id);if(el){el.classList.add('open');document.body.style.overflow='hidden';}};
    window.fecharDrawer = function(id){const el=document.getElementById(id);if(el){el.classList.remove('open');document.body.style.overflow='';}};
    window.toggleSobre = function(){var p=document.getElementById('sobreText');var btn=document.getElementById('sobreBtn');var aberto=btn.textContent.includes('menos');p.style.webkitLineClamp=aberto?'4':'unset';p.style.overflow=aberto?'hidden':'visible';btn.textContent=aberto?'Ver mais ↓':'Ver menos ↑';};
    window.handleUpload = function(input){var file=input.files[0];var prev=document.getElementById('upload-preview');var name=document.getElementById('upload-preview-name');if(file){name.textContent=file.name;prev.style.display='flex';}else{prev.style.display='none';}};
    window.selecionarTipo = function(nome,preco,id,disp){if(disp<=0)return;window.dispatchEvent(new CustomEvent('abrir-modal',{detail:{nome,preco,id}}));};
    window.selecionarTipoMobile = function(nome,preco,id,disp){if(disp<=0)return;if(typeof window.fecharDrawer==='function')window.fecharDrawer('drawer-bilhetes-mobile');setTimeout(()=>window.dispatchEvent(new CustomEvent('abrir-modal',{detail:{nome,preco,id}})),150);};
    window.toggleNotifDropdown = function(){const d=document.getElementById('notif-dropdown');if(d)d.classList.toggle('hidden');};
    document.addEventListener('keydown',function(e){if(e.key==='Escape'){document.querySelectorAll('.drawer-overlay.open').forEach(function(d){d.classList.remove('open');});document.body.style.overflow='';}});
    document.addEventListener('click',function(e){const d=document.getElementById('notif-dropdown');const btn=e.target.closest('button[wire\\:click="toggleOpen"]');if(d&&!btn&&!d.contains(e.target))d.classList.add('hidden');});
</script>
@livewireScripts
@stack('scripts')
</body>
</html>