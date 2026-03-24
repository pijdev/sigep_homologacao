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
        Schema::create('internos', function (Blueprint $table) {
            $table->integer('ipen')->primary();
            $table->string('nome', 255);
            $table->string('nome_social', 255);
            $table->string('cpf', 14)->nullable();
            $table->enum('lgbt', ['S', 'N'])->default('N')->nullable();
            $table->string('apelido', 100)->nullable();
            $table->enum('forma_pagamento', ['Pix', 'Salário'])->default('Pix')->nullable();
            $table->string('situacao', 100)->nullable();
            $table->string('ala', 50)->nullable();
            $table->string('galeria', 50)->nullable();
            $table->string('bloco', 50)->nullable();
            $table->integer('piso')->nullable();
            $table->string('tipo_residencia', 255)->nullable();
            $table->integer('res')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->enum('regalia', ['S', 'N'])->default('N');
            $table->enum('regalia_galeria', ['S', 'N'])->default('N');
            $table->enum('cor_roupa', ['Laranja', 'Verde'])->nullable();
            $table->string('regalia_setor', 255)->nullable();
            $table->integer('regalia_kit')->nullable();
            $table->datetime('data_ativo')->nullable();
            $table->datetime('data_alterado')->nullable();
            $table->datetime('data_inativo')->nullable();
            $table->integer('kit');
            $table->enum('tamanho_kit', ['P', 'M', 'G', 'G1', 'G2', 'G3'])->default('G');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos');
    }
};
