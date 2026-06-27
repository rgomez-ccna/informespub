<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VidaMinisterioParte extends Model
{
    protected $fillable = [
        'congregacion_id',
        'vida_ministerio_id',
        'seccion',
        'tipo_asignacion',
        'numero',
        'titulo',
        'duracion_minutos',
        'orden',
        'hora_inicio',
        'hora_fin',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function vidaMinisterio()
    {
        return $this->belongsTo(VidaMinisterio::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(VidaMinisterioAsignacion::class)
            ->orderBy('orden');
    }
}