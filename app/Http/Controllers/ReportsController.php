<?php

namespace App\Http\Controllers;

use App\Models\Reports;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    // Obtener todos los reportes
     public function index()
     {
         return Reports::all();
     }

    // Almacenar un nuevo reporte
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string',
            'titulo_2' => 'required|string',
            'parrafo' => 'required|string',
            'expedicion' => 'required|string',
            'tamano_letra_titulo' => 'required|string',
            'tamano_letra_titulo_2' => 'required|string',
            'tamano_letra_parrafo' => 'required|string',
            'tamano_letra_expedicion' => 'required|string',
            'margen_izquierdo' => 'required|string',
            'margen_derecho' => 'required|string',
            'margen_superior' => 'required|string',
            'margen_inferior' => 'required|string',
            'tamano_hoja' => 'required|string',
            'estilo_letra_titulo' => 'required|string',
            'estilo_letra_titulo_2' => 'required|string',
            'estilo_parrafo' => 'required|string',
            'estilo_expedicion' => 'required|string',
        ]);

        $report = Reports::create($data);
        return response()->json(['message' => 'Reporte creado exitosamente', 'data' => $report], 201);
    }

    // Mostrar un reporte específico
    public function show($id)
    {
        $report = Reports::findOrFail($id);
        return response()->json($report);
    }

    // Actualizar un reporte específico
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'titulo' => 'string',
            'titulo_2' => 'string',
            'parrafo' => 'string',
            'expedicion' => 'string',
            'tamano_letra_titulo' => 'string',
            'tamano_letra_titulo_2' => 'string',
            'tamano_letra_parrafo' => 'string',
            'tamano_letra_expedicion' => 'string',
            'margen_izquierdo' => 'string',
            'margen_derecho' => 'string',
            'margen_superior' => 'string',
            'margen_inferior' => 'string',
            'tamano_hoja' => 'string',
            'estilo_letra_titulo' => 'string',
            'estilo_letra_titulo_2' => 'string',
            'estilo_parrafo' => 'string',
            'estilo_expedicion' => 'string',
        ]);

        $report = Reports::findOrFail($id);
        $report->update($data);
        return response()->json(['message' => 'Reporte actualizado exitosamente', 'data' => $report]);
    }

    // Eliminar un reporte específico
    public function destroy($id)
    {
        $report = Reports::findOrFail($id);
        $report->delete();
        return response()->json(['message' => 'Reporte eliminado exitosamente']);
    }
}
