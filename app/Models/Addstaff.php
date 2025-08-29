<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addstaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 
        'name', 
        'apellidos', 
        'email', 'fecha_nacimiento',
        'municipio_expedicion', 'departamento_expedicion', 'direccion',
        'telefono', 'cargo',
    ];

    protected $casts = [
        'id' => 'string', // Solo id
    ];
}
