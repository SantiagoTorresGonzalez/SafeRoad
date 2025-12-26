<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuentas_cobro';

    protected $fillable = [
        // Básicos
        'numero', 'fecha_emision', 'valor_total', 'departamento', 'municipio', 'descripcion',
        
        // Acreedor (quien cobra)
        'nombre_acreedor', 'tipo_documento_acreedor', 'numero_documento_acreedor',
        'ciudad_expedicion_acreedor', 'direccion_acreedor', 'telefono_acreedor', 'email_acreedor',
        
        // Deudor (quien debe pagar)
        'nombre_deudor', 'tipo_documento_deudor', 'numero_documento_deudor',
        'ciudad_expedicion_deudor', 'direccion_deudor', 'telefono_deudor', 'email_deudor',
        
        // Concepto y servicio
        'concepto_cobro', 'descripcion_servicio', 'fecha_prestacion_servicio',
        'fecha_inicio_servicio', 'fecha_fin_servicio', 'lugar_prestacion_servicio',
        
        // Contractual
        'numero_contrato_referencia', 'fecha_contrato', 'tipo_contrato', 'objeto_contrato',
        
        // Firmas
        'firmado_acreedor', 'fecha_firma_acreedor', 'firma_acreedor_url', 'firma_acreedor_ip',
        'firmado_deudor', 'fecha_firma_deudor', 'firma_deudor_url', 'firma_deudor_ip',
        
        // Documento soporte fiscal
        'numero_documento_soporte', 'fecha_documento_soporte', 'documento_soporte_url',
        'requiere_validacion_previa', 'fecha_validacion_dian',
        
        // Legal y cobro judicial
        'estado_cobro_judicial', 'numero_proceso_judicial', 'fecha_inicio_proceso',
        'juzgado', 'radicado_judicial',
        
        // Mérito ejecutivo
        'tiene_merito_ejecutivo', 'deuda_reconocida_deudor', 'evidencias_obligacion', 'testigos',
        
        // Expedición
        'ciudad_expedicion_cuenta', 'fecha_hora_emision',
        
        // Plazos y vencimientos
        'dias_plazo_pago', 'fecha_vencimiento_real', 'dias_gracia', 'fecha_vencimiento_con_gracia',
        
        // Condiciones adicionales
        'clausulas_especiales', 'condiciones_pago', 'forma_pago_acordada',
        'penalidades_retraso', 'interes_mora_porcentaje', 'cobra_intereses_mora',
        
        // Consecutivo
        'prefijo_cuenta', 'serie_cuenta', 'consecutivo_cuenta',
        
        // Observaciones
        'observaciones_legales', 'notas_cobro',
        
        // Campos existentes
        'motivo_rechazo', 'motivo_devolucion', 'tipo_identificacion', 'identificacion',
        'tipo_cliente', 'nombre_beneficiario', 'plazo_pago', 'contrato_id', 'user_id',
        'estado_aprobacion', 'etapa_aprobacion', 'aprobado_por_id',
        'fecha_aprobacion', 'fecha_rechazo', 'fecha_envio_cliente', 'archived_at',
        'cliente_email', 'cliente_whatsapp', 'recordatorio_habilitado',
        'frecuencia_recordatorio_dias', 'proxima_fecha_recordatorio', 'contador_recordatorios',
        'estado_pago', 'fecha_pago', 'pagado_por', 'observaciones',
        
        // Campos financieros avanzados
        'subtotal', 'descuento_valor', 'descuento_porcentaje',
        'iva_porcentaje', 'iva_valor',
        'retencion_fuente_porcentaje', 'retencion_fuente_valor',
        'retencion_ica_porcentaje', 'retencion_ica_valor',
        'retencion_iva_porcentaje', 'retencion_iva_valor',
        'otras_retenciones_valor',
        'tiene_anticipo', 'valor_anticipo', 'valor_pendiente_pago',
        'referencia_anticipo', 'fecha_pago_anticipado',
        'tipo_cuenta_beneficiario', 'numero_cuenta_beneficiario',
        'banco_beneficiario', 'cuenta_corriente_usuario',
        'nit_beneficiario', 'rut_url', 'responsable_iva', 'gran_contribuyente',
        'numero_orden_compra', 'numero_cdp', 'numero_rgp',
        'fecha_vencimiento_factura',
        'observaciones_internas', 'justificacion_rechazo', 'justificacion_devolucion',
        'fecha_ultima_modificacion', 'modificado_por',
        
        // Tracking de envío (como remisiones)
        'codigo_tracking', 'estado_envio', 'metodo_envio', 'direccion_entrega',
        'contacto_entrega', 'fecha_envio_real', 'fecha_entrega_estimada', 'fecha_entrega_real',
        'recibido_por', 'cargo_receptor', 'firma_recepcion_url', 'fecha_confirmacion_recepcion',
        'observaciones_recepcion', 'prioridad', 'requiere_confirmacion', 'dias_para_pago',
        'recordatorios_enviados', 'ultimo_recordatorio_at',
        
        // Lotes
        'lote_actual_id',
    ];

    /**
     * Atributos que deben ser casteados a tipos nativos
     */
    protected $casts = [
        // Fechas
        'fecha_emision' => 'date',
        'fecha_prestacion_servicio' => 'date',
        'fecha_inicio_servicio' => 'date',
        'fecha_fin_servicio' => 'date',
        'fecha_contrato' => 'date',
        'fecha_firma_acreedor' => 'datetime',
        'fecha_firma_deudor' => 'datetime',
        'fecha_documento_soporte' => 'date',
        'fecha_validacion_dian' => 'datetime',
        'fecha_inicio_proceso' => 'date',
        'fecha_hora_emision' => 'datetime',
        'fecha_vencimiento_real' => 'date',
        'fecha_vencimiento_con_gracia' => 'date',
        'fecha_aprobacion' => 'datetime',
        'fecha_rechazo' => 'datetime',
        'fecha_envio_cliente' => 'datetime',
        'fecha_pago' => 'datetime',
        'fecha_pago_anticipado' => 'date',
        'fecha_vencimiento_factura' => 'date',
        'fecha_ultima_modificacion' => 'datetime',
        'archived_at' => 'datetime',
        'proxima_fecha_recordatorio' => 'date',
        
        // Booleanos
        'firmado_acreedor' => 'boolean',
        'firmado_deudor' => 'boolean',
        'requiere_validacion_previa' => 'boolean',
        'tiene_merito_ejecutivo' => 'boolean',
        'deuda_reconocida_deudor' => 'boolean',
        'cobra_intereses_mora' => 'boolean',
        'recordatorio_habilitado' => 'boolean',
        'tiene_anticipo' => 'boolean',
        'responsable_iva' => 'boolean',
        'gran_contribuyente' => 'boolean',
        
        // Decimales
        'valor_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento_valor' => 'decimal:2',
        'descuento_porcentaje' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'iva_valor' => 'decimal:2',
        'retencion_fuente_porcentaje' => 'decimal:2',
        'retencion_fuente_valor' => 'decimal:2',
        'retencion_ica_porcentaje' => 'decimal:2',
        'retencion_ica_valor' => 'decimal:2',
        'retencion_iva_porcentaje' => 'decimal:2',
        'retencion_iva_valor' => 'decimal:2',
        'otras_retenciones_valor' => 'decimal:2',
        'valor_anticipo' => 'decimal:2',
        'valor_pendiente_pago' => 'decimal:2',
        'interes_mora_porcentaje' => 'decimal:2',
        
        // Enteros
        'dias_plazo_pago' => 'integer',
        'dias_gracia' => 'integer',
        'consecutivo_cuenta' => 'integer',
        'frecuencia_recordatorio_dias' => 'integer',
        'contador_recordatorios' => 'integer',
        'contrato_id' => 'integer',
        'user_id' => 'integer',
        'aprobado_por_id' => 'integer',
        'modificado_por' => 'integer',
        
        // Tracking
        'fecha_envio_real' => 'datetime',
        'fecha_entrega_estimada' => 'datetime',
        'fecha_entrega_real' => 'datetime',
        'fecha_confirmacion_recepcion' => 'datetime',
        'ultimo_recordatorio_at' => 'datetime',
        'requiere_confirmacion' => 'boolean',
        'dias_para_pago' => 'integer',
        'recordatorios_enviados' => 'integer',
    ];

    /**
     * Relación: una cuenta de cobro tiene muchos ítems.
     */
    public function items()
    {
        return $this->hasMany(ItemCuentaCobro::class, 'cuenta_cobro_id');
    }

    /**
     * Relación: una cuenta de cobro pertenece a un contrato.
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }
    
    /**
     * Relación: una cuenta de cobro pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: historial de cambios.
     */
    public function historial()
    {
        return $this->hasMany(CuentaCobroHistorial::class, 'cuenta_cobro_id')->orderByDesc('created_at');
    }

    /**
     * Relación: usuario que aprobó finalmente.
     */
    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por_id');
    }

    /**
     * Relación: soportes (archivos adjuntos)
     */
    public function soportes()
    {
        return $this->hasMany(Soporte::class, 'cuenta_cobro_id');
    }

    /**
     * Relación: interacciones (historial de comunicaciones)
     */
    public function interacciones()
    {
        return $this->hasMany(Interaccion::class, 'cuenta_cobro_id')->orderByDesc('created_at');
    }

    /**
     * Relación: documentos adjuntos a la cuenta
     */
    public function documentos()
    {
        return $this->hasMany(Documento::class, 'cuenta_cobro_id')->orderByDesc('created_at');
    }

    /**
     * Relación: lotes a los que pertenece esta cuenta
     */
    public function lotes()
    {
        return $this->belongsToMany(LoteCuentaCobro::class, 'lote_cuenta_cobro', 'cuenta_cobro_id', 'lote_id')
            ->withPivot(['estado_en_lote', 'nota', 'procesado_at'])
            ->withTimestamps();
    }

    /**
     * Relación: lote actual (referencia rápida)
     */
    public function loteActual()
    {
        return $this->belongsTo(LoteCuentaCobro::class, 'lote_actual_id');
    }

    /**
     * Relación: eventos de tracking (seguimiento de envío)
     */
    public function trackingEventos()
    {
        return $this->hasMany(TrackingEvento::class, 'cuenta_cobro_id')->orderByDesc('created_at');
    }

    /**
     * Relación: usuario que última modificó
     */
    public function modificadoPorUsuario()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    /**
     * Calcula el valor total automáticamente sumando los ítems.
     */
    public function calcularTotal()
    {
        return $this->items->sum(fn($item) => $item->cantidad * $item->precio_unitario);
    }

    /**
     * Actualiza el valor_total basado en los ítems actuales.
     */
    public function actualizarValorTotal()
    {
        $this->valor_total = $this->calcularTotal();
        $this->save();
    }

    /**
     * Registrar entrada en historial.
     */
    public function registrarHistorial(?int $userId, string $accion, ?string $estadoAnterior, ?string $estadoNuevo, ?string $comentario = null): void
    {
        CuentaCobroHistorial::create([
            'cuenta_cobro_id' => $this->id,
            'user_id' => $userId,
            'accion' => $accion,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'comentario' => $comentario,
        ]);
    }

    /** Scope: no archivadas */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Helper: verifica si la cuenta está en revisión.
     */
    public function isEnRevision(): bool
    {
        return $this->estado_aprobacion === 'en_revision';
    }

    /**
     * Helper: verifica si un usuario puede aprobar esta cuenta según su rol y la etapa actual.
     */
    public function canUserApprove(?User $user): bool
    {
        if (!$user || !$this->isEnRevision()) {
            return false;
        }

        $roleName = $user->role?->name;
        if ($roleName === 'admin_programa') {
            return true; // Admin del programa puede aprobar en cualquier etapa
        }

        // Mapeo de roles a etapas
        $roleToEtapa = [
            'administrador' => 'administrador',
            'tesoreria' => 'tesoreria',
        ];

        return isset($roleToEtapa[$roleName]) && $this->etapa_aprobacion === $roleToEtapa[$roleName];
    }

    /**
     * Helper: obtiene el texto legible del estado de aprobación.
     */
    public function getEstadoTexto(): string
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'en_revision' => 'En Revisión',
            'en_correccion' => 'En Corrección',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'enviado_cliente' => 'Enviado al Cliente',
            'pagado' => 'Pagado',
        ];

        return $estados[$this->estado_aprobacion] ?? 'Desconocido';
    }

    /**
     * Helper: obtiene el texto legible de la etapa actual.
     */
    public function getEtapaTexto(): string
    {
        $etapas = [
            'administrador' => 'Administrador',
            'tesoreria' => 'Tesorería',
            'auxiliar' => 'Auxiliar',
        ];

        return $etapas[$this->etapa_aprobacion] ?? ucfirst(str_replace('_', ' ', $this->etapa_aprobacion ?? ''));
    }

    /**
     * Helper: obtiene el color del badge según el estado.
     */
    public function getEstadoColor(): string
    {
        $colores = [
            'pendiente' => '#FF9500',
            'en_revision' => '#007AFF',
            'en_correccion' => '#FF9500',
            'aprobado' => '#34C759',
            'rechazado' => '#FF3B30',
            'enviado_cliente' => '#5856D6',
            'pagado' => '#30D158',
        ];

        return $colores[$this->estado_aprobacion] ?? '#86868b';
    }

    /**
     * Helper: obtiene el icono del badge según el estado.
     */
    public function getEstadoIcono(): string
    {
        $iconos = [
            'pendiente' => 'schedule',
            'en_revision' => 'visibility',
            'en_correccion' => 'edit',
            'aprobado' => 'check_circle',
            'rechazado' => 'cancel',
            'enviado_cliente' => 'send',
            'pagado' => 'payments',
        ];

        return $iconos[$this->estado_aprobacion] ?? 'help';
    }

    /**
     * Helper: verifica si el usuario es el dueño auxiliar de esta cuenta.
     */
    public function isOwner(?User $user): bool
    {
        return $user && $user->id === $this->user_id && $user->role?->name === 'auxiliar';
    }

    /**
     * Helper: verifica si la cuenta está aprobada y lista para enviar al cliente.
     */
    public function canSendToClient(?User $user): bool
    {
        if (!$user || $this->estado_aprobacion !== 'aprobado') {
            return false;
        }

        $allowedRoles = ['administrador', 'tesoreria', 'admin_programa'];
        return in_array($user->role?->name, $allowedRoles);
    }

    /**
     * Helper: verifica si la cuenta está lista para registrar pago (Tesorería).
     */
    public function canRegisterPayment(?User $user): bool
    {
        if (!$user || $this->estado_aprobacion !== 'aprobado' || $this->etapa_aprobacion !== 'tesoreria') {
            return false;
        }

        return in_array($user->role?->name, ['tesoreria', 'admin_programa']);
    }

    /**
     * Calcular valor total desde componentes
     */
    public function calcularValorTotalDetallado(): float
    {
        $subtotal = $this->subtotal ?? $this->calcularTotal();
        $descuento = $this->descuento_valor ?? 0;
        $iva = $this->iva_valor ?? 0;
        $retenciones = ($this->retencion_fuente_valor ?? 0) +
                      ($this->retencion_ica_valor ?? 0) +
                      ($this->retencion_iva_valor ?? 0) +
                      ($this->otras_retenciones_valor ?? 0);

        return round($subtotal - $descuento + $iva - $retenciones, 2);
    }

    /**
     * Recalcular retenciones automáticamente
     */
    public function recalcularRetenciones(): void
    {
        $subtotal = $this->subtotal ?? $this->calcularTotal();

        // Retención en la fuente (típicamente 2%)
        $this->retencion_fuente_valor = $this->retencion_fuente_porcentaje > 0
            ? round($subtotal * ($this->retencion_fuente_porcentaje / 100), 2)
            : 0;

        // Retención ICA (típicamente 1.04%)
        $this->retencion_ica_valor = $this->retencion_ica_porcentaje > 0
            ? round($subtotal * ($this->retencion_ica_porcentaje / 100), 2)
            : 0;

        // Retención IVA (sólo si hay IVA)
        if ($this->iva_valor > 0 && $this->retencion_iva_porcentaje > 0) {
            $this->retencion_iva_valor = round($this->iva_valor * ($this->retencion_iva_porcentaje / 100), 2);
        } else {
            $this->retencion_iva_valor = 0;
        }

        $this->save();
    }

    /**
     * Obtener resumen financiero de la cuenta
     */
    public function getResumenFinanciero(): array
    {
        return [
            'subtotal' => $this->subtotal ?? $this->calcularTotal(),
            'descuento' => $this->descuento_valor ?? 0,
            'iva' => $this->iva_valor ?? 0,
            'retenciones_total' => ($this->retencion_fuente_valor ?? 0) +
                                 ($this->retencion_ica_valor ?? 0) +
                                 ($this->retencion_iva_valor ?? 0) +
                                 ($this->otras_retenciones_valor ?? 0),
            'valor_neto' => $this->valor_total,
            'anticipos' => $this->valor_anticipo ?? 0,
            'pendiente_pago' => $this->valor_pendiente_pago ?? 0,
        ];
    }

    /**
     * Verificar si tiene todos los documentos obligatorios
     */
    public function tieneTodosDocumentosObligatorios(): bool
    {
        $documentosObligatorios = ['factura', 'contrato'];
        $documentosSubidos = $this->documentos()->pluck('tipo_documento')->unique()->toArray();

        foreach ($documentosObligatorios as $doc) {
            if (!in_array($doc, $documentosSubidos)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtener documentos faltantes
     */
    public function getDocumentosFaltantes(): array
    {
        $documentosObligatorios = ['factura', 'contrato'];
        $documentosSubidos = $this->documentos()->pluck('tipo_documento')->unique()->toArray();

        return array_diff($documentosObligatorios, $documentosSubidos);
    }

    /**
     * Obtener estado del flujo con detalles
     */
    public function getEstadoConDetalles(): array
    {
        return [
            'estado' => $this->estado_aprobacion,
            'estado_texto' => $this->getEstadoTexto(),
            'estado_color' => $this->getEstadoColor(),
            'estado_icono' => $this->getEstadoIcono(),
            'etapa' => $this->etapa_aprobacion,
            'etapa_texto' => $this->getEtapaTexto(),
            'aprobado_por' => $this->aprobadoPor?->name,
            'fecha_aprobacion' => $this->fecha_aprobacion,
            'motivo_rechazo' => $this->motivo_rechazo,
            'fecha_rechazo' => $this->fecha_rechazo,
        ];
    }

    /**
     * Registrar modificación
     */
    public function registrarModificacion(?User $user = null): void
    {
        $this->update([
            'fecha_ultima_modificacion' => now(),
            'modificado_por' => ($user ?? auth()->user())?->id,
        ]);
    }

    /**
     * Obtener historial de cambios completo
     */
    public function getHistorialCompleto(): array
    {
        $historial = [];

        // Historial de estado
        foreach ($this->historial as $cambio) {
            $historial[] = [
                'tipo' => 'estado',
                'fecha' => $cambio->created_at,
                'usuario' => $cambio->usuario?->name,
                'accion' => $cambio->accion,
                'estado_anterior' => $cambio->estado_anterior,
                'estado_nuevo' => $cambio->estado_nuevo,
                'comentario' => $cambio->comentario,
            ];
        }

        // Interacciones
        foreach ($this->interacciones as $interaccion) {
            $historial[] = [
                'tipo' => 'interaccion',
                'fecha' => $interaccion->created_at,
                'usuario' => $interaccion->usuario?->name,
                'asunto' => $interaccion->asunto,
                'detalle' => $interaccion->detalle,
                'tipo_interaccion' => $interaccion->tipo,
            ];
        }

        // Documentos
        foreach ($this->documentos as $documento) {
            $historial[] = [
                'tipo' => 'documento',
                'fecha' => $documento->created_at,
                'usuario' => $documento->usuario?->name,
                'accion' => 'Documento subido',
                'nombre' => $documento->nombre_original,
                'tipo_documento' => $documento->tipo_documento,
            ];
        }

        // Ordenar por fecha descendente
        usort($historial, fn($a, $b) => $b['fecha']->timestamp <=> $a['fecha']->timestamp);

        return $historial;
    }

    /**
     * Puede ser archivada por un usuario
     */
    public function puedeSerArchivadaPor(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // El contratista que creó la cuenta o super admin
        return ($this->user_id === $user->id && $user->hasRole('contratista')) ||
               $user->hasRole('super_admin');
    }

    /**
     * Archivar cuenta
     */
    public function archivar(): void
    {
        $this->update(['archived_at' => now()]);
        $this->registrarHistorial(
            auth()->id(),
            'Archivado',
            $this->estado_aprobacion,
            $this->estado_aprobacion,
            'Cuenta archivada por ' . auth()->user()->name
        );
    }

    /**
     * Desarchivar cuenta
     */
    public function desarchivizar(): void
    {
        $this->update(['archived_at' => null]);
        $this->registrarHistorial(
            auth()->id(),
            'Desarchivado',
            $this->estado_aprobacion,
            $this->estado_aprobacion,
            'Cuenta desarchivada por ' . auth()->user()->name
        );
    }

    /**
     * Scope: cuentas archivadas
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    // ========================================================================
    // MÉTODOS PARA REQUISITOS LEGALES DE CUENTA DE COBRO
    // ========================================================================

    /**
     * Verifica si la cuenta cumple con los requisitos legales mínimos
     * Según el artículo de Gerencie.com sobre cuentas de cobro
     */
    public function cumpleRequisitosLegales(): array
    {
        $errores = [];
        
        // 1. Identificación del acreedor
        if (empty($this->nombre_acreedor) || empty($this->numero_documento_acreedor)) {
            $errores[] = 'Falta identificación completa del acreedor (quien cobra)';
        }
        
        // 2. Identificación del deudor
        if (empty($this->nombre_deudor) || empty($this->numero_documento_deudor)) {
            $errores[] = 'Falta identificación completa del deudor (quien debe pagar)';
        }
        
        // 3. Valor a cobrar
        if (empty($this->valor_total) || $this->valor_total <= 0) {
            $errores[] = 'Falta el valor a cobrar o es inválido';
        }
        
        // 4. Concepto por el que se hace el cobro
        if (empty($this->concepto_cobro) && empty($this->descripcion_servicio)) {
            $errores[] = 'Falta el concepto o descripción del servicio/producto cobrado';
        }
        
        // 5. Fecha en que se prestó el servicio
        if (empty($this->fecha_prestacion_servicio) && empty($this->fecha_inicio_servicio)) {
            $errores[] = 'Falta la fecha en que se prestó el servicio o vendió el producto';
        }
        
        // 6. Fecha de la cuenta de cobro
        if (empty($this->fecha_emision)) {
            $errores[] = 'Falta la fecha de emisión de la cuenta de cobro';
        }
        
        // 7. Firma del acreedor
        if (!$this->firmado_acreedor) {
            $errores[] = 'La cuenta de cobro debe estar firmada por el acreedor';
        }
        
        return [
            'cumple' => empty($errores),
            'errores' => $errores,
            'porcentaje_cumplimiento' => (7 - count($errores)) / 7 * 100
        ];
    }

    /**
     * Verifica si tiene mérito ejecutivo (poco común en cuentas de cobro)
     */
    public function tieneMeritoEjecutivo(): bool
    {
        // Para tener mérito ejecutivo necesita firma del deudor y reconocimiento de deuda
        return $this->tiene_merito_ejecutivo &&
               $this->firmado_deudor &&
               $this->deuda_reconocida_deudor;
    }

    /**
     * Verifica si es apta para proceso monitorio
     * (Proceso declarativo especial para cobro de deudas de mínima cuantía)
     */
    public function esAptaProcesoMonitorio(): array
    {
        $smlv = 1300000; // Salario mínimo legal vigente 2025 (ajustar según el año)
        $minimaCuantia = $smlv * 40;
        
        $requisitos = [
            'es_deuda_dineraria' => $this->valor_total > 0,
            'es_minima_cuantia' => $this->valor_total <= $minimaCuantia,
            'tiene_origen_contractual' => !empty($this->numero_contrato_referencia) || 
                                         !empty($this->tipo_contrato) ||
                                         !empty($this->concepto_cobro),
            'tiene_evidencias' => !empty($this->evidencias_obligacion) ||
                                 $this->firmado_deudor ||
                                 !empty($this->numero_contrato_referencia),
        ];
        
        $cumple = !in_array(false, $requisitos, true);
        
        return [
            'apta' => $cumple,
            'requisitos' => $requisitos,
            'valor_cuenta' => $this->valor_total,
            'limite_minima_cuantia' => $minimaCuantia,
            'recomendacion' => $cumple ? 
                'La cuenta es apta para proceso monitorio' :
                'Considere fortalecer las evidencias o conseguir firma del deudor'
        ];
    }

    /**
     * Calcula intereses de mora si aplican
     */
    public function calcularInteresesMora(): array
    {
        if (!$this->cobra_intereses_mora || !$this->fecha_vencimiento_real) {
            return [
                'aplica' => false,
                'dias_mora' => 0,
                'interes' => 0,
                'total_con_interes' => $this->valor_total,
            ];
        }
        
        $fechaVencimiento = \Carbon\Carbon::parse($this->fecha_vencimiento_real);
        $hoy = \Carbon\Carbon::now();
        
        // Si hay fecha de pago, usar esa; si no, usar hoy
        $fechaCalculo = $this->fecha_pago ? \Carbon\Carbon::parse($this->fecha_pago) : $hoy;
        
        if ($fechaCalculo->lte($fechaVencimiento)) {
            return [
                'aplica' => false,
                'dias_mora' => 0,
                'interes' => 0,
                'total_con_interes' => $this->valor_total,
            ];
        }
        
        $diasMora = $fechaVencimiento->diffInDays($fechaCalculo);
        
        // Calcular interés compuesto diario o simple según configuración
        $interesDiario = ($this->interes_mora_porcentaje / 365) / 100;
        $intereses = $this->valor_total * $interesDiario * $diasMora;
        
        return [
            'aplica' => true,
            'dias_mora' => $diasMora,
            'interes_porcentaje' => $this->interes_mora_porcentaje,
            'interes_valor' => round($intereses, 2),
            'total_con_interes' => round($this->valor_total + $intereses, 2),
            'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
            'fecha_calculo' => $fechaCalculo->format('Y-m-d'),
        ];
    }

    /**
     * Genera el número de cuenta de cobro con formato profesional
     */
    public function generarNumeroCompleto(): string
    {
        $partes = [];
        
        if ($this->prefijo_cuenta) {
            $partes[] = $this->prefijo_cuenta;
        }
        
        if ($this->serie_cuenta) {
            $partes[] = $this->serie_cuenta;
        }
        
        if ($this->consecutivo_cuenta) {
            $partes[] = str_pad($this->consecutivo_cuenta, 6, '0', STR_PAD_LEFT);
        } else {
            $partes[] = $this->numero;
        }
        
        return implode('-', $partes);
    }

    /**
     * Verifica si la cuenta está vencida
     */
    public function estaVencida(): bool
    {
        if (!$this->fecha_vencimiento_real) {
            return false;
        }
        
        $fechaComparar = $this->fecha_vencimiento_con_gracia ?? $this->fecha_vencimiento_real;
        return \Carbon\Carbon::parse($fechaComparar)->isPast();
    }

    /**
     * Obtiene días para vencimiento (negativo si ya venció)
     */
    public function getDiasParaVencimiento(): ?int
    {
        if (!$this->fecha_vencimiento_real) {
            return null;
        }
        
        $fechaComparar = $this->fecha_vencimiento_con_gracia ?? $this->fecha_vencimiento_real;
        return \Carbon\Carbon::now()->diffInDays($fechaComparar, false);
    }

    /**
     * Registra la firma del acreedor
     */
    public function firmarPorAcreedor(string $firmaUrl, ?string $ip = null): void
    {
        $this->update([
            'firmado_acreedor' => true,
            'fecha_firma_acreedor' => now(),
            'firma_acreedor_url' => $firmaUrl,
            'firma_acreedor_ip' => $ip ?? request()->ip(),
        ]);
        
        $this->registrarHistorial(
            auth()->id(),
            'Firma acreedor',
            $this->estado_aprobacion,
            $this->estado_aprobacion,
            'Cuenta firmada por el acreedor'
        );
    }

    /**
     * Registra la firma del deudor (importante para reconocimiento de deuda)
     */
    public function firmarPorDeudor(string $firmaUrl, ?string $ip = null, bool $reconoceDeuda = false): void
    {
        $this->update([
            'firmado_deudor' => true,
            'fecha_firma_deudor' => now(),
            'firma_deudor_url' => $firmaUrl,
            'firma_deudor_ip' => $ip ?? request()->ip(),
            'deuda_reconocida_deudor' => $reconoceDeuda,
        ]);
        
        $this->registrarHistorial(
            auth()->id(),
            'Firma deudor',
            $this->estado_aprobacion,
            $this->estado_aprobacion,
            'Cuenta firmada por el deudor' . ($reconoceDeuda ? ' con reconocimiento de deuda' : '')
        );
    }

    /**
     * Genera un formato de texto para la cuenta de cobro
     */
    public function generarTextoLegal(): string
    {
        $texto = "CUENTA DE COBRO {$this->generarNumeroCompleto()}

";
        
        if ($this->ciudad_expedicion_cuenta) {
            $texto .= "{$this->ciudad_expedicion_cuenta}, ";
        }
        $texto .= \Carbon\Carbon::parse($this->fecha_emision)->format('d \d\e F \d\e Y') . "

";
        
        // Párrafo principal
        $texto .= "El(la) señor(a) {$this->nombre_acreedor}, identificado(a) con {$this->tipo_documento_acreedor} ";
        $texto .= "número {$this->numero_documento_acreedor}";
        
        if ($this->ciudad_expedicion_acreedor) {
            $texto .= " expedida en {$this->ciudad_expedicion_acreedor}";
        }
        
        $texto .= ", debe a {$this->nombre_deudor}, identificado con {$this->tipo_documento_deudor} ";
        $texto .= "número {$this->numero_documento_deudor}, la suma de \$" . number_format($this->valor_total, 2);
        
        if ($this->concepto_cobro) {
            $texto .= " por concepto de: {$this->concepto_cobro}";
        }
        
        if ($this->fecha_prestacion_servicio) {
            $texto .= ", servicio prestado el " . \Carbon\Carbon::parse($this->fecha_prestacion_servicio)->format('d/m/Y');
        }
        
        $texto .= ".

";
        
        // Información adicional
        if ($this->numero_contrato_referencia) {
            $texto .= "Contrato No. {$this->numero_contrato_referencia}";
            if ($this->fecha_contrato) {
                $texto .= " de fecha " . \Carbon\Carbon::parse($this->fecha_contrato)->format('d/m/Y');
            }
            $texto .= "
";
        }
        
        if ($this->descripcion_servicio) {
            $texto .= "
Descripción del servicio:
{$this->descripcion_servicio}
";
        }
        
        // Firma
        $texto .= "

______________________________
";
        $texto .= "Firma del Acreedor
";
        $texto .= $this->nombre_acreedor . "
";
        $texto .= "{$this->tipo_documento_acreedor} {$this->numero_documento_acreedor}
";
        
        if ($this->telefono_acreedor) {
            $texto .= "Tel: {$this->telefono_acreedor}
";
        }
        
        return $texto;
    }

    /**
     * Scope: cuentas con proceso judicial
     */
    public function scopeConProcesoJudicial($query)
    {
        return $query->where('estado_cobro_judicial', '!=', 'Sin proceso');
    }

    /**
     * Scope: cuentas vencidas
     */
    public function scopeVencidas($query)
    {
        return $query->whereNotNull('fecha_vencimiento_real')
                     ->where('fecha_vencimiento_real', '<', now())
                     ->whereNull('fecha_pago');
    }

    /**
     * Scope: cuentas firmadas por ambas partes
     */
    public function scopeFirmadasCompletas($query)
    {
        return $query->where('firmado_acreedor', true)
                     ->where('firmado_deudor', true);
    }

    /**
     * Scope: cuentas con reconocimiento de deuda
     */
    public function scopeConReconocimientoDeuda($query)
    {
        return $query->where('deuda_reconocida_deudor', true);
    }
}
