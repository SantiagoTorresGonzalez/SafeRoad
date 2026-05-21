<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;  // solo tiene created_at, gestionado con useCurrent()

    protected $fillable = [
        'user_id',
        'accion',
        'descripcion',
        'entidad',
        'entidad_id',
        'datos',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'datos'      => 'array',
        'created_at' => 'datetime',
    ];

    // ── Relaciones ───────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Métodos estáticos de conveniencia ────────────────────────────

    /**
     * Registra una entrada en el log de auditoría.
     *
     * Uso rápido desde cualquier controlador:
     *   AuditLog::registrar('estado_cambiado', 'ReporteVial', $reporte->id, [
     *       'estado_anterior' => 'pendiente',
     *       'estado_nuevo'    => 'validado',
     *   ]);
     */
    public static function registrar(
        string  $accion,
        ?string $entidad    = null,
        ?int    $entidadId  = null,
        array   $datos      = [],
        ?string $descripcion = null
    ): self {
        $request = request();

        return static::create([
            'user_id'     => auth()->id(),
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'entidad'     => $entidad,
            'entidad_id'  => $entidadId,
            'datos'       => $datos ?: null,
            'ip'          => $request?->ip(),
            'user_agent'  => $request?->userAgent(),
        ]);
    }

    /**
     * Registra un cambio de estado en un ReporteVial.
     */
    public static function estadoCambiado(
        int    $reporteId,
        string $estadoAnterior,
        string $estadoNuevo,
        ?string $notas = null
    ): self {
        return static::registrar(
            accion:      'estado_cambiado',
            entidad:     'ReporteVial',
            entidadId:   $reporteId,
            datos:       [
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo'    => $estadoNuevo,
                'notas'           => $notas,
            ],
            descripcion: "Reporte #{$reporteId}: {$estadoAnterior} → {$estadoNuevo}",
        );
    }
}
