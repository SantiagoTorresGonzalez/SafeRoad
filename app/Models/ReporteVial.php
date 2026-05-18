<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteVial extends Model
{
    protected $table = 'reportes_viales';

    protected $fillable = [
        'tipo_riesgo',
        'descripcion',
        'latitud',
        'longitud',
        'municipio',
        'foto',
        'estado',
        'validado_por',
        'validado_at',
        'notas_autoridad',
    ];

    protected $casts = [
        'latitud'     => 'float',
        'longitud'    => 'float',
        'validado_at' => 'datetime',
    ];

    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por');
    }
}