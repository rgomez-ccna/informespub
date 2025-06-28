<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReunionVidaMinisterio extends Model
{
    protected $table = 'reunion_vida_ministerios';

    protected $fillable = [
        'fecha',
        'lectura_semanal',
        'presidente',
        'presidente_ayudante',
        'consejero_auxiliar',
        'consejero_ayudante',
        'cancion_inicio',
        'oracion_inicio',
        'tesoro_titulo',
        'tesoro_disertante',
        'perlas_disertante',
        'lectura_lector_principal',
        'lectura_lector_auxiliar',
        'asignaciones_maestros',
        'cancion_medio',
        'vida_cristiana',
        'estudio_conductor',
        'estudio_lector',
        'cancion_final',
        'oracion_final',
    ];

    protected $casts = [
        'asignaciones_maestros' => 'array',
        'vida_cristiana' => 'array',
    ];
}
