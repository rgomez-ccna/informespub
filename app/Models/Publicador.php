<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicador extends Model
{
    protected $fillable = [
        'congregacion_id',
        'nombre',
        'fnacimiento',
        'fbautismo',
        'hombre',
        'mujer',
        'oo',
        'ungido',
        'anciano',
        'sv',
        'precursor',
        'direccion',
        'telefono',
        'mail',
        'grupo',
        'rol',
        'estado'
    ];

public function registros()
{
    return $this->hasMany(Registro::class, 'id_publicador');
}

public function congregacion()
{
    return $this->belongsTo(Congregacion::class);
}


// Reunion vida y ministerio
public function vidaMinisterioCalificacions()
{
    return $this->hasMany(VidaMinisterioCalificacion::class);
}

public function vidaMinisterioAsignacions()
{
    return $this->hasMany(VidaMinisterioAsignacion::class);
}

}
