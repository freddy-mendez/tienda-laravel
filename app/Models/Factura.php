<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    public $primaryKey = 'factura_id';

    public $fillable = [
        'numero_factura',
        'fecha_emision',
        'cliente_id',
        'subtotal',
        'total_iva',
        'total_pagar',
        'metodo_pago'
    ];

    public $timestamps = false;

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_facturas', 'factura_id', 'producto_id')
                    ->withPivot('detalle_id', 'cantidad', 'precio_venta', 'subtotal_linea');
    }
}
