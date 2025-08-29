<?php

namespace App\Http\Controllers;

use App\Models\Addstaff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AddstaffController extends Controller
{
    // Tiempo de vida del caché en minutos
    protected $cacheTtl = 60; // 1 hora

    public function index()
    {
        $cacheKey = 'addstaff_all';
        
        return response()->json(
            Cache::remember($cacheKey, $this->cacheTtl, function() {
                return Addstaff::all();
            })
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
            'apellidos' => 'required|string',
            'email' => 'required|email|unique:addstaffs,email',
            'fecha_nacimiento' => 'required|date',
            'municipio_expedicion' => 'required|string',
            'departamento_expedicion' => 'required|string',
            'direccion' => 'required|string',
            'telefono' => 'required|string',
            'cargo' => 'required|string',
            'fecha_ingreso' => 'required|date',
            'fecha_salida' => 'nullable|date',
        ]);
        // TO DO implementar patron de diseño repository
        $addstaff = Addstaff::create($data);
        
        // Invalidar caché de lista completa
        Cache::forget('addstaff_all');
        
        return response()->json([
            'message' => 'Usuario creado exitosamente', 
            'data' => $addstaff
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $cacheKey = "addstaff_{$id}";
        
        return response()->json(
            Cache::remember($cacheKey, $this->cacheTtl, function() use ($id) {
                return Addstaff::findOrFail($id);
            })
        );
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'string',
            'apellidos' => 'string',
            'email' => 'email|unique:addstaffs,email,' . $id,
            'fecha_nacimiento' => 'date',
            'municipio_expedicion' => 'string',
            'departamento_expedicion' => 'string',
            'direccion' => 'string',
            'telefono' => 'string',
            'cargo' => 'string',
            'fecha_ingreso' => 'date',
            'fecha_salida' => 'nullable|date',
        ]);

        $addstaff = Addstaff::findOrFail($id);
        $addstaff->update($data);
        
        // Invalidar caché del registro individual y de la lista
        Cache::forget("addstaff_{$id}");
        Cache::forget('addstaff_all');
        
        return response()->json([
            'message' => 'Usuario actualizado exitosamente', 
            'data' => $addstaff
        ]);
    }

    public function destroy($id)
    {
        $addstaff = Addstaff::findOrFail($id);
        $addstaff->delete();
        
        // Invalidar caché del registro eliminado y de la lista
        Cache::forget("addstaff_{$id}");
        Cache::forget('addstaff_all');
        
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}