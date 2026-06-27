<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VidaMinisterio extends Model
{
    protected $fillable = [
        'congregacion_id',
        'user_id',
        'fecha',
        'hora_inicio',
        'lectura_semanal',
        'nombre_sala_auxiliar',
        'cancion_inicio',
        'cancion_medio',
        'cancion_final',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partes()
    {
        return $this->hasMany(VidaMinisterioParte::class)
            ->orderBy('orden');
    }

    public function asignaciones()
    {
        return $this->hasMany(VidaMinisterioAsignacion::class)
            ->orderBy('orden');
    }
}