<?php
/**
 * Script de Prueba Completa de Funcionalidades
 * ==============================================
 * Prueba: Terceros, Soportes, Consecutivos, Usuarios, Roles y Permisos
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Tercero;
use App\Models\Consecutivo;
use App\Models\CuentaCobro;
use App\Models\Contrato;
use App\Models\Soporte;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat('=', 70) . "\n";
echo "   🧪 PRUEBA COMPLETA DE FUNCIONALIDADES - CUENTAS DE COBRO\n";
echo str_repeat('=', 70) . "\n\n";

$errores = [];
$exitos = [];

// ============================================
// 1. VERIFICAR ROLES EXISTENTES
// ============================================
echo "📋 1. VERIFICANDO ROLES DEL SISTEMA...\n";
echo str_repeat('-', 50) . "\n";

$rolesRequeridos = ['auxiliar', 'administrador', 'tesoreria', 'admin_programa', 'super_admin'];
$rolesExistentes = Role::pluck('name')->toArray();

foreach ($rolesRequeridos as $rol) {
    if (in_array($rol, $rolesExistentes)) {
        echo "   ✅ Rol '$rol' existe\n";
        $exitos[] = "Rol $rol existe";
    } else {
        echo "   ❌ Rol '$rol' NO existe\n";
        $errores[] = "Rol $rol faltante";
    }
}
echo "\n";

// ============================================
// 2. CREAR USUARIOS DE PRUEBA
// ============================================
echo "👥 2. CREANDO USUARIOS DE PRUEBA...\n";
echo str_repeat('-', 50) . "\n";

$usuariosPrueba = [
    ['name' => 'Auxiliar Prueba', 'email' => 'auxiliar_test@test.com', 'role' => 'auxiliar'],
    ['name' => 'Administrador Prueba', 'email' => 'admin_test@test.com', 'role' => 'administrador'],
    ['name' => 'Tesorería Prueba', 'email' => 'tesoreria_test@test.com', 'role' => 'tesoreria'],
    ['name' => 'Admin Programa Prueba', 'email' => 'admin_programa_test@test.com', 'role' => 'admin_programa'],
];

foreach ($usuariosPrueba as $datos) {
    $usuario = User::where('email', $datos['email'])->first();
    
    if (!$usuario) {
        $role = Role::where('name', $datos['role'])->first();
        
        if ($role) {
            $usuario = User::create([
                'name' => $datos['name'],
                'email' => $datos['email'],
                'password' => Hash::make('Test123!'),
                'role_id' => $role->id,
            ]);
            echo "   ✅ Usuario '{$datos['email']}' creado con rol '{$datos['role']}'\n";
            $exitos[] = "Usuario {$datos['email']} creado";
        } else {
            echo "   ❌ No se pudo crear usuario '{$datos['email']}' - Rol '{$datos['role']}' no existe\n";
            $errores[] = "No se pudo crear usuario {$datos['email']}";
        }
    } else {
        echo "   ℹ️  Usuario '{$datos['email']}' ya existe\n";
    }
}
echo "\n";

// ============================================
// 3. VERIFICAR TERCEROS
// ============================================
echo "🏢 3. VERIFICANDO FUNCIONALIDAD DE TERCEROS...\n";
echo str_repeat('-', 50) . "\n";

// Crear tercero de prueba - Persona Natural
$terceroNatural = Tercero::where('identificacion', '1234567890')->first();
if (!$terceroNatural) {
    try {
        $terceroNatural = Tercero::create([
            'tipo_persona' => 'natural',
            'tipo_identificacion' => 'Cédula de Ciudadanía',
            'identificacion' => '1234567890',
            'nombre_completo' => 'Juan Pérez García (Prueba)',
            'email' => 'juan.perez@test.com',
            'telefono' => '3001234567',
            'direccion' => 'Calle 123 # 45-67',
            'pais' => 'Colombia',
            'departamento' => 'Cundinamarca',
            'ciudad' => 'Bogotá',
        ]);
        echo "   ✅ Tercero Natural creado: {$terceroNatural->nombre_completo}\n";
        $exitos[] = "Tercero Natural creado";
    } catch (\Exception $e) {
        echo "   ❌ Error creando Tercero Natural: " . $e->getMessage() . "\n";
        $errores[] = "Error creando Tercero Natural: " . $e->getMessage();
    }
} else {
    echo "   ℹ️  Tercero Natural ya existe: {$terceroNatural->nombre_completo}\n";
}

// Crear tercero de prueba - Persona Jurídica
$terceroJuridico = Tercero::where('identificacion', '900123456')->first();
if (!$terceroJuridico) {
    try {
        $terceroJuridico = Tercero::create([
            'tipo_persona' => 'juridica',
            'tipo_identificacion' => 'NIT',
            'identificacion' => '900123456',
            'dv' => '7',
            'razon_social' => 'Empresa de Prueba S.A.S.',
            'email' => 'empresa@test.com',
            'telefono' => '6012345678',
            'direccion' => 'Carrera 10 # 20-30, Oficina 501',
            'pais' => 'Colombia',
            'departamento' => 'Antioquia',
            'ciudad' => 'Medellín',
        ]);
        echo "   ✅ Tercero Jurídico creado: {$terceroJuridico->razon_social}\n";
        $exitos[] = "Tercero Jurídico creado";
    } catch (\Exception $e) {
        echo "   ❌ Error creando Tercero Jurídico: " . $e->getMessage() . "\n";
        $errores[] = "Error creando Tercero Jurídico: " . $e->getMessage();
    }
} else {
    echo "   ℹ️  Tercero Jurídico ya existe: {$terceroJuridico->razon_social}\n";
}

$totalTerceros = Tercero::count();
echo "   📊 Total de terceros en el sistema: {$totalTerceros}\n\n";

// ============================================
// 4. VERIFICAR CONSECUTIVOS
// ============================================
echo "🔢 4. VERIFICANDO CONSECUTIVOS...\n";
echo str_repeat('-', 50) . "\n";

$estadoConsecutivo = Consecutivo::getEstadoConsecutivo('Cuenta de Cobro');

if ($estadoConsecutivo['valido']) {
    $cons = $estadoConsecutivo['consecutivo'];
    echo "   ✅ Consecutivo válido encontrado:\n";
    echo "      - Prefijo: {$cons->prefijo}\n";
    echo "      - Rango: {$cons->numero_inicial} - {$cons->numero_final}\n";
    echo "      - Actual: {$cons->numero_actual}\n";
    echo "      - Disponibles: {$estadoConsecutivo['disponibles']}\n";
    echo "      - Uso: {$estadoConsecutivo['porcentaje_uso']}%\n";
    echo "      - Vigencia: {$cons->vigencia_inicio->format('d/m/Y')} - {$cons->vigencia_fin->format('d/m/Y')}\n";
    echo "      - Días restantes: {$estadoConsecutivo['dias_restantes']}\n";
    
    if (!empty($estadoConsecutivo['alertas'])) {
        echo "      - ⚠️ Alertas:\n";
        foreach ($estadoConsecutivo['alertas'] as $alerta) {
            echo "        * [{$alerta['tipo']}] {$alerta['titulo']}: {$alerta['mensaje']}\n";
        }
    }
    $exitos[] = "Consecutivo válido";
} else {
    echo "   ❌ No hay consecutivo válido: {$estadoConsecutivo['mensaje']}\n";
    
    // Crear uno de prueba
    echo "   🔧 Creando consecutivo de prueba...\n";
    try {
        $nuevoConsecutivo = Consecutivo::create([
            'tipo_documento' => 'Cuenta de Cobro',
            'prefijo' => 'CC',
            'numero_inicial' => 1,
            'numero_final' => 9999,
            'numero_actual' => 0,
            'vigencia_inicio' => now()->startOfYear(),
            'vigencia_fin' => now()->endOfYear(),
            'resolucion' => 'Resolución Prueba 2025',
            'activo' => true,
        ]);
        echo "   ✅ Consecutivo de prueba creado: CC0001-CC9999\n";
        $exitos[] = "Consecutivo creado";
    } catch (\Exception $e) {
        echo "   ❌ Error creando consecutivo: " . $e->getMessage() . "\n";
        $errores[] = "Error creando consecutivo";
    }
}
echo "\n";

// ============================================
// 5. VERIFICAR CONTRATOS
// ============================================
echo "📝 5. VERIFICANDO CONTRATOS...\n";
echo str_repeat('-', 50) . "\n";

$totalContratos = Contrato::count();
echo "   📊 Total de contratos: {$totalContratos}\n";

if ($totalContratos == 0) {
    echo "   🔧 Creando contrato de prueba...\n";
    try {
        Contrato::create([
            'numero' => 'CONT-2025-001',
            'tipo' => 'Prestación de servicios',
            'objeto' => 'Contrato de prueba para verificación del sistema',
            'valor' => 50000000,
            'fecha_inicio' => now()->startOfYear(),
            'fecha_fin' => now()->endOfYear(),
            'estado' => 'activo',
        ]);
        echo "   ✅ Contrato de prueba creado\n";
        $exitos[] = "Contrato de prueba creado";
    } catch (\Exception $e) {
        echo "   ❌ Error creando contrato: " . $e->getMessage() . "\n";
        $errores[] = "Error creando contrato";
    }
}
echo "\n";

// ============================================
// 6. VERIFICAR PERMISOS
// ============================================
echo "🔐 6. VERIFICANDO PERMISOS...\n";
echo str_repeat('-', 50) . "\n";

$permisosBase = [
    'create_cuenta_cobro',
    'view_own_cuenta_cobro',
    'edit_own_cuenta_cobro',
    'upload_documents',
    'approve_cuenta_cobro',
    'reject_cuenta_cobro',
    'view_reports',
    'manage_users',
    'manage_contracts',
];

foreach ($permisosBase as $permiso) {
    $existe = Permission::where('name', $permiso)->exists();
    if ($existe) {
        echo "   ✅ Permiso '$permiso' existe\n";
    } else {
        echo "   ⚠️  Permiso '$permiso' no existe\n";
    }
}
echo "\n";

// ============================================
// 7. VERIFICAR CUENTAS DE COBRO
// ============================================
echo "💰 7. VERIFICANDO CUENTAS DE COBRO...\n";
echo str_repeat('-', 50) . "\n";

$totalCuentas = CuentaCobro::count();
$cuentasPendientes = CuentaCobro::where('estado_aprobacion', 'pendiente')->count();
$cuentasAprobadas = CuentaCobro::where('estado_aprobacion', 'aprobado_tesoreria')->count();

echo "   📊 Total de cuentas de cobro: {$totalCuentas}\n";
echo "   📋 Pendientes de aprobación: {$cuentasPendientes}\n";
echo "   ✅ Aprobadas (listas para pago): {$cuentasAprobadas}\n";
echo "\n";

// ============================================
// 8. VERIFICAR CATÁLOGOS
// ============================================
echo "📚 8. VERIFICANDO CATÁLOGOS...\n";
echo str_repeat('-', 50) . "\n";

$catalogos = [
    'paises' => DB::table('paises')->count(),
    'departamentos' => DB::table('departamentos')->count(),
    'municipios' => DB::table('municipios')->count(),
    'responsabilidades_fiscales' => DB::table('responsabilidades_fiscales')->count(),
    'puc_catalogo' => DB::table('puc_catalogo')->count(),
    'productos_servicios' => DB::table('productos_servicios')->count(),
    'centros_costo' => DB::table('centros_costo')->count(),
];

foreach ($catalogos as $catalogo => $cantidad) {
    $estado = $cantidad > 0 ? "✅" : "⚠️";
    echo "   {$estado} {$catalogo}: {$cantidad} registros\n";
    
    if ($cantidad > 0) {
        $exitos[] = "Catálogo $catalogo poblado";
    } else {
        $errores[] = "Catálogo $catalogo vacío";
    }
}
echo "\n";

// ============================================
// 9. VERIFICAR SOPORTES/DOCUMENTOS
// ============================================
echo "📎 9. VERIFICANDO SISTEMA DE SOPORTES...\n";
echo str_repeat('-', 50) . "\n";

$totalSoportes = Soporte::count();
echo "   📊 Total de soportes en el sistema: {$totalSoportes}\n";

// Verificar directorio de storage
$storagePath = storage_path('app/public/soportes');
if (is_dir($storagePath)) {
    echo "   ✅ Directorio de soportes existe: {$storagePath}\n";
    $exitos[] = "Directorio de soportes existe";
} else {
    echo "   ⚠️  Directorio de soportes no existe (se creará al subir el primer archivo)\n";
}
echo "\n";

// ============================================
// RESUMEN FINAL
// ============================================
echo str_repeat('=', 70) . "\n";
echo "   📊 RESUMEN DE VERIFICACIÓN\n";
echo str_repeat('=', 70) . "\n\n";

echo "✅ Verificaciones exitosas: " . count($exitos) . "\n";
echo "❌ Errores encontrados: " . count($errores) . "\n\n";

if (!empty($errores)) {
    echo "📋 ERRORES A CORREGIR:\n";
    foreach ($errores as $i => $error) {
        echo "   " . ($i + 1) . ". {$error}\n";
    }
    echo "\n";
}

// ============================================
// CREDENCIALES DE PRUEBA
// ============================================
echo str_repeat('=', 70) . "\n";
echo "   🔑 CREDENCIALES DE USUARIOS DE PRUEBA\n";
echo str_repeat('=', 70) . "\n\n";

echo "Todos usan contraseña: Test123!\n\n";

$usuarios = User::with('role')
    ->whereIn('email', array_column($usuariosPrueba, 'email'))
    ->orWhere('email', 'daniel00250@hotmail.com')
    ->orWhere('email', 'superadmin@example.com')
    ->get();

echo str_pad('EMAIL', 40) . str_pad('ROL', 20) . "CONTRASEÑA\n";
echo str_repeat('-', 80) . "\n";

foreach ($usuarios as $u) {
    $rol = $u->role ? $u->role->name : 'Sin rol';
    $pass = in_array($u->email, ['daniel00250@hotmail.com']) ? 'cosita1225*' : 
            ($u->email === 'superadmin@example.com' ? 'SuperAdmin123!' : 'Test123!');
    echo str_pad($u->email, 40) . str_pad($rol, 20) . "{$pass}\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "   ✅ PRUEBA COMPLETADA\n";
echo str_repeat('=', 70) . "\n\n";
