<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->string('municipio')->nullable()->after('localizacao');
            $table->string('provincia')->nullable()->default('Luanda')->after('municipio');
            $table->time('hora_inicio')->nullable()->after('data_evento');
            $table->time('hora_fim')->nullable()->after('hora_inicio');
            $table->date('data_fim')->nullable()->after('hora_fim');
            $table->boolean('multiplos_dias')->default(false)->after('data_fim');
            $table->boolean('online')->default(false)->after('multiplos_dias');
            $table->string('link_externo')->nullable()->after('online');
            $table->integer('ingressos_por_pessoa')->default(1)->after('lotacao_maxima');
            $table->boolean('lista_espera')->default(false)->after('ingressos_por_pessoa');
            $table->boolean('privado')->default(false)->after('lista_espera');
            $table->boolean('aprovacao_manual')->default(false)->after('privado');
            $table->boolean('permitir_comentarios')->default(true)->after('aprovacao_manual');
            $table->boolean('participantes_publicos')->default(true)->after('permitir_comentarios');
            $table->boolean('notif_nova_inscricao')->default(true)->after('participantes_publicos');
            $table->boolean('notif_lembrete_24h')->default(true)->after('notif_nova_inscricao');
            $table->boolean('notif_resumo_semanal')->default(false)->after('notif_lembrete_24h');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn([
                'municipio',
                'provincia',
                'hora_inicio',
                'hora_fim',
                'data_fim',
                'multiplos_dias',
                'online',
                'link_externo',
                'ingressos_por_pessoa',
                'lista_espera',
                'privado',
                'aprovacao_manual',
                'permitir_comentarios',
                'participantes_publicos',
                'notif_nova_inscricao',
                'notif_lembrete_24h',
                'notif_resumo_semanal',
            ]);
        });
    }
};