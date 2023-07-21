<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\Pagos\pago;
use App\Models\Pedido\Pedido;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        try {
            $pedidoId = $request->input('pedidoId');
    
            // Verificar si ya existe un pago asociado al pedido
            $existePago = Pago::where('pedido_id', $pedidoId)->exists();
            if ($existePago) {
                return response()->json(['error' => 'Ya existe un pago asociado a este pedido'], 400);
            }
    
            $pago = new Pago();
            $pago->pedido_id = $pedidoId;
            $pago->monto = $request->monto;
            $pago->estado = 'Aprobado';
            $pago->created_at = now();
            $pago->updated_at = now();
            $pago->save();
    
            // Obtener el pedido asociado al pago
            $pedido = Pedido::find($pedidoId);
            if ($pedido) {
                // Actualizar el estado del pedido a "Pago"
                $pedido->estado = 'Pago';
                $pedido->save();
    
                // Actualizar el estado de la mesa a "Libre"
                $mesa = $pedido->mesa;
                if ($mesa) {
                    $mesa->Estado = 'Libre';
                    $mesa->save();
                }
            }
    
            return response()->json(['message' => 'Pago generado con éxito y estado del pedido actualizado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el pago', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function PagosGenerados(){
        $pedidos = Pedido::with(['mesa', 'productos'])
        ->orderBy('id', 'desc')
        ->where('estado','=','Pago')
        ->get();
    
    return response()->json($pedidos, 200);
    }
     

    }
