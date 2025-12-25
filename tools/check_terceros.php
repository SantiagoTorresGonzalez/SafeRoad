<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Corrigiendo estructura de tabla terceros ===\n\n";

// En SQLite no podemos alterar columnas directamente, así que vamos a verificar
// y crear una migración si es necesario

// Verificar columnas de la tabla terceros
$columns = DB::select("PRAGMA table_info('terceros')");
echo "Columnas actuales en terceros:\n";
foreach ($columns as $col) {
    $nullable = $col->notnull ? 'NOT NULL' : 'NULL';
    $default = $col->dflt_value ?? 'none';
    echo "  - {$col->name}: {$col->type} {$nullable} (default: {$default})\n";
}

echo "\n";
