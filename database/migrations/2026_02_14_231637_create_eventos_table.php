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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias'); // Relacionamento
            $table->string('titulo');
            $table->text('descricao');
            $table->string('localizacao');
            $table->dateTime('data_evento');
            $table->string('imagem_capa')->nullable();
            $table->integer('lotacao_maxima');
            $table->enum('status', ['rascunho', 'publicado', 'encerrado'])->default('rascunho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
