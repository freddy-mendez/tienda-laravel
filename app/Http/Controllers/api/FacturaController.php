<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Factura;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $facturas = Factura::with(['cliente', 'productos'])->get();
        return response()->json($facturas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $user = Auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else if ($user->role->nombre === 'admin' || $user->role->nombre === 'cliente') {

            $factura = Factura::create($request->all());
            $factura->load('cliente');

            foreach ($request->productos as $producto) {
                $factura->productos()->attach($producto['producto_id'], [
                    'cantidad' => $producto['cantidad'],
                    'precio_venta' => $producto['precio_venta'],
                    'subtotal_linea' => $producto['cantidad'] * $producto['precio_venta']
                ]);
            }
            $factura->load('productos');

            return response()->json($factura, 201);
        } else {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $factura = Factura::with(['cliente', 'productos'])->find($id);
        if ($factura) {
            return response()->json($factura, 200);
        } else {
            return response()->json(['message' => 'Factura not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $factura = Factura::find($id);
        if ($factura) {
            $factura->update($request->all());
            $factura->load('cliente');
            return response()->json($factura, 200);
        } else {
            return response()->json(['message' => 'Factura not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $factura = Factura::find($id);
        if ($factura) {
            $factura->delete();
            return response()->json(['message' => 'Factura deleted'], 200);
        } else {
            return response()->json(['message' => 'Factura not found'], 404);
        }
    }

    public function agregarProducto(Request $request, string $id)
    {
        $factura = Factura::find($id);
        if (!$factura) {
            return response()->json(['message' => 'Factura not found'], 404);
        }

        $productoId = $request->producto_id;
        $cantidad = $request->cantidad;
        $precioVenta = $request->precio_venta;
        $subtotalLinea = $cantidad * $precioVenta;

        // Agregar el producto a la factura
        $factura->productos()->attach($productoId, [
            'cantidad' => $cantidad,
            'precio_venta' => $precioVenta,
            'subtotal_linea' => $subtotalLinea
        ]);

        // Recargar la factura con los productos actualizados
        $factura->load('cliente', 'productos');
        return response()->json($factura, 200);
    }
}
