<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Categorias\CategoriasController;
use App\Http\Controllers\Mesa\MesaController;
use App\Http\Controllers\Pagos\PagoController;
use App\Http\Controllers\Pedido\PedidoController;
use App\Http\Controllers\Productos\ProductosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return 'API';
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

//Mesas api
Route::get('v1/Mesas', [MesaController::class, 'Index'])->middleware('auth:sanctum');
Route::post('v1/Mesas', [MesaController::class, 'store'])->middleware('auth:sanctum');
Route::delete('v1/Mesas/destroy/{id}', [MesaController::class, 'destroy'])->middleware('auth:sanctum');
Route::put('v1/Mesas/update/{id}', [MesaController::class, 'update'])->middleware('auth:sanctum');

//api categorias
Route::get('v1/categorias', [CategoriasController::class, 'index'])->middleware('auth:sanctum');
Route::post('v1/categorias/store', [CategoriasController::class, 'store'])->middleware('auth:sanctum');
Route::delete('v1/categorias/destroy/{id}', [CategoriasController::class, 'destroy'])->middleware('auth:sanctum');
Route::put('v1/categorias/update/{id}', [CategoriasController::class, 'update'])->middleware('auth:sanctum');

//productos api
Route::get('v1/productos', [ProductosController::class, 'index'])->middleware('auth:sanctum');
Route::post('v1/productos/store', [ProductosController::class, 'store'])->middleware('auth:sanctum');
Route::put('v1/productos/update/{id}', [ProductosController::class, 'update'])->middleware('auth:sanctum');
Route::delete('v1/productos/destroy/{id}', [ProductosController::class, 'destroy'])->middleware('auth:sanctum');

//pedidos

Route::get('v1/pedidos-cocina', [PedidoController::class, 'index'])->middleware('auth:sanctum');
Route::post('v1/pedidos-cocina/store', [PedidoController::class, 'store'])->middleware('auth:sanctum');
Route::get('v1/pedidos-liberados', [PedidoController::class, 'Pedidosliberados'])->middleware('auth:sanctum');
Route::put('/v1/liberar-pedido/{pedidoId}', [PedidoController::class, 'liberarPedido']);

//pagos

Route::get('v1/pagos/lista', [PagoController::class, 'PagosGenerados'])->middleware('auth:sanctum');
Route::post('v1/generarpago', [PagoController::class, 'store'])->middleware('auth:sanctum');
