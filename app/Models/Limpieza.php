<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limpieza extends Model
{
   protected $fillable = ['mes', 'grupo_asignado', 'superintendente', 'auxiliar', 'observaciones'];

}
