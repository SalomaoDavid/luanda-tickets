<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Dados devolvidos pelo PushManager.subscribe() do navegador —
            // são exatamente o que o pacote minishlink/web-push precisa
            // para conseguir enviar uma notificação para este dispositivo.
            $table->text('endpoint');                 // endereço único desta subscrição/dispositivo
            $table->string('public_key')->nullable();  // chave pública da subscrição (p256dh)
            $table->string('auth_token')->nullable();  // token de autenticação da subscrição (auth)
            $table->string('content_encoding')->nullable()->default('aes128gcm');

            $table->timestamps();

            // Um mesmo endpoint nunca se repete — evita subscrições duplicadas
            // para o mesmo dispositivo/navegador.
            $table->string('endpoint_hash', 64)->nullable();
            $table->unique('endpoint_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};