<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'congregacion_id',   
        'name',   
        'email',
        'password',
        'role',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function esSuperadmin()
    {
        return $this->role === 'superadmin';
    }

    public function esSecretario()
    {
        return $this->role === 'secretario';
    }

    public function esColaborador()
    {
        return $this->role === 'colaborador';
    }

    public function esTablero()
    {
        return $this->role === 'tablero';
    }
    
   
}
