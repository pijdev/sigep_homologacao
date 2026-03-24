<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Signature('grant:permission {email} {permission_code}')]
#[Description('Grant permission to user')]
class GrantPermissionCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $permissionCode = $this->argument('permission_code');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Usuário não encontrado: " . $email);
            return;
        }

        $permission = DB::table('autenticacao_permissoes')
            ->where('codigo', $permissionCode)
            ->first();

        if (!$permission) {
            $this->error("Permissão não encontrada: " . $permissionCode);
            return;
        }

        // Check if already granted
        $existing = DB::table('autenticacao_usuario_permissao')
            ->where('user_id', $user->id)
            ->where('permissao_id', $permission->id)
            ->first();

        if ($existing) {
            $this->error("Permissão já concedida ao usuário.");
            return;
        }

        // Grant permission
        DB::table('autenticacao_usuario_permissao')->insert([
            'user_id' => $user->id,
            'permissao_id' => $permission->id,
            'nivel' => 'owner',
            'permitido' => true,
            'observacao' => 'Permissão concedida via command line',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Permissão concedida com sucesso!");
        $this->info("Usuário: " . $user->name . " (" . $user->email . ")");
        $this->info("Permissão: " . $permission->codigo . " (" . $permission->nome . ")");
    }
}
