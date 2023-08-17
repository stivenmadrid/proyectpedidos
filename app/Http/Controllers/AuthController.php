<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
        $user = User::where('email', $validatedData['email'])->first();
    
        if ($user) {
            return response()->json(['message' => 'Error, el usuario ya existe'], 422);
        }
    
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);
    
        // Asignar el rol predeterminado al usuario recién registrado
        $defaultRole = Role::where('name', 'usuario')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }
    
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

    public function listusers(){
        
        $user = User::all();
        return response()->json($user);
    }


    public function assignRole(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $role = Role::findOrFail($validatedData['role_id']);

        $user->assignRole($role);

        return response()->json(['message' => 'Rol asignado al usuario exitosamente']);
    }

  

  
    
}
