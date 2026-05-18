<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SafeRoadRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'        => 'autoridad_municipal',
                'description' => 'Funcionario de Secretaría de Movilidad. Valida y gestiona reportes ciudadanos.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'analista',
                'description' => 'Investigador o académico. Solo lectura y exportación de datos.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['name' => $rol['name']],
                $rol
            );
        }

        $this->command->info('Roles de SafeRoad SC creados correctamente.');
    }
}