<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Factura #{{ $factura->numero_factura }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 14px;

            color: #33 .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .details {
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f4f4f4;
            }

            .total {
                font-weight: bold;
                text-align: right;
            }
    </style>
</head>

<body>
    <div class="header">
        <h2>Tienda Laravel</h2>
        <p>Factura #: {{ $factura->numero_factura }}</p>
        <p>Fecha: {{ $factura->fecha_emision}}</p>
    </div>
    <div class="details">
        <strong>Cliente:</strong> {{ $factura->cliente->nombre ?? 'Consumidor Final' }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->productos as $producto)
                <tr>
                    <td>{{ $producto->nombre_producto }}</td>
                    <td>{{ $producto->pivot->cantidad }}</td>
                    <td>${{ number_format($producto->pivot->precio_venta, 2) }}</td>
                    <td>${{ number_format($producto->pivot->subtotal_linea, 2) }}</td>
                    
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">iva:</td>
                <td>${{ number_format($factura->total_iva, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="total">Total:</td>
                <td>${{ number_format($factura->total_pagar, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>