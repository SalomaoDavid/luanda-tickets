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
      Schema::table('users', function (Blueprint $table){
        $table->String('cover')->nullable()->after('avatar');
     });  
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('reservas', function (Blueprint $table){
        $table->dropColumn('cover');  
    });
    
    }      
};
