<?php

namespace App\Http\Controllers;

use App\Models\Addsalary;
use App\Models\Addstaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddsalaryController extends Controller
{
    protected $cacheTtl = 30; // Reducido a 30 minutos

    public function index()
    {
        $cacheKey = 'salaries_all_optimized_v2';
        
        return response()->json(
            Cache::remember($cacheKey, $this->cacheTtl, function() {
                // Optimización 1: Selección explícita de columnas necesarias
                $salaries = Addsalary::select([
                    'addsalaries.id',
                    'addsalaries.id_empleado',
                    'addsalaries.salario',
                    'addsalaries.tiempo_pago',
                    'addsalaries.created_at',
                    'addsalaries.updated_at'
                ])
                ->with(['staff' => function($query) {
                    // Optimización 2: Carga ansiosa solo con campos necesarios
                    $query->select([
                        'id',
                        'name',
                        'apellidos',
                        'cargo'
                    ]);
                }])
                ->get();
                
                // Optimización 3: Transformación de datos para evitar N+1
                return $salaries->map(function($salary) {
                    return [
                        'id' => $salary->id,
                        'id_empleado' => $salary->id_empleado,
                        'salario' => $salary->salario,
                        'tiempo_pago' => $salary->tiempo_pago,
                        'created_at' => $salary->created_at,
                        'updated_at' => $salary->updated_at,
                        'name' => $salary->staff->name ?? null,
                        'apellidos' => $salary->staff->apellidos ?? null,
                        'cargo' => $salary->staff->cargo ?? null,
                    ];
                });
            })
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateSalaryData($request);
        
        DB::beginTransaction();
        try {
            $salary = Addsalary::create($data);
            $this->clearAllSalaryCaches();
            DB::commit();
            
            return response()->json([
                'message' => 'Salario agregado exitosamente', 
                'data' => $salary
            ], 201);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear salario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateSalaryData($request, $id);
        
        DB::beginTransaction();
        try {
            $salary = Addsalary::findOrFail($id);
            $salary->update($data);
            $this->clearAllSalaryCaches();
            DB::commit();
            
            return response()->json([
                'message' => 'Salario actualizado exitosamente', 
                'data' => $salary
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar salario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $salary = Addsalary::findOrFail($id);
            $salary->delete();
            $this->clearAllSalaryCaches();
            DB::commit();
            
            return response()->json(['message' => 'Salario eliminado exitosamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al eliminar salario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function validateSalaryData(Request $request, $id = null)
    {
        return $request->validate([
            'id_empleado' => $id ? 'numeric|exists:addstaffs,id' : 'required|numeric|exists:addstaffs,id',
            'salario' => $id ? 'numeric' : 'required|string',
            'tiempo_pago' => $id ? 'string' : 'required|string',
        ]);
    }

    protected function clearAllSalaryCaches()
    {
        Cache::forget('salaries_all_optimized_v2');
        Cache::forget('salaries_all_with_staff'); // Por si acaso existe versión anterior
        
        // Si tienes cachés individuales, podrías implementar un sistema más robusto aquí
        // como usar tags de Redis si tu driver lo soporta
    }
    
    // Relación en el modelo Addsalary (debes agregar esto en tu modelo)
    /*
    public function staff()
    {
        return $this->belongsTo(Addstaff::class, 'id_empleado', 'id');
    }
    */
}