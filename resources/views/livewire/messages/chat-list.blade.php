<div class="flex flex-col h-full bg-white/30 backdrop-blur-xl">

<div class="p-4 border-b">

<h2 class="font-bold text-gray-600">
Mensagens
</h2>

</div>


<div class="flex-1 overflow-y-auto">

@forelse($conversations as $conv)

@php
$outroParticipante = $conv->users->where('id','!=',auth()->id())->first();
$ultimaMensagem = $conv->messages->last();
@endphp

<div
wire:click="selectConversation({{ $conv->id }})"
class="flex items-center gap-4 p-4 hover:bg-white/50 cursor-pointer transition">

<img
src="{{ $outroParticipante && $outroParticipante->avatar ? asset('storage/'.$outroParticipante->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($outroParticipante->name ?? 'Grupo') }}"
class="w-10 h-10 rounded-full object-cover">

<div class="flex-1">

<div class="flex justify-between">

<h3 class="font-semibold text-gray-700">
{{ $outroParticipante->name ?? 'Grupo' }}
</h3>

<span class="text-xs text-gray-400">

{{ $ultimaMensagem ? $ultimaMensagem->created_at->format('H:i') : '' }}

</span>

</div>


<p class="text-sm text-gray-500 truncate">

@if($ultimaMensagem)

{{ $ultimaMensagem->user_id == auth()->id() ? 'Tu: ' : '' }}

{{ $ultimaMensagem->body }}

@endif

</p>

</div>

</div>

@empty

<div class="flex items-center justify-center h-full text-gray-400">

Nenhuma conversa

</div>

@endforelse

</div>

</div>