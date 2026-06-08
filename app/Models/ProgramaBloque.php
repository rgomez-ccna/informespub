<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaBloque extends Model
{
    protected $fillable = [
        'congregacion_id',
        'programa_id',
        'user_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'activo',
        'orden',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function programa()
    {
        return $this->belongsTo(Programa::class);
    }

    public function registros()
    {
        return $this->hasMany(ProgramaRegistro::class)->orderBy('fecha')->orderBy('orden');
    }
}