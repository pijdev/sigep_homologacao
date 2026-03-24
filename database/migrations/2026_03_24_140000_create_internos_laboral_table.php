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
        Schema::create('internos_laboral', function (Blueprint $table) {
            $table->id();
            $table->integer('ipen');
            $table->string('estabelecimento', 255);
            $table->date('remicao_inicio')->nullable();
            $table->date('remicao_fim')->nullable();
            $table->date('liberacao_inicio')->nullable();
            $table->date('liberacao_fim')->nullable();
            $table->string('dias_semana', 50)->nullable();
            $table->json('dias_semana_json')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->datetime('data_ativo')->nullable();
            $table->datetime('data_alterado')->nullable();
            $table->datetime('data_inativo')->nullable();
            $table->datetime('importado_em')->nullable();
            $table->integer('importado_por')->nullable();
            $table->timestamps();

            $table->unique(['ipen', 'status'], 'uq_interno_laboral_ipen');
            $table->index('status', 'idx_laboral_status');
            $table->index('ipen', 'idx_laboral_ipen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos_laboral');
    }
};
