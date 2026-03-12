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
        Schema::table('conversations_enum', function (Blueprint $table) {
            DB::statement("ALTER TABLE conversations MODIFY COLUMN tipo ENUM('pessoal', 'grupo', 'suport', 'aviso_admin', 'evento') DEFAULT 'pessoal'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations_enum', function (Blueprint $table) {
            //
        });
    }
};
