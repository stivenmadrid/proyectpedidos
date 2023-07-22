<?php

namespace App\Http\Controllers\Mesa;

use App\Events\NuevaMesaEvent;
use App\Http\Controllers\Controller;
use App\Models\Mesa\Mesa;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mesa = Mesa::all();

        return response()->json($mesa);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'capacidad' => 'required',
            'Estado' => 'in:Libre,Ocupada',
            'Descripcion' => 'required',
        ]);

        try {
            $mesa = new Mesa();
            $mesa->nombre = $request->input('nombre');
            $mesa->capacidad = $request->input('capacidad');
            $mesa->Estado = $request->input('Estado');
            $mesa->Descripcion = $request->input('Descripcion');
            $mesa->save();

            // Emitir el evento NuevaMesaEvent

            return response()->json([
                'message' => 'Mesa creada exitosamente',
                'data' => $mesa,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la mesa: '.$e->getMessage(),
            ], 500);
        }

    }

    public function destroy($id)
    {
        try {
            $mesa = Mesa::find($id);
            if (! $mesa) {
                return response()->json(['message' => 'Registro no encontrado'], 404);
            }
            $mesa->delete();

            return response()->json(['message' => 'Mesa eliminada con éxito']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la mesa'.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $mesa = Mesa::find($id);
            if (! $mesa) {
                return response()->json(['message' => 'Registro no encontrado'], 404);
            }
            $mesa->nombre = $request->input('nombre');
            $mesa->capacidad = $request->input('capacidad');
            $mesa->Estado = $request->input('Estado');
            $mesa->Descripcion = $request->input('Descripcion');
            $mesa->save();

            return response()->json(['message' => 'Registro actualizado con éxito', 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar el registro'.$e->getMessage()], 500);
        }

    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nombre' => 'required',
    //         'capacidad' => 'required',
    //         'Estado' => 'in:Libre,Ocupada',
    //         'Descripcion' => 'required',
    //     ]);

    //     $mesa = new Mesa();
    //     $mesa->nombre = $request->input('nombre');
    //     $mesa->capacidad = $request->input('capacidad');
    //     $mesa->Estado = $request->input('Estado');
    //     $mesa->Descripcion = $request->input('Descripcion');
    //     $mesa->save();

    //   // Emitir el evento NuevaMesaEvent
    //   Event::dispatch(new NuevaMesaEvent([
    //     'nombre' => $mesa->nombre,
    //     'capacidad' => $mesa->capacidad,
    //     'Estado' => $mesa->Estado,
    //     'Descripcion' => $mesa->Descripcion,
    // ]));

    //     return response()->json([
    //         'message' => 'Mesa creada exitosamente',
    //         'data' => $mesa
    //     ], 201);
    // }
}
