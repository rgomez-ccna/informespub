<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VidaMinisterioAsignacion extends Model
{
    protected $fillable = [
        'congregacion_id',
        'vida_ministerio_id',
        'vida_ministerio_parte_id',
        'publicador_id',
        'tipo_asignacion',
        'rol',
        'sala',
        'fecha',
        'orden',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function vidaMinisterio()
    {
        return $this->belongsTo(VidaMinisterio::class);
    }

    public function vidaMinisterioParte()
    {
        return $this->belongsTo(VidaMinisterioParte::class);
    }

    public function publicador()
    {
        return $this->belongsTo(Publicador::class);
    }
}