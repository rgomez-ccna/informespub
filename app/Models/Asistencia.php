<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $fillable = [
        'congregacion_id',
        'a_servicio',
        'mes',
        'tipo',
        'reuniones',
        'total',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }
}