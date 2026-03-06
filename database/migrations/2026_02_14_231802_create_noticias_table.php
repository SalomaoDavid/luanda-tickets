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
        Schema::create('noticias', function (Blueprint $table) {
    $table->id();
    // Tornamos estes campos nullable para as notícias automáticas passarem
    $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('set null');
    $table->foreignId('autor_id')->nullable()->constrained('users')->onDelete('set null');
    
    $table->string('titulo');
    $table->string('slug')->unique();
    $table->string('link_original')->unique()->nullable(); // FUNDAMENTAL para o rastreador
    $table->string('fonte')->nullable(); // Ex: Platina Line, AngoRussia
    
    $table->text('conteudo');
    $table->string('imagem_destaque')->nullable();
    $table->timestamp('publicado_em')->nullable();
    $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
