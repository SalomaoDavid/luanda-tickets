@extends('layouts.app')

@section('content')
<div class="w-full px-3 py-6 md:max-w-7xl md:mx-auto md:px-6 md:py-10">

    {{-- CABEÇALHO --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">
                Painel Financeiro
            </span>
            <h1 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight mt-2 flex items-center gap-2">
                <span class="text-emerald-400">✅</span> Vendas Confirmadas
            </h1>
        </div>
        <a href="{{ route('admin.reservas') }}" class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 px-5 py-2.5 rounded-xl font-bold transition text-xs uppercase tracking-wider backdrop-blur-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar para Pendentes
        </a>
    </div>

    {{-- CARDS DE RESUMO FINANCEIRO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        {{-- Card de Total --}}
        <div class="glass-sidebar p-6 rounded-2xl border border-sky-500/20 bg-slate-900/40 backdrop-blur-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sky-500/10 rounded-full blur-2xl group-hover:bg-sky-500/20 transition"></div>
            <p class="text-[11px] font-black uppercase text-slate-400 tracking-wider">
                {{ auth()->user()->role === 'admin' ? 'Faturamento Global' : 'Meu Faturamento' }}
            </p>
            <h3 class="text-3xl font-black text-sky-400 mt-2 tracking-tight">
                {{ number_format($pagamentos->sum('total'), 0, ',', '.') }} <span class="text-lg text-sky-200/60 font-semibold">Kz</span>
            </h3>
        </div>
        
        {{-- Card de Ingressos --}}
        <div class="glass-sidebar p-6 rounded-2xl border border-emerald-500/20 bg-slate-900/40 backdrop-blur-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition"></div>
            <p class="text-[11px] font-black uppercase text-slate-400 tracking-wider">Bilhetes Vendidos</p>
            <h3 class="text-3xl font-black text-emerald-400 mt-2 tracking-tight">
                {{ $pagamentos->count() }} <span class="text-lg text-emerald-200/60 font-semibold">unidades</span>
            </h3>
        </div>
    </div>

    {{-- TABELA DE VENDAS --}}
    <div class="bg-slate-900/60 backdrop-blur-md rounded-2xl shadow-2xl overflow-hidden border border-white/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-white/10 text-[11px] font-black uppercase tracking-wider text-slate-400">
                        <th class="p-5">Cliente</th>
                        <th class="p-5">Evento</th>
                        <th class="p-5 text-center">Quantidade</th>
                        <th class="p-5 text-center">Total Pago</th>
                        <th class="p-5 text-right">Aprovado em</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm">
                    @forelse($pagamentos as $pago)
                    <tr class="hover:bg-white/[0.02] transition duration-150">
                        <td class="p-5 font-semibold text-slate-100">
                            {{ $pago->nome_cliente }}
                        </td>
                        <td class="p-5 text-xs font-medium uppercase text-slate-300">
                            {{ $pago->tipoIngresso->evento->titulo ?? 'Evento não encontrado' }}
                        </td>
                        <td class="p-5 text-center">
                            <span class="inline-block bg-white/5 border border-white/10 font-bold text-slate-200 px-3 py-1 rounded-lg text-xs">
                                {{ $pago->quantidade }}
                            </span>
                        </td>
                        <td class="p-5 text-center font-bold text-emerald-400">
                            {{ number_format($pago->total, 0, ',', '.') }} Kz
                        </td>
                        <td class="p-5 text-right text-xs text-slate-400 font-mono">
                            {{ $pago->updated_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500 text-sm">
                            Nenhuma venda confirmada até o momento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection