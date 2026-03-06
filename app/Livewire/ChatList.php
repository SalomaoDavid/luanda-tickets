<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{
    protected $listeners = ['startConversation'];
    public function render()
    {
        $query = Conversation::query()->with(['messages', 'users']);

        // Se não for admin, filtra apenas as conversas onde o usuário participa
        if (Auth::user()->role !== 'admin') {
            $query->whereHas('users', function($q) {
                $q->where('users.id', Auth::id());
            });
        }

        return view('livewire.chat-list', [
            'conversations' => $query->latest('updated_at')->get()
        ]);
    }

    public function selectConversation($id)
    {
        // Dispara um evento para o ChatBox carregar a conversa selecionada
        $this->dispatch('loadConversation', conversationId: $id);
    }

    public function startConversation($userId)
{
    $authId = auth()->id();

    // 1. Verifica se já existe uma conversa entre esses dois
    $conversation = Conversation::whereHas('users', function ($query) use ($authId) {
        $query->where('users.id', $authId);
    })->whereHas('users', function ($query) use ($userId) {
        $query->where('users.id', $userId);
    })->first();

    // 2. Se não existir, cria uma
    if (!$conversation) {
        $conversation = Conversation::create();
        $conversation->users()->attach([$authId, $userId]);
    }

    // 3. Avisa o app.blade.php para abrir o modal e trocar para a tela de chat
    $this->dispatch('loadConversation', conversation: $conversation->id);
    
    // Dispara um evento customizado para o Alpine abrir o modal
    $this->dispatch('open-chat-modal');
}
}