<?php

namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagesIndex extends Component
{
    public $selectedConversation;
    // ✅ Controlar a reatividade do ChatBox por ID único e estável
    public $selectedConversationId;
    public $searchUser = '';
    public $searchResults = [];

    // ✅ Distingue "o utilizador escolheu mesmo esta conversa" (clique,
    // ou link direto tipo ?user_id=) de "foi só a mais recente, escolhida por
    // defeito ao abrir o ícone genérico de mensagens". Só o primeiro caso deve
    // esconder a lista em mobile.
    public $forcarMostrarChat = false;

    protected $listeners = [
        'refresh' => '$refresh',
        'refresh-list' => '$refresh',
        // ✅ Só reencaminham para as funções que já existem e já funcionam,
        // para o ChatList (lista única) conseguir abrir/esconder conversas
        // sem duplicar nenhuma lógica.
        'loadConversation' => 'onChatListSelected',
        'delete-conversation-request' => 'onChatListDeleteRequested',
    ];

    public function mount($conversation = null)
    {
        // Bypass total: Lendo direto da URL do navegador
        $urlUserId = $_GET['user_id'] ?? null;
        $urlEventoId = $_GET['evento_id'] ?? null;

        if ($urlUserId && $urlUserId != auth()->id()) {
            // Chamamos o startChat e forçamos a execução
            $this->startChat($urlUserId, $urlEventoId);

            // Se a conversa foi preenchida, injetamos o ID reativo e paramos
            if ($this->selectedConversation) {
                $this->selectedConversationId = $this->selectedConversation->id;
                $this->forcarMostrarChat = true; // veio de um link direto — é intencional
                return;
            }
        }

        // Se o bypass falhar ou não houver parâmetros, segue a vida normal
        if ($conversation) {
            $c = \App\Models\Conversation::find($conversation);
            $this->selectedConversation = ($c && $c->temParticipante(auth()->id())) ? $c : null;
            $this->forcarMostrarChat = true; // veio de um parâmetro explícito — é intencional
        } else {
            // Escolhida por defeito (a mais recente, e que não esteja
            // escondida da lista por este utilizador) — NÃO marca como
            // intencional, para o mobile continuar a mostrar a lista primeiro
            $this->selectedConversation = \App\Models\Conversation::visibleTo(auth()->id())
                ->orderBy('updated_at', 'desc')
                ->first();
        }

        // Garante que o ID reativo inicial está preenchido
        if ($this->selectedConversation) {
            $this->selectedConversationId = $this->selectedConversation->id;
        }
    }

    // ✅ Botão "Voltar" no mobile faz $set('selectedConversationId', null).
    // Sem isto o show-chat nunca desligava, porque o CSS depende de $selectedConversation.
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

        // Só pessoas com alguma ligação de seguir (em qualquer sentido) aparecem
        // como opção para iniciar uma conversa nova a partir desta pesquisa.
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

    // ✅ Apelido: aceita o clique de 'selectConversation' vindo do Blade
    public function selectConversation($id)
    {
        $this->loadConversation($id);
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
            // ✅ Sincroniza o ID reativo para redesenhar o Chat Box instantaneamente
            $this->selectedConversationId = $this->selectedConversation->id;

            $this->selectedConversation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $this->dispatch('scroll-down');
        }
    }

    /**
     * Único ponto de criação/busca de conversa — não decide mais "tipo" na
     * criação (isso causava conversas duplicadas e conversas que não abriam
     * nem apagavam). O "tipo" é sempre 'evento' ou 'pessoal'; um aviso de
     * admin é um selo por mensagem (Message::is_aviso_admin), não um tipo
     * de conversa separado.
     */
    public function startChat($userId, $eventoId = null)
    {
        $authId = auth()->id();

        $this->selectedConversation = \App\Models\Conversation::findOrCreateBetween(
            (int) $authId,
            (int) $userId,
            $eventoId ? (int) $eventoId : null
        );

        $this->selectedConversationId = $this->selectedConversation->id;
    }

    public function render()
    {
        $userId = auth()->id();

        // Nota: este $conversations não é usado pela view (a lista real vem
        // do componente ChatList), mas mantemos o mesmo escopo de
        // visibilidade por consistência.
        $conversations = Conversation::visibleTo($userId)
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

        if (!$conversation || !$conversation->temParticipante(auth()->id())) {
            return;
        }

        // ✅ Já não apaga a conversa inteira (isso apagava também do lado
        // do outro participante). Só esconde da MINHA lista — a conversa
        // e as mensagens continuam a existir para quem está do outro
        // lado, e reaparece sozinha na minha lista se chegar mensagem nova.
        $conversation->hideFor(auth()->id());

        if ($this->selectedConversation && $this->selectedConversation->id == $id) {
            $this->selectedConversation = null;
            $this->selectedConversationId = null;
            // Notifica o ChatBox para resetar o estado
            $this->dispatch('reset-chat');
        }

        // Atualiza a própria lista lateral
        $this->dispatch('refresh-list');
    }

    // ═══════════════════════════════════════════════════════════════
    // ✅ PONTES para o ChatList (lista única de conversas)
    // ───────────────────────────────────────────────────────────────
    // Estas 2 funções não têm NENHUMA lógica própria — só recebem o
    // aviso do ChatList e chamam, exatamente da mesma forma que já
    // acontecia antes, as funções que já existiam e já funcionavam
    // (loadConversation e deleteConversation, ambas acima, inalteradas).
    // ═══════════════════════════════════════════════════════════════

    public function onChatListSelected($conversationId)
    {
        $this->forcarMostrarChat = true; // clique real na lista — é intencional
        $this->loadConversation($conversationId);
    }

    // ✅ Clique em alguém da lista "online" que ainda não tem conversa.
    // Reaproveita o startChat() já existente (mesma lógica de sempre), só
    // acrescenta a marcação para o mobile mostrar logo o chat.
    public function onlineUserClicked($userId)
    {
        $this->forcarMostrarChat = true;
        $this->startChat($userId);
    }

    public function onChatListDeleteRequested($id)
    {
        $this->deleteConversation($id);
    }
}