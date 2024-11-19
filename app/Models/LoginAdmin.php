<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class LoginAdmin extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false; // No es auto-incremental
    protected $keyType = 'string'; // Tipo string

    protected $fillable = [
        'id', // NIT
        'name',
        'email',
        'direccion',
        'telefono',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}
