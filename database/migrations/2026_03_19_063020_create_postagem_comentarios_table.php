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
        Schema::create('postagem_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('postagem_id')->constrained('postagens')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('postagem_comentarios')->onDelete('cascade');
            $table->text('corpo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postagem_comentarios');
    }
};
