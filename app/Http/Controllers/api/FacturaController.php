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
        $factura = Factura::create($request->all());
        $factura->load('cliente');
        return response()->json($factura, 201);
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
