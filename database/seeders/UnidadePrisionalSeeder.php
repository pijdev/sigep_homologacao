<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadePrisionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ler o arquivo CSV
        $csvFile = base_path('temp/unidades.csv');
        $lines = file($csvFile);

        // Remover o cabeçalho
        array_shift($lines);

        foreach ($lines as $line) {
            // O CSV usa ; como separador e " para delimitar strings
            $row = str_getcsv($line, ';');
            $id = trim($row[0]);
            $nome = trim($row[1], '"');

            DB::table('unidades_prisionais')->updateOrInsert(
                ['id' => $id],
                [
                    'nome' => $nome,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Unidades prisionais importadas com sucesso!');
    }
}
