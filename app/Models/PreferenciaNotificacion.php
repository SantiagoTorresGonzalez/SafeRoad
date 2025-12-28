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
        'notif_nueva_cuenta',
        'notif_cuenta_aprobada',
        'notif_cuenta_rechazada',
        'notif_cuenta_devuelta',
        'notif_cuenta_pagada',
        'notif_cuenta_anulada',
        'notif_asignacion_rol',
        'notif_recordatorios',
        'notif_vencimientos',
        'notif_actualizaciones_tracking',
        'notif_lotes_procesados',
        'frecuencia_resumen',
        'hora_resumen',
        'no_molestar_activo',
        'no_molestar_inicio',
        'no_molestar_fin',
    ];

    protected $casts = [
        'email_habilitado' => 'boolean',
        'app_habilitado' => 'boolean',
        'notif_nueva_cuenta' => 'boolean',
        'notif_cuenta_aprobada' => 'boolean',
        'notif_cuenta_rechazada' => 'boolean',
        'notif_cuenta_devuelta' => 'boolean',
        'notif_cuenta_pagada' => 'boolean',
        'notif_cuenta_anulada' => 'boolean',
        'notif_asignacion_rol' => 'boolean',
        'notif_recordatorios' => 'boolean',
        'notif_vencimientos' => 'boolean',
        'notif_actualizaciones_tracking' => 'boolean',
        'notif_lotes_procesados' => 'boolean',
        'no_molestar_activo' => 'boolean',
    ];

    public static function obtenerOCrear($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_habilitado' => true,
                'app_habilitado' => true,
                'notif_nueva_cuenta' => true,
                'notif_cuenta_aprobada' => true,
                'notif_cuenta_rechazada' => true,
                'notif_cuenta_devuelta' => true,
                'notif_cuenta_pagada' => true,
                'notif_cuenta_anulada' => true,
                'notif_asignacion_rol' => true,
                'notif_recordatorios' => true,
                'notif_vencimientos' => true,
                'notif_actualizaciones_tracking' => true,
                'notif_lotes_procesados' => true,
                'frecuencia_resumen' => 'inmediato',
                'hora_resumen' => '09:00',
                'no_molestar_activo' => false,
                'no_molestar_inicio' => '22:00',
                'no_molestar_fin' => '08:00',
            ]
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}