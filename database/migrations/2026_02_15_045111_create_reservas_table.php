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
    {Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tipo_ingresso_id')->constrained('tipo_ingressos');
        $table->string('nome_cliente');
        $table->string('whatsapp');
        $table->integer('quantidade');
        $table->decimal('total', 10, 2);
        $table->string('status')->default('pendente'); // pendente, pago, cancelado
        $table->string('codigo_bilhete')->unique()->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
