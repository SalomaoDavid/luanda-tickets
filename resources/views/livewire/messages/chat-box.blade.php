<div class="flex flex-col h-full">

@php $receiver = $conversation->getReceiver(); @endphp

{{-- HEADER --}}
<header class="flex items-center justify-between px-6 py-4 border-b bg-white/50 backdrop-blur-lg">

<div class="flex items-center gap-4">

<img
src="{{ $receiver->avatar ? asset('storage/'.$receiver->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($receiver->name) }}"
class="w-10 h-10 rounded-full object-cover">

<div>
<h3 class="font-semibold text-gray-700">
{{ $receiver->name }}
</h3>

<p class="text-xs text-green-500">
Online
</p>
</div>

</div>


<div class="flex gap-4 text-gray-500 text-xl">

<button>📩</button>
<button>📞</button>

</div>

</header>


{{-- MENSAGENS --}}
<div
id="chat-content"
class="flex-1 overflow-y-auto p-6 space-y-6">

@foreach($messages as $msg)

@php $isMine = $msg->user_id === auth()->id(); @endphp

<div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">

<div class="max-w-[60%]">

<div
class="px-5 py-3 rounded-3xl shadow

{{ $isMine
? 'bg-gradient-to-r from-indigo-400 to-blue-500 text-white rounded-br-md'
: 'bg-white text-gray-700 rounded-bl-md'
}}">

{{ $msg->body }}

</div>

<div class="text-xs text-gray-400 mt-1">

{{ $msg->created_at->format('H:i') }}

</div>

</div>

</div>

@endforeach

</div>


{{-- INPUT --}}
<footer class="p-5 border-t bg-white/50 backdrop-blur-lg">

<form
wire:submit.prevent="sendMessage"
class="flex items-center gap-4 bg-white rounded-full px-6 py-3 shadow">

<input
type="text"
wire:model="body"
placeholder="Type a message..."
class="flex-1 outline-none text-gray-600">

<button
type="submit"
class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center">

➤

</button>

</form>

</footer>


<script>
window.addEventListener('scroll-down', () => {

const container = document.getElementById('chat-content');

if(container){
container.scrollTop = container.scrollHeight;
}

});
</script>

</div>