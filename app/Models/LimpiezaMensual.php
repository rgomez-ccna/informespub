<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimpiezaMensual extends Model
{
    use HasFactory;

      protected $fillable = [
        'fecha',
        'congregacion',
        'observaciones',
        'observacion_general',
    ];
}
