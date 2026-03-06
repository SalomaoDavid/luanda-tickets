@extends('layouts.app')

@section('title', $noticia->titulo)

@section('content')
<article class="max-w-4xl mx-auto px-4 py-12">
    
    <a href="{{ route('noticias.index') }}" class="inline-flex items-center text-blue-600 font-bold mb-8 hover:gap-2 transition-all">
        ← Voltar para Notícias
    </a>

    <header class="mb-8">
        <span class="bg-blue-100 text-blue-700 text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full">
            {{ $noticia->fonte ?? 'Entretenimento' }}
        </span>
        
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 mt-4 leading-tight">
            {{ $noticia->titulo }}
        </h1>

        <div class="flex items-center gap-4 mt-6 text-gray-500 text-sm">
            <span class="flex items-center gap-1">📅 {{ date('d/m/Y', strtotime($noticia->publicado_em)) }}</span>
            <span class="flex items-center gap-1">👤 Redação Luanda Tickets</span>
        </div>
    </header>

    <div class="mb-12 rounded-[32px] overflow-hidden shadow-2xl bg-gray-100">
        @if($noticia->imagem_destaque)
            <img src="{{ $noticia->imagem_destaque }}" 
                 alt="{{ $noticia->titulo }}" 
                 class="w-full h-[400px] md:h-[500px] object-cover">
        @else
            <div class="w-full h-64 flex items-center justify-center text-gray-400">
                Sem imagem disponível
            </div>
        @endif
    </div>

    <div class="prose prose-blue prose-lg max-w-none text-gray-700 leading-relaxed">
        {{-- O {!! !!} permite que o HTML vindo do RSS (negritos, parágrafos) funcione --}}
        {!! $noticia->conteudo !!}
    </div>

    <footer class="mt-16 pt-8 border-t border-gray-100">
        <div class="bg-blue-50 p-8 rounded-3xl flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h4 class="font-bold text-blue-900 text-lg">Gostou desta novidade?</h4>
                <p class="text-blue-700">Partilha com os teus amigos e não percas nenhum evento!</p>
            </div>
            <div class="flex gap-4">
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition">Partilhar</button>
            </div>
        </div>
    </footer>

</article>

<style>
    /* Ajuste para imagens que venham dentro do conteúdo do RSS */
    .prose img {
        border-radius: 1.5rem;
        margin-left: auto;
        margin-right: auto;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection