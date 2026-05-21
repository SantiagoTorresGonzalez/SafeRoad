<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class SafeRoadUsersSeeder extends Seeder
{
    public function run(): void
    {
        $rolAutoridad = Role::where('name', 'autoridad_municipal')->first();
        $rolAnalista  = Role::where('name', 'analista')->first();

        // Autoridades municipales
        $autoridades = [
            ['name' => 'Autoridad Chía',       'email' => 'autoridad.chia@saferoad.co'],
            ['name' => 'Autoridad Zipaquirá',   'email' => 'autoridad.zipaquira@saferoad.co'],
            ['name' => 'Autoridad Cajicá',      'email' => 'autoridad.cajica@saferoad.co'],
            ['name' => 'Autoridad Tocancipá',   'email' => 'autoridad.tocancipa@saferoad.co'],
            ['name' => 'Autoridad Cota',        'email' => 'autoridad.cota@saferoad.co'],
        ];

        foreach ($autoridades as $datos) {
            $user = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name'     => $datos['name'],
                    'password' => Hash::make('SafeRoad2024*'),
                ]
            );
            if ($rolAutoridad) {
                $user->role()->associate($rolAutoridad);
                $user->save();
            }
        }

        // Analista
        $analista = User::updateOrCreate(
            ['email' => 'analista@saferoad.co'],
            [
                'name'     => 'Analista SafeRoad',
                'password' => Hash::make('SafeRoad2024*'),
            ]
        );
        if ($rolAnalista) {
            $analista->role()->associate($rolAnalista);
            $analista->save();
        }

        // Planificador territorial
        $planificador = User::updateOrCreate(
            ['email' => 'planificador@saferoad.co'],
            [
                'name'     => 'Planificador Territorial',
                'password' => Hash::make('SafeRoad2024*'),
            ]
        );
        $rolPlanificador = Role::where('name', 'planificador_territorial')->first();
        if ($rolPlanificador) {
            $planificador->role()->associate($rolPlanificador);
            $planificador->save();
        }

        $this->command->info('Usuarios de SafeRoad SC creados correctamente.');
        $this->command->info('Contraseña de todos los usuarios: SafeRoad2024*');
    }
}
