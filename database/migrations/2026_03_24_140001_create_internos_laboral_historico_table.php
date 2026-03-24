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
        Schema::create('internos_laboral_historico', function (Blueprint $table) {
            $table->id();
            $table->datetime('data_importacao');
            $table->integer('total_importados')->default(0);
            $table->integer('registros_novos')->default(0);
            $table->integer('registros_atualizados')->default(0);
            $table->integer('registros_inativados')->default(0);
            $table->integer('internos_encontrados')->default(0);
            $table->integer('internos_nao_encontrados')->default(0);
            $table->integer('importado_por')->nullable();
            $table->timestamps();

            $table->index('data_importacao', 'idx_laboral_hist_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos_laboral_historico');
    }
};
