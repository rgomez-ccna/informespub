<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodador extends Model
{
    protected $fillable = [
    'fecha',
    'acceso_1',
    'acceso_2',
    'auditorio',
    'nota_final',
    'es_nuevo_programa', // ← este
];


}
