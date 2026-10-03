<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use Illuminate\Console\Command;

class LimparConversasVazias extends Command
{
    protected $signature = 'conversas:limpar-vazias';
    protected $description = 'Remove conversas sem nenhuma mensagem, criadas há mais de 15 minutos (chats abertos e abandonados sem enviar nada)';

    public function handle(): int
    {
        $removidas = Conversation::doesntHave('messages')
            ->where('created_at', '<', now()->subMinutes(15))
            ->delete();

        $this->info("Conversas vazias removidas: {$removidas}");
        return self::SUCCESS;
    }
}