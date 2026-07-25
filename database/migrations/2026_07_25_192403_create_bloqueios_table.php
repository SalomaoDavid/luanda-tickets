<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloqueios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloqueador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bloqueado_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['bloqueador_id', 'bloqueado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloqueios');
    }
};