<?php

namespace Database\Seeders;

use App\Models\Permissao;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Seeder;

class AclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            'censura' => 'Censura',
            'almoxarifado' => 'Almoxarifado',
            'laboral' => 'Laboral',
            'seg_trab' => 'Segurança do Trabalho',
            'rh' => 'Recursos Humanos',
            'coord' => 'Coordenação',
            'eclusa' => 'Eclusa',
            'direcao' => 'Direção',
            'portaria' => 'Portaria',
            'ti' => 'Tecnologia da Informação',
            'serralheria' => 'Serralheria',
            'escola' => 'Escola',
            'carga' => 'Carga / Logística',
            'industria' => 'Indústria',
            'juridico' => 'Jurídico',
            'cozinha' => 'Cozinha',
            'manutencao' => 'Manutenção',
        ];

        foreach ($setores as $slug => $nome) {
            $setor = Setor::query()->updateOrCreate(
                ['slug' => $slug],
                ['nome' => $nome, 'descricao' => $nome, 'ativo' => true]
            );

            Permissao::query()->updateOrCreate(
                ['codigo' => 'setor:' . $slug],
                [
                    'parent_id' => null,
                    'setor_id' => $setor->id,
                    'nome' => $nome,
                    'tipo' => 'setor',
                    'slug' => $slug,
                    'descricao' => 'Acesso ao setor ' . $nome,
                    'ordem' => 10,
                    'ativo' => true,
                ]
            );
        }

        $adminSection = Permissao::query()->updateOrCreate(
            ['codigo' => 'menu:administracao'],
            [
                'parent_id' => null,
                'setor_id' => null,
                'nome' => 'Administração',
                'tipo' => 'menu',
                'slug' => 'administracao',
                'descricao' => 'Grupo administrativo do sistema',
                'ordem' => 1,
                'ativo' => true,
            ]
        );

        $adminUsersModule = Permissao::query()->updateOrCreate(
            ['codigo' => 'modulo:administracao.usuarios'],
            [
                'parent_id' => $adminSection->id,
                'setor_id' => null,
                'nome' => 'Usuários',
                'tipo' => 'modulo',
                'slug' => 'usuarios',
                'descricao' => 'Gerenciamento de usuários e ACL',
                'ordem' => 2,
                'ativo' => true,
            ]
        );

        Permissao::query()->updateOrCreate(
            ['codigo' => 'pagina:administracao.usuarios.index'],
            [
                'parent_id' => $adminUsersModule->id,
                'setor_id' => null,
                'nome' => 'Listagem de usuários',
                'tipo' => 'pagina',
                'slug' => 'index',
                'descricao' => 'Página de listagem de usuários',
                'ordem' => 3,
                'ativo' => true,
            ]
        );

        foreach ([
            'criar' => 'Criar usuários',
            'editar' => 'Editar usuários',
            'permissoes' => 'Gerenciar permissões dos usuários',
        ] as $slug => $nome) {
            Permissao::query()->updateOrCreate(
                ['codigo' => 'acao:administracao.usuarios.' . $slug],
                [
                    'parent_id' => $adminUsersModule->id,
                    'setor_id' => null,
                    'nome' => $nome,
                    'tipo' => 'acao',
                    'slug' => $slug,
                    'descricao' => $nome,
                    'ordem' => 4,
                    'ativo' => true,
                ]
            );
        }

        User::query()
            ->where(function ($query) {
                $query->where('login', 'admin')
                    ->orWhere('email', 'admin@sigep.local');
            })
            ->update(['is_system_admin' => true, 'is_active' => true]);
    }
}
