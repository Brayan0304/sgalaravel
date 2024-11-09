<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addsalary extends Model
{
    use HasFactory;

    protected $table = 'addsalaries';
    protected $fillable = [
        'id_empleado', 'salario', 'tiempo_pago',
    ];

    protected $casts = [
        'id_empleado' => 'string', // Solo id_empleado
    ];

    public function staff()
    {
        return $this->belongsTo(Addstaff::class, 'id_empleado', 'id');
    }


}

