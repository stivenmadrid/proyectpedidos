<?php

namespace App\Http\Controllers\Indicadores;

use App\Http\Controllers\Controller;
use App\Models\Pedido\Pedido;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function NumeroVentasMes()
    {
        $indicadores = Pedido::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_pedidos')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    
        return response()->json($indicadores);
    }
    
}
