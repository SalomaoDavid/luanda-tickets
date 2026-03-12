@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-black text-white uppercase italic">✅ Vendas Confirmadas</h1>
        <a href="{{ route('admin.reservas') }}" class="bg-white/10 text-slate-400 px-6 py-2 rounded-full font-bold hover:bg-white/20 transition text-xs uppercase tracking-widest">
            Voltar para Pendentes
        </a>
    </div>

    {{-- CARDS DE RESUMO FINANCEIRO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Card de Total --}}
        <div class="post-card p-6 border-l-4 border-sky-500">
            <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest">
                {{ auth()->user()->role === 'admin' ? 'Faturamento Global' : 'Meu Faturamento' }}
            </p>
            <h3 class="text-2xl font-black text-white mt-2">
                {{ number_format($pagamentos->sum('total'), 0, ',', '.') }} Kz
            </h3>
        </div>
        
        {{-- Card de Ingressos --}}
        <div class="post-card p-6 border-l-4 border-purple-500">
            <p class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Bilhetes Vendidos</p>
            <h3 class="text-2xl font-black text-white mt-2">{{ $pagamentos->count() }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-[32px] shadow-xl overflow-hidden border border-green-100">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-green-50 border-b border-green-100 font-black text-[10px] uppercase text-green-600">
                    <th class="p-6">Cliente</th>
                    <th class="p-6">Evento</th>
                    <th class="p-6 text-center">Quantidade</th>
                    <th class="p-6 text-center">Total Pago</th>
                    <th class="p-6 text-right">Data de Aprovação</th>
                </tr>
            </thead>
            <tbody>
    {{-- A variável enviada pelo Controller é $pagamentos --}}
            @foreach($pagamentos as $pago)
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                {{-- Aqui usamos $pago (singular) para pegar os dados de cada linha --}}
                <td class="p-6 font-bold text-gray-900">{{ $pago->nome_cliente }}</td>
                <td class="p-6 text-sm uppercase text-gray-600">
                    {{ $pago->tipoIngresso->evento->titulo ?? 'Evento não encontrado' }}
                </td>
                <td class="p-6 text-center font-black text-gray-900">{{ $pago->quantidade }}</td>
                <td class="p-6 text-center font-bold text-green-600">
                    {{ number_format($pago->total, 0, ',', '.') }} Kz
                </td>
                <td class="p-6 text-right text-xs text-gray-400 font-mono">
                    {{ $pago->updated_at->format('d/m/Y H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>
@endsection