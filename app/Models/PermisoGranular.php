<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermisoGranular extends Model
{
    protected $table = 'permisos_granulares';

    protected $fillable = [
        'role_id',
        'etapa_flujo',
        'accion',
        'estado_requerido',
        'puede_crear',
        'puede_leer',
        'puede_editar',
        'puede_eliminar',
        'puede_aprobar',
        'puede_rechazar',
        'puede_devolver',
        'puede_devolver_correccion',
        'puede_comentar',
        'puede_subir_documentos',
        'puede_descargar_documentos',
        'puede_registrar_pago',
        'puede_enviar_cliente',
        'puede_archivar',
        'puede_ver_todas_cuentas',
        'puede_ver_reportes',
        'puede_gestionar_usuarios',
        'puede_gestionar_contratos',
        'campos_visibles',
        'campos_editables',
        'roles_visibles',
        'departamentos_visibles',
        'valor_minimo_aprobacion',
        'valor_maximo_aprobacion',
        'requiere_segundo_aprobador',
        'dias_para_aprobar',
        'activo',
        'descripcion',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
    ];

    protected $casts = [
        'puede_crear' => 'boolean',
        'puede_leer' => 'boolean',
        'puede_editar' => 'boolean',
        'puede_eliminar' => 'boolean',
        'puede_aprobar' => 'boolean',
        'puede_rechazar' => 'boolean',
        'puede_devolver' => 'boolean',
        'puede_devolver_correccion' => 'boolean',
        'puede_comentar' => 'boolean',
        'puede_subir_documentos' => 'boolean',
        'puede_descargar_documentos' => 'boolean',
        'puede_registrar_pago' => 'boolean',
        'puede_enviar_cliente' => 'boolean',
        'puede_archivar' => 'boolean',
        'puede_ver_todas_cuentas' => 'boolean',
        'puede_ver_reportes' => 'boolean',
        'puede_gestionar_usuarios' => 'boolean',
        'puede_gestionar_contratos' => 'boolean',
        'requiere_segundo_aprobador' => 'boolean',
        'activo' => 'boolean',
        'campos_visibles' => 'array',
        'campos_editables' => 'array',
        'roles_visibles' => 'array',
        'departamentos_visibles' => 'array',
        'fecha_inicio_vigencia' => 'datetime',
        'fecha_fin_vigencia' => 'datetime',
    ];

    /**
     * Relación: pertenece a Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Scope: permisos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true)
                     ->where(function ($q) {
                         $q->whereNull('fecha_inicio_vigencia')
                           ->orWhere('fecha_inicio_vigencia', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('fecha_fin_vigencia')
                           ->orWhere('fecha_fin_vigencia', '>=', now());
                     });
    }

    /**
     * Scope: permisos por rol
     */
    public function scopeByRol($query, ?Role $role)
    {
        if (!$role) {
            return $query->whereRaw('1 = 0');
        }
        return $query->where('role_id', $role->id);
    }

    /**
     * Scope: permisos por etapa
     */
    public function scopeByEtapa($query, $etapa)
    {
        return $query->where(function ($q) use ($etapa) {
            $q->whereNull('etapa_flujo')
              ->orWhere('etapa_flujo', $etapa);
        });
    }

    /**
     * Verificar si tiene permiso específico
     */
    public function tienePermiso(string $nombrePermiso): bool
    {
        $atributo = 'puede_' . $nombrePermiso;

        // Resolve possible alias -> canonical action keys for granular permissions
        $canonical = config('granular_action_aliases.' . $nombrePermiso, $nombrePermiso);
        $atributo = 'puede_' . $canonical;

        return isset($this->$atributo) && $this->$atributo;
    }

    /**
     * Verificar si está vigente
     */
    public function esVigente(): bool
    {
        if (!$this->activo) {
            return false;
        }

        $ahora = now();

        if ($this->fecha_inicio_vigencia && $ahora->isBefore($this->fecha_inicio_vigencia)) {
            return false;
        }

        if ($this->fecha_fin_vigencia && $ahora->isAfter($this->fecha_fin_vigencia)) {
            return false;
        }

        return true;
    }

    /**
     * Obtener campos visibles
     */
    public function getCamposVisibles(): array
    {
        return $this->campos_visibles ?? [
            'numero', 'fecha_emision', 'nombre_beneficiario', 'valor_total',
            'estado_aprobacion', 'etapa_aprobacion', 'created_at'
        ];
    }

    /**
     * Obtener campos editables
     */
    public function getCamposEditables(): array
    {
        return $this->campos_editables ?? [];
    }

    /**
     * Verificar si puede ver todas las cuentas
     */
    public function puedeVerTodasCuentas(): bool
    {
        return $this->puede_ver_todas_cuentas;
    }

    /**
     * Verificar si puede ver cuenta según filtros
     */
    public function puedeVerCuenta(CuentaCobro $cuenta, ?Role $rolDeContratista = null): bool
    {
        if ($this->puedeVerTodasCuentas()) {
            return true;
        }

        // Si tiene roles visibles, verificar
        if ($this->roles_visibles && count($this->roles_visibles) > 0) {
            if (!$rolDeContratista || !in_array($rolDeContratista->name, $this->roles_visibles)) {
                return false;
            }
        }

        // Si tiene departamentos visibles, verificar
        if ($this->departamentos_visibles && count($this->departamentos_visibles) > 0) {
            if (!in_array($cuenta->departamento, $this->departamentos_visibles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verificar si puede aprobar valor
     */
    public function puedeAprobarValor(float $valor): bool
    {
        if ($this->valor_minimo_aprobacion && $valor < $this->valor_minimo_aprobacion) {
            return false;
        }

        if ($this->valor_maximo_aprobacion && $valor > $this->valor_maximo_aprobacion) {
            return false;
        }

        return true;
    }

    /**
     * Obtener descripción del permiso
     */
    public function getDescripcion(): string
    {
        return $this->descripcion ?? 'Permiso granular del sistema';
    }

    /**
     * Obtener resumen de permisos
     */
    public function getResumenPermisos(): array
    {
        return [
            'lectura' => $this->puede_leer,
            'creacion' => $this->puede_crear,
            'edicion' => $this->puede_editar,
            'eliminacion' => $this->puede_eliminar,
            'aprobacion' => $this->puede_aprobar,
            'rechazo' => $this->puede_rechazar,
            'devolucion' => $this->puede_devolver,
            'comentarios' => $this->puede_comentar,
            'documentos_subida' => $this->puede_subir_documentos,
            'documentos_descarga' => $this->puede_descargar_documentos,
            'pago_registro' => $this->puede_registrar_pago,
            'cliente_envio' => $this->puede_enviar_cliente,
            'archivado' => $this->puede_archivar,
            'reportes' => $this->puede_ver_reportes,
        ];
    }
}
