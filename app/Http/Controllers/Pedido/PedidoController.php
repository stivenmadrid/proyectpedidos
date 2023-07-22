<?php

namespace App\Http\Controllers\Pedido;

use App\Events\NuevoPedidoEvent;
use App\Http\Controllers\Controller;
use App\Models\Mesa\Mesa;
use App\Models\Pedido\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'productos'])
            ->orderBy('id', 'desc')
            ->where('estado', '=', 'Nuevo pedido')
            ->get();

        return response()->json($pedidos, 200);
    }

    public function Pedidosliberados()
    {
        $pedidos = Pedido::with(['mesa', 'productos'])
            ->orderBy('id', 'desc')
            ->where('estado', '=', 'Liberado')
            ->get();

        return response()->json($pedidos, 200);
    }

    public function store(Request $request)
    {
        try {
            $mesaId = $request->mesa_id ?? null;

            if ($mesaId !== null) {
                // Obtener la mesa correspondiente al ID proporcionado
                $mesa = Mesa::findOrFail($mesaId);

                // Verificar si la mesa está ocupada
                if ($mesa->Estado === 'Ocupada') {
                    return response()->json(['message' => 'No se puede realizar el pedido porque la mesa seleccionada está ocupada'], 400);
                }

                // Actualizar el estado de la mesa a "Ocupada"
                $mesa->Estado = 'Ocupada';
                $mesa->save();
            }

            // Crear el pedido
            $pedido = new Pedido;
            $pedido->mesa_id = $mesaId;
            $pedido->estado = 'Nuevo pedido'; // Asignar el estado del pedido
            $pedido->cliente = $request->cliente; // Obtener el nombre del cliente desde la solicitud
            $pedido->total = $request->total;
            $pedido->created_at = now();
            $pedido->updated_at = now();
            $pedido->save();

            foreach ($request->productos as $producto) {
                $pedido->productos()->attach($producto['id'], ['cantidad' => $producto['cantidad'], 'observacion' => $producto['observaciones'] ?? null]);

            }

            // Cargar la relación 'mesa' y 'productos' con los datos actualizados
            $pedido->load('mesa', 'productos');

            // Convertir el objeto Pedido a un array asociativo
            $pedidoArray = $pedido->toArray();

            // Emitir el evento NuevoPedidoEvent con el array asociativo del pedido
            Event::dispatch(new NuevoPedidoEvent($pedidoArray));

            return response()->json(['message' => 'Pedido creado con éxito']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el pedido: '.$e->getMessage()], 500);
        }
    }

    public function liberarPedido($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);

        if (! $pedido) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        // Actualizar el estado del pedido a "Liberado"
        $pedido->estado = 'Liberado';
        $pedido->save();

        return response()->json(['message' => 'Pedido liberado con éxito']);
    }
}
