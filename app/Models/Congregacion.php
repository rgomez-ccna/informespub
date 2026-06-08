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


    // Tablero de programas
    public function programas()
{
    return $this->hasMany(Programa::class);
}

public function programaRegistros()
{
    return $this->hasMany(ProgramaRegistro::class);
}

public function programaBloques()
{
    return $this->hasMany(ProgramaBloque::class);
}

}