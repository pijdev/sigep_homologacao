<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioTesteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Atualizar usuários existentes com login
        $usuarios = \App\Models\User::whereIn('email', ['admin@sigep.local', 'usuario@sigep.local'])->get();

        foreach ($usuarios as $user) {
            $user->login = $user->email === 'admin@sigep.local' ? 'admin' : 'usuario';
            $user->save();

            // Verificar se já tem vínculo com a unidade 8019
            $vinculoExistente = \App\Models\UsuarioUnidade::where('user_id', $user->id)
                ->where('unidade_id', 8019)
                ->first();

            if (!$vinculoExistente) {
                // Vincular à unidade padrão (8019 - FLORIANÓPOLIS - PENITENCIÁRIA)
                \App\Models\UsuarioUnidade::create([
                    'user_id' => $user->id,
                    'unidade_id' => 8019,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Usuários atualizados e vinculados com sucesso!');
    }
}
