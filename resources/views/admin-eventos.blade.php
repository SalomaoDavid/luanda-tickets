@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-gray-900 uppercase">🎭 Gestão de Conteúdo</h1>
            <p class="text-gray-500 font-medium">Lista de todos os eventos registados no sistema</p>
        </div>
        {{-- Procure este botão no topo da sua lista de eventos --}}
            <a href="{{ route('admin.eventos.criar') }}" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                <span>+</span> Novo Evento
            </a>
    </div>

    <div class="bg-white rounded-[32px] shadow-2xl overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 font-black text-xs uppercase text-gray-400">
                    <th class="p-6">Evento</th>
                    <th class="p-6">Data / Local</th>
                    <th class="p-6">Ingressos</th>
                    <th class="p-6">Estado</th>
                    <th class="p-6 text-right">Acções</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($eventos as $evento)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-6">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="w-12 h-12 rounded-lg object-cover bg-gray-200" onerror="this.src='https://placehold.co/100x100?text=Sem+Foto'">
                            <div class="font-bold text-gray-900 uppercase text-sm">{{ $evento->titulo }}</div>
                        </div>
                    </td>
                    <td class="p-6">
                        <div class="text-sm font-bold text-gray-700">{{ date('d/m/Y', strtotime($evento->data_evento)) }}</div>
                        <div class="text-xs text-gray-400">{{ $evento->localizacao }}</div>
                    </td>
                    <td class="p-6">
                        <div class="flex flex-wrap gap-1">
                            @foreach($evento->tiposIngresso as $tipo)
                                <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-bold">
                                    {{ $tipo->nome }}: {{ number_format($tipo->preco, 0, ',', '.') }} KZ
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="p-6">
                        @if($evento->status == 'publicado')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase border border-green-200">Visível</span>
                        @else
                            <span class="bg-gray-100 text-gray-400 px-3 py-1 rounded-full text-[10px] font-black uppercase border border-gray-200">Rascunho</span>
                        @endif
                    </td>
                    <td class="p-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.eventos.editar', $evento->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.eventos.eliminar', $evento->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isto apagará todos os ingressos e reservas deste evento. Continuar?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Nenhum evento registado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection