<?php

use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\ProveedorApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST pública de COMVERZA (v1)
|--------------------------------------------------------------------------
| Tres procesos/tablas expuestos SIN validación ni autenticación, tal como
| lo pide el ejercicio: categorías, productos y proveedores.
|
|   GET     /api/v1/{recurso}        -> listar todos
|   GET     /api/v1/{recurso}/{id}   -> obtener uno
|   POST    /api/v1/{recurso}        -> crear
|   PUT     /api/v1/{recurso}/{id}   -> actualizar
|   DELETE  /api/v1/{recurso}/{id}   -> eliminar
*/
Route::prefix('v1')->name('api.')->group(function () {
    Route::get('/', fn () => response()->json([
        'api' => 'COMVERZA API',
        'version' => '1.0',
        'recursos' => [
            'categorias' => url('/api/v1/categorias'),
            'productos' => url('/api/v1/productos'),
            'proveedores' => url('/api/v1/proveedores'),
        ],
    ]))->name('index');

    Route::apiResource('categorias', CategoriaApiController::class)->parameters(['categorias' => 'id']);
    Route::apiResource('productos', ProductoApiController::class)->parameters(['productos' => 'id']);
    Route::apiResource('proveedores', ProveedorApiController::class)->parameters(['proveedores' => 'id']);
});
