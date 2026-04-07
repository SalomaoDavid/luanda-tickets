<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Página não encontrada — Luanda Tickets</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(18px); border-bottom: 1px solid rgba(59, 130, 246, 0.2); }
        @keyframes fadeIn { to { opacity: 1; } }
        .fade-in { opacity: 0; animation: fadeIn 0.5s ease forwards; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="text-white bg-slate-900 min-h-screen flex flex-col">

    {{-- Fundo --}}
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('images/luanda-noite.png') }}" class="w-full h-full object-cover brightness-50">
    </div>

    {{-- Header --}}
    <header class="glass fixed top-0 left-0 right-0 h-16 flex items-center px-6 z-50">
        <a href="{{ route('home') }}" class="text-xl font-bold">
            <span class="text-blue-400">Luanda</span> <span class="text-white">bilhetes</span>
        </a>
    </header>

    {{-- Conteúdo --}}
    <main class="flex-1 flex items-center justify-center pt-16 px-4">
        <div class="text-center fade-in max-w-md">
            <div class="float text-8xl mb-6">🎟</div>
            <h1 class="text-8xl font-black text-blue-400 mb-2">404</h1>
            <h2 class="text-2xl font-bold text-white mb-3">Página não encontrada</h2>
            <p class="text-gray-400 mb-8 leading-relaxed">
                O evento, página ou recurso que procuras não existe ou foi removido.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('home') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-2xl transition">
                    🏠 Voltar ao início
                </a>
                <a href="{{ route('eventos.todos') }}"
                   class="border border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white font-semibold px-6 py-3 rounded-2xl transition">
                    🎫 Ver eventos
                </a>
            </div>
        </div>
    </main>

</body>
</html>