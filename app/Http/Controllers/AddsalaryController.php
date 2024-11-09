<?php

namespace App\Http\Controllers;

use App\Models\Addsalary;
use App\Models\Addstaff;
use Illuminate\Http\Request;

class AddsalaryController extends Controller
{
    public function index()
{
    // Obtener salarios con el nombre, apellido y cargo del empleado
    $salaries = Addsalary::select('addsalaries.*')
        ->join('addstaffs', \DB::raw('CAST(addsalaries.id_empleado AS VARCHAR)'), '=', \DB::raw('CAST(addstaffs.id AS VARCHAR)'))
        ->addSelect('addstaffs.name', 'addstaffs.apellidos', 'addstaffs.cargo')
        ->get();

    return response()->json($salaries);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'id_empleado' => 'required|numeric|exists:addstaffs,id',
            'salario' => 'required|string',
            'tiempo_pago' => 'required|string',
        ]);

        // Crear el salario sin pasar el id, ya que se autoincrementa
        $salary = Addsalary::create($data);
        return response()->json(['message' => 'Salario agregado exitosamente', 'data' => $salary], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'id_empleado' => 'numeric|exists:addstaffs,id',
            'salario' => 'numeric',
            'tiempo_pago' => 'string',
        ]);

        $salary = Addsalary::findOrFail($id);
        $salary->update($data);
        return response()->json(['message' => 'Salario actualizado exitosamente', 'data' => $salary]);
    }

    public function destroy($id)
    {
        $salary = Addsalary::findOrFail($id);
        $salary->delete();
        return response()->json(['message' => 'Salario eliminado exitosamente']);
    }
}
