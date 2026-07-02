<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\ProductoController;
use App\Http\Controllers\api\FacturaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileUploadController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('producto', ProductoController::class);
    Route::apiResource('factura', FacturaController::class);
    Route::post('factura/{id}/add-producto', [FacturaController::class, 'agregarProducto']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('producto/inventario/upload', [FileUploadController::class, 'uploadCsv']);
});

Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    Route::get('cliente', [ClienteController::class, 'index']);
    Route::get('producto', [ProductoController::class, 'index']);
    Route::get('factura', [FacturaController::class, 'index']);
    Route::post('factura', [FacturaController::class, 'store']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('registrar', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
