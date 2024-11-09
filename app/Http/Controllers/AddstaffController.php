<?php


namespace App\Http\Controllers;

use App\Models\Addstaff;
use Illuminate\Http\Request;

class AddstaffController extends Controller
{
    public function index()
    {
        return response()->json(Addstaff::all());
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
        ]);

        $addstaff = Addstaff::create($data);
        return response()->json(['message' => 'Usuario creado exitosamente', 'data' => $addstaff], 201);
    }

    public function show($id)
    {
        $addstaff = Addstaff::findOrFail($id);
        return response()->json($addstaff);
    }

    public function update(Request $request, $id)
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
        ]);

        $addstaff = Addstaff::findOrFail($id);
        $addstaff->update($data);
        return response()->json(['message' => 'Usuario actualizado exitosamente', 'data' => $addstaff]);
    }

    public function destroy($id)
    {
        $addstaff = Addstaff::findOrFail($id);
        $addstaff->delete();
        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }
}
