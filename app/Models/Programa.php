<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programa extends Model
{
    protected $fillable = [
    'congregacion_id',
    'nombre',
    'slug',
    'descripcion',
    'activo',
    'orden',
];

public function campos()
{
    return $this->hasMany(ProgramaCampo::class)->orderBy('orden');
}

public function registros()
{
    return $this->hasMany(ProgramaRegistro::class);
}

public function bloques()
{
    return $this->hasMany(ProgramaBloque::class)
        ->orderByDesc('fecha_inicio')
        ->orderByDesc('id');
}


}
