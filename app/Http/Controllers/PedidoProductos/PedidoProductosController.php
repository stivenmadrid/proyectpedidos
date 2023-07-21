<?php

namespace App\Http\Controllers\PedidoProductos;

use App\Http\Controllers\Controller;
use App\Models\Pedido\Pedido;
use Illuminate\Http\Request;

class PedidoProductosController extends Controller
{
    public function agregarProducto(Request $request, $pedidoId)
    {
        $pedido = Pedido::findOrFail($pedidoId);
        $productoId = $request->input('producto_id');
        $cantidad = $request->input('cantidad');

        $pedido->productos()->attach($productoId, ['cantidad' => $cantidad]);

        return response()->json(['message' => 'Producto agregado al pedido con éxito']);
    }

    public function eliminarProducto(Request $request, $pedidoId)
    {
        $pedido = Pedido::findOrFail($pedidoId);
        $productoId = $request->input('producto_id');

        $pedido->productos()->detach($productoId);

        return response()->json(['message' => 'Producto eliminado del pedido con éxito']);
    }
}
