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
            'titulo' => 'sometimes|required|string',
            'titulo_2' => 'sometimes|required|string',
            'parrafo' => 'sometimes|required|string',
            'expedicion' => 'sometimes|required|string',
            'tamano_letra_titulo' => 'sometimes|required|string',
            'tamano_letra_titulo_2' => 'sometimes|required|string',
            'tamano_letra_parrafo' => 'sometimes|required|string',
            'tamano_letra_expedicion' => 'sometimes|required|string',
            'margen_izquierdo' => 'sometimes|required|string',
            'margen_derecho' => 'sometimes|required|string',
            'margen_superior' => 'sometimes|required|string',
            'margen_inferior' => 'sometimes|required|string',
            'tamano_hoja' => 'sometimes|required|string',
            'estilo_letra_titulo' => 'sometimes|required|string',
            'estilo_letra_titulo_2' => 'sometimes|required|string',
            'estilo_parrafo' => 'sometimes|required|string',
            'estilo_expedicion' => 'sometimes|required|string',
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
