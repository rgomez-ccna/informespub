<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReunionPublica extends Model
{
    protected $fillable = ['fecha', 'presidente', 'lector', 'es_nuevo_programa'];

}
