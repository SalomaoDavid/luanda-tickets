<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notif_eventos')->default(true)->after('pesquisavel');
            $table->boolean('notif_bilhetes')->default(true)->after('notif_eventos');
            $table->boolean('notif_mensagens')->default(true)->after('notif_bilhetes');
            $table->boolean('notif_seguidores')->default(false)->after('notif_mensagens');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notif_eventos','notif_bilhetes',
                'notif_mensagens','notif_seguidores',
            ]);
        });
    }
};