<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaCobroHistorial extends Model
{
    use HasFactory;

    protected $table = 'cuentas_cobro_historial';

    protected $fillable = [
        'cuenta_cobro_id', 'user_id', 'accion', 'estado_anterior', 'estado_nuevo', 'comentario'
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    // Alias para compatibilidad
    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Alias para compatibilidad con el controlador
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIcono(): string
    {
        return match ($this->accion) {
            'creado' => 'add_circle',
            'revisado' => 'visibility',
            'aprobado' => 'check_circle',
            'rechazado' => 'cancel',
            'enviado_cliente' => 'send',
            'pagado' => 'paid',
            'pago_rechazado' => 'cancel',
            'devuelto' => 'undo',
            'devuelto_general' => 'replay',
            'reenviado' => 'redo',
            'anulado' => 'block',
            'archivado' => 'archive',
            'desarchivado' => 'unarchive',
            'editado' => 'edit',
            default => 'info',
        };
    }

    public function getColor(): string
    {
        return match ($this->accion) {
            'creado' => '#0A84FF',
            'revisado' => '#5856D6',
            'aprobado' => '#34C759',
            'rechazado' => '#FF3B30',
            'enviado_cliente' => '#5856D6',
            'pagado' => '#34C759',
            'pago_rechazado' => '#FF3B30',
            'devuelto' => '#FF9500',
            'devuelto_general' => '#FF9500',
            'reenviado' => '#0A84FF',
            'anulado' => '#8E8E93',
            'archivado' => '#8E8E93',
            'desarchivado' => '#0A84FF',
            'editado' => '#5856D6',
            default => '#8E8E93',
        };
    }

    public function getEtiqueta(): string
    {
        return match ($this->accion) {
            'creado' => 'Creada',
            'revisado' => 'En Revisión',
            'aprobado' => 'Aprobada',
            'rechazado' => 'Rechazada',
            'enviado_cliente' => 'Enviada al Cliente',
            'pagado' => 'Pagada',
            'pago_rechazado' => 'Pago Rechazado',
            'devuelto' => 'Devuelta',
            'devuelto_general' => 'Devuelta para Ajuste',
            'reenviado' => 'Reenviada',
            'anulado' => 'Anulada',
            'archivado' => 'Archivada',
            'desarchivado' => 'Desarchivada',
            'editado' => 'Editada',
            default => ucfirst($this->accion),
        };
    }
}
