<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrarConversasAvisoAdmin extends Command
{
    protected $signature = 'conversas:migrar-aviso-admin';
    protected $description = 'Converte conversas tipo=aviso_admin para o novo modelo (selo na mensagem, sem tipo separado)';

    public function handle()
    {
        $avisos = Conversation::where('tipo', 'aviso_admin')->get();
        $this->info("Encontradas {$avisos->count()} conversas 'aviso_admin'.");

        foreach ($avisos as $aviso) {
            $userA = $aviso->sender_id;
            $userB = $aviso->receiver_id;

            $pessoal = Conversation::whereNull('evento_id')
                ->where('id', '!=', $aviso->id)
                ->where(function ($q) use ($userA, $userB) {
                    $q->where(fn ($i) => $i->where('sender_id', $userA)->where('receiver_id', $userB))
                      ->orWhere(fn ($i) => $i->where('sender_id', $userB)->where('receiver_id', $userA));
                })
                ->first();

            DB::transaction(function () use ($aviso, $pessoal) {
                if ($pessoal) {
                    $aviso->messages()->update([
                        'conversation_id' => $pessoal->id,
                        'is_aviso_admin'  => true,
                    ]);
                    $pessoal->touch();
                    $aviso->delete();
                    $this->line("→ Conversa #{$aviso->id} fundida na #{$pessoal->id}.");
                } else {
                    $aviso->messages()->update(['is_aviso_admin' => true]);
                    $aviso->update(['tipo' => 'pessoal']);
                    $this->line("→ Conversa #{$aviso->id} convertida para 'pessoal'.");
                }
            });
        }

        $this->info('Concluído.');
    }
}