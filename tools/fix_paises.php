<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verificando datos con filtro activo ===\n\n";

echo "Países (todos): " . DB::table('paises')->count() . "\n";
echo "Países (activos): " . DB::table('paises')->where('activo', true)->count() . "\n";

echo "\nDepartamentos (todos): " . DB::table('departamentos')->count() . "\n";

// Verificar estructura de la tabla paises
echo "\n=== Muestra de países ===\n";
$paises = DB::table('paises')->limit(5)->get();
foreach ($paises as $p) {
    echo json_encode($p) . "\n";
}

// Si no hay países activos, activarlos
if (DB::table('paises')->where('activo', true)->count() == 0) {
    echo "\n⚠️  No hay países activos. Activando todos...\n";
    DB::table('paises')->update(['activo' => true]);
    echo "✅ Países activados: " . DB::table('paises')->where('activo', true)->count() . "\n";
}
