<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Comando de correção única — diagnostica e corrige, de uma só vez, as
 * causas mais prováveis de "mensagens não apagam":
 *
 * 1) Verifica se as 4 colunas de "esconder" (hidden_for_sender_at /
 *    hidden_for_receiver_at) existem mesmo nas tabelas `conversations` e
 *    `messages` — e cria as que estiverem em falta, sem dar erro se já
 *    existirem (ao contrário de uma migration normal).
 * 2) Corrige o selo "Comunicado Oficial" que ficou marcado em mensagens
 *    antigas que não deviam tê-lo (regressão da migração anterior).
 *
 * Uso: php artisan mensagens:corrigir
 */
class FixMensagens extends Command
{
    protected $signature = 'mensagens:corrigir';
    protected $description = 'Corrige colunas em falta e o selo de aviso-admin mal aplicado';

    public function handle()
    {
        $this->info('--- 1) A verificar colunas de "esconder" ---');

        $alvo = [
            'conversations' => ['hidden_for_sender_at', 'hidden_for_receiver_at'],
            'messages'      => ['hidden_for_sender_at', 'hidden_for_receiver_at'],
        ];

        foreach ($alvo as $tabela => $colunas) {
            if (!Schema::hasTable($tabela)) {
                $this->error("Tabela '$tabela' não existe — algo está muito errado, para já.");
                continue;
            }

            foreach ($colunas as $coluna) {
                if (Schema::hasColumn($tabela, $coluna)) {
                    $this->line("  ✓ $tabela.$coluna já existe.");
                    continue;
                }

                $this->warn("  ✗ $tabela.$coluna NÃO existia — a criar agora...");
                try {
                    Schema::table($tabela, function ($table) use ($coluna) {
                        $table->timestamp($coluna)->nullable();
                    });
                    $this->info("  ✓ $tabela.$coluna criada com sucesso.");
                } catch (\Throwable $e) {
                    $this->error("  ✗ Falhou a criar $tabela.$coluna: " . $e->getMessage());
                }
            }
        }

        $this->info('');
        $this->info('--- 2) A corrigir selo "Comunicado Oficial" em mensagens antigas ---');

        if (Schema::hasColumn('messages', 'is_aviso_admin')) {
            $afetadas = DB::table('messages')
                ->where('is_aviso_admin', true)
                ->where('created_at', '<', now())
                ->update(['is_aviso_admin' => false]);

            $this->info("  ✓ $afetadas mensagem(ns) corrigida(s) (selo removido das antigas).");
        } else {
            $this->error('  ✗ Coluna is_aviso_admin não existe em messages — verifica a migration correspondente.');
        }

        $this->info('');
        $this->info('--- 3) Teste rápido de escrita ---');

        try {
            $msg = DB::table('messages')->first();
            if ($msg) {
                DB::table('messages')->where('id', $msg->id)->update([
                    'hidden_for_sender_at' => null,
                    'hidden_for_receiver_at' => null,
                ]);
                $this->info('  ✓ Conseguiu escrever nas colunas novas de messages sem erro.');
            } else {
                $this->line('  (sem mensagens na base de dados para testar — não é um problema)');
            }
        } catch (\Throwable $e) {
            $this->error('  ✗ Erro ao escrever: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('Concluído. Agora tenta apagar uma mensagem na app e diz-me o que acontece.');

        return 0;
    }
}