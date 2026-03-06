@extends('layouts.app')

@section('title', 'Centro de Comando - Painel')

@section('content')
<style>
    .tech-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 1.25rem;
        transition: all 0.3s ease;
    }
    .tech-card:hover { border-color: rgba(14, 165, 233, 0.4); }
    .neon-text { text-shadow: 0 0 10px rgba(14, 165, 233, 0.3); }
    .progress-bar { height: 4px; border-radius: 2px; background: rgba(255,255,255,0.1); overflow: hidden; }
    .progress-fill { height: 100%; transition: width 1s ease-in-out; }
</style>

<div class="max-w-7xl mx-auto p-6 md:p-10 relative z-10" x-data="{ showModal: false, imgUrl: '', clienteNome: '', whatsapp: '', qtd: '', total: '' }">
    
    {{-- Cabeçalho --}}
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="text-4xl uppercase tracking-tighter font-black neon-text text-white">
                <span class="text-sky-400">Centro</span> Administrativo
            </h1>
            <p class="text-slate-500 text-[10px] font-black tracking-[0.3em] uppercase mt-1">Luanda Tickets Analise Geral</p>
        </div>
        <div class="text-right">
            <span class="text-sky-400 text-[10px] font-black uppercase">Eventos Ativos: {{ $eventosAtivos ?? 0 }}</span>
        </div>
    </div>

    {{-- 1. Cards de Estatísticas Globais --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="tech-card p-6 border-l-4 border-l-blue-500">
            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-2">Receita Total Bruta</p>
            <h2 class="text-2xl font-black text-white">{{ number_format($receitaTotal ?? 0, 0, ',', '.') }} Kz</h2>
        </div>
        
        <div class="tech-card p-6 border-l-4 border-l-emerald-500">
            <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-2">Lucro Luanda Tickets (Taxa)</p>
            <h2 class="text-2xl font-black text-white">{{ number_format(($receitaTotal ?? 0) * 0.10, 0, ',', '.') }} Kz</h2>
            <p class="text-[7px] text-slate-500 font-bold mt-1 uppercase">Baseado em taxa de 10%</p>
        </div>

        <div class="tech-card p-6 border-l-4 border-l-amber-500">
            <p class="text-[9px] font-black text-amber-400 uppercase tracking-widest mb-2">Repasse aos Organizadores</p>
            <h2 class="text-2xl font-black text-white">{{ number_format(($receitaTotal ?? 0) * 0.90, 0, ',', '.') }} Kz</h2>
            <p class="text-[7px] text-slate-500 font-bold mt-1 uppercase">Líquido a pagar</p>
        </div>

        <div class="tech-card p-6 border-l-4 border-l-red-500">
            <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-2">Validação</p>
            <h2 class="text-2xl font-black text-red-400">{{ $pendentesCount ?? 0 }} Pendentes</h2>
        </div>
    </div>

    {{-- 2. NOVA SEÇÃO: PERFORMANCE POR SHOW --}}
    <div class="mb-12">
        <h3 class="text-[10px] font-black text-slate-400 uppercase mb-6 italic tracking-[0.2em] flex items-center gap-2">
            <span class="w-2 h-2 bg-sky-500 rounded-full animate-pulse"></span> // Desempenho_por_Evento
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($eventosPerformance ?? [] as $perf)
            <div class="tech-card p-6 bg-white/[0.02]">
                <h4 class="text-white font-black text-sm uppercase mb-4 truncate">{{ $perf->titulo }}</h4>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-[9px] mb-1 font-bold">
                            <span class="text-slate-400">NORMAIS: {{ $perf->qtd_normal }}</span>
                            <span class="text-sky-400">{{ number_format($perf->total_normal, 0, ',', '.') }} Kz</span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill bg-sky-500" style="width: {{ $perf->perc_vendas }}%"></div></div>
                    </div>

                    <div>
                        <div class="flex justify-between text-[9px] mb-1 font-bold">
                            <span class="text-slate-400">VIP: {{ $perf->qtd_vip }}</span>
                            <span class="text-purple-400">{{ number_format($perf->total_vip, 0, ',', '.') }} Kz</span>
                        </div>
                        <div class="progress-bar"><div class="progress-fill bg-purple-500" style="width: {{ $perf->perc_vip_vendas }}%"></div></div>
                    </div>

                    <div class="pt-2 border-t border-white/5 flex justify-between items-center">
                        <span class="text-[8px] font-black text-slate-500 uppercase">Total Arrecadado</span>
                        <span class="text-sm font-black text-white">{{ number_format($perf->total_geral, 0, ',', '.') }} Kz</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="tech-card p-8">
            <h3 class="text-[10px] font-black text-slate-400 uppercase mb-8 italic text-center w-full">// Comparativo_Receita</h3>
            <canvas id="chartReceitaEventos" class="max-h-[250px]"></canvas>
        </div>

        <div class="lg:col-span-2 tech-card p-8">
            <h3 class="text-[10px] font-black text-slate-400 uppercase mb-8 italic">// Registo_de_Vendas_Recentes</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 font-black uppercase border-b border-white/10">
                            <th class="pb-4">Cliente</th>
                            <th class="pb-4">Evento</th>
                            <th class="pb-4 text-right">Total</th>
                            <th class="pb-4 text-center">Gestão</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($vendasDetalhadas as $venda)
                        <tr class="text-slate-300 hover:bg-white/5 transition-all">
                            <td class="py-4 font-bold text-white">{{ $venda->nome_cliente }}</td>
                            <td class="py-4 opacity-70">{{ Str::limit($venda->tipoIngresso->evento->titulo ?? 'N/A', 20) }}</td>
                            <td class="py-4 text-right font-black text-sky-400">{{ number_format($venda->total, 0, ',', '.') }} Kz</td>
                            <td class="py-4 flex justify-center gap-3">
                                <button @click="showModal = true; imgUrl = '{{ asset('storage/' . $venda->comprovativo) }}'; clienteNome = '{{ $venda->nome_cliente }}'; whatsapp = '{{ $venda->whatsapp }}';" class="bg-white/5 hover:bg-sky-500/20 border border-white/10 p-2 rounded text-blue-400 transition">📂</button>
                                
                                {{-- PARTE CONCERTADA AQUI: Alterado admin.vendas.destroy para reserva.eliminar --}}
                                <form action="{{ route('reserva.eliminar', $venda->id) }}" method="POST" onsubmit="return confirm('Apagar registo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-white/5 hover:bg-red-500/20 border border-white/10 p-2 rounded text-red-500 transition">🗑️</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm">
        <div class="tech-card max-w-lg w-full p-6 border border-white/20" @click.away="showModal = false">
            <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-4">
                <h3 class="text-white font-black uppercase text-xs tracking-widest">Comprovativo_Pagamento</h3>
                <button @click="showModal = false" class="text-slate-500 hover:text-white text-2xl">&times;</button>
            </div>
            <img :src="imgUrl" class="w-full h-auto rounded-xl border border-white/10 mb-4 max-h-80 object-contain shadow-2xl">
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white/5 p-3 rounded-lg"><p class="text-[8px] text-slate-500 uppercase font-black">Cliente</p><p class="text-white font-bold" x-text="clienteNome"></p></div>
                <div class="bg-white/5 p-3 rounded-lg"><p class="text-[8px] text-slate-500 uppercase font-black">WhatsApp</p><p class="text-green-400 font-bold" x-text="whatsapp"></p></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxBar = document.getElementById('chartReceitaEventos').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($eventosPerformance)->pluck('titulo')) !!},
            datasets: [{
                label: 'Receita por Show (Kz)',
                data: {!! json_encode(collect($eventosPerformance)->pluck('total_geral')) !!},
                backgroundColor: '#0ea5e9',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 9 } } }
            }
        }
    });
</script>
@endsection