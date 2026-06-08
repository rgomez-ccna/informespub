<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaCampo extends Model
{
    protected $fillable = [
    'programa_id',
    'nombre',
    'slug',
    'tipo',
    'opciones',
    'obligatorio',
    'visible_en_listado',
    'buscable',
    'activo',
    'orden',
];

protected $casts = [
    'opciones' => 'array',
    'obligatorio' => 'boolean',
    'visible_en_listado' => 'boolean',
    'buscable' => 'boolean',
    'activo' => 'boolean',
];

public function programa()
{
    return $this->belongsTo(Programa::class);
}

public function valores()
{
    return $this->hasMany(ProgramaValor::class);
}
}
