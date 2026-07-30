<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── EVENTOS ──────────────────────────────────────────────
        Schema::table('eventos', function (Blueprint $table) {
            $table->index('status');                        // WHERE status = 'publicado'
            $table->index('data_evento');                   // ORDER BY / WHERE data_evento
            $table->index('user_id');                       // WHERE user_id = X (criador)
            $table->index('categoria_id');                  // WHERE categoria_id = X
            $table->index(['status', 'data_evento']);       // combinado — filtros comuns
        });

        // ── RESERVAS ─────────────────────────────────────────────
        Schema::table('reservas', function (Blueprint $table) {
            $table->index('status');                        // WHERE status = 'pago'
            $table->index('tipo_ingresso_id');              // JOIN com tipo_ingressos
            $table->index('user_id');                       // WHERE user_id = X
            $table->index(['status', 'tipo_ingresso_id']); // relatórios
        });

        // ── BILHETES ─────────────────────────────────────────────
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->index('evento_id');                     // JOIN frequente
            $table->index('bloqueado');                     // WHERE bloqueado = true
            $table->index('validado_em');                   // WHERE validado_em IS NOT NULL
            $table->index('lote_id');                       // GROUP BY lote_id
            $table->index('pedido_id');                     // JOIN com pedidos
        });

        // ── BILHETES_AUDITORIA ────────────────────────────────────
        Schema::table('bilhetes_auditoria', function (Blueprint $table) {
            $table->index('acao');                          // WHERE acao IN (...)
            $table->index('codigo_unico');                  // WHERE codigo_unico = X
            $table->index('created_at');                    // WHERE created_at >= X
            $table->index('bilhete_id');                    // JOIN com bilhetes
            $table->index(['acao', 'created_at']);          // relatórios por período
        });

        // ── COMENTARIOS ──────────────────────────────────────────
        Schema::table('comentarios', function (Blueprint $table) {
            $table->index('evento_id');                     // WHERE evento_id = X
            $table->index('user_id');                       // WHERE user_id = X
            $table->index('parent_id');                     // WHERE parent_id IS NULL
        });

        // ── POSTAGENS ────────────────────────────────────────────
        Schema::table('postagens', function (Blueprint $table) {
            $table->index('user_id');                       // WHERE user_id = X
            $table->index('created_at');                    // ORDER BY created_at
        });

        // ── SEGUIDORES ───────────────────────────────────────────
        Schema::table('seguidores', function (Blueprint $table) {
            // UNIQUE em [seguidor_id, seguido_id] já cobre queries por seguidor_id
            // Falta índice por seguido_id para "quem me segue"
            $table->index('seguido_id');
        });

        // ── SALDOS_CRIADORES ─────────────────────────────────────
        Schema::table('saldos_criadores', function (Blueprint $table) {
            $table->index('estado');                        // WHERE estado = 'pendente'
            $table->index('criador_id');                    // GROUP BY criador_id
            $table->index(['criador_id', 'estado']);        // relatórios por criador
        });

        // ── NOTIFICATIONS ────────────────────────────────────────
        Schema::table('notifications', function (Blueprint $table) {
            // read_at é consultado frequentemente para count de não lidas
            $table->index(['notifiable_id', 'read_at']);
        });

        // ── CURTIDAS ─────────────────────────────────────────────
        Schema::table('curtidas', function (Blueprint $table) {
            // UNIQUE [user_id, evento_id] já cobre. Adicionar índice por evento_id
            $table->index('evento_id');                     // COUNT curtidas por evento
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['data_evento']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['categoria_id']);
            $table->dropIndex(['status', 'data_evento']);
        });

        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tipo_ingresso_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status', 'tipo_ingresso_id']);
        });

        Schema::table('bilhetes', function (Blueprint $table) {
            $table->dropIndex(['evento_id']);
            $table->dropIndex(['bloqueado']);
            $table->dropIndex(['validado_em']);
            $table->dropIndex(['lote_id']);
            $table->dropIndex(['pedido_id']);
        });

        Schema::table('bilhetes_auditoria', function (Blueprint $table) {
            $table->dropIndex(['acao']);
            $table->dropIndex(['codigo_unico']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['bilhete_id']);
            $table->dropIndex(['acao', 'created_at']);
        });

        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropIndex(['evento_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['parent_id']);
        });

        Schema::table('postagens', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('seguidores', function (Blueprint $table) {
            $table->dropIndex(['seguido_id']);
        });

        Schema::table('saldos_criadores', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['criador_id']);
            $table->dropIndex(['criador_id', 'estado']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_id', 'read_at']);
        });

        Schema::table('curtidas', function (Blueprint $table) {
            $table->dropIndex(['evento_id']);
        });
    }
};