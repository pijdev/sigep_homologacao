<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Atualizar valores nulos para evitar conflitos
        DB::table('users')->whereNull('login')->update(['login' => uniqid('user_')]);

        Schema::table('users', function (Blueprint $table) {
            // Tornar o campo obrigatório
            $table->string('login', 50)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
