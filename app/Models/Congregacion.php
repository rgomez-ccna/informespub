<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Congregacion extends Model
{
    protected $fillable = [
        'nombre',
        'ciudad',
        'provincia',
        'activa',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function publicadors()
    {
        return $this->hasMany(Publicador::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}