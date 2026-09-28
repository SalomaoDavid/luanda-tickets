<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bilhetes', function (Blueprint $table) {
            // Só informativo — diz a que dia específico este bilhete dá
            // direito a entrar, quando faz parte de um "Passe Completo"
            // de vários dias. Não interfere em nada da validação/segurança
            // do scanner (HMAC, bloqueio, uso único) — isso continua igual.
            // Nulo para bilhetes normais de eventos de 1 dia só.
            $table->unsignedTinyInteger('numero_dia')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bilhetes', function (Blueprint $table) {
            $table->dropColumn('numero_dia');
        });
    }
};