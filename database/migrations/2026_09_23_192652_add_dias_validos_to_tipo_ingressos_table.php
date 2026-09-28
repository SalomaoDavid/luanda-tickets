<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_ingressos', function (Blueprint $table) {
            // Nulo para bilhetes normais. Só se preenche quando o criador marca
            // este tipo de bilhete como "Passe Completo" num evento de vários
            // dias (ex: Festival) — guarda quantos dias esse bilhete cobre.
            $table->unsignedTinyInteger('dias_validos')->nullable()->after('quantidade_total');
        });
    }

    public function down(): void
    {
        Schema::table('tipo_ingressos', function (Blueprint $table) {
            $table->dropColumn('dias_validos');
        });
    }
};