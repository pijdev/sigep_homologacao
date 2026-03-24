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
        Schema::create('internos_laboral_historico_detalhado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_historico')->nullable();
            $table->unsignedBigInteger('id_interno_laboral')->nullable();
            $table->integer('ipen');
            $table->string('campo', 80);
            $table->text('valor_antigo')->nullable();
            $table->text('valor_novo')->nullable();
            $table->datetime('data_alteracao')->useCurrent();
            $table->enum('operacao', ['INSERIDO', 'ATUALIZADO', 'INATIVADO', 'REATIVADO', 'EXCLUIDO']);
            $table->integer('alterado_por')->nullable();
            $table->timestamps();

            $table->index('id_historico', 'idx_laboral_det_hist');
            $table->index('ipen', 'idx_laboral_det_ipen');
            $table->index('data_alteracao', 'idx_laboral_det_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos_laboral_historico_detalhado');
    }
};
