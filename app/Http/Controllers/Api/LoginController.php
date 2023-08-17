<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;


class LoginController extends Controller {

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions()->pluck('name');
    
            return response()->json([
                'token' => $token,
                'roles' => $roles,
                'permissions' => $permissions,
            ], 200);
        } else {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name', // Validar que el rol exista
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }
    
        $role = Role::where('name', $request->role)->first(); // Encontrar el rol por nombre
    
        if (!$role) {
            return response()->json(['message' => 'Error, el rol especificado no existe'], 422);
        }
    
        $user = User::where('email', $request->email)->first();
    
        if ($user) {
            return response()->json(['message' => 'Error, el usuario ya existe'], 422);
        }
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        $user->assignRole($role); // Asignar el rol al usuario
    
        return response()->json(['message' => 'Usuario registrado exitosamente'], 201);
    }
    
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            // Revoke all user tokens
            $user->tokens()->delete();

            return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
        } else {
            return response()->json(['message' => 'No se encontró ningún usuario autenticado'], 401);
        }
    }

    public function listusers() {
        $user = User::with('roles')->get();
        return response()->json($user);
        
    }

    public function eliminarUsuario($id) {
        $user = User::find($id);
        
        if (!$user) {
          return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
      
        $user->delete();
      
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
      }
      

      
      public function actualizarUsuario(Request $request, $id)
      {
          $user = User::find($id);
      
          if (!$user) {
              return response()->json(['message' => 'Usuario no encontrado'], 404);
          }
      
          $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required|exists:roles,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol especificado no existe.',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 422);
        }
        
      
          $role = Role::where('name', $request->role)->first();
      
          if (!$role) {
              return response()->json(['message' => 'Error, el rol especificado no existe'], 422);
          }
      
          $user->update([
              'name' => $request->name,
              'email' => $request->email,
          ]);
      
          $user->syncRoles([$role]); // Actualizar los roles del usuario
      
          return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
      }
      
      public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Contraseña restablecida correctamente'], 200)
        : response()->json(['message' => 'Error al restablecer la contraseña'], 400);
}

public function resetUserPassword(Request $request, $userId)
{
    $user = User::find($userId);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    $validator = Validator::make($request->all(), [
        'password' => 'required|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 422);
    }

    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return response()->json(['message' => 'Contraseña restablecida correctamente'], 200);
}
}

