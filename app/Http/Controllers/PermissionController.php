<?php

namespace App\Http\Controllers;

use Dotenv\Exception\ValidationException;
use Spatie\Permission\Models\Permission;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use function PHPUnit\Framework\returnSelf;

class PermissionController extends Controller
{
    public function index()
    {
        try{
            $permissions = Permission::all();
            return response()->json($permissions);
        }catch(Exception $e){
        return response()->json(['message'=>"Ups no se pudo registrar el rol!", $e->getMessage()], 401);
        }
        
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name', // Asegurarse de que el nombre del permiso sea único
            'guard_name' => 'required', // Asegurarse de tener un guard_name
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }
    
        $permiso = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);
    
        return response()->json(['message' => 'Permiso creado exitosamente'], 201);
    
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:permissions,name,' . $id,
        'guard_name' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 422);
    }

    $permission->update([
        'name' => $request->name,
        'guard_name' => $request->guard_name,
    ]);

    return response()->json(['message' => 'Permiso actualizado exitosamente'], 200);
}
   
        
      
    
public function destroy($id)
{
    try {
        $permiso = Permission::findOrFail($id); // Busca el permiso por ID

        $permiso->delete(); // Elimina el permiso

        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado exitosamente.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el permiso.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}
