<?php

namespace App\Http\Controllers\Productos;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Producto\Producto;
use App\Models\Categoria;
use Dotenv\Exception\ValidationException;
use Exception;

use Illuminate\Http\Request;

class ProductosController extends Controller

{  public function index()
    {
        $productos = Producto::with('categoria')->get();

    return response()->json($productos, 200);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|unique:productos',
                'descripcion' => 'required',
                'precio' => 'required|filled',
                'categoria_id' => 'required'
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'nombre.unique' => 'El nombre ya está en uso.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'precio.required' => 'El precio es obligatorio.',
                'precio.filled' => 'El precio es obligatorio.',
                'categoria_id.required' => 'El ID de categoría es obligatorio.'
            ]);
        
            $validator->validate();
        
    
            $producto =  new Producto;
            $producto->nombre = $request->input('nombre');
            $producto->descripcion = $request->input('descripcion');
            $producto->precio = $request->input('precio');
            $producto->categoria_id = $request->input('categoria_id');
            $producto->save();
    
            return response()->json(['message' => 'Producto registrado con éxito'],200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al registrar el producto: ' . $e->getMessage()], 500);
        }
    }
      

    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->update($request->all());
            return response()->json(['message' => 'Producto actualizado con éxito'],200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error al actualizar el producto: ' . $e->getMessage()], 500);
        }
    }
    

    public function destroy($id)
    {
        Producto::findOrFail($id)->delete();
        return response()->json(['message' => 'Producto eliminado con éxito']);
    }
}
