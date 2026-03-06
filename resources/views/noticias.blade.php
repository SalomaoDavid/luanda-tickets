@extends('layouts.app')

@section('title', 'Notícias de Entretenimento')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-2xl shadow-sm mb-8 border border-gray-100">
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900">Novidades de Luanda</h1>
            <p class="text-gray-500">Notícias atualizadas automaticamente via RSS</p>
        </div>
        
        <a href="{{ route('noticias.sincronizar') }}" 
           class="mt-4 md:mt-0 bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-200">
            <span>🔄</span> Sincronizar Notícias
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($noticias as $item)
            {{-- O ARTICLE é o seu retângulo principal. Tudo deve estar dentro dele --}}
            <article class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition flex flex-col">
                
                {{-- 1. A imagem agora está DENTRO do article, no topo --}}
                <div class="h-48 overflow-hidden">
                    <img src="{{ $item->imagem_destaque ?? 'https://via.placeholder.com/400x250?text=Sem+Imagem' }}" 
                         class="w-full h-full object-cover">
                </div>

                {{-- 2. O conteúdo textual --}}
                <div class="p-6 flex flex-col flex-grow">
                    <span class="text-blue-600 text-xs font-black uppercase tracking-widest">{{ $item->fonte }}</span>
                    
                    <h2 class="text-xl font-bold mt-2 leading-tight text-gray-900">
                        {{ $item->titulo }}
                    </h2>

                    {{-- Resumo da notícia (apenas um, para não repetir) --}}
                    <p class="text-gray-600 text-sm mt-3 line-clamp-3">
                        {{ Str::limit(strip_tags($item->conteudo), 120) }}
                    </p>

                    {{-- Rodapé do cartão --}}
                    <div class="mt-auto pt-6 flex justify-between items-center">
                        <span class="text-gray-400 text-xs">{{ date('d/m/Y', strtotime($item->publicado_em)) }}</span>
                        <a href="{{ route('noticias.detalhes', $item->slug) }}" class="text-blue-600 font-bold text-sm hover:underline">
                            Ler mais →
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-20">
                <div class="text-6xl mb-4">📭</div>
                <p class="text-gray-500">Ainda não há notícias rastreadas. Clique no botão azul acima para buscar!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $noticias->links() }}
    </div>
</div>
@endsection