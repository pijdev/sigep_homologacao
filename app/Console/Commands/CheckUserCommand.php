<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Signature('check:user {email}')]
#[Description('Check if user exists and show details')]
class CheckUserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("Usuário encontrado:");
            $this->info("Nome: " . $user->name);
            $this->info("Email: " . $user->email);
            $this->info("Admin: " . ($user->is_system_admin ? 'Sim' : 'Não'));
            $this->info("ID: " . $user->id);
            $this->info("Criado em: " . $user->created_at);

            // Check permissions
            $permissions = DB::table('autenticacao_usuario_permissao')
                ->join('autenticacao_permissoes', 'autenticacao_permissoes.id', '=', 'autenticacao_usuario_permissao.permissao_id')
                ->where('autenticacao_usuario_permissao.user_id', $user->id)
                ->get(['autenticacao_permissoes.codigo', 'autenticacao_permissoes.nome']);

            $this->info("Permissões:");
            foreach ($permissions as $perm) {
                $this->info("  - " . $perm->codigo . " (" . $perm->nome . ")");
            }
        } else {
            $this->error("Usuário não encontrado: " . $email);
        }
    }
}
