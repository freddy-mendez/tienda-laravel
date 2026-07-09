<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Factura;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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


    public function cargaMasiva(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        // Lee el archivo completo como arreglo: [0 => hojaFacturas, 1 => hojaDetalle]
        $hojas = Excel::toArray([], $request->file('archivo'));

        $filasFacturas = $hojas[0]; // primera hoja
        $filasDetalle  = $hojas[1]; // segunda hoja

        $encabezadoFact = array_shift($filasFacturas); // quita fila de encabezados
        $encabezadoDet  = array_shift($filasDetalle);

        $resultado = [];

        DB::beginTransaction();
        try {
            foreach ($filasFacturas as $fila) {
                [$numeroFactura, $fechaEmision, $clienteId, $metodoPago] = $fila;

                if (!Cliente::find($clienteId)) {
                    throw ValidationException::withMessages([
                        'cliente_id' => "Cliente $clienteId no existe (factura $numeroFactura)"
                    ]);
                }

                // Detalles que pertenecen a esta factura
                $detallesFactura = collect($filasDetalle)->filter(
                    fn ($d) => $d[0] === $numeroFactura
                );

                if ($detallesFactura->isEmpty()) {
                    throw ValidationException::withMessages([
                        'detalle' => "La factura $numeroFactura no tiene detalles"
                    ]);
                }

                $subtotal = 0;
                $totalIva = 0;
                $detallesParaGuardar = [];

                foreach ($detallesFactura as $d) {
                    [, $productoId, $cantidad, $precioVenta] = $d;
                    $producto = Producto::find($productoId);

                    if (!$producto) {
                        throw ValidationException::withMessages([
                            'producto_id' => "Producto $productoId no existe (factura $numeroFactura)"
                        ]);
                    }

                    $subtotalLinea = $cantidad * $precioVenta;
                    $ivaLinea = $subtotalLinea * ($producto->iva_porcentaje / 100);

                    $subtotal += $subtotalLinea;
                    $totalIva += $ivaLinea;

                    $detallesParaGuardar[] = [
                        'producto_id' => $productoId,
                        'cantidad' => $cantidad,
                        'precio_venta' => $precioVenta,
                        'subtotal_linea' => $subtotalLinea,
                    ];
                }

                $factura = Factura::create([
                    'numero_factura' => $numeroFactura,
                    'fecha_emision' => $fechaEmision,
                    'cliente_id' => $clienteId,
                    'subtotal' => $subtotal,
                    'total_iva' => $totalIva,
                    'total_pagar' => $subtotal + $totalIva,
                    'metodo_pago' => $metodoPago,
                ]);

                foreach ($detallesParaGuardar as $det) {
                    $factura->productos()->attach($det['producto_id'], [
                        'cantidad' => $det['cantidad'],
                        'precio_venta' => $det['precio_venta'],
                        'subtotal_linea' => $det['subtotal_linea'],
                    ]);
                }

                $resultado[] = $factura->numero_factura;
            }

            DB::commit();

            return response()->json([
                'message' => 'Carga masiva exitosa',
                'facturas_creadas' => $resultado,
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error en la carga masiva', 'error' => $e->getMessage()], 500);
        }
    }

}
