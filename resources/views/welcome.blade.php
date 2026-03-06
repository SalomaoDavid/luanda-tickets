@extends('layouts.app')

@section('title', 'Luanda Tickets - Rede Social de Entretenimento')

@section('content')

<!-- Espaço de publicação -->
<div class="glass p-6 rounded-3xl shadow-xl">
    <form method="POST" action="{{ route('social.publicar') }}">
        @csrf
        <textarea name="conteudo" placeholder="O que estás a pensar?"
                  class="w-full bg-gray-800 p-4 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-yellow-400"
                  rows="3"></textarea>

        <div class="flex justify-between items-center mt-4">
            <div class="text-gray-400 text-sm">
                📷 Foto | 🎥 Vídeo | 🎟 Evento
            </div>
            <button type="submit" class="gold-bg text-black px-6 py-2 rounded-xl font-semibold hover:scale-105 transition">
                Publicar
            </button>
        </div>
    </form>
</div>

<!-- Stories / Eventos -->
<div class="flex space-x-6 overflow-x-auto scroll-hidden pb-4">
    @foreach($eventos->take(5) as $evento)
    <div class="text-center">
        <div class="w-24 h-24 rounded-full border-4 border-yellow-400 overflow-hidden shadow-lg">
            <img src="{{ $evento->imagem_capa ? asset('storage/' . $evento->imagem_capa) : 'https://picsum.photos/200' }}"
                 class="w-full h-full object-cover">
        </div>
        <p class="mt-2 text-sm truncate">{{ $evento->titulo }}</p>
    </div>
    @endforeach
</div>

<!-- Posts -->
@foreach($eventos as $evento)
<div class="glass p-8 rounded-3xl shadow-2xl">

    <div class="flex items-center space-x-4">
        <img src="{{ $evento->user && $evento->user->profile_photo_path ? asset('storage/' . $evento->user->profile_photo_path) : 'https://picsum.photos/50' }}"
             class="w-14 h-14 rounded-full border-2 border-yellow-400">

        <div>
            <h3 class="font-bold text-lg gold">
                {{ $evento->user ? $evento->user->name : 'Usuário' }}
            </h3>
            <p class="text-xs text-gray-400">
                {{ \Carbon\Carbon::parse($evento->data_evento)->diffForHumans() }}
            </p>
        </div>
    </div>

    <h2 class="mt-6 text-xl font-semibold">{{ $evento->titulo }}</h2>

    <p class="mt-4 text-gray-200 text-lg leading-relaxed">
        {{ $evento->descricao }}
    </p>

    @if($evento->imagem_capa)
        <img src="{{ asset('storage/' . $evento->imagem_capa) }}" class="mt-6 w-full rounded-2xl">
    @endif

    <div class="flex justify-between mt-8 text-gray-300 text-lg">
        <form method="POST" action="{{ route('evento.curtir', $evento->id) }}">
            @csrf
            <button type="submit" class="hover:text-yellow-400 transition duration-300">
                👍 Curtir ({{ $evento->curtidas->count() }})
            </button>
        </form>

        <button class="hover:text-yellow-400 transition duration-300">
            💬 Comentar
        </button>

        <a href="{{ route('evento.detalhes', $evento->id) }}"
           class="gold-bg text-black px-4 py-2 rounded-xl font-semibold hover:scale-105 transition">
            🎟 Comprar
        </a>
    </div>
</div>
@endforeach

@endsection