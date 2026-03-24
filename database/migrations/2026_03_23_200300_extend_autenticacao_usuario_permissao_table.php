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
        if (! Schema::hasTable('autenticacao_usuario_permissao')) {
            Schema::create('autenticacao_usuario_permissao', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('permissao_id')->constrained('autenticacao_permissoes')->cascadeOnDelete();
                $table->foreignId('unidade_id')->nullable()->constrained('unidades_prisionais')->nullOnDelete();
                $table->enum('nivel', ['read', 'write', 'owner'])->default('read');
                $table->boolean('permitido')->default(true);
                $table->text('observacao')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'permissao_id', 'unidade_id'], 'auth_usuario_perm_unidade_unique');
                $table->index(['user_id', 'nivel']);
            });

            return;
        }

        Schema::table('autenticacao_usuario_permissao', function (Blueprint $table) {
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'permissao_id')) {
                $table->foreignId('permissao_id')->after('user_id')->constrained('autenticacao_permissoes')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'unidade_id')) {
                $table->foreignId('unidade_id')->nullable()->after('permissao_id')->constrained('unidades_prisionais')->nullOnDelete();
            }
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'nivel')) {
                $table->enum('nivel', ['read', 'write', 'owner'])->default('read')->after('unidade_id');
            }
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'permitido')) {
                $table->boolean('permitido')->default(true)->after('nivel');
            }
            if (! Schema::hasColumn('autenticacao_usuario_permissao', 'observacao')) {
                $table->text('observacao')->nullable()->after('permitido');
            }
        });

        Schema::table('autenticacao_usuario_permissao', function (Blueprint $table) {
            $table->unique(['user_id', 'permissao_id', 'unidade_id'], 'auth_usuario_perm_unidade_unique');
            $table->index(['user_id', 'nivel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('autenticacao_usuario_permissao')) {
            return;
        }

        Schema::table('autenticacao_usuario_permissao', function (Blueprint $table) {
            $table->dropUnique('auth_usuario_perm_unidade_unique');
            $table->dropIndex(['user_id', 'nivel']);
            if (Schema::hasColumn('autenticacao_usuario_permissao', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
            if (Schema::hasColumn('autenticacao_usuario_permissao', 'permissao_id')) {
                $table->dropConstrainedForeignId('permissao_id');
            }
            if (Schema::hasColumn('autenticacao_usuario_permissao', 'unidade_id')) {
                $table->dropConstrainedForeignId('unidade_id');
            }
            $table->dropColumn(['nivel', 'permitido', 'observacao']);
        });
    }
};
