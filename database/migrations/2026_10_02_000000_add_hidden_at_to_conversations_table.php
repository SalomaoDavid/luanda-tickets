<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Mesma lógica da conversa: esconder uma mensagem é por
            // utilizador, nunca apaga de verdade nem afeta o outro lado.
            // "sender"/"receiver" aqui referem-se aos papéis da CONVERSA
            // (conversations.sender_id / receiver_id), não a quem escreveu
            // a mensagem — por isso dá para esconder qualquer mensagem
            // (tua ou da outra pessoa) só do teu lado.
            $table->timestamp('hidden_for_sender_at')->nullable();
            $table->timestamp('hidden_for_receiver_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['hidden_for_sender_at', 'hidden_for_receiver_at']);
        });
    }
};