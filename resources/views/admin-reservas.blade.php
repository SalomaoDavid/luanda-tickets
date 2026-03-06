@extends('layouts.app')

@section('content')
{{-- Alpine.js para gerir o Modal de Visualização --}}
<div class="max-w-7xl mx-auto px-4 py-10" x-data="{ showModal: false, imgUrl: '', clienteNome: '' }">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase italic tracking-tighter">📋 Fila de Validação</h1>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">Gestão de Reservas Pendentes</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('admin.pagos') }}" class="bg-slate-900 text-white px-6 py-2 rounded-full font-bold shadow-lg hover:bg-sky-600 transition-all text-xs uppercase">
                Ver Confirmados ✅
            </a>
            <div class="bg-yellow-500 text-white px-6 py-2 rounded-full font-bold shadow-lg text-xs uppercase">
                {{ $reservas->count() }} Pendentes
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[32px] shadow-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 font-black text-xs uppercase text-gray-400">
                        <th class="p-6">Cliente</th>
                        <th class="p-6">Evento / Tipo</th>
                        <th class="p-6 text-center">Qtd</th>
                        <th class="p-6 text-center">Total</th>
                        <th class="p-6 text-center">Pagamento</th>
                        <th class="p-6 text-right">Acções</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Coluna Cliente --}}
                        <td class="p-6">
                            <div class="font-bold text-gray-900">{{ $reserva->nome_cliente }}</div>
                            <div class="text-[11px] text-sky-600 font-mono">{{ $reserva->whatsapp }}</div>
                        </td>

                        {{-- Coluna Evento --}}
                        <td class="p-6">
                            <div class="font-bold text-gray-700 uppercase text-xs">
                                {{ $reserva->tipoIngresso->evento->titulo ?? 'Evento N/D' }}
                            </div>
                            <div class="text-[10px] text-purple-600 font-black uppercase tracking-widest">
                                {{ $reserva->tipoIngresso->nome ?? 'Tipo N/D' }}
                            </div>
                        </td>

                        {{-- Coluna Qtd --}}
                        <td class="p-6 text-center font-black text-gray-900">{{ $reserva->quantidade }}</td>

                        {{-- Coluna Total --}}
                        <td class="p-6 text-center font-bold text-blue-700 bg-blue-50/30">
                            {{ number_format($reserva->total, 0, ',', '.') }} Kz
                        </td>
                        
                        {{-- Coluna Comprovativo (Botão que abre o Modal) --}}
                        <td class="p-6 text-center">
                            @if($reserva->comprovativo)
                                <button @click="showModal = true; imgUrl = '{{ asset('storage/' . $reserva->comprovativo) }}'; clienteNome = '{{ $reserva->nome_cliente }}'" 
                                        class="bg-sky-50 text-sky-600 px-4 py-2 rounded-xl border border-sky-100 hover:bg-sky-600 hover:text-white transition-all font-black text-[10px] uppercase tracking-tighter">
                                    Ver Comprovativo 🖼️
                                </button>
                            @else
                                <span class="text-red-400 text-[10px] font-black uppercase italic">Sem Ficheiro</span>
                            @endif
                        </td>

                        {{-- Coluna Acções --}}
                        <td class="p-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                {{-- Botão Confirmar (Move para a lista de pagos) --}}
                                <form action="{{ route('reservas.confirmar', $reserva->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-xl hover:bg-green-700 transition-all font-black text-[10px] uppercase shadow-md active:scale-95">
                                        Confirmar
                                    </button>
                                </form>

                                {{-- Botão Eliminar --}}
                                <form action="{{ route('reservas.eliminar', $reserva->id) }}" method="POST" onsubmit="return confirm('Apagar esta reserva definitivamente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-600 transition-colors p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-20 text-center text-gray-300 font-black uppercase tracking-widest italic text-xs">
                            Nenhuma reserva pendente de validação
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL DE VISUALIZAÇÃO DO COMPROVATIVO --}}
    <div x-show="showModal" 
         x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100">
        
        <div class="bg-white rounded-[40px] max-w-2xl w-full p-2 shadow-2xl overflow-hidden border border-white/20" @click.away="showModal = false">
            
            {{-- Cabeçalho do Modal --}}
            <div class="flex justify-between items-center p-6">
                <div>
                    <h3 class="font-black uppercase text-gray-900 text-[10px] tracking-[0.3em] italic">Validar_Pagamento.sys</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase" x-text="'Cliente: ' + clienteNome"></p>
                </div>
                <button @click="showModal = false" class="text-gray-400 hover:text-red-500 transition-colors text-2xl font-light">&times;</button>
            </div>

            {{-- Imagem do Comprovativo (Lógica igual à Welcome) --}}
            <div class="rounded-[30px] overflow-hidden bg-gray-100 flex justify-center mx-2 mb-2 border border-gray-200">
                <img :src="imgUrl" 
                     class="max-h-[70vh] w-auto object-contain shadow-inner"
                     onerror="this.src='https://placehold.co/600x800?text=Erro+ao+Carregar+Imagem'">
            </div>

            {{-- Rodapé do Modal --}}
            <div class="p-6 flex gap-3">
                <button @click="showModal = false" class="flex-1 bg-gray-100 text-gray-600 font-black py-4 rounded-2xl uppercase tracking-widest text-[10px] hover:bg-gray-200 transition-all">
                    Fechar
                </button>
                <a :href="imgUrl" target="_blank" class="flex-1 bg-slate-900 text-white font-black py-4 rounded-2xl uppercase tracking-widest text-[10px] text-center hover:bg-sky-600 transition-all shadow-xl">
                    Abrir Original
                </a>
            </div>
        </div>
    </div>
</div>

<style> 
    [x-cloak] { display: none !important; } 
    /* Scrollbar personalizada para a tabela se for muito larga */
    .overflow-x-auto::-webkit-scrollbar { height: 4px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
@endsection