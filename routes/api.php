<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddstaffController;

Route::get('/addstaffs', [AddstaffController::class, 'index']);
Route::post('/addstaffs', [AddstaffController::class, 'store']);
Route::get('/addstaffs/{id}', [AddstaffController::class, 'show']);
Route::put('/addstaffs/{id}', [AddstaffController::class, 'update']);
Route::delete('/addstaffs/{id}', [AddstaffController::class, 'destroy']);
