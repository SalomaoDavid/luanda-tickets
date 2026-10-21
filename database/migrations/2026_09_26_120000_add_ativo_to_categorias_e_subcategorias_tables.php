<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Aditivo — não mexe em nenhuma coluna existente.
        // "ativo" controla se a categoria/subcategoria continua a aparecer
        // como opção ao criar um NOVO evento; eventos já criados com ela
        // continuam a funcionar e a exibi-la normalmente.
        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('tipo');
        });

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });

        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
};