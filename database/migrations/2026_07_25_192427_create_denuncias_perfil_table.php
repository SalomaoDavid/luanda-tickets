<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncias_perfil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denunciante_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('denunciado_id')->constrained('users')->onDelete('cascade');
            $table->string('motivo', 50);
            $table->timestamps();
            $table->unique(['denunciante_id','denunciado_id']); // 1 denúncia por par
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias_perfil');
    }
};