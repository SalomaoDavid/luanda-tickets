<?php

namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagesIndex extends Component // Corrigido de Components para Component
{
    public $selectedConversation;
    public $searchUser = '';
    public $searchResults = [];

    protected $listeners = ['refresh' => '$refresh', 'refresh-list' => '$refresh'];

    public function mount($conversation = null)
    {
        if (request()->has('user_id')) {
            $this->startChat(request('user_id'));
            return;
        }

        if ($conversation) {
            $this->selectedConversation = Conversation::find($conversation);
        } else {
            // Pega a conversa que teve a mensagem mais recente
            $this->selectedConversation = auth()->user()->conversations()
                ->orderBy('updated_at', 'desc')
                ->first();
        }
    }

    public function updatedSearchUser($value)
    {
        if (strlen($value) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = User::where('name', 'like', '%' . $value . '%')
            ->where('id', '!=', auth()->id())
            ->take(5)
            ->get();
    }

    public function loadConversation($id)
    {
        // Verifica se a conversa pertence ao usuário logado
        $this->selectedConversation = Conversation::where('id', $id)
            ->where(function($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })->first();

        if ($this->selectedConversation) {
            $this->selectedConversation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $this->dispatch('scroll-down');
        }
    }

    public function toggleBlock($conversationId)
    {
        $conv = Conversation::find($conversationId);
        if ($conv) {
            // Inverte o status de bloqueio (requer coluna is_blocked na tabela conversations)
            $conv->update(['is_blocked' => !$conv->is_blocked]);
            $this->dispatch('refresh-list');
        }
    }

    public function startChat($userId)
    {
        $authId = auth()->id();

        $conversation = Conversation::where(function($query) use ($authId, $userId) {
            $query->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function($query) use ($authId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $authId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id' => $authId,
                'receiver_id' => $userId,
            ]);
        }

        $this->searchUser = '';
        $this->searchResults = [];
        $this->selectedConversation = $conversation;
        $this->dispatch('refresh-list');
    }

    public function render()
    {
        $userId = auth()->id();
        
        // Buscamos conversas com contagem de mensagens não lidas
        $conversations = Conversation::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->withCount(['messages as unread_count' => function($query) use ($userId) {
                $query->where('user_id', '!=', $userId)->whereNull('read_at');
            }])
            ->orderBy('updated_at', 'desc') // Garante que quem mandou mensagem agora suba para o topo
            ->get();

        return view('livewire.messages.messages-index', [
            'conversations' => $conversations
        ])->layout('layouts.app');
    }

        public function deleteConversation($id)
    {
        $conversation = Conversation::find($id);
        if ($conversation) {
            $conversation->messages()->delete(); 
            $conversation->delete();

            if ($this->selectedConversation && $this->selectedConversation->id == $id) {
                $this->selectedConversation = null;
                // Notifica o ChatBox para resetar o estado
                $this->dispatch('reset-chat'); 
            }
            
            // Atualiza a própria lista lateral
            $this->dispatch('refresh-list');
        }
    }
}