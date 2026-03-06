<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-200 via-purple-200 to-pink-200 p-8">

<div class="w-[1000px] h-[700px] bg-white/40 backdrop-blur-xl rounded-[40px] shadow-2xl flex overflow-hidden">

{{-- SIDEBAR AVATARES --}}
<aside class="w-[90px] bg-white/30 backdrop-blur-xl flex flex-col items-center py-6 gap-6 overflow-y-auto">

@foreach($conversations as $conv)

@php 
$receiver = $conv->getReceiver();
$avatar = $receiver->avatar
? asset('storage/'.$receiver->avatar)
: "https://ui-avatars.com/api/?name=".urlencode($receiver->name);
@endphp

<div class="cursor-pointer" wire:click="loadConversation({{ $conv->id }})">

<img
src="{{ $avatar }}"
class="w-12 h-12 rounded-full shadow border-2 border-white hover:scale-110 transition">

</div>

@endforeach

</aside>


{{-- ÁREA PRINCIPAL --}}
<main class="flex-1 flex flex-col">

@if($selectedConversation)

@livewire(
'messages.chat-box',
['conversation'=>$selectedConversation],
key($selectedConversation->id)
)

@else

<div class="flex-1 flex items-center justify-center text-gray-400 text-lg">
Selecione uma conversa
</div>

@endif

</main>

</div>

</div>