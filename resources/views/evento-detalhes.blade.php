@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')

<style>
[x-cloak]{display:none!important;}
</style>

<div x-data="{ modalAberto: false, ingressoNome: '', ingressoPreco: 0, ingressoId: '', quantidade: 1 }" class="min-h-screen">
    
    <div class="grid md:grid-cols-3 gap-10">
        {{-- COLUNA ESQUERDA --}}
        <div class="md:col-span-2 space-y-6">
            <div class="grid grid-cols-4 gap-2 rounded-2xl overflow-hidden shadow-xl">
                <div class="col-span-4">
                    <img src="{{ asset('storage/'.$evento->imagem_capa) }}" class="w-full h-[420px] object-cover">
                </div>
                @foreach($evento->fotos->take(4) as $foto)
                    <img src="{{ asset('storage/'.$foto->caminho) }}" class="w-full h-32 object-cover">
                @endforeach
            </div>

            <div class="bg-white rounded-2xl shadow p-8 text-gray-900">
                <h1 class="text-3xl font-bold mb-4">{{ $evento->titulo }}</h1>
                <div class="flex flex-wrap gap-6 text-gray-600">
                    <span>📅 {{ date('d/m/Y', strtotime($evento->data_evento)) }}</span>
                    <span>⏰ {{ date('H:i', strtotime($evento->data_evento)) }}</span>
                    <span>📍 {{ $evento->localizacao }}</span>
                    <a href="{{ route('mensagens.index', ['user_id' => $evento->user_id, 'evento_id' => $evento->id]) }}" 
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-full transition-all shadow-md group">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        
                        <span class="text-[11px] font-black uppercase tracking-tight"></span>
                    </a>
                      
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow p-8 text-gray-900">
                <h3 class="text-xl font-bold mb-4">Sobre o evento</h3>
                <p class="leading-relaxed text-gray-600">{!! nl2br(e($evento->descricao)) !!}</p>
            </div>
        </div>

        {{-- COLUNA DIREITA --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg p-8 sticky top-24 text-gray-900">
                <h3 class="text-xl font-bold mb-6">🎟 Bilhetes</h3>
                <div class="space-y-4">
                    @foreach($evento->tiposIngresso as $tipo)
                    <div class="border rounded-xl p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $tipo->nome }}</p>
                            <p class="text-xs text-red-500 font-bold">Restam {{ $tipo->quantidade_disponivel }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold">{{ number_format($tipo->preco,0,',','.') }} Kz</p>
                            <button 
                                @click="modalAberto = true; ingressoNome = '{{ $tipo->nome }}'; ingressoPreco = {{ $tipo->preco }}; ingressoId = '{{ $tipo->id }}'"
                                class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Comprar
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="modalAberto" x-cloak class="fixed inset-0 z-[999] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="modalAberto = false"></div>
        
        <div x-show="modalAberto" x-transition class="relative bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl text-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Finalizar compra</h3>
                <button @click="modalAberto = false" class="text-gray-400 hover:text-red-500 text-3xl">&times;</button>
            </div>

            <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="font-bold" x-text="ingressoNome"></p>
                    <p class="text-xl text-blue-600 font-black" x-text="(Number(ingressoPreco) * Number(quantidade)).toLocaleString() + ' Kz'"></p>
                </div>

                <input type="text" name="nome_cliente" placeholder="Seu nome" required class="w-full border rounded-lg p-3">
                <input type="text" name="whatsapp" placeholder="WhatsApp" required class="w-full border rounded-lg p-3">

                <div class="flex items-center justify-between border rounded-lg p-3">
                    <span>Qtd</span>
                    <input type="number" name="quantidade" min="1" x-model="quantidade" class="w-16 border rounded p-1 text-center font-bold">
                </div>

                <div class="text-sm">
                    <label class="text-gray-500">Comprovativo (Foto/PDF)</label>
                    <input type="file" name="comprovativo" required class="w-full mt-1">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Confirmar Compra</button>
            </form>
        </div>
    </div>
</div>
@endsection