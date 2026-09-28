<?php

namespace App\Livewire\Messages;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;

class ChatBox extends Component
{
    protected $listeners = [
        'loadConversation', 
        'refresh' => '$refresh', 
        'reset-chat' => 'resetChat'
    ]; 
    
    public $conversation;
    public $messageBody = '';

    public function mount(Conversation $conversation = null)
    {
        if ($conversation) {
            $this->conversation = $conversation;
            $this->messageBody = '';

            // ✅ Só marca como lida se já existir mesmo na BD — uma conversa
            // ainda em memória (ver MessagesIndex::startChat) não tem
            // mensagens para marcar.
            if ($conversation->exists) {
                $this->markAsRead();
            }
        }
    }

    public function resetChat()
    {
        $this->conversation = null;
    }

    public function loadConversation($conversationId)
    {
        if (!$conversationId) {
            $this->resetChat();
            return;
        }

        $conversa = Conversation::find($conversationId);

        if (!$conversa || !$conversa->temParticipante(auth()->id())) {
            $this->resetChat();
            return;
        }

        $this->conversation = $conversa;
        $this->markAsRead();
        
        $this->dispatch('scroll-down'); 
    }

    public function toggleBlock()
    {
        // ✅ Uma conversa ainda não gravada não pode ser bloqueada
        if (!$this->conversation || !$this->conversation->exists) return;

        $userId = auth()->id();

        if ($this->conversation->is_blocked && $this->conversation->blocked_by !== $userId) {
            return;
        }

        $this->conversation->is_blocked = !$this->conversation->is_blocked;
        $this->conversation->blocked_by = $this->conversation->is_blocked ? $userId : null;
        $this->conversation->save();

        if ($this->conversation->is_blocked) {
            $this->messageBody = '';
        }

        $this->dispatch('refresh-list');
        $this->dispatch('$refresh');
    }

    public function clearMessages()
    {
        // ✅ Sem mensagens gravadas, não há nada para limpar
        if (!$this->conversation || !$this->conversation->exists) return;

        $this->conversation->messages()->delete();
        $this->conversation->touch();

        $this->dispatch('$refresh');
        $this->dispatch('refresh-list');
    }

    public function markAsRead()
    {
        if (!$this->conversation) return;

        $this->conversation->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        auth()->user()->unreadNotifications()
            ->get()
            ->filter(fn($n) =>
                $n->type === 'App\Notifications\NewMessageNotification' &&
                ($n->data['conversation_id'] ?? null) == $this->conversation->id
            )
            ->each->markAsRead();

        $this->dispatch('refresh-list');
    }

    public function sendMessage($body = null)
    {
        $text = trim($body !== null ? $body : $this->messageBody);

        if (empty($text) || !$this->conversation) return;

        if ($this->conversation->is_blocked) return;

        // ✅ NOVO — só agora, com a 1ª mensagem mesmo confirmada, é que a
        // conversa passa a existir de verdade na BD (ver
        // MessagesIndex::startChat, que só a monta em memória).
        if (!$this->conversation->exists) {
            $this->conversation->save();
            $this->dispatch('conversation-created', conversationId: $this->conversation->id);
        }

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id'         => auth()->id(),
            'body'            => $text,
        ]);

        $this->conversation->touch();
        $this->messageBody = '';

        try {
            broadcast(new \App\Events\MessageSent($message));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->dispatch('scroll-down');
        $this->dispatch('refresh-list');

        $receiverId = $this->conversation->sender_id === auth()->id()
            ? $this->conversation->receiver_id
            : $this->conversation->sender_id;

        $hasUnread = $this->conversation->messages()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->exists();

        if ($hasUnread) {
            $receiver = \App\Models\User::find($receiverId);
            if ($receiver) {
                try {
                    $receiver->notify(\App\Notifications\NewMessageNotification::fromMessage($message));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

    public function render()
    {
        $messages = collect();
        
        if ($this->conversation && $this->conversation->exists) {
            $messages = $this->conversation->messages()
                ->with('user')
                ->latest() 
                ->take(50)
                ->get()
                ->reverse();
        }

        return view('livewire.messages.chat-box', [
            'messages' => $messages
        ]);
    }
}