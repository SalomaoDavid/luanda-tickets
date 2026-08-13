<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // URL de qualquer plataforma: YouTube, Vimeo, TikTok, link directo
            $table->string('video_preview', 500)->nullable()->after('imagem_capa');
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn('video_preview');
        });
    }
};