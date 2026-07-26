<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('visibilidade_perfil', 20)->default('publico')->after('bio');
            $table->string('quem_mensagens', 20)->default('todos')->after('visibilidade_perfil');
            $table->boolean('mostrar_bilhetes')->default(true)->after('quem_mensagens');
            $table->boolean('mostrar_seguidores')->default(true)->after('mostrar_bilhetes');
            $table->boolean('pesquisavel')->default(true)->after('mostrar_seguidores');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'visibilidade_perfil',
                'quem_mensagens',
                'mostrar_bilhetes',
                'mostrar_seguidores',
                'pesquisavel',
            ]);
        });
    }
};