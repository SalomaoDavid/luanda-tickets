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
        Schema::create('bilhetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos');
            $table->foreignId('tipo_ingressos_id')->constrained('tipo_ingressos'); 
            // O nome dentro de constrained() deve ser igual ao nome da tabela criada na outra migration
            $table->string('codigo_unico')->unique(); // Será usado no QR Code
            $table->timestamp('validado_em')->nullable(); // Para o scanner na porta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bilhetes');
    }
};
