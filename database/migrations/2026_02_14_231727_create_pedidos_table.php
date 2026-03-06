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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Cliente que compra
            $table->decimal('total_pago', 10, 2);
            $table->string('metodo_pagamento')->default('Transferencia');
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->string('comprovativo_path')->nullable(); // Foto do talão do Express
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
