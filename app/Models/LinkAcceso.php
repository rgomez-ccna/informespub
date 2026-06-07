<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkAcceso extends Model
{
    protected $table = 'link_accesos';

    protected $fillable = [
        'congregacion_id',
        'token',
        'expires_at',
        'password_hash',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function congregacion()
    {
        return $this->belongsTo(Congregacion::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}