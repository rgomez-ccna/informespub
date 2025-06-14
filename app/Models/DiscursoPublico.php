<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscursoPublico extends Model
{
 protected $fillable = [
        'fecha',
        'conferencia',
        'disertante',
        'congregacion',
        'horario',
        'tipo',
        'es_nuevo_programa_visita',
        'es_nuevo_programa_salida',
    ];


}
