<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddstaffController;
use App\Http\Controllers\AddsalaryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CertificadoController;

// Ruta de la página de inicio
Route::get('/', function () {
    return view('welcome');
});

Route::get('api/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Rutas de la API para Addstaff
Route::prefix('api/addstaff')->group(function () {
    Route::get('/', [AddstaffController::class, 'index']); // Obtener todos los usuarios
    Route::post('/', [AddstaffController::class, 'store']); // Crear un nuevo usuario
    Route::get('/{id}', [AddstaffController::class, 'show']); // Obtener un usuario específico
    Route::put('/actualizar/{id}', [AddstaffController::class, 'update']); // Actualizar un usuario específico
    Route::delete('/{id}', [AddstaffController::class, 'destroy']); // Eliminar un usuario específico
});

// Rutas para salarios
Route::prefix('api/addsalary')->group(function () {
    Route::get('/', [AddsalaryController::class, 'index']); // Obtener todos los salarios
    Route::post('/', [AddsalaryController::class, 'store']); // Crear un nuevo salario
    Route::put('/actualizar/{id}', [AddsalaryController::class, 'update']); // Actualizar un salario específico
    Route::delete('/{id}', [AddsalaryController::class, 'destroy']); // Eliminar un salario específico
});

Route::prefix('api/reports')->group(function () {
    Route::post('/', [ReportsController::class, 'store']);
    Route::get('/', [ReportsController::class, 'index']);
    Route::get('/{id}', [ReportsController::class, 'show']);
    Route::put('/{id}', [ReportsController::class, 'update']);
    Route::delete('/{id}', [ReportsController::class, 'destroy']);
});

// Rutas de la API para geneación de reportes
Route::get('api/certificados', [CertificadoController::class, 'index']);
Route::post('api/certificados', [CertificadoController::class, 'store']);
