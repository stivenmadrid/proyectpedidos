<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Categorias\CategoriasController;
use App\Http\Controllers\Mesa\MesaController;
use App\Http\Controllers\Pagos\PagoController;
use App\Http\Controllers\Pedido\PedidoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Productos\ProductosController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Indicadores\IndicadorController;
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


//usuarios
Route::post('/login', [App\Http\Controllers\Api\LoginController::class, 'login']);
Route::post('V1/register', [App\Http\Controllers\Api\LoginController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Api\LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::get('V1/usuarios',[App\Http\Controllers\Api\LoginController::class,'listusers'])->middleware('auth:sanctum');
Route::post('v1/users/assign-role', [App\Http\Controllers\Api\LoginController::class, 'assignRole'])->middleware('auth:sanctum');
Route::delete('v1/usuarios/destroy/{id}', [App\Http\Controllers\Api\LoginController::class, 'eliminarUsuario'])->middleware('auth:sanctum');
Route::put('v1/usuarios/actualizar/{id}',[App\Http\Controllers\Api\LoginController::class,'actualizarUsuario'])->middleware('auth:sanctum');
Route::post('v1/reset-password/{id}',[App\Http\Controllers\Api\LoginController::class,'resetUserPassword'])->middleware('auth:sanctum');

//Roles
Route::get('v1/roles', [RoleController::class, 'index'])->middleware('auth:sanctum');
Route::post('v1/roles', [RoleController::class, 'store'])->middleware('auth:sanctum');
Route::put('v1/roles/update/{id}',[RoleController::class,'update'])->middleware('auth:sanctum');
Route::delete('v1/roles/destroy/{id}',[RoleController::class,'destroy'])->middleware('auth:sanctum');

//permisos
Route::get('V1/permissions', [PermissionController::class, 'index'])->middleware('auth:sanctum');
Route::post('V1/permissions', [PermissionController::class, 'store'])->middleware('auth:sanctum');
Route::put('V1/permissions/update/{id}', [PermissionController::class, 'update'])->middleware('auth:sanctum');
Route::delete('V1/permissions/destroy/{permission}', [PermissionController::class, 'destroy'])->middleware('auth:sanctum');

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
Route::get('v1/productos/categoria/{categoryId}', [ProductosController::class, 'getByCategory'])->middleware('auth:sanctum');

//pedidos
Route::get('v1/pedidos-cocina', [PedidoController::class, 'index'])->middleware('auth:sanctum');
Route::get('v1/lista-pedidos',[PedidoController::class,'listapedidos']);
Route::post('v1/pedidos-cocina/store', [PedidoController::class, 'store'])->middleware('auth:sanctum');
Route::get('v1/pedidos-liberados', [PedidoController::class, 'Pedidosliberados'])->middleware('auth:sanctum');
Route::put('/v1/liberar-pedido/{pedidoId}', [PedidoController::class, 'liberarPedido'])->middleware('auth:sanctum');
Route::post('v1/pedidos/{pedido}/adiciones', [PedidoController::class, 'agregarAdicion'])->middleware('auth:sanctum');
//pagos

Route::get('v1/pagos/lista', [PagoController::class, 'PagosGenerados'])->middleware('auth:sanctum');
Route::post('v1/generarpago', [PagoController::class, 'store'])->middleware('auth:sanctum');


//indicadores

Route::get('Numero-ventas-mes',[IndicadorController::class,'NumeroVentasMes'])->middleware('auth:sanctum');