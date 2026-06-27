<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VidaMinisterioCalificacion extends Model
{
    protected $fillable = [
        'congregacion_id',
        'publicador_id',
        'tipo_asignacion',
        'activo',
        'observacion',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function publicador()
    {
        return $this->belongsTo(Publicador::class);
    }
}