<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Creando Consecutivo de Prueba ===\n\n";

$consecutivo = DB::table('consecutivos')->insertGetId([
    'tipo_documento' => 'Cuenta de Cobro',
    'resolucion' => 'AUTO-2025',
    'prefijo' => 'CC',
    'numero_inicial' => 1,
    'numero_final' => 9999,
    'numero_actual' => 1,
    'vigencia_inicio' => now()->toDateString(),
    'vigencia_fin' => now()->addYear()->toDateString(),
    'activo' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Consecutivo creado con ID: {$consecutivo}\n";
echo "   Resolución: AUTO-2025\n";
echo "   Prefijo: CC\n";
echo "   Rango: 1 - 9999\n";
echo "   Vigencia: " . now()->toDateString() . " hasta " . now()->addYear()->toDateString() . "\n";
