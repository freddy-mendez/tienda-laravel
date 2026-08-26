<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $productos = Producto::all();
        return response()->json($productos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $producto = Producto::create($request->all());
        return response()->json($producto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $producto = Producto::with('facturas')->find($id);
        if ($producto) {
            return response()->json($producto, 200);
        } else {
            return response()->json(['message' => 'Producto not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $producto = Producto::find($id);
        if ($producto) {
            $producto->update($request->all());
            return response()->json($producto, 200);
        } else {
            return response()->json(['message' => 'Producto not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $producto = Producto::find($id);
        if ($producto) {
            $producto->delete();
            return response()->json(['message' => 'Producto deleted'], 200);
        } else {
            return response()->json(['message' => 'Producto not found'], 404);
        }
    }
    public function uploadImagen(Request $request, string $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($producto->imagen_path && Storage::disk('public')->exists($producto->imagen_path)) {
            Storage::disk('public')->delete($producto->imagen_path);
        }

        $extension = $request->file('imagen')->getClientOriginalExtension();
        $nombreArchivo = "producto_{$producto->producto_id}.{$extension}";

        $path = $request->file('imagen')->storeAs('productos', $nombreArchivo, 'public');

        $producto->update(['imagen_path' => $path]);

        return response()->json([
            'message' => 'Imagen actualizada correctamente',
            'url' => Storage::url($producto->imagen_path),
        ]);
    }

}
