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
        Schema::create('postagem_reacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('postagem_id')->constrained('postagens')->onDelete('cascade');
            $table->enum('tipo', ['curtida', 'adoro']);
            $table->unique(['user_id', 'postagem_id']); // só uma reação por utilizador por postagem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postagem_reacoes');
    }
};
