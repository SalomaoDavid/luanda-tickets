<?php
namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{
    protected $listeners = [
    'startConversation',
    'refresh-list' => '$refresh', // ✅ NOVO — sem isto, esta lista nunca soube quando algo mudava
];

    // Recebido do messages-index.blade.php, só para saber qual conversa destacar na lista
    public $selectedConversationId = null;

    public function render()
    {
        $userId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        // ✅ Admin acompanha todas as conversas do sistema; qualquer outro
        // utilizador só vê as suas próprias — mesma estrutura (sender_id/
        // receiver_id) que já funciona no resto do chat.
        $query = Conversation::query();
        if (!$isAdmin) {
            $query->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            });
        }

        $conversations = $query
            ->withCount(['messages as unread_count' => function ($query) use ($userId) {
                $query->where('user_id', '!=', $userId)->whereNull('read_at');
            }])
            ->with('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livewire.messages.chat-list', [
            'conversations' => $conversations
        ]);
    }

    public function selectConversation($id)
    {
        // Dispara um evento para o ChatBox (e agora também o MessagesIndex) carregarem a conversa selecionada
        $this->dispatch('loadConversation', conversationId: $id);
    }

    // ✅ NOVO — só dispara o aviso; quem realmente elimina é o
    // MessagesIndex::deleteConversation(), que já existia e já funcionava.
    public function requestDelete($id)
    {
        $this->dispatch('delete-conversation-request', id: $id);
    }

    public function startConversation($userId)
    {
        $authId = auth()->id();

        // ✅ CORRIGIDO — usava uma relação "users" (tabela pivot) que não existe
        // na estrutura real (sender_id/receiver_id), por isso o Conversation::create()
        // ia sem nenhum campo preenchido e rebentava com "Field 'sender_id' doesn't
        // have a default value". Agora segue a mesma lógica já usada e testada em
        // MessagesIndex::startChat().
        $conversation = \App\Models\Conversation::whereNull('evento_id')
            ->where(function ($q) use ($authId, $userId) {
                $q->where(function ($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $authId)->where('receiver_id', $userId);
                })->orWhere(function ($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $userId)->where('receiver_id', $authId);
                });
            })->first();

        if (!$conversation) {
            $conversation = \App\Models\Conversation::create([
                'sender_id'   => $authId,
                'receiver_id' => $userId,
                'tipo'        => 'pessoal',
            ]);
        }

        // ✅ CORRIGIDO — o nome do parâmetro tinha de ser "conversationId"
        // (estava "conversation") para o MessagesIndex::onChatListSelected
        // conseguir mesmo abrir a conversa.
        $this->dispatch('loadConversation', conversationId: $conversation->id);
        $this->dispatch('open-chat-modal');
    }
}