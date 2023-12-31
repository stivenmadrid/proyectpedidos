<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::all();

        return response()->json($user);

    }


    public function store(Request $request)
    {

        $inputs = $request->input();
        $inputs['password'] = Hash::make(trim($request->input('password')));

        // Verificar si ya existe un usuario con el mismo correo electrónico
        $existingUser = User::where('email', $request->input('email'))->first();

        if ($existingUser) {
            return response()->json([
                'mensaje' => 'El usuario ya existe en la base de datos',
            ]);
        }

        $user = User::create($inputs);

        return response()->json([
            'data' => $user,
            'mensaje' => 'Usuario registrado con éxito',
        ]);
    }

   
}
