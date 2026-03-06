@extends('layouts.app')

@section('title', 'Todos os Eventos')

@section('content')

<div class="px-6">

    {{-- TÍTULO --}}
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-white">
            Explorar <span class="text-sky-400">Eventos</span>
        </h1>
    </div>

    @if($eventos->isEmpty())

        <div class="bg-slate-900/40 border border-white/10 rounded-3xl p-20 text-center">
            <p class="text-gray-400 text-xl">Nenhum evento encontrado.</p>
        </div>

    @else

    {{-- GRID 4 COLUNAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        @foreach($eventos as $evento)

        @php
            $categoria = optional($evento->categoria)->nome ?? 'Evento';

            $badgeColor = match(strtolower($categoria)) {
                'festa' => 'bg-orange-500',
                'viagem' => 'bg-blue-500',
                'show' => 'bg-purple-500',
                'conferência' => 'bg-emerald-500',
                default => 'bg-sky-500'
            };

            $precoMinimo = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
        @endphp

        <div class="rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300">

            {{-- IMAGEM RETANGULAR --}}
            <div class="relative h-44">

                <img src="{{ asset('storage/' . $evento->imagem_capa) }}"
                     class="w-full h-full object-cover">

                {{-- BADGE CATEGORIA --}}
                <div class="absolute top-3 left-3 {{ $badgeColor }} text-white text-xs px-3 py-1 rounded-full font-semibold">
                    {{ ucfirst($categoria) }}
                </div>

                {{-- PREÇO SOBRE IMAGEM --}}
                <div class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-md text-white text-sm px-3 py-1 rounded-lg font-bold">
                    Kz {{ number_format($precoMinimo, 0, ',', '.') }}
                </div>

                {{-- BOTÕES LIKE/DISLIKE --}}
                <div class="absolute top-3 right-3 flex gap-2">

                    <form action="{{ route('evento.curtir', $evento->id) }}" method="POST">
                        @csrf
                        <button class="w-8 h-8 rounded-full bg-black/60 text-white flex items-center justify-center hover:scale-110 transition">
                            ❤️
                        </button>
                    </form>

                    <form action="{{ route('evento.dislike', $evento->id) }}" method="POST">
                        @csrf
                        <button class="w-8 h-8 rounded-full bg-black/60 text-white flex items-center justify-center hover:scale-110 transition">
                            👎
                        </button>
                    </form>

                </div>

            </div>

            {{-- PARTE BRANCA --}}
            <div class="bg-white p-5">

                <h2 class="text-gray-800 font-bold text-lg mb-2 truncate">
                    {{ $evento->titulo }}
                </h2>

                <p class="text-gray-500 text-sm mb-4 truncate">
                    {{ date('d M Y', strtotime($evento->data_evento)) }}
                    • {{ $evento->localizacao }}
                </p>

                <div class="flex items-center justify-between">

                    <span class="text-xs text-gray-500">
                        {{ $evento->curtidas->count() }} Curtidas
                    </span>

                    <a href="{{ route('evento.detalhes', $evento->id) }}"
                       class="bg-sky-500 hover:bg-sky-600 text-white text-sm px-4 py-2 rounded-lg font-semibold transition">
                        Comprar
                    </a>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    @endif

</div>

@endsection