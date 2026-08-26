<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\ProductoController;
use App\Http\Controllers\api\FacturaController;
use App\Http\Controllers\Api\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('producto', ProductoController::class);
    Route::apiResource('factura', FacturaController::class);
    Route::post('factura/{id}/add-producto', [FacturaController::class, 'agregarProducto']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('registrar', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('producto/{id}/imagen', [ProductoController::class, 'uploadImagen']);
