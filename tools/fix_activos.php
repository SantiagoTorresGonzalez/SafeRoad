<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verificando datos con filtros (activo=true) ===\n\n";

echo "Países activos: " . DB::table('paises')->where('activo', true)->count() . "\n";
echo "Responsabilidades Fiscales activas: " . DB::table('responsabilidades_fiscales')->where('activo', true)->count() . "\n";

// Si no hay responsabilidades activas, activarlas
$respActivas = DB::table('responsabilidades_fiscales')->where('activo', true)->count();
if ($respActivas == 0) {
    echo "\n⚠️  No hay responsabilidades fiscales activas. Activando todas...\n";
    DB::table('responsabilidades_fiscales')->update(['activo' => true]);
    echo "✅ Responsabilidades activadas: " . DB::table('responsabilidades_fiscales')->where('activo', true)->count() . "\n";
}

// Verificar PUC
$pucActivo = DB::table('puc_catalogo')->where('activo', true)->count();
echo "\nPUC Catálogo activo: " . $pucActivo . "\n";
if ($pucActivo == 0) {
    $pucTotal = DB::table('puc_catalogo')->count();
    echo "PUC total: " . $pucTotal . "\n";
    if ($pucTotal > 0) {
        DB::table('puc_catalogo')->update(['activo' => true]);
        echo "✅ PUC activado\n";
    }
}

// Verificar Productos/Servicios
$prodActivo = DB::table('productos_servicios')->where('activo', true)->count();
echo "\nProductos/Servicios activos: " . $prodActivo . "\n";
if ($prodActivo == 0) {
    $prodTotal = DB::table('productos_servicios')->count();
    echo "Productos total: " . $prodTotal . "\n";
    if ($prodTotal > 0) {
        DB::table('productos_servicios')->update(['activo' => true]);
        echo "✅ Productos activados\n";
    }
}

// Verificar Centros de Costo
$ccActivo = DB::table('centros_costos')->where('activo', true)->count();
echo "\nCentros de Costo activos: " . $ccActivo . "\n";

echo "\n=== Proceso completado ===\n";
echo "Recarga la página para ver los cambios.\n";
