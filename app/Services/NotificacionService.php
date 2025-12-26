<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificacionService
{
    /**
     * Obtener estadísticas de notificaciones para un usuario
     */
    /**
     * Obtener estadísticas de notificaciones para un usuario
     */
    public function obtenerEstadisticas(int $userId): array
    {
        $query = Notificacion::where('user_id', $userId);

        $stats = [
            'total' => (clone $query)->count(),
            'no_leidas' => (clone $query)->where('leida', false)->count(),
            'leidas' => (clone $query)->where('leida', true)->count(),
            'por_tipo' => [],
            'por_prioridad' => [],
            'hoy' => (clone $query)->whereDate('created_at', Carbon::today())->count(),
            'ultimas_24h' => (clone $query)
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->count(),
            'ultima_semana' => (clone $query)
                ->where('created_at', '>=', Carbon::now()->subWeek())
                ->count(),
            // AGREGAMOS ESTA PARA QUE NO SE ROMPA LA LÍNEA 410 DE LA VISTA
            'accion_pendiente' => (clone $query)
                ->where('leida', false)
                ->whereNotNull('accion_url')
                ->count(),
        ];

        // Intentar obtener estadísticas por tipo si la columna existe
        try {
            $stats['por_tipo'] = (clone $query)
                ->select('tipo', DB::raw('count(*) as cantidad'))
                ->groupBy('tipo')
                ->pluck('cantidad', 'tipo')
                ->toArray();
        } catch (\Exception $e) {
            $stats['por_tipo'] = [];
        }

        // Intentar obtener estadísticas por prioridad
        try {
            // Primero verificamos si la columna 'prioridad' existe realmente en la tabla
            $stats['por_prioridad'] = (clone $query)
                ->select('prioridad', DB::raw('count(*) as cantidad'))
                ->groupBy('prioridad')
                ->pluck('cantidad', 'prioridad')
                ->toArray();
        } catch (\Exception $e) {
            $stats['por_prioridad'] = [];
        }

        return $stats;
    }

    /**
     * Crear un recordatorio programado
     */
    public function crearRecordatorio(
        int $userId,
        string $titulo,
        string $mensaje,
        \DateTime $fechaRecordatorio,
        ?int $cuentaCobroId = null,
        array $opciones = []
    ): Notificacion {
        return Notificacion::create([
            'user_id' => $userId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => 'recordatorio',
            'categoria' => $opciones['categoria'] ?? 'recordatorio',
            'prioridad' => $opciones['prioridad'] ?? 'normal',
            'cuenta_cobro_id' => $cuentaCobroId,
            'accion_url' => $opciones['accion_url'] ?? null,
            'programada_para' => $fechaRecordatorio,
            'leida' => false,
        ]);
    }

    /**
     * Notificar a un usuario sobre una cuenta de cobro
     */
    public function notificarCuentaCobro(
        int $userId,
        int $cuentaCobroId,
        string $tipo,
        string $titulo,
        string $mensaje,
        array $opciones = []
    ): Notificacion {
        return Notificacion::create([
            'user_id' => $userId,
            'cuenta_cobro_id' => $cuentaCobroId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'categoria' => $opciones['categoria'] ?? 'cuenta_cobro',
            'prioridad' => $opciones['prioridad'] ?? 'normal',
            'accion_url' => $opciones['accion_url'] ?? route('cuentas_cobro.show', $cuentaCobroId),
            'leida' => false,
        ]);
    }

    /**
     * Notificar a múltiples usuarios
     */
    public function notificarMultiples(
        array $userIds,
        string $tipo,
        string $titulo,
        string $mensaje,
        array $opciones = []
    ): int {
        $created = 0;
        foreach ($userIds as $userId) {
            Notificacion::create([
                'user_id' => $userId,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'categoria' => $opciones['categoria'] ?? 'sistema',
                'prioridad' => $opciones['prioridad'] ?? 'normal',
                'cuenta_cobro_id' => $opciones['cuenta_cobro_id'] ?? null,
                'accion_url' => $opciones['accion_url'] ?? null,
                'leida' => false,
            ]);
            $created++;
        }
        return $created;
    }

    /**
     * Notificar a usuarios con un rol específico
     */
    public function notificarRol(
        string $roleName,
        string $tipo,
        string $titulo,
        string $mensaje,
        array $opciones = []
    ): int {
        $userIds = User::whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        })->pluck('id')->toArray();

        return $this->notificarMultiples($userIds, $tipo, $titulo, $mensaje, $opciones);
    }

    /**
     * Limpiar notificaciones antiguas
     */
    public function limpiarAntiguas(int $diasAntiguedad = 90): int
    {
        return Notificacion::where('created_at', '<', Carbon::now()->subDays($diasAntiguedad))
            ->where('leida', true)
            ->delete();
    }

    /**
     * Procesar recordatorios programados
     */
    public function procesarRecordatoriosProgramados(): int
    {
        return Notificacion::where('tipo', 'recordatorio')
            ->where('programada_para', '<=', Carbon::now())
            ->where('leida', false)
            ->whereNull('fecha_enviada')
            ->update(['fecha_enviada' => Carbon::now()]);
    }
}
