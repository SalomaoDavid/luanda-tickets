<?php
namespace App\Livewire\Messages;

use Livewire\Component;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ChatList extends Component
{
    // ⚠️ CORRIGIDO — estava vazio. Sem isto, o ChatList nunca soube que uma
    // conversa tinha sido escondida (isso acontece no MessagesIndex, que é
    // um componente irmão/pai, não o ChatList), por isso a lista nunca
    // voltava a consultar a base de dados — só o JS otimista escondia a
    // linha na hora, e ela reaparecia assim que o ChatList fosse
    // re-renderizado por qualquer outro motivo, com os dados antigos.
    protected $listeners = [
        'refresh-list' => '$refresh',
        'refresh' => '$refresh',
    ];

    // Recebido do messages-index.blade.php, só para saber qual conversa destacar na lista
    public $selectedConversationId = null;

    public function render()
    {
        $userId = auth()->id();

        // Admin vê só as próprias conversas, como qualquer utilizador — sem
        // bypass. Uma eventual tela de moderação de todas as conversas do
        // sistema seria uma tela separada, nunca misturada na caixa de
        // mensagens pessoal (era isso que listava conversas onde o admin
        // não participava e não conseguia abrir).
        $conversations = Conversation::visibleTo($userId)
            // ✅ Uma conversa é criada assim que clicas em alguém (para o
            // chat já abrir pronto), antes de qualquer mensagem ser
            // enviada. Se desistires sem escrever nada, essa conversa
            // vazia não deve aparecer na lista como "Sem mensagens".
            ->whereHas('messages')
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

    // ⚠️ AJUSTADO — antes só avisava o MessagesIndex e era ELE quem escondia
    // a conversa. Isso criava uma "corrida": este componente (ChatList)
    // respondia e redesenhava-se a si próprio ANTES de o aviso sequer
    // chegar ao MessagesIndex, por isso a conversa aparecia escondida por
    // um instante (JS otimista), voltava a aparecer (ChatList redesenhado
    // com os dados antigos, ainda não escondidos) e só desaparecia de vez
    // quando o segundo pedido (do MessagesIndex) terminava.
    //
    // Agora escondemos aqui mesmo, no mesmo pedido do clique — por isso o
    // primeiro redesenho do ChatList já sai correto. Continuamos a avisar
    // o MessagesIndex a seguir, para ele poder fechar o chat se esta era a
    // conversa aberta (chamar hideFor() outra vez aí não tem problema —
    // só volta a gravar a mesma data, não desfaz nada).
    public function requestDelete($id)
    {
        $conversation = Conversation::find($id);
        if ($conversation && $conversation->temParticipante(auth()->id())) {
            $conversation->hideFor(auth()->id());
        }

        $this->dispatch('delete-conversation-request', id: $id);
    }
}