<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;

#[Signature('list:users')]
#[Description('List all users in the system')]
class ListUsersCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        if ($users->count() === 0) {
            $this->error("Nenhum usuário encontrado no sistema.");
            return;
        }

        $this->info("Usuários encontrados (" . $users->count() . "):");
        $this->info(str_repeat("-", 80));

        foreach ($users as $user) {
            $this->info(sprintf(
                "ID: %d | Nome: %s | Email: %s | Admin: %s",
                $user->id,
                $user->name,
                $user->email,
                $user->is_system_admin ? 'Sim' : 'Não'
            ));
        }

        $this->info(str_repeat("-", 80));
        $this->info("Total: " . $users->count() . " usuários");
    }
}
