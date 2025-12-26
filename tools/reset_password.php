<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'daniel00250@hotmail.com';
$newPassword = 'cosita1225*';

$user = User::where('email', $email)->first();

if ($user) {
    $user->password = Hash::make($newPassword);
    $user->save();
    echo "✅ Contraseña actualizada para: {$user->email}\n";
    echo "   Nueva contraseña: {$newPassword}\n";
} else {
    echo "❌ Usuario no encontrado con email: {$email}\n";
    
    // Listar usuarios existentes
    echo "\nUsuarios existentes:\n";
    foreach (User::all() as $u) {
        echo "  - {$u->email} (rol_id: {$u->role_id})\n";
    }
}
