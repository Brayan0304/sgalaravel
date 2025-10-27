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
                // Obtener salarios primero
                $salaries = Addsalary::select([
                    'addsalaries.id',
                    'addsalaries.id_empleado',
                    'addsalaries.salario',
                    'addsalaries.tiempo_pago',
                    'addsalaries.created_at',
                    'addsalaries.updated_at'
                ])->get();

                // Extraer los ids de empleado, normalizarlos a string y únicos
                $employeeIds = $salaries->pluck('id_empleado')
                    ->filter()
                    ->map(fn($v) => (string) trim($v))
                    ->unique()
                    ->values()
                    ->all();

                // Cargar empleados una sola vez con ids como strings para evitar el error de Postgres
                $employees = Addstaff::select(['id', 'name', 'apellidos', 'cargo'])
                    ->whereIn('id', $employeeIds)
                    ->get()
                    ->keyBy(function($item) { return (string) $item->id; });

                // Transformar y adjuntar datos del empleado (si existe)
                return $salaries->map(function($salary) use ($employees) {
                    $emp = $employees[(string) $salary->id_empleado] ?? null;
                    return [
                        'id' => $salary->id,
                        'id_empleado' => $salary->id_empleado,
                        'salario' => $salary->salario,
                        'tiempo_pago' => $salary->tiempo_pago,
                        'created_at' => $salary->created_at,
                        'updated_at' => $salary->updated_at,
                        'name' => $emp->name ?? null,
                        'apellidos' => $emp->apellidos ?? null,
                        'cargo' => $emp->cargo ?? null,
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
            // El ID de empleado se almacena como string en la tabla addstaffs
            'id_empleado' => $id ? 'string|exists:addstaffs,id' : 'required|string|exists:addstaffs,id',
            // Salario siempre numérico
            'salario' => $id ? 'numeric' : 'required|numeric',
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