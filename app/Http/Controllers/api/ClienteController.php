<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();

        if ($user->role->nombre == 'admin') {
            $clientes = Cliente::with('facturas')->get();
            return response()->json($clientes);
        } else if ($user->role->nombre == 'cliente') {
            $clientes = Cliente::with('facturas')->where('numero_documento',$user->cliente->numero_documento)->get();
            return response()->json($clientes);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = Auth::user();
        if ($user->role->nombre == 'admin') {
            $role = Role::where('nombre', 'cliente')->first();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => $role->id,
            ]);
            $request->merge(["user_id"=>$user->id]);
            //return response()->json($request, 201);
            $cliente = Cliente::create($request->all());
            return response()->json($cliente, 201);
        } else {
            return response()->json(['message' => 'No tiene permisos para crear un Cliente'], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = Auth::user();
        if ($user->role->nombre == 'admin') {
            $cliente = Cliente::find($id);
            if ($cliente) {
                return response()->json($cliente, 200);
            } else {
                return response()->json(['message' => 'Cliente not found'], 404);
            }
        } else if ($user->role->nombre == 'cliente') {
            $cliente = Cliente::find($id);
            if ($cliente && $cliente->numero_documento==$user->cliente->numero_documento) {
                return response()->json($cliente, 200);
            } else {
                return response()->json(['message' => 'Cliente not found'], 404);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $user = Auth::user();
        if ($user->role->nombre == 'admin') {
            $cliente = Cliente::find($id);
            if ($cliente) {
                $cliente->update($request->all());
                return response()->json($cliente, 200);
            } else {
                return response()->json(['message' => 'Cliente not found'], 404);
            }
        } else if ($user->role->nombre == 'cliente') {
            $cliente = Cliente::find($id);
            if ($cliente && $cliente->numero_documento==$user->cliente->numero_documento) {
                $cliente->update($request->all());
                return response()->json($cliente, 200);
            } else {
                return response()->json(['message' => 'Cliente not found'], 404);
            }
        } 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $user = Auth::user();
        if ($user->role->nombre == 'admin') {
            $cliente = Cliente::find($id);
            if ($cliente) {
                $cliente->delete();
                return response()->json(['message' => 'Cliente deleted'], 200);
            } else {
                return response()->json(['message' => 'Cliente not found'], 404);
            }
        } else {
            return response()->json(['message' => 'No tiene permisos para eliminar un Cliente'], 403);
        }
    }
}
