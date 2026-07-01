<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
     public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:password',
        ]);

        $role = Role::where('nombre', 'cliente')->first();
 
        $user = User::create([
            'name' => $request->nombre . ' ' . $request->apellido,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'confirm_password' => bcrypt($request->password),
            'role_id' => $role->id,
        ]);

        $request->merge(["user_id"=>$user->id]);
        $cliente = Cliente::create($request->all());


 
        // Crear token para el dispositivo móvil o cliente API
        $token = $user->createToken('tienda_api_web')->plainTextToken;
 
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
 
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = Auth::user();

        if ($user->tokens()->exists() && $user->tokens()->where('name', 'tienda_api_web')->exists()) {
            $user->tokens()->where('name', 'tienda_api_web')->delete();
        }
        
        $token = $user->createToken('tienda_api_web')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
    }

    
}
