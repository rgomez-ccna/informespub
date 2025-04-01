<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{

    protected $fillable = [
        'id_publicador',
        'a_servicio',
        'mes',
        'actividad', // 0 o 1 -> si hizo predicación en ese mes
        'horas',     // solo para auxiliares y precursores
        'cursos',
        'notas',
        'aux',       // si hizo precursor auxiliar ese mes
    ];
    


    public function publicador()
{
    return $this->belongsTo(Publicador::class, 'id_publicador');
}

}
