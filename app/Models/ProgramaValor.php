<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaValor extends Model
{
    protected $fillable = [
    'programa_registro_id',
    'programa_campo_id',
    'valor_texto',
    'valor_numero',
    'valor_fecha',
    'valor_hora',
    'valor_json',
];

protected $casts = [
    'valor_json' => 'array',
    'valor_fecha' => 'date',
];

public function registro()
{
    return $this->belongsTo(ProgramaRegistro::class, 'programa_registro_id');
}

public function campo()
{
    return $this->belongsTo(ProgramaCampo::class, 'programa_campo_id');
}
}
