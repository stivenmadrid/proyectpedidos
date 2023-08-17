<?php

namespace App\Http\Controllers\Pedido;

use App\Events\NuevoPedidoEvent;
use App\Http\Controllers\Controller;
use App\Models\Adiciones\Adicion;
use App\Models\Mesa\Mesa;
use App\Models\Pedido\Pedido;
use App\Models\Producto\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function listapedidos()
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
        DB::beginTransaction(); // Iniciar la transacción

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
            $pedido->save();

            // Adjuntar los productos al pedido con la cantidad y el campo cocina
            $productosCocina = [];
        foreach ($request->productos as $producto) {
            // Asegurarse de que el producto existe antes de adjuntarlo al pedido
            $productoExistente = Producto::find($producto['id']);
            if ($productoExistente) {
                $pedido->productos()->attach($producto['id'], [
                    'cantidad' => $producto['cantidad'],
                    'observacion' => $producto['observaciones'] ?? null,
                    'cocina' => $producto['cocina'] ?? false,
                ]);

                // Agregar el producto al array de productos para cocina con toda la información
                if ($producto['cocina'] ?? false) {
                    $productoCocinaInfo = [
                        'id' => $productoExistente->id,
                        'nombre' => $productoExistente->nombre,
                        'cantidad' => $producto['cantidad'],
                        'observaciones' => $producto['observaciones'] ?? null,
                    ];
                    $productosCocina[] = $productoCocinaInfo;
                }
            }
        }

        // Cargar la relación 'mesa', 'productos' y 'adiciones' con los datos actualizados
        $pedido->load('mesa', 'productos', 'adiciones');

        // Emitir el evento NuevoPedidoEvent con el modelo Pedido completo
        event(new NuevoPedidoEvent($pedido));

        DB::commit(); // Confirmar la transacción si no hay errores

        return response()->json(['message' => 'Pedido creado con éxito', 'pedido_id' => $pedido->id]);
    } catch (\Exception $e) {
        DB::rollBack(); // Deshacer la transacción en caso de error
        return response()->json(['message' => 'Error al crear el pedido: ' . $e->getMessage()], 500);
    }
}
   
        public function agregarAdicion(Request $request, $pedidoId)
        {
            try {
                // Encuentra el pedido correspondiente al ID proporcionado
                $pedido = Pedido::findOrFail($pedidoId);

                // Obtén los datos de las adiciones desde la solicitud
                $adicionesData = $request->input('adiciones');

                // Recorre los datos de las adiciones y crea cada adición asociada al pedido
                foreach ($adicionesData as $adicionData) {
                    $cantidad = $adicionData['cantidad'];
                    $cocina = $adicionData['cocina'] ?? false;
                    $observacion = $adicionData['observacion'];

                    // Crea la adición y asóciala al pedido
                    $adicion = new Adicion();
                    $adicion->pedido_id = $pedido->id;
                    $adicion->cantidad = $cantidad;
                    $adicion->cocina = $cocina;
                    $adicion->observacion = $observacion;
                    $adicion->save();

                    // Agrega los productos asociados a la adición, si los hay
                    $productosData = $adicionData['productos'] ?? [];
                    foreach ($productosData as $productoData) {
                        $productoId = $productoData['id'];
                        $productoCantidad = $productoData['cantidad'];

                        // Agrega el producto a la adición utilizando la relación many-to-many
                        $adicion->productos()->attach($productoId, ['cantidad' => $productoCantidad]);
                    }
        }

        // Recarga la relación 'adiciones' en el pedido
        $pedido->load('adiciones');

        return response()->json(['message' => 'Adiciones agregadas con éxito', 'pedido' => $pedido], 200);
    } catch (Exception $e) {
        return response()->json(['message' => 'Error al agregar las adiciones: ' . $e->getMessage()], 500);
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
