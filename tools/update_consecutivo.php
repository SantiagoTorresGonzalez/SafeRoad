<?php
/**
 * Actualizar Consecutivo
 */
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Consecutivo;

$cons = Consecutivo::where('tipo_documento', 'Cuenta de Cobro')->where('activo', true)->first();

if ($cons) {
    $cons->numero_final = 9999;
    $cons->vigencia_fin = now()->addYear();
    $cons->prefijo = 'CC';
    $cons->save();
    echo "Consecutivo actualizado: CC0001-CC9999, vigencia hasta " . $cons->vigencia_fin->format('d/m/Y') . "\n";
} else {
    $cons = Consecutivo::create([
        'tipo_documento' => 'Cuenta de Cobro',
        'prefijo' => 'CC',
        'numero_inicial' => 1,
        'numero_final' => 9999,
        'numero_actual' => 0,
        'vigencia_inicio' => now(),
        'vigencia_fin' => now()->addYear(),
        'resolucion' => 'Resolución Interna 2025',
        'activo' => true,
    ]);
    echo "Consecutivo creado: CC0001-CC9999\n";
}
