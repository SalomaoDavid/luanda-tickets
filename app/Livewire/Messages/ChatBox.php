<?php

namespace App\Livewire\Messages;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;

class ChatBox extends Component
{
    // Adicionamos 'reset-chat' para limpar a tela quando a conversa é escondida
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
     * Limpa o estado do chat (essencial para quando a conversa é escondida)
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

        $conversa = Conversation::find($conversationId);

        // Segurança: só carrega conversas em que o utilizador participa
        if (!$conversa || !$conversa->temParticipante(auth()->id())) {
            $this->resetChat();
            return;
        }

        $this->conversation = $conversa;
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
    public function sendMessage($body = null, $comoAviso = false)
    {
        $text = trim($body !== null ? $body : $this->messageBody);

        if (empty($text) || !$this->conversation) return;

        if ($this->conversation->is_blocked) return;

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id'         => auth()->id(),
            'body'            => $text,
            'is_aviso_admin'  => $comoAviso && auth()->user()->role === 'admin',
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
                    $receiver->notify(\App\Notifications\NewMessageNotification::fromMessage($message));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

    /**
     * Esconde uma ou várias mensagens só para mim — nunca apaga de verdade
     * nem afeta o que a outra pessoa vê. $ids vem do Alpine (seleção feita
     * no próprio navegador, via pressão longa + toque nos círculos).
     *
     * IMPORTANTE: usamos update() em massa via query builder (não
     * $mensagem->save()), que NÃO mexe no updated_at da mensagem — não há
     * aqui a mesma regra de "reaparece sozinha" que a conversa tem, então
     * isto não tem o problema de timestamp que a conversa tinha.
     */
    public function apagarSelecionadas($ids)
    {
        if (!$this->conversation || empty($ids)) return;

        $ids = array_values(array_unique(array_map('intval', (array) $ids)));
        $userId = auth()->id();

        $souSender = (int) $this->conversation->sender_id === $userId;
        $souReceiver = (int) $this->conversation->receiver_id === $userId;

        if (!$souSender && !$souReceiver) {
            // Segurança: nem sender nem receiver desta conversa — não faz nada.
            return;
        }

        $coluna = $souSender ? 'hidden_for_sender_at' : 'hidden_for_receiver_at';

        $this->conversation->messages()
            ->whereIn('id', $ids)
            ->update([$coluna => now()]);

        $this->dispatch('$refresh');
        $this->dispatch('refresh-list');
    }

    public function render()
    {
        $messages = collect();

        if ($this->conversation) {
            $userId = auth()->id();
            $souSender = (int) $this->conversation->sender_id === $userId;
            $coluna = $souSender ? 'hidden_for_sender_at' : 'hidden_for_receiver_at';

            $messages = $this->conversation->messages()
                ->whereNull($coluna)
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