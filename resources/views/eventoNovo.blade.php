@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')

<style>
[x-cloak]{display:none!important;}
</style>

<div x-data="{ modalAberto: false, ingressoNome: '', ingressoPreco: 0, ingressoId: '', quantidade: 1 }"
     class="min-h-screen bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-10">

<div class="grid md:grid-cols-3 gap-10">

{{-- COLUNA ESQUERDA --}}
<div class="md:col-span-2 space-y-6">

{{-- GALERIA EVENTO --}}
<div class="grid grid-cols-4 gap-2 rounded-2xl overflow-hidden shadow-xl">

<div class="col-span-4">
<img src="{{ asset('storage/'.$evento->imagem_capa) }}"
class="w-full h-[420px] object-cover">
</div>

@foreach($evento->fotos->take(4) as $foto)
<img src="{{ asset('storage/'.$foto->caminho) }}"
class="w-full h-32 object-cover hover:scale-105 transition">
@endforeach

</div>

{{-- TITULO --}}
<div class="bg-white rounded-2xl shadow p-8">

<h1 class="text-3xl font-bold text-gray-900 mb-4">
{{ $evento->titulo }}
</h1>

<div class="flex flex-wrap gap-8 text-gray-600">

<div class="flex items-center gap-3">
<span class="text-xl">📅</span>
<span>{{ date('d/m/Y', strtotime($evento->data_evento)) }}</span>
</div>

{{-- HORA DO EVENTO --}}
<div class="flex items-center gap-3">
<span class="text-xl">⏰</span>
<span>{{ date('H:i', strtotime($evento->data_evento)) }}</span>
</div>

<div class="flex items-center gap-3">
<span class="text-xl">📍</span>
<span>{{ $evento->localizacao }}</span>
</div>

<div class="flex items-center gap-3">
<span class="text-xl">🏷</span>
<span>{{ $evento->categoria->nome ?? '' }}</span>
</div>

</div>

</div>

{{-- DESCRIÇÃO --}}
<div class="bg-white rounded-2xl shadow p-8">

<h3 class="text-xl font-bold mb-4">
Sobre o evento
</h3>

<p class="text-gray-600 leading-relaxed">
{!! nl2br(e($evento->descricao)) !!}
</p>

</div>

</div>


{{-- COLUNA DIREITA --}}
<div class="space-y-6">

{{-- CARD COMPRA --}}
<div class="bg-white rounded-2xl shadow-lg p-8 sticky top-10">

<h3 class="text-xl font-bold mb-6">
🎟 Bilhetes
</h3>

<div class="space-y-4">

@foreach($evento->tiposIngresso as $tipo)
<div class="border rounded-xl p-4 flex justify-between items-center">
    <div>
        <p class="font-semibold text-gray-800">{{ $tipo->nome }}</p>
        <p class="text-sm text-red-500 font-semibold">Restam {{ $tipo->quantidade_disponivel }} bilhetes</p>
    </div>

    <div class="text-right">
        <p class="font-bold text-lg text-gray-900">{{ number_format($tipo->preco,0,',','.') }} Kz</p>
        
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

</div>

</div>


{{-- MODAL CHECKOUT --}}
{{-- MODAL CHECKOUT --}}
<div
    x-show="modalAberto"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-lg"
>
    <div
        @click.away="modalAberto = false"
        x-transition.scale
        class="bg-white/90 backdrop-blur-xl rounded-2xl w-full max-w-md p-8 shadow-2xl"
    >

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                Finalizar compra
            </h3>

            <button
                @click="modalAberto = false"
                class="text-gray-500 hover:text-red-500 text-2xl"
            >
                ×
            </button>
        </div>

        <form action="{{ route('reserva.guardar') }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4"
        >
            @csrf

            <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
            <input type="hidden" name="evento_id" value="{{ $evento->id }}">

            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="font-semibold text-gray-800" x-text="ingressoNome"></p>
                <p class="text-xl font-bold text-blue-600"
                   x-text="(Number(ingressoPreco) * Number(quantidade)).toLocaleString() + ' Kz'">
                </p>
            </div>

            <input
                type="text"
                name="nome_cliente"
                placeholder="Seu nome"
                required
                class="w-full border rounded-lg p-3 focus:border-blue-500 outline-none"
            >

            <input
                type="text"
                name="whatsapp"
                placeholder="Seu WhatsApp"
                required
                class="w-full border rounded-lg p-3 focus:border-blue-500 outline-none"
            >

            <div class="flex items-center justify-between border rounded-lg p-3">
                <span class="text-gray-600">Quantidade</span>
                <input
                    type="number"
                    name="quantidade"
                    min="1"
                    x-model="quantidade"
                    class="w-16 border rounded p-2 text-center"
                >
            </div>

            <div>
                <label class="text-sm text-gray-600">
                    Comprovativo de pagamento
                </label>
                <input
                    type="file"
                    name="comprovativo"
                    required
                    class="w-full border rounded-lg p-3"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
            >
                Finalizar Compra
            </button>
        </form>

    </div>
</div>

@endsection