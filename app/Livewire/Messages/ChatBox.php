<?php

namespace App\Livewire\Messages;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;

class ChatBox extends Component
{
    // Adicionamos 'reset-chat' para limpar a tela quando deletar na lateral
    // E 'refresh' para forçar a re-renderização
    protected $listeners = [
        'loadConversation', 
        'refresh' => '$refresh', 
        'reset-chat' => 'resetChat'
    ]; 
    
    public $conversation;
    public $messageBody = '';

    public function mount(Conversation $conversation = null)
    {
        if ($conversation && $conversation->exists) {
            $this->conversation = $conversation;
            $this->messageBody = ''; // Garante campo limpo
            $this->markAsRead();
        }
    }

    /**
     * Limpa o estado do chat (essencial para quando a conversa é deletada)
     */
    public function resetChat()
    {
        $this->conversation = null;
    }

    /**
     * Carrega a conversa e marca como lida
     */
    public function loadConversation($conversationId)
    {
        if (!$conversationId) {
            $this->resetChat();
            return;
        }

        $this->conversation = Conversation::find($conversationId);
        $this->markAsRead();
        
        // Dispara o scroll para o fundo após carregar
        $this->dispatch('scroll-down'); 
    }

    /**
     * Alterna o status de bloqueio
     */
    public function toggleBlock()
    {
        if (!$this->conversation) return;

        $userId = auth()->id();

        // Só quem bloqueou pode desbloquear
        if ($this->conversation->is_blocked && $this->conversation->blocked_by !== $userId) {
            return; // Não faz nada — não foi tu que bloqueaste
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
    /**
     * Limpa as mensagens (Mantém a conversa na lista)
     */
    public function clearMessages()
    {
        if (!$this->conversation) return;

        // Deleta as mensagens
        $this->conversation->messages()->delete();

        // Atualiza o timestamp da conversa
        $this->conversation->touch();

        $this->dispatch('$refresh');
        $this->dispatch('refresh-list');
    }

    /**
     * Marca mensagens como lidas
     */
    public function markAsRead()
    {
        if (!$this->conversation) return;

        $this->conversation->messages()
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Marca notificações desta conversa como lidas
        auth()->user()->unreadNotifications()
            ->get()
            ->filter(fn($n) =>
                $n->type === 'App\Notifications\NewMessageNotification' &&
                ($n->data['conversation_id'] ?? null) == $this->conversation->id
            )
            ->each->markAsRead();

        $this->dispatch('refresh-list');
    }
    /**
     * Envia mensagem com trava de bloqueio
     */
    public function sendMessage($body = null)
    {
        $text = trim($body !== null ? $body : $this->messageBody);

        if (empty($text) || !$this->conversation) return;

        if ($this->conversation->is_blocked) return;

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id'         => auth()->id(),
            'body'            => $text,
        ]);

        $this->conversation->touch();
        $this->messageBody = '';

        // ✅ Envia via WebSocket (ShouldBroadcastNow) para quem estiver com a
        // conversa aberta do outro lado — chega quase instantaneamente,
        // sem esperar pelo próximo wire:poll.
        // Protegido em try/catch: se o Reverb/Pusher falhar por qualquer
        // motivo (config, rede, etc.), a mensagem continua enviada na mesma
        // — só perde-se o "empurrão" em tempo real, nunca a mensagem em si.
        try {
            broadcast(new \App\Events\MessageSent($message));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->dispatch('scroll-down');
        $this->dispatch('refresh-list');

        // Só notifica se o destinatário não leu ainda (não está na conversa)
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
                    $receiver->notify(new \App\Notifications\NewMessageNotification($message->id));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

    public function render()
    {
        $messages = collect();
        
        if ($this->conversation) {
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