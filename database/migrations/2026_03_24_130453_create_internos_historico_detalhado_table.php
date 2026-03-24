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
        Schema::create('internos_historico_detalhado', function (Blueprint $table) {
            $table->id();
            $table->integer('ipen');
            $table->string('campo', 50);
            $table->text('valor_antigo')->nullable();
            $table->text('valor_novo')->nullable();
            $table->dateTime('data_alteracao')->useCurrent()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('operacao', ['INSERIDO', 'ATUALIZADO', 'INATIVADO']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos_historico_detalhado');
    }
};
