@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="mb-8">
        <a href="{{ route('admin.eventos') }}" class="text-blue-600 font-bold text-sm">← Voltar para a lista</a>
        <h1 class="text-3xl font-black text-gray-900 uppercase mt-2">Editar Evento</h1>
    </div>

    <form action="{{ route('admin.eventos.atualizar', $evento->id) }}" method="POST" class="bg-white p-8 rounded-[32px] shadow-xl border border-gray-100">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Título --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Título do Evento</label>
                <input type="text" name="titulo" value="{{ $evento->titulo }}" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none font-bold">
            </div>

            {{-- Localização --}}
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Localização</label>
                <input type="text" name="localizacao" value="{{ $evento->localizacao }}" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{-- Data --}}
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Data e Hora</label>
                <input type="datetime-local" name="data_evento" value="{{ date('Y-m-d\TH:i', strtotime($evento->data_evento)) }}" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{-- Lotação --}}
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Lotação Máxima</label>
                <input type="number" name="lotacao_maxima" value="{{ $evento->lotacao_maxima }}" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Status de Publicação</label>
                <select name="status" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-600">
                    <option value="publicado" {{ $evento->status == 'publicado' ? 'selected' : '' }}>Publicado (Visível)</option>
                    <option value="rascunho" {{ $evento->status == 'rascunho' ? 'selected' : '' }}>Rascunho (Oculto)</option>
                </select>
            </div>

            {{-- Descrição --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-black uppercase text-gray-400 mb-2">Descrição do Evento</label>
                <textarea name="descricao" rows="4" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none">{{ $evento->descricao }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black uppercase tracking-widest shadow-lg hover:bg-blue-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection