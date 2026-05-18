<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoRiesgo extends Model
{
    protected $table = 'puntos_riesgo';

    protected $fillable = [
        'municipio',
        'latitud',
        'longitud',
        'descripcion',
        'total_muertes',
        'anio',
        'nivel_riesgo',
    ];

    protected $casts = [
        'latitud'       => 'float',
        'longitud'      => 'float',
        'total_muertes' => 'integer',
        'anio'          => 'integer',
    ];
}