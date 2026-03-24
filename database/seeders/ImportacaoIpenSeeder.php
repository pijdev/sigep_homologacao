<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportacaoIpenSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar setor Importação iPEN
        $setorId = DB::table('autenticacao_setores')->insertGetId([
            'slug' => 'importacao_ipen',
            'nome' => 'Importação iPEN',
            'descricao' => 'Módulo de importação de dados do relatório 1-8 do iPEN',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar permissões ACL
        $permissoes = [
            // Menu principal
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'menu.administracao.importacao_ipen',
                'nome' => 'Importação iPEN',
                'tipo' => 'menu',
                'slug' => 'administracao/importacao-ipen',
                'descricao' => 'Menu principal do módulo de importação iPEN',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Módulo
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'modulo.importacao_ipen',
                'nome' => 'Módulo Importação iPEN',
                'tipo' => 'modulo',
                'slug' => 'importacao-ipen',
                'descricao' => 'Módulo completo de importação iPEN',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Página principal
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'pagina.importacao_ipen.relatorio_18',
                'nome' => 'Relatório 1-8 iPEN',
                'tipo' => 'pagina',
                'slug' => 'importacao-ipen/relatorio-18',
                'descricao' => 'Página de importação do relatório 1-8 do iPEN',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ação de processar
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'acao.importacao_ipen.processar',
                'nome' => 'Processar Importação',
                'tipo' => 'acao',
                'slug' => 'importacao-ipen/processar',
                'descricao' => 'Ação de processar importação de dados',
                'ordem' => 4,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('autenticacao_permissoes')->insert($permissoes);

        // Conceder permissões ao admin
        $adminId = DB::table('users')->where('is_system_admin', true)->value('id');
        if ($adminId) {
            foreach ($permissoes as $permissao) {
                $permissaoId = DB::table('autenticacao_permissoes')
                    ->where('codigo', $permissao['codigo'])
                    ->value('id');

                DB::table('autenticacao_usuario_permissao')->insert([
                    'user_id' => $adminId,
                    'permissao_id' => $permissaoId,
                    'nivel' => 'owner',
                    'permitido' => true,
                    'observacao' => 'Permissão automática para admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Permissões ACL para Importação iPEN criadas com sucesso!');
    }
}
