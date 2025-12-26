<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verificando datos de ubicación ===\n\n";

// Usando DB directamente
echo "Países: " . DB::table('paises')->count() . "\n";
echo "Departamentos: " . DB::table('departamentos')->count() . "\n";
echo "Municipios: " . DB::table('municipios')->count() . "\n";

// Tarifas Rete ICA
try {
    echo "Tarifas Rete ICA: " . DB::table('tarifas_rete_ica')->count() . "\n";
} catch (\Exception $e) {
    echo "Tarifas Rete ICA: tabla no existe\n";
}

// Responsabilidades Fiscales
try {
    echo "Responsabilidades Fiscales: " . DB::table('responsabilidades_fiscales')->count() . "\n";
} catch (\Exception $e) {
    echo "Responsabilidades Fiscales: tabla no existe\n";
}

echo "\n=== Datos cargados ===\n";
