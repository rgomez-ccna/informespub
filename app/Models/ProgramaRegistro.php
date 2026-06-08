<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaRegistro extends Model
{
protected $fillable = [
    'congregacion_id',
    'programa_id',
    'programa_bloque_id',
    'user_id',
    'fecha',
    'titulo',
    'estado',
    'tipo_fila',
    'texto_especial',
    'orden',
];

protected $casts = [
    'fecha' => 'date',
];

public function programa()
{
    return $this->belongsTo(Programa::class);
}

public function valores()
{
    return $this->hasMany(ProgramaValor::class);
}

public function bloque()
{
    return $this->belongsTo(ProgramaBloque::class, 'programa_bloque_id');
}
}
