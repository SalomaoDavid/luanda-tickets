<?php

namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagesIndex extends Component 
{
    public $selectedConversation;
    public $selectedConversationId; 
    public $searchUser = '';
    public $searchResults = [];
    public $forcarMostrarChat = false;

    protected $listeners = [
        'refresh' => '$refresh',
        'refresh-list' => '$refresh',
        'loadConversation' => 'onChatListSelected',
        'delete-conversation-request' => 'onChatListDeleteRequested',
        // ✅ NOVO — o ChatBox avisa aqui quando uma conversa que só existia
        // em memória (ver startChat) é finalmente gravada na BD.
        'conversation-created' => 'onConversationCreated',
    ];

    public function mount($conversation = null)
    {
        $urlUserId = $_GET['user_id'] ?? null;
        $urlEventoId = $_GET['evento_id'] ?? null;

        if ($urlUserId && $urlUserId != auth()->id()) {
            $this->startChat($urlUserId, $urlEventoId);

            if ($this->selectedConversation) {
                $this->forcarMostrarChat = true; // veio de um link direto — é intencional
                return;
            }
        }

        if ($conversation) {
            $c = \App\Models\Conversation::find($conversation);
            $this->selectedConversation = ($c && $c->temParticipante(auth()->id())) ? $c : null;
            $this->forcarMostrarChat = true;
        } else {
            $this->selectedConversation = auth()->user()->conversations()
                ->orderBy('updated_at', 'desc')
                ->first();
        }

        if ($this->selectedConversation) {
            $this->selectedConversationId = $this->selectedConversation->id;
        }
    }

    public function updatedSelectedConversationId($value)
    {
        if (!$value) {
            $this->selectedConversation = null;
        }
    }

    public function updatedSearchUser($value)
    {
        if (strlen($value) < 2) {
            $this->searchResults = [];
            return;
        }

        $authId = auth()->id();

        $this->searchResults = User::where('name', 'like', '%' . $value . '%')
            ->where('id', '!=', $authId)
            ->where(function ($q) use ($authId) {
                $q->whereIn('id', function ($sub) use ($authId) {
                    $sub->select('seguido_id')->from('seguidores')->where('seguidor_id', $authId);
                })->orWhereIn('id', function ($sub) use ($authId) {
                    $sub->select('seguidor_id')->from('seguidores')->where('seguido_id', $authId);
                });
            })
            ->take(5)
            ->get();
    }

    public function selectConversation($id)
    {
        $this->loadConversation($id);
    }

    public function loadConversation($id)
    {
        $this->selectedConversation = Conversation::where('id', $id)
            ->where(function($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })->first();

        if ($this->selectedConversation) {
            $this->selectedConversationId = $this->selectedConversation->id;

            $this->selectedConversation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $this->dispatch('scroll-down');
        }
    }

    public function startChat($userId, $eventoId = null)
    {
        $authId = auth()->id();
        $authUser = auth()->user();

        $tipoDefinido = 'pessoal'; 

        if ($eventoId) {
            $tipoDefinido = 'evento'; 
        } elseif ($authUser->role === 'admin') {
            $tipoDefinido = 'aviso_admin'; 
        }

        // Busca a conversa existente
        $conversation = \App\Models\Conversation::when(
                $eventoId,
                fn ($q) => $q->where('evento_id', $eventoId),
                fn ($q) => $q->whereNull('evento_id')
            )
            ->where(function($q) use ($authId, $userId) {
                $q->where(function($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $authId)->where('receiver_id', $userId);
                })->orWhere(function($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $userId)->where('receiver_id', $authId);
                });
            })->first();

        if ($conversation) {
            // ✅ Já existe (com ou sem mensagens ainda) — reaproveita sempre.
            $this->selectedConversation = $conversation;
            $this->selectedConversationId = $conversation->id;
            return;
        }

        // ✅ NOVO — já não grava nada na BD aqui. Só monta o objeto em
        // memória; só é persistido a sério quando a 1ª mensagem for
        // enviada (ver ChatBox::sendMessage). Isto elimina as "conversas
        // fantasma" vazias que ficavam por criar quando alguém abria o
        // chat e desistia sem escrever nada.
        $evento = \App\Models\Evento::find($eventoId);

        $conversation = new \App\Models\Conversation([
            'sender_id'   => $authId,
            'receiver_id' => $userId,
            'evento_id'   => $eventoId,
            'titulo'      => $evento ? "Interesse: " . $evento->titulo : "Conversa sobre Evento",
            'tipo'        => $tipoDefinido,
        ]);

        $this->selectedConversation = $conversation;
        // Chave sintética (a conversa ainda não tem ID real) — muda sempre
        // que se tenta falar com uma pessoa/evento diferente, para o
        // wire:key do ChatBox forçar o remount correto.
        $this->selectedConversationId = 'novo-' . $userId . '-' . ($eventoId ?? '0');
    }

    public function render()
    {
        $userId = auth()->id();
        
        $conversations = Conversation::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->withCount(['messages as unread_count' => function($query) use ($userId) {
                $query->where('user_id', '!=', $userId)->whereNull('read_at');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livewire.messages.messages-index', [
            'conversations' => $conversations
        ])->layout('layouts.app');
    }

    public function deleteConversation($id)
    {
        $conversation = Conversation::find($id);
        if ($conversation && $conversation->temParticipante(auth()->id())) {
            $conversation->messages()->delete(); 
            $conversation->delete();

            if ($this->selectedConversation && $this->selectedConversation->id == $id) {
                $this->selectedConversation = null;
                $this->selectedConversationId = null;
                $this->dispatch('reset-chat'); 
            }
            
            $this->dispatch('refresh-list');
        }
    }

    public function onChatListSelected($conversationId)
    {
        $this->forcarMostrarChat = true;
        $this->loadConversation($conversationId);
    }

    public function onlineUserClicked($userId)
    {
        $this->forcarMostrarChat = true;
        $this->startChat($userId);
    }

    public function onChatListDeleteRequested($id)
    {
        $this->deleteConversation($id);
    }

    // ✅ NOVO — o ChatBox chama isto quando persiste, a sério, uma conversa
    // que até aí só existia em memória (1ª mensagem confirmada).
    public function onConversationCreated($conversationId)
    {
        $this->selectedConversationId = $conversationId;
    }
}