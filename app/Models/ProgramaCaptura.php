<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaCaptura extends Model
{
    use HasFactory;
    
    protected $fillable = ['fecha','imagenes'];
  protected $casts = ['imagenes'=>'array','fecha'=>'date'];
}
