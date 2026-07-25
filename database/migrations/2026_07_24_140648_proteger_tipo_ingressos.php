<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trigger que bloqueia alteração de bilhetes logo após publicação do evento
        DB::unprepared("
            CREATE TRIGGER tipo_ingressos_proteger_quantidade
            BEFORE UPDATE ON tipo_ingressos
            FOR EACH ROW
            BEGIN
                -- Bloqueia se o evento já estiver PUBLICADO
                -- independentemente de haver vendas ou não
                IF (
                    SELECT status FROM eventos
                    WHERE id = OLD.evento_id
                ) = 'publicado' THEN
                    IF NEW.quantidade_total != OLD.quantidade_total
                    OR NEW.preco != OLD.preco
                    OR NEW.nome != OLD.nome THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'SEGURANÇA: não é permitido alterar bilhetes de um evento publicado.';
                    END IF;
                END IF;
            END
        ");

        // Trigger que impede stock negativo
        DB::unprepared("
            CREATE TRIGGER tipo_ingressos_sem_stock_negativo
            BEFORE UPDATE ON tipo_ingressos
            FOR EACH ROW
            BEGIN
                IF NEW.quantidade_disponivel < 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: quantidade_disponivel não pode ser negativa.';
                END IF;

                IF NEW.quantidade_disponivel > NEW.quantidade_total THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: quantidade_disponivel não pode ser maior que quantidade_total.';
                END IF;
            END
        ");

        // Trigger que impede DELETE de tipo_ingressos de eventos publicados
        DB::unprepared("
            CREATE TRIGGER tipo_ingressos_proteger_delete
            BEFORE DELETE ON tipo_ingressos
            FOR EACH ROW
            BEGIN
                IF (
                    SELECT status FROM eventos
                    WHERE id = OLD.evento_id
                ) = 'publicado' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'SEGURANÇA: não é permitido eliminar bilhetes de um evento publicado.';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tipo_ingressos_proteger_quantidade');
        DB::unprepared('DROP TRIGGER IF EXISTS tipo_ingressos_sem_stock_negativo');
        DB::unprepared('DROP TRIGGER IF EXISTS tipo_ingressos_proteger_delete');
    }
};