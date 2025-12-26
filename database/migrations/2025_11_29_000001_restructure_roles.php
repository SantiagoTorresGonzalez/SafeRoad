<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Limpiar roles antiguos
        Role::truncate();

        // 2. Crear nuevos roles
        $roles = [
            'admin_programa' => 'Admin del Programa - Control total, reportes, usuarios',
            'auxiliar' => 'Auxiliar - Crea cuentas, ve clientes, historial',
            'administrador' => 'Administrador - Aprueba, supervisa y verifica',
            'tesoreria' => 'Tesoreria - Valida montos, paga y notifica cliente',
        ];

        $roleIds = [];
        foreach ($roles as $name => $desc) {
            $role = Role::create([
                'name' => $name,
                'description' => $desc,
            ]);
            $roleIds[$name] = $role->id;
        }

        // 3. Asignar usuario espec�fico a admin_programa (usar consultas directas)
        $adminEmail = 'daniel00250@hotmail.com';
        $exists = DB::table('users')->where('email', $adminEmail)->exists();

        if ($exists) {
            DB::table('users')->where('email', $adminEmail)->update(['role_id' => $roleIds['admin_programa']]);
        }

        // 4. Resetear otros usuarios
        DB::table('users')->where('email', '!=', $adminEmail)->update(['role_id' => null]);

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversible
    }
};
