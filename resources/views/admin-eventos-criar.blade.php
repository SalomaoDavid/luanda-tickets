@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- Header Estruturado --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <a href="{{ route('admin.eventos') }}" class="group flex items-center text-slate-400 hover:text-sky-500 transition-all font-bold text-xs uppercase tracking-[0.2em]">
                    <span class="mr-2 transform group-hover:-translate-x-1 transition-transform">←</span> Voltar ao Painel
                </a>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter mt-2">
                    Novo <span class="text-sky-500 italic">Evento</span>
                </h1>
            </div>
            <div class="hidden md:block">
                <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Modo de Edição</p>
                    <p class="text-sky-500 font-bold italic">Administrador</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.eventos.guardar') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- 1. Informações Básicas --}}
            <div class="bg-white p-8 md:p-10 rounded-[40px] shadow-xl shadow-slate-200/50 border border-white">
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-white font-bold text-sm">01</span>
                    <h3 class="text-sm font-black uppercase text-slate-900 tracking-widest italic">Informações Gerais</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Título do Evento</label>
                        <input type="text" name="titulo" required placeholder="Ex: Grande Show da Virada" 
                               class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl p-5 focus:bg-white focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 outline-none transition-all font-bold text-slate-800 placeholder:text-slate-300">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Categoria</label>
                        <select name="categoria_id" required class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl p-5 focus:bg-white focus:border-sky-500 outline-none transition-all font-bold text-slate-600 appearance-none">
                            <option value="">Selecione...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Estado de Publicação</label>
                        <select name="status" class="w-full bg-sky-50 border-2 border-sky-50 rounded-2xl p-5 focus:bg-white focus:border-sky-500 outline-none transition-all font-bold text-sky-600 appearance-none">
                            <option value="publicado">Publicado (Visível no site)</option>
                            <option value="rascunho">Rascunho (Privado)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2. Ingressos (Area Acesa) --}}
            <div class="bg-white p-8 md:p-10 rounded-[40px] shadow-xl shadow-slate-200/50 border border-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-sky-500/5 rounded-full -mr-16 -mt-16"></div>
                
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-white font-bold text-sm">02</span>
                    <h3 class="text-sm font-black uppercase text-slate-900 tracking-widest italic">Preçário e Bilheteira</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Normal --}}
                    <div class="p-6 rounded-[32px] bg-slate-50 border border-slate-100 group hover:border-sky-200 transition-colors">
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-4 tracking-tighter italic">Setor Normal</p>
                        <div class="flex gap-3">
                            <div class="w-1/2">
                                <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Preço (Kz)</label>
                                <input type="number" name="preco_normal" placeholder="0.00" class="w-full bg-white rounded-xl p-4 font-black text-slate-700 outline-none border border-transparent focus:border-sky-500">
                            </div>
                            <div class="w-1/2">
                                <label class="text-[9px] font-bold text-slate-400 block mb-1 uppercase">Quantidade</label>
                                <input type="number" name="qtd_normal" placeholder="0" class="w-full bg-white rounded-xl p-4 font-black text-slate-700 outline-none border border-transparent focus:border-sky-500">
                            </div>
                        </div>
                    </div>

                    {{-- VIP --}}
                    <div class="p-6 rounded-[32px] bg-sky-500/[0.03] border border-sky-100 group hover:border-sky-300 transition-colors">
                        <p class="text-[10px] font-black uppercase text-sky-500 mb-4 tracking-tighter italic">Setor VIP</p>
                        <div class="flex gap-3">
                            <div class="w-1/2">
                                <label class="text-[9px] font-bold text-sky-400 block mb-1 uppercase">Preço (Kz)</label>
                                <input type="number" name="preco_vip" placeholder="0.00" class="w-full bg-white rounded-xl p-4 font-black text-slate-700 outline-none border border-transparent focus:border-sky-500">
                            </div>
                            <div class="w-1/2">
                                <label class="text-[9px] font-bold text-sky-400 block mb-1 uppercase">Quantidade</label>
                                <input type="number" name="qtd_vip" placeholder="0" class="w-full bg-white rounded-xl p-4 font-black text-slate-700 outline-none border border-transparent focus:border-sky-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Logística e Média --}}
            <div class="bg-white p-8 md:p-10 rounded-[40px] shadow-xl shadow-slate-200/50 border border-white">
                <div class="flex items-center gap-3 mb-8">
                    <span class="w-8 h-8 rounded-lg bg-sky-500 flex items-center justify-center text-white font-bold text-sm">03</span>
                    <h3 class="text-sm font-black uppercase text-slate-900 tracking-widest italic">Local e Fotos</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Localização</label>
                        <input type="text" name="localizacao" required placeholder="Ex: Multiusos do Kilamba" class="w-full bg-slate-50 border-none rounded-2xl p-5 outline-none font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Data do Evento</label>
                        <input type="datetime-local" name="data_evento" required class="w-full bg-slate-50 border-none rounded-2xl p-5 outline-none font-bold text-slate-600">
                    </div>
                    
                    {{-- UPLOAD DE IMAGENS --}}
                    <div class="p-6 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest text-center">Imagem de Capa (Principal)</label>
                        <input type="file" name="imagem_capa" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-sky-500 file:text-white hover:file:bg-slate-900">
                    </div>

                    <div class="p-6 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest text-center">Galeria Extras (Ken Burns)</label>
                        <input type="file" name="galeria[]" multiple class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-slate-900 file:text-white hover:file:bg-sky-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 ml-1 tracking-widest">Descrição</label>
                        <textarea name="descricao" rows="5" placeholder="Escreva o line-up, regras e atrações..." class="w-full bg-slate-50 border-none rounded-3xl p-6 outline-none font-medium text-slate-600"></textarea>
                        <input type="hidden" name="lotacao_maxima" value="1000"> {{-- Valor padrão para não quebrar --}}
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="group relative bg-slate-900 text-white px-12 py-6 rounded-3xl font-black uppercase tracking-[0.2em] shadow-2xl hover:bg-sky-500 transition-all duration-500">
                    Publicar Evento
                    <span class="absolute -top-2 -right-2 bg-sky-400 text-[8px] px-2 py-1 rounded-lg animate-bounce">LIVE</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Estilo para remover as setas do input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.5rem center;
        background-size: 1.5em;
    }
</style>
@endsection