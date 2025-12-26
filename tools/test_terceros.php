<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Estructura de tabla terceros ===\n\n";

$columns = DB::select("PRAGMA table_info('terceros')");
foreach ($columns as $col) {
    $nullable = $col->notnull ? 'NOT NULL' : 'NULLABLE';
    $default = $col->dflt_value !== null ? "default={$col->dflt_value}" : 'no default';
    echo sprintf("%-30s %-15s %-10s %s\n", $col->name, $col->type, $nullable, $default);
}

echo "\n=== Probando INSERT ===\n";

try {
    $id = DB::table('terceros')->insertGetId([
        'tipo_persona' => 'natural',
        'tipo_identificacion' => 'CC',
        'identificacion' => 'TEST' . time(),
        'nombre_completo' => 'Test User',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✅ INSERT exitoso con ID: {$id}\n";
    // Limpiar
    DB::table('terceros')->where('id', $id)->delete();
    echo "✅ Registro de prueba eliminado\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
