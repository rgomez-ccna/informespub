<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaMinisterio extends Model
{
     protected $fillable = [
        'fecha',
        'hora',
        'conductor',
        'punto_encuentro',
        'territorio',
        'es_nueva_semana',
        'es_fila_info',
    ];
}
