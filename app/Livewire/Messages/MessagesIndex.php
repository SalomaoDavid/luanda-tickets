<?php

namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagesIndex extends Component 
{
    public $selectedConversation;
    public $searchUser = '';
    public $searchResults = [];

    protected $listeners = ['refresh' => '$refresh', 'refresh-list' => '$refresh'];

            
    public function mount($conversation = null)
{
    // Bypass total: Lendo direto da URL do navegador
    $urlUserId = $_GET['user_id'] ?? null;
    $urlEventoId = $_GET['evento_id'] ?? null;

    if ($urlUserId && $urlUserId != auth()->id()) {
        // Chamamos o startChat e forçamos a execução
        $this->startChat($urlUserId, $urlEventoId);
        
        // Se a conversa foi preenchida, não fazemos mais nada
        if ($this->selectedConversation) {
            return;
        }
    }

    // Se o bypass falhar ou não houver parâmetros, segue a vida normal
    if ($conversation) {
        $this->selectedConversation = \App\Models\Conversation::find($conversation);
    } else {
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

    
            public function startChat($userId, $eventoId = null)
        {
            $authId = auth()->id();
            $authUser = auth()->user();

            // --- LÓGICA DE DECISÃO PRIORIZANDO O EVENTO ---
            $tipoDefinido = 'pessoal'; 

            if ($eventoId) {
                // Se existe um ID de evento, o contexto É o evento (Venda/Interesse)
                $tipoDefinido = 'evento'; 
            } elseif ($authUser->role === 'admin') {
                // Se não for evento e for admin, é um aviso oficial
                $tipoDefinido = 'aviso_admin'; 
            }

            // 2. Busca a conversa existente
            $conversation = \App\Models\Conversation::where('evento_id', $eventoId)
                ->where(function($q) use ($authId, $userId) {
                    $q->where(function($inner) use ($authId, $userId) {
                        $inner->where('sender_id', $authId)->where('receiver_id', $userId);
                    })->orWhere(function($inner) use ($authId, $userId) {
                        $inner->where('sender_id', $userId)->where('receiver_id', $authId);
                    });
                })->first();

            // 3. Criação com o título específico de "Evento"
            if (!$conversation) {
                $evento = \App\Models\Evento::find($eventoId);
                
                $conversation = \App\Models\Conversation::create([
                    'sender_id'   => $authId,
                    'receiver_id' => $userId,
                    'evento_id'   => $eventoId,
                    'titulo'      => $evento ? "Interesse: " . $evento->titulo : "Conversa sobre Evento",
                    'tipo'        => $tipoDefinido,
                ]);
            }

            $this->selectedConversation = $conversation;
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