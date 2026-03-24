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
        if (! Schema::hasTable('autenticacao_permissoes')) {
            Schema::create('autenticacao_permissoes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('autenticacao_permissoes')->nullOnDelete();
                $table->foreignId('setor_id')->nullable()->constrained('autenticacao_setores')->nullOnDelete();
                $table->string('codigo', 160)->unique();
                $table->string('nome', 160);
                $table->string('tipo', 30);
                $table->string('slug', 80);
                $table->string('descricao', 255)->nullable();
                $table->unsignedInteger('ordem')->default(0);
                $table->boolean('ativo')->default(true);
                $table->timestamps();
                $table->index(['tipo', 'ativo']);
                $table->index(['setor_id', 'tipo']);
            });

            return;
        }

        Schema::table('autenticacao_permissoes', function (Blueprint $table) {
            if (! Schema::hasColumn('autenticacao_permissoes', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('autenticacao_permissoes')->nullOnDelete();
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'setor_id')) {
                $table->foreignId('setor_id')->nullable()->after('parent_id')->constrained('autenticacao_setores')->nullOnDelete();
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'codigo')) {
                $table->string('codigo', 160)->unique()->after('setor_id');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'nome')) {
                $table->string('nome', 160)->after('codigo');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'tipo')) {
                $table->string('tipo', 30)->after('nome');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'slug')) {
                $table->string('slug', 80)->after('tipo');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'descricao')) {
                $table->string('descricao', 255)->nullable()->after('slug');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'ordem')) {
                $table->unsignedInteger('ordem')->default(0)->after('descricao');
            }
            if (! Schema::hasColumn('autenticacao_permissoes', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('ordem');
            }
        });

        Schema::table('autenticacao_permissoes', function (Blueprint $table) {
            $table->index(['tipo', 'ativo']);
            $table->index(['setor_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('autenticacao_permissoes')) {
            return;
        }

        Schema::table('autenticacao_permissoes', function (Blueprint $table) {
            if (Schema::hasColumn('autenticacao_permissoes', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
            if (Schema::hasColumn('autenticacao_permissoes', 'setor_id')) {
                $table->dropConstrainedForeignId('setor_id');
            }
            $table->dropIndex(['tipo', 'ativo']);
            $table->dropIndex(['setor_id', 'tipo']);
            $table->dropColumn([
                'codigo',
                'nome',
                'tipo',
                'slug',
                'descricao',
                'ordem',
                'ativo',
            ]);
        });
    }
};
