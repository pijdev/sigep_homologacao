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
        Schema::create('internos_historico', function (Blueprint $table) {
            $table->id();
            $table->timestamp('data_importacao')->useCurrent()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('registros_novos')->default(0);
            $table->integer('registros_atualizados')->default(0);
            $table->integer('registros_inativados')->default(0);
            $table->integer('total_importados')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos_historico');
    }
};
