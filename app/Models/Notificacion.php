<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'cuenta_cobro_id',
        'leida',
        'fecha_leida',
        'canal',
        'prioridad',
        'categoria',
        'datos_extra',
        'enviado_email',
        'fecha_envio_email',
        'accion_requerida',
        'accion_url',
        'accion_texto',
        'expira_en',
        'relacionado_id',
        'relacionado_tipo',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_leida' => 'datetime',
        'enviado_email' => 'boolean',
        'fecha_envio_email' => 'datetime',
        'accion_requerida' => 'boolean',
        'expira_en' => 'datetime',
        'datos_extra' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class, 'cuenta_cobro_id');
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function marcarComoLeida(): void
    {
        $this->leida = true;
        $this->fecha_leida = now();
        $this->save();
    }
}
