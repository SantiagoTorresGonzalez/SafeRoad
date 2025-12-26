<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferenciaNotificacion extends Model
{
    protected $table = 'preferencia_notificaciones';

    protected $fillable = [
        'user_id',
        'email_habilitado',
        'app_habilitado',
        'frecuencia_resumen',
        'hora_resumen',
        'no_molestar_activo'
    ];

    public static function obtenerOCrear($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_habilitado' => true,
                'app_habilitado' => true,
                'frecuencia_resumen' => 'inmediato',
                'no_molestar_activo' => false
            ]
        );
    }
}