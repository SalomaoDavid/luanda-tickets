<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contas bancárias do Admin (visíveis no modal de pagamento)
        Schema::create('contas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->string('nome_banco', 100);
            $table->string('titular', 150);
            $table->string('iban', 50)->unique();
            $table->string('numero_conta', 50)->nullable();
            $table->string('logo', 255)->nullable(); // caminho: images/bancos/bfa.png
            $table->boolean('activa')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();
        });

        // Dados bancários privados dos criadores (só o admin vê)
        Schema::create('dados_bancarios_criadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nome_banco', 100);
            $table->string('titular', 150);
            $table->string('iban', 50);
            $table->string('numero_conta', 50)->nullable();
            $table->timestamps();
            $table->unique('user_id'); // 1 conta por criador por agora
        });

        // Saldos — registo automático dos 90%/10% por reserva confirmada
        Schema::create('saldos_criadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->onDelete('cascade');
            $table->foreignId('criador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->decimal('valor_total', 12, 2);    // total pago pelo utilizador
            $table->decimal('valor_admin', 12, 2);    // 10%
            $table->decimal('valor_criador', 12, 2);  // 90%
            $table->enum('estado', ['pendente','pago'])->default('pendente');
            $table->timestamp('pago_em')->nullable();
            $table->string('referencia_transferencia', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldos_criadores');
        Schema::dropIfExists('dados_bancarios_criadores');
        Schema::dropIfExists('contas_bancarias');
    }
};