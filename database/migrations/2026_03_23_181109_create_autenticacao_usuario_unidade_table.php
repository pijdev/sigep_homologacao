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
        Schema::create('autenticacao_usuario_unidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unidade_id')->constrained('unidades_prisionais')->onDelete('cascade');
            $table->timestamps();

            // Garantir que um usuário não possa ter o mesmo vínculo duplicado
            $table->unique(['user_id', 'unidade_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autenticacao_usuario_unidade');
    }
};
