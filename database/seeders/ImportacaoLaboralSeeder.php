<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportacaoLaboralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar setor Importação Laboral
        $setorId = DB::table('autenticacao_setores')->insertGetId([
            'slug' => 'importacao_laboral',
            'nome' => 'Importação Laboral',
            'descricao' => 'Módulo de importação de dados de remições por trabalho (Relatório 6-4)',
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
                'codigo' => 'menu.administracao.importacao_laboral',
                'nome' => 'Importação Laboral',
                'tipo' => 'menu',
                'slug' => 'administracao/importacao-laboral',
                'descricao' => 'Menu principal do módulo de importação de remições por trabalho',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Módulo
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'modulo.importacao_laboral',
                'nome' => 'Módulo Importação Laboral',
                'tipo' => 'modulo',
                'slug' => 'importacao-laboral',
                'descricao' => 'Módulo completo de importação de remições por trabalho',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Página principal
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'pagina.importacao_laboral.relatorio_64',
                'nome' => 'Relatório 6-4 (Trabalho)',
                'tipo' => 'pagina',
                'slug' => 'importacao-laboral/relatorio-64',
                'descricao' => 'Página de importação do relatório 6-4 (Remições por Trabalho)',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ação de processar
            [
                'parent_id' => null,
                'setor_id' => $setorId,
                'codigo' => 'acao.importacao_laboral.processar',
                'nome' => 'Processar Importação Laboral',
                'tipo' => 'acao',
                'slug' => 'importacao-laboral/processar',
                'descricao' => 'Ação de processar importação de dados de remições por trabalho',
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

        $this->command->info('Permissões ACL para Importação Laboral (6-4) criadas com sucesso!');
    }
}
