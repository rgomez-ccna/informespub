<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkAcceso extends Model
{
    protected $table = 'link_accesos';
    protected $fillable = ['token','expires_at','password_hash','created_by'];
    protected $casts = ['expires_at'=>'datetime'];
}
