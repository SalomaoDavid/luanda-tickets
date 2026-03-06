<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de Acesso | Luanda Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="bg-[#0a0a0c] text-white min-h-screen flex flex-col items-center p-6">

    <header class="w-full max-w-md flex justify-between items-center mb-10 mt-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-sky-500 rounded-lg rotate-12 flex items-center justify-center shadow-[0_0_20px_rgba(14,165,233,0.5)]">
                <span class="text-white font-black">LT</span>
            </div>
            <h1 class="text-xl font-bold tracking-tighter uppercase italic">Validator<span class="text-sky-500">PRO</span></h1>
        </div>
        <div class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center">
            <span class="text-xs">👤</span>
        </div>
    </header>

    <main class="w-full max-w-md flex-1 flex flex-col items-center">
        
        <div class="relative w-full aspect-square max-w-[300px] mb-8 group">
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-sky-500 rounded-tl-xl"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-sky-500 rounded-tr-xl"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-sky-500 rounded-bl-xl"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-sky-500 rounded-br-xl"></div>
            
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-full h-[2px] bg-sky-400 shadow-[0_0_15px_#0ea5e9] opacity-50 relative -top-1/4 animate-bounce"></div>
            </div>

            <div class="absolute inset-0 flex flex-col items-center justify-center opacity-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <p class="text-[10px] font-bold mt-2 tracking-[0.3em] uppercase">Aguardando Código</p>
            </div>
        </div>

        <div class="w-full space-y-4">
            <div class="glass-panel p-2 rounded-2xl">
                <input type="text" 
                       placeholder="DIGITE O CÓDIGO MANUALMENTE..." 
                       class="w-full bg-transparent border-none text-center py-4 text-sm font-bold tracking-[0.2em] focus:ring-0 placeholder:text-gray-600 uppercase">
            </div>

            <button class="w-full bg-white text-black font-black py-5 rounded-2xl uppercase tracking-widest text-xs hover:bg-sky-500 hover:text-white transition-all duration-500 shadow-xl shadow-white/5">
                Verificar Acesso
            </button>

            <button class="w-full glass-panel text-white/40 font-bold py-4 rounded-2xl uppercase tracking-[0.2em] text-[10px] flex items-center justify-center gap-2">
                <span class="animate-pulse w-2 h-2 bg-red-500 rounded-full"></span>
                Ativar Camera (Scanner)
            </button>
        </div>

        <div class="w-full mt-12">
            <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Últimas Validações</h3>
            <div class="space-y-3 opacity-50">
                <div class="glass-panel p-4 rounded-xl flex justify-between items-center italic">
                    <span class="text-xs font-bold font-mono">LT-X82J91S</span>
                    <span class="text-[9px] bg-green-500/20 text-green-400 px-2 py-0.5 rounded border border-green-500/30 font-black">VALIDADO</span>
                </div>
                <div class="glass-panel p-4 rounded-xl flex justify-between items-center italic">
                    <span class="text-xs font-bold font-mono">LT-P02K55L</span>
                    <span class="text-[9px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded border border-red-500/30 font-black">INVÁLIDO</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-auto py-6">
        <p class="text-[10px] text-gray-600 font-bold tracking-widest uppercase italic">Luanda Tickets © 2026 // Security Protocol</p>
    </footer>

</body>
</html>