<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PucCatalogoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Desactivar llaves foráneas para poder usar truncate
        Schema::disableForeignKeyConstraints();

        // Clear existing data
        DB::table('puc_catalogo')->truncate();

        // 2. Volver a activarlas
        Schema::enableForeignKeyConstraints();

        $path = database_path('data/puc_codes.php');
        
        if (!file_exists($path)) {
            $this->command->warn("File not found: $path. Using basic fallback list.");
            $this->seedBasicList();
            return;
        }

        $puc = require $path;
        
        $this->command->info('Seeding PUC codes... found ' . count($puc) . ' entries.');

        $chunks = array_chunk($puc, 500);
        
        foreach ($chunks as $chunk) {
            $dataToInsert = [];
            foreach ($chunk as $cuenta) {
                $dataToInsert[] = array_merge($cuenta, [
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('puc_catalogo')->insert($dataToInsert);
        }
    }

    private function seedBasicList(): void
    {
        $basicPuc = [
            ['codigo' => '1', 'nombre' => 'ACTIVO', 'nivel' => 1, 'tipo' => 'clase'],
            ['codigo' => '11', 'nombre' => 'DISPONIBLE', 'nivel' => 2, 'tipo' => 'grupo'],
            // Puedes agregar más aquí si el archivo no existe
        ];

        foreach ($basicPuc as $cuenta) {
            DB::table('puc_catalogo')->updateOrInsert(
                ['codigo' => $cuenta['codigo']],
                array_merge($cuenta, [
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}