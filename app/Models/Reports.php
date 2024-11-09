<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo', 
        'titulo_2', 
        'parrafo', 
        'expedicion', 
        'tamano_letra_titulo', 
        'tamano_letra_titulo_2', 
        'tamano_letra_parrafo', 
        'tamano_letra_expedicion', 
        'estilo_letra_titulo', 
        'estilo_letra_titulo_2', 
        'estilo_parrafo', 
        'estilo_expedicion', 
        'margen_izquierdo', 
        'margen_derecho', 
        'margen_superior', 
        'margen_inferior', 
        'tamano_hoja'
    ];
}
