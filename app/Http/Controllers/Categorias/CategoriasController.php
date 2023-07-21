<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Categoria\categoria;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoria = categoria::all();
        return response()->json($categoria);
    }

 
   
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'Nombre_Categoria' => 'required'
        ], [
            'required' => 'El campo :attribute es obligatorio.'
        ]);
        
            //se valida si algun campo esta vacio genere el eror 
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $categoria = new Categoria();
            $categoria->Nombre_Categoria = $request->input('Nombre_Categoria');
            $categoria->save();
    
            return response()->json(['message' => 'Categoría registrada con éxito'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la categoría: ' . $e->getMessage()], 500);
        }
    }
    
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator($request->all(), [
            'Nombre_Categoria' => 'required'
        ], [
            'required' => 'El campo :attribute es obligatorio.'
        ]);

            //se valida si algun campo esta vacio genere el eror 
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

        try{
            $categoria = categoria::find($id);
            $categoria->Nombre_Categoria = $request->input('Nombre_Categoria');
            $categoria->save();
            return response()->json(['message' => 'Categoria actualizada con éxito'],201);
        }catch(Exception $e){
        return response()->json(['error' => 'Error al actualizar el registro: '.$e->getMessage()],500);
        }
        

    }

    
    public function destroy(string $id)
    {

        try{
            $categoria = categoria::find($id);
            $categoria->delete();
            return response()->json(['message'=> 'Categoria elimanda con éxito'],201);
        }catch(Exception $e){
            return response()->json(['message'=> 'Error al eliminar la categoria: ' .$e->getMessage()],500);
        }
  
    }
}
