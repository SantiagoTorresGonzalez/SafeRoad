<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferenciaNotificacion extends Model
{
    // Nombre de la tabla en la DB
    protected $table = 'preferencia_notificaciones';

    protected $fillable = [
        'user_id',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'frecuencia_resumen'
    ];

    /**
     * Lógica para obtener o crear las preferencias del usuario
     */
    public static function obtenerOCrear($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_enabled' => true,
                'push_enabled' => true,
                'frecuencia_resumen' => 'inmediato'
            ]
        );
    }
}