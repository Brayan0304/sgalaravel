<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginAdminController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|unique:login_admins,id', // NIT debe ser único
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:login_admins,email',
            'direccion' => 'required|string',
            'telefono' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = LoginAdmin::create([
            'id' => $request->id, // NIT como ID
            'name' => $request->name,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Usuario registrado correctamente', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = LoginAdmin::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
        ], 200);
    }
}
