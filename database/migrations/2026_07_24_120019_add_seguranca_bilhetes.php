<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. ADICIONA COLUNAS DE SEGURANÇA À TABELA BILHETES ──
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->string('hmac_assinatura', 64)->nullable()->after('codigo_unico');
            $table->string('lote_id', 20)->nullable()->after('hmac_assinatura');
            $table->tinyInteger('tentativas_invalidas')->default(0)->after('lote_id');
            $table->boolean('bloqueado')->default(false)->after('tentativas_invalidas');
        });

        // ── 2. CRIA TABELA DE AUDITORIA (append-only) ──
        Schema::create('bilhetes_auditoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bilhete_id');
            $table->string('acao', 30); // emitido, validado, tentativa_invalida, bloqueado
            $table->string('codigo_unico', 36);
            $table->string('scanner_ip', 45)->nullable();
            $table->string('scanner_user_agent')->nullable();
            $table->unsignedBigInteger('validado_por')->nullable(); // user_id do scanner
            $table->json('snapshot')->nullable(); // snapshot dos dados no momento
            $table->timestamp('created_at')->useCurrent();
            // SEM updated_at — append-only
        });

        // ── 3. CRIA TABELA DE LOTES ──
        Schema::create('bilhetes_lotes', function (Blueprint $table) {
            $table->id();
            $table->string('lote_id', 20)->unique();
            $table->unsignedBigInteger('reserva_id');
            $table->unsignedBigInteger('evento_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('quantidade');
            $table->decimal('total', 10, 2);
            $table->timestamp('emitido_em')->useCurrent();
            $table->string('emitido_por_ip', 45)->nullable();
        });

        // ── 4. TRIGGER MySQL — impede UPDATE de campos críticos ──
        DB::unprepared("
            CREATE TRIGGER bilhetes_imutavel_update
            BEFORE UPDATE ON bilhetes
            FOR EACH ROW
            BEGIN
                -- Impede alteração do código único
                IF NEW.codigo_unico != OLD.codigo_unico THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: codigo_unico é imutável após criação.';
                END IF;

                -- Impede alteração da assinatura HMAC
                IF OLD.hmac_assinatura IS NOT NULL
                   AND NEW.hmac_assinatura != OLD.hmac_assinatura THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: hmac_assinatura é imutável após criação.';
                END IF;

                -- Impede alteração do pedido associado
                IF NEW.pedido_id != OLD.pedido_id THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: pedido_id é imutável após criação.';
                END IF;

                -- Impede alteração do evento associado
                IF NEW.evento_id != OLD.evento_id THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: evento_id é imutável após criação.';
                END IF;

                -- Impede revalidação (bilhete já usado não pode ser desmarcado)
                IF OLD.validado_em IS NOT NULL AND NEW.validado_em IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: bilhete já validado não pode ser revertido.';
                END IF;
            END
        ");

        // ── 5. TRIGGER MySQL — impede DELETE de bilhetes ──
        DB::unprepared("
            CREATE TRIGGER bilhetes_imutavel_delete
            BEFORE DELETE ON bilhetes
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'SEGURANÇA: bilhetes não podem ser eliminados.';
            END
        ");

        // ── 6. TRIGGER MySQL — impede DELETE da auditoria ──
        DB::unprepared("
            CREATE TRIGGER auditoria_append_only_delete
            BEFORE DELETE ON bilhetes_auditoria
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'SEGURANÇA: registos de auditoria não podem ser eliminados.';
            END
        ");

        // ── 7. TRIGGER MySQL — impede UPDATE da auditoria ──
        DB::unprepared("
            CREATE TRIGGER auditoria_append_only_update
            BEFORE UPDATE ON bilhetes_auditoria
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'SEGURANÇA: registos de auditoria não podem ser alterados.';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS bilhetes_imutavel_update');
        DB::unprepared('DROP TRIGGER IF EXISTS bilhetes_imutavel_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS auditoria_append_only_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS auditoria_append_only_update');

        Schema::dropIfExists('bilhetes_lotes');
        Schema::dropIfExists('bilhetes_auditoria');

        Schema::table('bilhetes', function (Blueprint $table) {
            $table->dropColumn(['hmac_assinatura', 'lote_id', 'tentativas_invalidas', 'bloqueado']);
        });
    }
};