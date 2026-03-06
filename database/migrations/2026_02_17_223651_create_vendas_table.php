<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('vendas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('evento_id')->constrained()->onDelete('cascade');
        $table->foreignId('tipo_ingresso_id')->constrained('tipo_ingressos')->onDelete('cascade');
        
        // Dados do Cliente
        $table->string('nome_cliente');
        $table->string('whatsapp');
        $table->string('email')->nullable();
        
        // Financeiro
        $table->decimal('valor_pago', 15, 2);
        $table->string('comprovativo_path')->nullable(); // Caminho do arquivo da imagem
        
        // Status e Segurança
        $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
        $table->string('codigo_bilhete')->unique(); // O código que estará no QR Code
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
